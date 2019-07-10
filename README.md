# Chaos Server project

## Requirements

1. PHP 7.0 or later
2. Apache Web Server with mod_php
3. Linux (other OSs can work but require small changes to the permissions instructions)

## Installation

1. Clone the project from git to a location on your server.  This location should be outside of the Web server's doc root.

2. Ensure that the module/chaos.db database file is writable by the Web server process, e.g.:
   chmod 777 module
   chmod 666 module/chaos.db
   (or change them to be owned by the Web server process).

3. Add a new vhost to Apache:
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
4. Restart Apache

5. Open http://server_address:20081/chaos.html . Here you can set the mode and see results table after you run the test script

6. Open test/test.php and update BASE_URL if needed.

7. Run the test script from test directory: php test.php. The results will be automatically updated in the browser.

8. You can change the configuration json file in the 'module' directory, refresh the application and repeat the steps: 4-5

9. Enjoy!
