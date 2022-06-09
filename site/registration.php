<?php

// Do some input filtering
$formv['title_research']  = filter_input(INPUT_POST, 'title_research', FILTER_SANITIZE_SPECIAL_CHARS);
$formv['protocol_number']   = filter_input(INPUT_POST, 'protocol_number', FILTER_SANITIZE_SPECIAL_CHARS);
$formv['metc_number']   = filter_input(INPUT_POST, 'metc_number', FILTER_SANITIZE_SPECIAL_CHARS);
$formv['number_participants']   = filter_input(INPUT_POST, 'number_participants', FILTER_SANITIZE_NUMBER_INT);
$formv['end_date']   = filter_input(INPUT_POST, 'end_date', FILTER_SANITIZE_SPECIAL_CHARS);
$formv['principal_investigator'] = filter_input(INPUT_POST, 'principal_investigator', FILTER_SANITIZE_SPECIAL_CHARS);
$formv['telephone_number']  = filter_input(INPUT_POST, 'telephone_number', FILTER_SANITIZE_SPECIAL_CHARS);
$formv['email_prinicipal_investigator']  = filter_input(INPUT_POST, 'email_prinicipal_investigator', FILTER_SANITIZE_SPECIAL_CHARS);
$formv['email']  = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_SPECIAL_CHARS);
$formv['order_number']  = filter_input(INPUT_POST, 'order_number', FILTER_SANITIZE_SPECIAL_CHARS);

// create a timestamp to use in the filename
$letterTimestamp = date('YmdHi');

// moved data-dir outside the site-dir
$uploaddir = '../data/';

// format filename: protocolNumber_letterTimestamp_originalName
$uploadfile = $uploaddir . $formv['protocol_number'] . "_" .$letterTimestamp. "_" . basename($_FILES['metc_letter']['name']);

$formv['filename'] = $uploadfile;
echo '<pre>';

echo $uploadfile. "<br>";

if (move_uploaded_file($_FILES['metc_letter']['tmp_name'], $uploadfile)) {
    echo "File is valid, and was successfully uploaded.\n";
} else {
    echo "Possible file upload attack!\n";
}

echo 'Here is some more debugging info:';
print_r($_FILES);


echo "<pre>";

print_r($formv);
echo "<br>";
echo "TODO: insert this into a database table....<br>";
echo "TODO: upload pdf, store in MySQL BLOB or as file with reference in database?<br>";
echo "TODO: send email to research.data.fgb@vu.nl<br>";
