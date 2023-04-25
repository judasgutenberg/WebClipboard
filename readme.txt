#WebClipBoard

This is a clipboard system to use primarily for transferring small amounts of text data (the sort that would be in a clipboard) between computers or other networked devices.  This is similar to other web-based clipboards, but the advantage is that the code would run on a server you control. This way you can keep your clips around indefinitely and revisit old clips. It has a simple login system so that multiple people can use it and keep their clips separate.  In this way, it's like a Slack channel where you communicate with yourself.  This was a feature I used all the time on Slack, and when my employer switched to Microsoft Teams, I missed it.  (Microsoft Teams later implemented this functionality.)  The advantage with this system is that all you need is a web browser to use it and aren't dependent on a huge clunky application that either needs to be installed or won't run at all (if you have older hardware).  

The clipboard also supports file uploads in addition to simple text and preserves the filename of the uploaded file.

Currently the passwords are stored in plaintext (bad!) but I will eventually fix this when I add some user management and have a way to encyrpt passwords on save.

None of this code has pretenses of being any more than it is. It's very straightforward and imperative.
Note: like all applications that store user data, this system uses cookies.

To use this, you will need to run the WebClipBoard.sql file on a MySQL server you control.  

Since there is no user admin functionality (for now), you will need to add your users from within MySQL by running something like this command in MySQL:

INSERT INTO `user`(email, password, created) VALUES ('youremail.com', 'yourpassword', '2023-01-01');

Then you will need to point to your database in a config.php file in the root with this structure:

<?php

$servername = "localhost";
$username = "your_username";
$database = "your_database";
$password = "your_password";
$encryptionPassword = "your_cookie_encryption_password";
