<?php 

$formv['title_res']  = filter_input(INPUT_POST, 'title_res', FILTER_SANITIZE_SPECIAL_CHARS);
$formv['prot_num']   = filter_input(INPUT_POST, 'prot_num', FILTER_SANITIZE_SPECIAL_CHARS);
$formv['num_part']   = filter_input(INPUT_POST, 'num_part', FILTER_SANITIZE_NUMBER_INT);
$formv['end_date']   = filter_input(INPUT_POST, 'end_date', FILTER_SANITIZE_SPECIAL_CHARS);
$formv['researcher'] = filter_input(INPUT_POST, 'researcher', FILTER_SANITIZE_SPECIAL_CHARS);
$formv['telephone']  = filter_input(INPUT_POST, 'telephone', FILTER_SANITIZE_SPECIAL_CHARS);
$formv['order_num']  = filter_input(INPUT_POST, 'order_num', FILTER_SANITIZE_SPECIAL_CHARS);

$uploaddir = 'data/';
$uploadfile = $uploaddir . $formv['prot_num'] . "_" . basename($_FILES['letter']['name']);

$formv['filename'] = $uploadfile;
echo '<pre>';

echo $uploadfile. "<br>";

if (move_uploaded_file($_FILES['letter']['tmp_name'], $uploadfile)) {
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