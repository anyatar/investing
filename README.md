# CRUD project

## Requirements

1. PHP 7.0 or later
2. Apache Web Server with mod_php
3. Linux (other OSs can work but require small changes to the permissions instructions)

## Installation

1. Clone the project from git to a location on your server.  This location should be outside of the Web server's doc root.

2. Add a new vhost to Apache:
```
Listen 20081
<VirtualHost *:20081>
DocumentRoot "/path/to/project/public"
<Directory "/path/to/project/public">
   Options Indexes FollowSymLinks ExecCGI
   AllowOverride All
   Require all granted
  </Directory>
</VirtualHost>
 ```  
3. Restart Apache

4. Open http://server_address:20080/chaos.html . Here you can set the mode and see results table after you run the test script

5. Run the test script from test directory: php test.php. The results will be automatically updated in the browser.

6. You can change the configuration json file in the 'module' directory, refresh the application and repeat the steps: 4-5

7. Enjoy!
