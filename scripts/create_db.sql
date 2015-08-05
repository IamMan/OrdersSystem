CREATE DATABASE db_orders;
GRANT ALL ON db_orders.* TO mm@'localhost' IDENTIFIED BY 'superman';

create table orders (
 id int PRIMARY KEY NOT NULL AUTO_INCREMENT,
 title VARCHAR(150) NOT NULL,
 description VARCHAR(2000) NOT NULL,
 price NUMERIC(15,2) NOT NULL,
 creator_id int NOT NULL,
 resolver_id int NULL
);

create table transaction_log(
 order_id int PRIMARY KEY NOT NULL,
 resolver_id int NOT NULL
);

CREATE TABLE accounts(
 id int PRIMARY KEY NOT NULL AUTO_INCREMENT,
 login varchar(50) NOT NULL,
 password varchar(32) NOT NULL,
 cash NUMERIC(15,2) NOT NULL DEFAULT 0,
 CONSTRAINT IX_Login UNIQUE (login)
);

insert into accounts(login, password) VALUE ('admin', md5('admin'));
insert into accounts(login, password) VALUE ('ilya', md5('pass'));
insert into accounts(login, password) VALUE ('egor', md5('passpass'));
insert into accounts(login, password) VALUE ('test1', md5('pass'));
insert into accounts(login, password) VALUE ('test2', md5('pass'));
insert into accounts(login, password) VALUE ('test3', md5('pass'));

