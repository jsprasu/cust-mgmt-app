# Customer management app.

Customer management app with Slim, MySQL, Doctrine, ElasticSearch & Docker.

## Prerequisites

Make sure you have installed Docker and it is active. Refer https://docs.docker.com/engine/install/ for more details.

## Install the Application

1. Checkout or download the Github repository.

2. Open terminal and go to project directory.
```bash
cd <project-directory>
```

3. Move to docker folder inside the project directory.
```bash
cd docker
```

4. Build the docker containers.
```bash
docker-compose build
```

5. Start docker containers.
```bash
docker-compose up -d
```

6. Check whether the containers are up and running.
```bash
docker ps
```

7. Go inside the PHP container.
```bash
docker exec -it cust-mgmt-app-php bash
```

8. Install composer dependencies.
```bash
composer install
```

9. Run the migrations.
```bash
./vendor/bin/doctrine-migrations migrate
```

10. Exit from the PHP container.
```bash
exit
```

11. Add the host. Make sure to use the same domain mentioned below as it is used in the Nginx config file.

    a. In Mac, /etc/hosts.

    b. In Windows, C:\Windows\System32\drivers\etc\hosts.
```bash
127.0.0.1 cust-mgmt-app.local
```

That's it! Project setup is complete now.

## Usage

Please refer the API documentation.
