<?php 

$formv['title_res']  = filter_input(INPUT_POST, 'title_res', FILTER_SANITIZE_SPECIAL_CHARS);
$formv['prot_num']   = filter_input(INPUT_POST, 'prot_num', FILTER_SANITIZE_SPECIAL_CHARS);
$formv['num_part']   = filter_input(INPUT_POST, 'num_part', FILTER_SANITIZE_NUMBER_INT);
$formv['end_date']   = filter_input(INPUT_POST, 'end_date', FILTER_SANITIZE_SPECIAL_CHARS);
$formv['researcher'] = filter_input(INPUT_POST, 'researcher', FILTER_SANITIZE_SPECIAL_CHARS);
$formv['telephone']  = filter_input(INPUT_POST, 'telephone', FILTER_SANITIZE_SPECIAL_CHARS);
$formv['order_num']  = filter_input(INPUT_POST, 'order_num', FILTER_SANITIZE_SPECIAL_CHARS);

echo "<pre>";

print_r($formv);
echo "<br>";
echo "TODO: insert this into a database table....<br>";
echo "TODO: upload pdf, store in MySQL BLOB or as file with reference in database?<br>";
echo "TODO: send email to research.data.fgb@vu.nl<br>";