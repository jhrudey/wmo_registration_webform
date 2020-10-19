# Registration Form For FGB Research Projects Requiring Research Participants Insurance

## Data-folder for PDF's 

Make sure the data-folder is writable for the webserver, for Ubuntu do something like:

```ubuntu 
$ sudo chown myuser:www-data data/

$ sudo chmod g+w data/
```

For Mac?  [https://stackoverflow.com/questions/35190025/mac-localhost-site-folder-permission-issue](https://stackoverflow.com/questions/35190025/mac-localhost-site-folder-permission-issue)

### Allow bigger file upload (php.ini)

```php
post_max_size = 16M
upload_max_filesize = 16M
```

