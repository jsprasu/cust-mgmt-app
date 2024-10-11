use mysql;

-- TODO: Change the username and password
CREATE USER IF NOT EXISTS 'cust_mgmt_user'@'%' IDENTIFIED BY 'cust_mgmt_user_123';

-- TODO: Change the username here
GRANT ALL PRIVILEGES ON *.* TO 'cust_mgmt_user'@'%';
FLUSH PRIVILEGES;

-- TODO: Change the database name here
CREATE DATABASE IF NOT EXISTS cust_mgmt_app;
