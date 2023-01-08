#Web Clip Board#

This is clipboard to use for transferring small amounts of data (the sort that would be in a clipboard) between computers or other networked devices.  This is similar to other web-based clipboards, but the advantage is that the code would run on a server your control. This way you can keep your clips around indefinitely and revisit old clips. It has a simple login system so that multiple people can use it and keep their clips separate.  In this way, it's like a Slack channel where you communicate
with yourself.  This was a feature I used all the time on Slack, and when my company employer to Microsoft Teams, I missed it.  (Microsoft Teams later implemented this functionality.)  The advantage with this system is that all you need is a web browser to use it and are dependent on a huge clunky application that either needs to be installed or won't run at all (if you have older hardware).

Note: like all applications that store user data, this system uses cookies.

To use this, you will need a config.php file in the root with this structure:

<?php
$servername = "localhost";
$username = "your_username";
$database = "your_database";
$password = "your_password";
$encryptionPassword = "your_cookie_encryption_password";
