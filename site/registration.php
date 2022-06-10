<?php
// include the custom functions
require 'inc/include.php';

// Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
// Load Composer's autoloader
require 'vendor/autoload.php';

// get the mailer configuration/user/passwd
require_once 'inc/mail.cfg.php';

// set some pre-check variable values
$sendMyMail = false;
$uploadSuccess = false;
$uploaddir = '../data/';

// Do input filtering
// TODO: look at filters, correct for intended use!
$formv['title_research']  = filter_input(INPUT_POST, 'title_research', FILTER_SANITIZE_SPECIAL_CHARS);
$formv['protocol_number']   = filter_input(INPUT_POST, 'protocol_number', FILTER_SANITIZE_SPECIAL_CHARS);
$formv['metc_number']   = filter_input(INPUT_POST, 'metc_number', FILTER_SANITIZE_SPECIAL_CHARS);
$formv['number_participants']   = filter_input(INPUT_POST, 'number_participants', FILTER_SANITIZE_NUMBER_INT);
$formv['end_date']   = filter_input(INPUT_POST, 'end_date', FILTER_SANITIZE_SPECIAL_CHARS);
$formv['principal_investigator'] = filter_input(INPUT_POST, 'principal_investigator', FILTER_SANITIZE_SPECIAL_CHARS);
$formv['telephone_number']  = filter_input(INPUT_POST, 'telephone_number', FILTER_SANITIZE_SPECIAL_CHARS);
$formv['email_prinicipal_investigator']  = filter_input(INPUT_POST, 'email_prinicipal_investigator', FILTER_SANITIZE_SPECIAL_CHARS);
$formv['email']  = filter_input(INPUT_POST, 'user_email', FILTER_SANITIZE_SPECIAL_CHARS);
$formv['order_number']  = filter_input(INPUT_POST, 'order_number', FILTER_SANITIZE_SPECIAL_CHARS);

// create a timestamp to use in the filename
$letterTimestamp = date('YmdHi');
// moved data-dir outside the site-dir

// format filename: protocolNumber_letterTimestamp_originalName
$uploadfile = $uploaddir . $formv['protocol_number'] . "_" . $letterTimestamp . "_" . basename($_FILES['metc_letter']['name']);
// add this filename to the data-array
$formv['filename'] = $uploadfile;

// check if we can move the uploaded file to the data uploaddir
if (move_uploaded_file($_FILES['metc_letter']['tmp_name'], $uploadfile)) {
    echo "File is valid, and was successfully uploaded.\n";
    $uploadSuccess = true;
} else {
    echo "Hmm, something went wrong here!\n";
    $uploadSuccess = false;
}

// ok, now we got the checks out of the way, we can start inserting/mailing....

if ($uploadSuccess) {
    echo '<pre>';
    echo '<hr>';
    // TODO: insert this into a database table....

    insertNewRegistration($formv);
    echo '<hr>';

    // Confirmation Email
    // TODO: send confirmation email to researcher
    // Determine address to send to
    $toAddress = "";
    if(isset($formv['email']) && $formv['email'] != "") {
        $toAddress = $formv['email'];
        $sendMyMail = true;
    }elseif(isset($formv['email_prinicipal_investigator']) && $formv['email_prinicipal_investigator'] != "") {
        $toAddress = $formv['email_prinicipal_investigator'];
        $sendMyMail = true;
    }else{
        die("No valid emailaddress found!");
        $sendMyMail = false;
    }

    echo "<p>TO: $toAddress</p>";
    // Notification Email
    // TODO: send notification email to research.data.fgb@vu.nl
    
    // FIXME: remove this!
    $sendMyMail = false;

    $recipientAddress = "m.e.benard@vu.nl"; 
    $rcpToRDM = $recipientAddress;
    // compose message
    $subjectRDM = 'New Insurance Registration Form: ' . $formv['title_research'] . '';
    $messageRDM = '<html><body>';
    $messageRDM .= '<p>';
    $messageRDM .= 'project: ' . $formv['title_research'] . '<br><br>';
    $messageRDM .= '</p>';
    $messageRDM .= '</body></html>';

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

        // only try to send if checks are OK!
        if ($sendMyMail) {
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
            echo $show;
        }
    } catch (Exception $e) {
        $ok = false;
        $show = '<div class="alert alert-danger"> Bericht kon niet worden verzonden. Mailer Error: ' . $mailRDM->ErrorInfo . '</div>';
        echo $show;
    }

    // -------------------------------------------------
    // just for debug!
    if (!$sendMyMail) {
        echo '<pre>';
        echo "mail not send!<br>";
        echo 'Subject: ' . $subjectRDM;
        echo $messageRDM;
        echo '</pre>';
    }
} // end 