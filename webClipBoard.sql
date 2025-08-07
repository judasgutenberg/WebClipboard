CREATE TABLE clipboard_item(
  clipboard_item_id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NULL,
  clip TEXT NULL,
  file_extension VARCHAR(10) NULL,
  file_name VARCHAR(250) NULL,
  type_id INT NULL,
  created DATETIME ,
  altered DATETIME NULL
);

CREATE TABLE `clipboard_item_type` ( 
  `clipboard_item_type_id` int(11) NOT NULL AUTO_INCREMENT, 
  `name` varchar(200) DEFAULT NULL, 
  `created` datetime DEFAULT NULL, 
  `is_public` tinyint(4) DEFAULT NULL, 
  `user_id` int(11) DEFAULT NULL, 
  PRIMARY KEY (`clipboard_item_type_id`) ) 

CREATE TABLE user(
  user_id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(100) NULL,
  password VARCHAR(100) NULL,
  role VARCHAR(50) DEFAULT 'normal',
  expired DATETIME NULL,
  created DATETIME
);
