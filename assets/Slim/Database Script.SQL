-- JAKUB RYBICKI 
CREATE DATABASE JakubRybickiDB;
USE JakubRybickiDB;

CREATE TABLE House(
house_ID int  NOT NULL auto_increment  
, name VARCHAR(30)  NOT NULL  
, tokenID VARCHAR(10)  NOT NULL  
, owner_User_ID CHAR(10)  NOT NULL  
,PRIMARY KEY (house_ID)
);


CREATE TABLE Users (
user_ID int  NOT NULL  AUTO_INCREMENT
, username VARCHAR(25)  NOT NULL  
, password VARCHAR(40)  NOT NULL  
, email VARCHAR(30)   
, phoneNumber INTEGER   
, house_ID int  
,PRIMARY KEY (user_ID)
,FOREIGN KEY (house_ID) REFERENCES House(house_ID)
);

CREATE TABLE HouseUser (
house_ID int  NOT NULL  
, user_id int  NOT NULL  
,FOREIGN KEY (house_ID) REFERENCES House(house_ID)
);


CREATE TABLE Category (
category_ID int  NOT NULL  
, CategoryName VARCHAR(25)  NOT NULL  
, house_ID int    
,PRIMARY KEY (category_ID)
,FOREIGN KEY (house_ID) REFERENCES House(house_ID)
);

CREATE TABLE Item (
Item_ID int  NOT NULL AUTO_INCREMENT  
, Product_Name VARCHAR(25)  NOT NULL  
, QTY INTEGER  NOT NULL
, Barcode INTEGER   
, warning_qty INTEGER    
, priority_number int    
, house_ID int not null
,PRIMARY KEY (item_ID)
,FOREIGN KEY (house_ID) REFERENCES House(house_ID)
);


CREATE TABLE CategoryItem (
CategoryItemID int   NOT NULL  AUTO_INCREMENT
, Item_ID int Not NULL  
, category_ID int not NULL  
,PRIMARY KEY (CategoryItemID)
,FOREIGN KEY (Item_ID) REFERENCES Item(Item_ID)
);
