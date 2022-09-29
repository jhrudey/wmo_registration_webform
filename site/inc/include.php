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
  * insert a new registration record into the database
  *
  * @param array $formv
  * @return void
  */
function insertNewRegistration($formv)
{
    $mysqli = new mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PASS, MYSQL_DB);
    if (mysqli_connect_errno()) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        exit();
    }
    $query = 'INSERT INTO registrations (title_research,protocol_number,metc_number,number_participants,begin_date,end_date,principal_investigator,telephone_number,email_principal_investigator,email,order_number,metc_letter, comments_respondent)
    VALUES (
        "' . $formv['title_research'] . '",
        "' . $formv['protocol_number'] . '",
        "' . $formv['metc_number'] . '",
        ' . $formv['number_participants'] . ',
        "' . $formv['begin_date'] . '",
        "' . $formv['end_date'] . '",
        "' . $formv['principal_investigator'] . '",
        "' . $formv['telephone_number'] . '",
        "' . $formv['email_principal_investigator'] . '",
        "' . $formv['email'] . '",
        "' . $formv['order_number'] . '",
        "' . $formv['filename'] . '",
        "' . $formv['comments_respondent'] .'"
    );';
   // echo "<p>$query</p>";
// FIXME: do not echo, only return!
    if (mysqli_query($mysqli, $query)) {
    //    echo "New record created successfully";
    return;
    } else {
        echo "Error: " . $$query . "" . mysqli_error($mysqli);
        return;
    }
    //print_r($formv);
    //return;
}
