<?php

/**
 * @project: wmo_insurence_resistration
 * 
 * Functions
 */

// include the MySQL credentials
require 'db.cfg.php';

// database config
define('MYSQL_HOST', $mysqlhost);
define('MYSQL_USER', $mysqluser);
define('MYSQL_PASS', $mysqlpass);
define('MYSQL_DB', $mysqldb);

/**
 * Put data to database table
 *   
 * @param array $submitArray form values
 * 
 * @return void
 */

function insertNewRegistration($formv)
{
    $mysqli = new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PASS, MYSQL_DB);
    if (mysqli_connect_errno()) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        exit();
    }
    $query = 'INSERT INTO registrations (title_research,protocol_number,metc_number,number_participants,end_date,principal_investigator,telephone_number,email_prinicipal_investigator,email,order_number,metc_letter) 
    VALUES (
        "' . $formv['title_research'] . '",
        "' . $formv['protocol_number'] . '",
        "' . $formv['metc_number'] . '",
        ' . $formv['number_participants'] . ',
        "' . $formv['end_date'] . '",
        "' . $formv['principal_investigator'] . '",
        "' . $formv['telephone_number'] . '",
        "' . $formv['email_prinicipal_investigator'] . '",
        "' . $formv['email'] . '",
        "' . $formv['order_number'] . '",
        "' . $formv['filename'] . '"
    );';
   // echo "<p>$query</p>";

    if (mysqli_query($mysqli, $query)) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $$query . "" . mysqli_error($mysqli);
    }
    print_r($formv);
    return;
}



/*
$formv['title_research']
$formv['protocol_number']
$formv['metc_number']
$formv['number_participants']
$formv['end_date']
$formv['principal_investigator'] 
$formv['telephone_number']
$formv['email_prinicipal_investigator']
$formv['user_email']
$formv['order_number']

*/
function insertNewDataset($lic, $useCount, $session)
{
    $mysqli = new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PASS, MYSQL_DB);
    if (mysqli_connect_errno()) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        exit();
    }
    $query = 'INSERT INTO saltdata (licensecode, usecount, consent, mysession) VALUES ("' . $lic . '", ' . $useCount . ', 1, "' . $session . '" );';
    if (mysqli_query($mysqli, $query)) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $$query . "" . mysqli_error($mysqli);
    }
}
/*
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
    `user_email` varchar(45) DEFAULT NULL,
    `order_number` varchar(45) DEFAULT NULL,
    `metc_letter` varchar(255) DEFAULT NULL,
    `status` int DEFAULT '1',
    `comments` text,
    `date_time_created` datetime DEFAULT CURRENT_TIMESTAMP,
    `date_time_modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
*/