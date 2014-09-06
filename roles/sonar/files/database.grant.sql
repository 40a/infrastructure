CREATE DATABASE sonar CHARACTER SET utf8 COLLATE utf8_general_ci;
grant all privileges on sonar.\* to 'sonar'@'localhost' identified by 'sonar';
flush privileges;
