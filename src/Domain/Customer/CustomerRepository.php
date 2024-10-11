<?php

declare(strict_types=1);

namespace App\Domain\Customer;

use App\Application\Settings\SettingsInterface;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use App\Domain\Customer\Customer as CustomerDomain;
use Doctrine\ORM\Query;
use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;

/**
 * CustomerRepository contains methods and logic for all customer related operations
 */
class CustomerRepository
{
    private EntityManager $em;
    private Client $elasticClient;

    public function __construct(SettingsInterface $settingsInterface)
    {
        // Build the doctrine entity manager
        $settings = $settingsInterface->get('doctrine');
        // Use the ArrayAdapter or the FilesystemAdapter depending on the value of the 'dev_mode' setting
        // You can substitute the FilesystemAdapter for any other cache you prefer from the symfony/cache library
        $cache = $settings['dev_mode'] ?
        new ArrayAdapter() :
        new FilesystemAdapter(directory: $settings['cache_dir']);

        $config = ORMSetup::createAttributeMetadataConfiguration(
            $settings['metadata_dirs'],
            $settings['dev_mode'],
            null,
            $cache
        );

        $connection = DriverManager::getConnection($settings['connection']);
        $this->em = new EntityManager($connection, $config);

        // ElasticSearch setup
        $this->elasticClient = ClientBuilder::create()
            ->setSSLVerification(false)
            ->setHosts(['http://cust-mgmt-app-elasticsearch:9200'])
            ->build();
    }

    /**
     * Read all customers with search options
     * 
     * @param array $queryParams
     * @return array
     */
    public function findAll(array $queryParams): array
    {
        // Check if query parameters are set
        // and build the match array
        if (
            !empty($queryParams['name']) ||
            !empty($queryParams['email']) ||
            !empty($queryParams['phone_number'])
        ) {
            $match = [];

            if (!empty($queryParams['name'])) {
                $name = ['match' => ['name' => $queryParams['name']]];
                array_push($match, $name);
            }

            if (!empty($queryParams['email'])) {
                $email = ['match' => ['email' => $queryParams['email']]];
                array_push($match, $email);
            }

            if (!empty($queryParams['phone_number'])) {
                $phoneNumber = ['match' => ['phone_number' => $queryParams['phone_number']]];
                array_push($match, $phoneNumber);
            }

            $params = [
                'index' => 'customers',
                'body'  => [
                    'size' => 25,
                    'query' => [
                        'bool' => [
                            'should' => array_values($match)
                        ],
                    ],
                ]
            ];

            // Search through ElasticSearch
            $results = $this->elasticClient->search($params);
            
            return $results['hits']['hits'];
        } else {
            // Return all records from table
            $query = $this->em->getRepository(CustomerDomain::class)
                ->createQueryBuilder('c')
                ->getQuery();
            $result = $query->getResult(Query::HYDRATE_ARRAY);
            
            return $result;
        }
    }

    /**
     * Read customer by id
     * 
     * @param int $id
     * @param bool $raw
     * @return CustomerDomain|array
     * @throws CustomerNotFoundException
     */
    public function findUserOfId(int $id, bool $raw = false): CustomerDomain|array
    {
        $customer = $this->em->getRepository(CustomerDomain::class)
        ->findOneBy(['id' => $id]);

        if (empty($customer)) {
            throw new CustomerNotFoundException();
        }

        $array = [
            'id' => $customer->getId(),
            'name' => $customer->getName(),
            'email' => $customer->getEmail(),
            'phone_number' => $customer->getPhoneNumber()
        ];

        return $raw ? $customer : $array;
    }

    /**
     * Add customer
     * 
     * @param array $postData
     * @return void
     */
    public function create(array $postData): void
    {
        // Validations
        if (
            !isset($postData['name']) ||
            !isset($postData['email']) ||
            !isset($postData['phone_number']) ||
            empty($postData['name']) ||
            empty($postData['email']) ||
            empty($postData['phone_number'])
            ) {
                throw new CustomerDetailsIncompleteException();
        }

        if (!filter_var($postData['email'], FILTER_VALIDATE_EMAIL)) {
            throw new CustomerEmailIncompleteException();
        }

        $name = $postData['name'];
        $email = $postData['email'];
        $phoneNumber = $postData['phone_number'];

        $customerData = new CustomerDomain($name, $email, $phoneNumber);
        $this->em->persist($customerData);
        $this->em->flush();
        
        $this->updateElasticSearchRecords($customerData, 'add');
    }

    /**
     * Update customer
     * 
     * @param CustomerDomain $customer
     * @param string $name
     * @param string $email
     * @param string $phoneNumber
     * @return void
     */
    public function update(CustomerDomain $customer, string $name, string $email, string $phoneNumber): void
    {
        $customer->setName($name);
        $customer->setEmail($email);
        $customer->setPhoneNumber($phoneNumber);
        
        $this->em->persist($customer);
        $this->em->flush();

        $this->updateElasticSearchRecords($customer, 'update');
    }

    /**
     * Delete customer
     * 
     * @param CustomerDomain $customer
     * @return void
     */
    public function delete(CustomerDomain $customer): void
    {
        $this->updateElasticSearchRecords($customer, 'delete');

        $this->em->remove($customer);
        $this->em->flush();
    }

    /**
     * Add, update or remove the elastic search index
     * 
     * @param CustomerDomain $customer
     * @param string $action
     * @return void
     */
    private function updateElasticSearchRecords(CustomerDomain $customer, string $action): void
    {
        $indexArray = [
            'index' => 'customers',
            'id' => $customer->getId(),
            'body' => [
                'id' => $customer->getId(),
                'name' => $customer->getName(),
                'email' => $customer->getEmail(),
                'phone_number' => $customer->getPhoneNumber(),
            ]
        ];

        switch ($action) {
            case 'add':
                $this->elasticClient->index($indexArray);
                break;
            case 'edit':
                $this->elasticClient->update($indexArray);
                break;
            case 'delete':
                $params = [
                    'index' => 'customers',
                    'id'    => $customer->getId(),
                ];
                $this->elasticClient->delete($params);
                break;
        }
    }
}
