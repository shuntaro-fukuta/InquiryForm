CREATE DATABASE IF NOT EXISTS skillcheck;

CREATE USER IF NOT EXISTS skillcheck IDENTIFIED by 'password';
GRANT ALL ON skillcheck.* TO 'skillcheck'@'localhost' identified BY 'password';
FLUSH PRIVILEGES;
