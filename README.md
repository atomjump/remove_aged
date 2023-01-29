<img src="https://atomjump.com/images/logo80.png">

__WARNING: this project has now moved to https://src.atomjump.com/atomjump/remove_aged.git__

# remove_aged
Removes aged messages and any corresponding images from an AtomJump Messaging database.

# Requirements

AtomJump Messaging Server >= 0.8.0


# Installation


```
sudo php install.php
```

Add an hourly (or some other timeframe) CRON entry for index.php e.g.

```
	0 * * * *       /usr/bin/php /your_server_path/api/plugins/remove_aged/index.php
```

Your AtomJump Messaging server's main .htaccess file:

After 
```
RewriteRule image-exists - [L,PT]
```
this should be added to your AtomJump Messaging server's main .htaccess file:
```
#Get out of here early - we know we don't need further processing
RewriteRule remove-image - [L,PT]
```







