CREATE DATABASE db_orders IF NOT EXISTS;
GRANT ALL ON db_orders.* TO mm@'172.20.208.77' IDENTIFIED BY 'PASSWORD';
GRANT ALL ON db_accounts.* TO mm@'172.20.208.77' IDENTIFIED BY 'PASSWORD';

create table orders (
 id int PRIMARY KEY NOT NULL AUTO_INCREMENT,
 title VARCHAR(150) NOT NULL,
 description VARCHAR(2000) NOT NULL,
 price NUMERIC(15,2) NOT NULL,
 creator_id int NOT NULL,
 resolver_id int NULL
);

CREATE TABLE accounts(
 id int PRIMARY KEY NOT NULL AUTO_INCREMENT,
 login varchar(50) NOT NULL,
 password varchar(50) NOT NULL,
 cash NUMERIC(15,2) NOT NULL DEFAULT 0,
 CONSTRAINT IX_Login UNIQUE (login)
);

insert into accounts(login, password) VALUE ('admin', 'admin');

SELECT * from accounts;
SELECT * from orders;

update accounts set cash = cash + 100 where id = 10