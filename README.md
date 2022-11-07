# remove_aged
Removes aged messages and any corresponding images from an AtomJump Messaging database.



# Installation

After 
```
RewriteRule image-exists - [L,PT]
```
This should be added to your AtomJump Messaging server's main .htaccess file:
```
#Get out of here early - we know we don't need further processing
RewriteRule remove-image - [L,PT]
```



