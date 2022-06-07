# Registration Form for FGB research projects requiring Research Participants Insurance



## YARN

Install dependencies

```bash
$ yarn install
```

Move relevant files to css/js-folders in `/site/`

```bash 
$ yarn buildme
```

## MySQL database

```sql
CREATE DATABASE IF NOT EXISTS `wmo_insurance_registration`;

CREATE TABLE IF NOT EXISTS `registrations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title_research` text,
  `protocol_number` char(14) DEFAULT NULL,
  `metc_number` varchar(30) DEFAULT NULL,
  `number_participants` int DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `principal_investigator` varchar(50) DEFAULT NULL,
  `telephone_number` varchar(15) DEFAULT NULL,
  `email_prinicipal_investigator` varchar(45) DEFAULT NULL,
  `email` varchar(45) DEFAULT NULL,
  `order_number` varchar(45) DEFAULT NULL,
  `metc_letter` varchar(255) DEFAULT NULL,
  `date_time_created` datetime DEFAULT CURRENT_TIMESTAMP,
  `date_time_modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
```

### Database credentials

Create/edit the `site/inc/db.cfg.php` file (not in git) to store the credentials:

```php
<?php
// localhost
$mysqlhost = 'localhost:3306';
$mysqluser = 'some_username';
$mysqlpass = 'some_password';
$mysqldb   = 'some_database';
```

## Data - file storage

Make sure the data-folder is well protected. At least **deny** directory listing with a .htaccess file:

`.htaccess` file: 

```apache
Options -Indexes
```
### Data-folder for PDF's 

Make sure the data-folder is writable for the webserver, for Ubuntu do something like:

```bash 
$ sudo chown myuser:www-data data/
$ sudo chmod g+w data/
```
### Allow bigger file upload (php.ini)

```php
post_max_size = 16M
upload_max_filesize = 16M
```
