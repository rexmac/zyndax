zyndax v0.01a

SYSTEM REQUIREMENTS
-------------------
Zyndax requires PHP 5.3 or later.

INSTALLATION
------------
After downloading, create a symbolic link in the library folder to your copy
of Zend Framework:

ln -s [PATH_TO_ZEND_FRAMEWORK]/library/Zend/ Zend

Configure a virtual host:

<VirtualHost *:80>
    ServerName zyndax.local
    DocumentRoot /var/www/zyndax/public

    ErrorLog /var/log/apache2/zyndax_error.log
    LogLevel warn
    CustomLog /var/log/apache2/zyndax_access.log combined

    <Directory /var/www/zyndax/public>
        Options FollowSymLinks
        AllowOverride All
        Order deny,allow
        Deny from all
        Allow from 127.0.0.1
    </Directory>
</VirtualHost>

LICENSE
-------
The files in this archive are released under the Zend Framework license.
You can find a copy of this license in LICENSE.txt.

