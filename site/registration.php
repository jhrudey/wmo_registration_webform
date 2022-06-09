<?php
// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require 'vendor/autoload.php';
// get the mailer configuration 
require_once 'inc/mail.cfg.php';

$sendMail = false;

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
    $sendMail = true;
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

if($sendMail) {

$recipientAddress = "m.e.benard@vu.nl";

$subjectRDM = 'New Insurance Registration Form: ' . $formv['title_research'] . '';

$messageRDM = '<html><body>';

$messageRDM .= '<p>';
$messageRDM .= 'project: ' . $formv['title_research'] . '<br><br>';
$messageRDM .= '</p>';

$messageRDM .= '</body></html>';

$rcpToRDM = $recipientAddress;

$mailRDM = new PHPMailer(true);
try {
    //Server settings
    //$mailRDM->SMTPDebug = SMTP::DEBUG_SERVER;                    // Enable verbose debug output
    $mailRDM->isSMTP();                                            // Send using SMTP
    $mailRDM->Host       = $mailHost;                              // Set the SMTP server to send through
    $mailRDM->SMTPAuth   = true;                                   // Enable SMTP authentication

    $mailRDM->Username   = $mailUser;                              // SMTP username
    $mailRDM->Password   = $mailPasswd;                            // SMTP password

    $mailRDM->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
    $mailRDM->Port       = 587;                                    // TCP port to connect to

    //Recipients
    $mailRDM->setFrom($mailFromAddress, $mailFromDisplayName);
    $mailRDM->addAddress($rcpToRDM);      // Add a recipient
    $mailRDM->addReplyTo('research.data.fgb@vu.nl', 'FGB Research Data');
    // Content
    $mailRDM->isHTML(true);       // Set email format to HTML
    $mailRDM->Subject = $subjectRDM;
    $mailRDM->Body    = $messageRDM;

    $mailRDM->send();
    $ok = true;
    $show  = '<p>';
    $show .= '<h2> Thank you!</h2>';
    $show .= 'Your request was send to the Support desk.';
    $show .= '</p>';
    $show .= '<p>';
    $show .= '<a href="./">Click here to go back to the request form</a>. You will be automatically transfered in <span id="seconds">15</span> seconds.';

    $show  .= '<p>';
    $show .= '';
} catch (Exception $e) {
    $ok = false;
    $show = '<div class="alert alert-danger"> Bericht kon niet worden verzonden. Mailer Error: ' . $mailRDM->ErrorInfo . '</div>';
}

echo $show;
// -------------------------------------------------
}