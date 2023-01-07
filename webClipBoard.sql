CREATE TABLE clipboard_item(
clipboard_item_id INT,
location_id INT NULL,
value VARCHAR(255),
created DATETIME NULL,
created DATETIME
);


CREATE TABLE user(
user_id INT AUTO_INCREMENT PRIMARY KEY,
email INT NULL,
expired DATETIME NULL,
created DATETIME
);