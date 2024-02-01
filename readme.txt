#WebClipBoard

This is a clipboard system to use primarily for transferring small amounts of text data (the sort that would be in a clipboard) between computers or other networked devices.  This is similar to other web-based clipboards, but the advantage is that the code would run on a server you control. This way you can keep your clips around indefinitely, revisit old clips, and even use it as a kind of cloud-based journal. It has a simple login system so that multiple people can use it and keep their clips separate.  In this way, it's like a Slack channel where you communicate with yourself.  This was a feature I used all the time on Slack, and when my employer switched to Microsoft Teams, I missed it.  (Microsoft Teams later implemented this functionality.)  The advantage with this system is that all you need is a web browser to use it and aren't dependent on a huge clunky application that either needs to be installed or won't run at all (if you have older hardware).  It wouldn't take too much work to add chat functionality to this, allowing you to send messages to other people on the system. Then it would have most of what you would need for your own private Slack-like system.

The clipboard also supports file uploads in addition to simple text and preserves the filename of the uploaded file.

Passwords are encrypted and new accounts are easily created (there is no email verification!).

None of this code has pretenses of being any more than it is. It's very straightforward and imperative.
Note: like all applications that store user data, this system uses cookies.

To use this, you will need to run the WebClipBoard.sql file on a MySQL server you control.  

Create new accounts from within the system so the passwords will be encrypted correctly. Otherwise you will need to encrypt the passwords using the PHP function crypt(YOUR_PASSWORD, $encryptionPassword) and then run the following from within MySQL:

INSERT INTO `user`(email, password, created) VALUES ('your@email.com', 'your_encrypted_password', '2023-01-01');

Then you will need to point to your database in a config.php file in the root with this structure:

<?php

$servername = "localhost";
$username = "your_username";
$database = "your_database";
$password = "your_password";
$encryptionPassword = "your_cookie_encryption_password";
$cookiename = "your_cookie_name";

To see this in action (and create your own clipboard hosted on my server until I stop paying the bills), go here:

http://randomsprocket.com/cb/index.php

If you do this, keep in mind that I can read your clipboard's contents, though your password is encrypted beyond my ability to recover it.
