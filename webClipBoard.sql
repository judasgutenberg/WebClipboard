CREATE TABLE clipboard_item(
clipboard_item_id INT AUTO_INCREMENT PRIMARY KEY,
user_id INT NULL,
clip TEXT NULL,
file_extension VARCHAR(10) NULL,
file_name VARCHAR(250) NULL,
created DATETIME ,
altered DATETIME NULL
);


CREATE TABLE user(
user_id INT AUTO_INCREMENT PRIMARY KEY,
email VARCHAR(100) NULL,
password VARCHAR(100) NULL,
role VARCHAR(50) DEFAULT 'normal',
expired DATETIME NULL,
created DATETIME
);