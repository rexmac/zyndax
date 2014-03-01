#zyndax v0.02a

__Update - 2014/03/01:__ It's been a _very_ long time since I've used this project.
I've tried to update the files in this repo to their last known working state, but
there are definitely some bugs and maybe even some missing components. It's also
only compatible with Zend Framework 1, which is rather dated at this point, and I
have no intentions of ever again using ZF1 or this extension of the framework. It
remains here, on GitHub, purely for posterity.

---

##System Requirements

Zyndax requires PHP 5.3 or later.

##Installation

After cloning or downloading and unpacking, run `php composer.phar install`.

Configure a virtual host:

```apache
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
```

##Copyright and License

Copyright (c) 2012 Rex McConnell

Unless otherwise specified, the files in this archive are released under the [Modified BSD license](http://rexmac.github.com/license/bsd2c.txt).
You can find a copy of this license in LICENSE.txt.

