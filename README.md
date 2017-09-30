# Chrome Extension to Get Warning Text From Database
## Getting Started
### Prerequisites
1. Chrome Browser. The plugin was implemented using Chrome Version 61.0.3163.91 (Official Build) (64-bit)
2. XAMPP. It can download from <https://www.apachefriends.org/download.html>. Version 7.0.23 / PHP 7.0.23 for OS X was used.

## Running the plugin
This section will introduce how to run this plugin in your Chrome Browser.

1. Saving "db1" folder into ~/Applications/XAMPP/htdocs
2. Open manager-osx
3. start MYSQL Database and Apache Web Server (See _Known Issue_ if occuring problems)
4. Go to <http://localhost/phpmyadmin/> and import database
5. Go to <chrome://extensions/> and tick the "Developer mode"
6. Load "Chrome_Extension" folder
7. Click the icon to run the plugin

## Known Issue
### Cannot start MYSQL Database in XAMPP
1. Change the port number. The author uses 3307. And try again.
2. Run 

```
> $ sudo killall mysqld
> $ sudo /Applications/XAMPP/xamppfiles/bin/mysql.server start
```
and try agian.
## Author
Codes and this README are written by Sirui Li.



