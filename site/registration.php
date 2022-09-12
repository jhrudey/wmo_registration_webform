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
$formv['title_research']  = filter_input(INPUT_POST, 'title_research', FILTER_SANITIZE_SPECIAL_CHARS);
$formv['protocol_number']   = filter_input(INPUT_POST, 'protocol_number', FILTER_SANITIZE_SPECIAL_CHARS);
$formv['metc_number']   = filter_input(INPUT_POST, 'metc_number', FILTER_SANITIZE_SPECIAL_CHARS);
$formv['number_participants']   = filter_input(INPUT_POST, 'number_participants', FILTER_SANITIZE_NUMBER_INT);
$formv['begin_date']   = filter_input(INPUT_POST, 'begin_date', FILTER_SANITIZE_SPECIAL_CHARS);
$formv['end_date']   = filter_input(INPUT_POST, 'end_date', FILTER_SANITIZE_SPECIAL_CHARS);
$formv['principal_investigator'] = filter_input(INPUT_POST, 'principal_investigator', FILTER_SANITIZE_SPECIAL_CHARS);
$formv['telephone_number']  = filter_input(INPUT_POST, 'telephone_number', FILTER_SANITIZE_SPECIAL_CHARS);
$formv['email_prinicipal_investigator']  = filter_input(INPUT_POST, 'email_prinicipal_investigator', FILTER_SANITIZE_EMAIL);
$formv['email']  = filter_input(INPUT_POST, 'user_email', FILTER_SANITIZE_EMAIL);
$formv['order_number']  = filter_input(INPUT_POST, 'order_number', FILTER_SANITIZE_SPECIAL_CHARS);
$formv['comments_respondent']  = filter_input(INPUT_POST, 'comments_respondent', FILTER_SANITIZE_SPECIAL_CHARS);

// create a timestamp to use in the filename
$letterTimestamp = date('YmdHi');

// format filename: protocolNumber_letterTimestamp_originalName
$uploadfile = $uploaddir . $formv['protocol_number'] . "_" . $letterTimestamp . "_" . basename($_FILES['metc_letter']['name']);
// add this filename to the data-array
$formv['filename'] = $uploadfile;

// check if we can move the uploaded file to the data uploaddir
if (move_uploaded_file($_FILES['metc_letter']['tmp_name'], $uploadfile)) {
    $uploadMessage = "File is valid, and was successfully uploaded.";
    $uploadSuccess = true;
} else {
    $uploadMessage = "Hmm, something went wrong here!";
    $uploadSuccess = false;
}

// ok, now we got the checks out of the way, we can start inserting/mailing....
if ($uploadSuccess) {
    insertNewRegistration($formv);

    // Sending Notificaition and Confirmation Emails

    // Determine respondent address to use
    $toAddress = "";
    if (isset($formv['email']) && $formv['email'] != "") {
        $toAddress = $formv['email'];
        $sendMyMail = true;
    } elseif (isset($formv['email_prinicipal_investigator']) && $formv['email_prinicipal_investigator'] != "") {
        $toAddress = $formv['email_prinicipal_investigator'];
        $sendMyMail = true;
    } else {
        die("No valid emailaddress found!");
        $sendMyMail = false;
    }

    // FIXME: remove this ($sendMyMail = false;)
    // $sendMyMail = false;

    // --- start RDM Notification mail --
    $recipientAddress = $researchDataMailAddress;

    $rcpToRDM = $recipientAddress;
    // compose message
    $subjectRDM = 'New Record Participants Insurance Register: ' . $formv['title_research'] . '';
    $messageRDM = '<html><body>';
    $messageRDM .= '<p>';
    $messageRDM .= '' . $formv['title_research'] . ' with ' . $formv['protocol_number'] . ' has been submitted to the Participants Insurance Register by ' . $toAddress . '<br><br>';
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
            // we're sending email, set $notificationMailOK variable to true
            $notificationMailOK = true;
        }
    } catch (Exception $e) {
        // sending did not work for some reason! Set $okRMD to false
        $notificationMailOK = false;
    }
    // --- end RDM Notification mail --

    // --- start Respondent confirmation mail ---
    $rcpToRespondent = $toAddress;
    // compose message
    $subjectRespondent = 'WMO Insurance Registration: ' . $formv['title_research'] . '';

    $messageRespondent = '<html><body>';
    $messageRespondent .= '<p>';
    $messageRespondent .= 'Thank you for registering the information about your medical research that requires participants\' insurance. If you have any questions or comments please contact <a href="mailto:research.data.fgb@vu.nl">research.data.fgb@vu.nl</a>.<br><br>';
    $messageRespondent .= '</p>';
    $messageRespondent .= '</body></html>';

    $mailRespondent = new PHPMailer(true);
    try {
        //Server settings
        //$mailRespondent->SMTPDebug = SMTP::DEBUG_SERVER;                    // Enable verbose debug output
        $mailRespondent->isSMTP();                                            // Send using SMTP
        $mailRespondent->Host       = $mailHost;                              // Set the SMTP server to send through
        $mailRespondent->SMTPAuth   = true;                                   // Enable SMTP authentication

        $mailRespondent->Username   = $mailUser;                              // SMTP username
        $mailRespondent->Password   = $mailPasswd;                            // SMTP password

        $mailRespondent->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
        $mailRespondent->Port       = 587;                                    // TCP port to connect to

        //Recipients
        $mailRespondent->setFrom($mailFromAddress, $mailFromDisplayName);
        $mailRespondent->addAddress($rcpToRespondent);      // Add a recipient
        $mailRespondent->addReplyTo('research.data.fgb@vu.nl', 'FGB Research Data');
        // Content
        $mailRespondent->isHTML(true);       // Set email format to HTML
        $mailRespondent->Subject = $subjectRespondent;
        $mailRespondent->Body    = $messageRespondent;

        // only try to send if checks are OK!
        if ($sendMyMail) {
            $mailRespondent->send();
            // we're sending email, set $confirmationMailOK to true
            $confirmationMailOK = true;
        }
    } catch (Exception $e) {
        // sending did not work for some reason! Set $confirmationMailOK to false
        $confirmationMailOK = false;
    }
    // --- end Respondent confirmation mail ---
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <!-- Local CSS -->
        <link rel="stylesheet" href="css/style.css">

        <title class="title">Registration Participant Insurance Registration Form</title>
    </head>

    <body>
        <img src="img/FGB_logo_rgb_wit_en_tcm264-852539.svg">
        <h3>Registration Form for FGB Research Projects Requiring Insurance for Research Subjects</h3>
        <br>
        <div class="container px-4 py-2">
        <?php
        // -- display a send/fail message to respondent
        if ($notificationMailOK == true && $confirmationMailOK == true) {  // if both messages send successfully
            echo '<h1 class="text-success"> Thank you!</h1>';
            echo '<p>';
            echo 'Your request was send to the Research Data Team.';
            echo '</p><p>';
            echo '<a href="./">Click here to go back to the request form</a>';
            echo '<p>';
        } else {  // display error message
            echo '<h1 class="text-danger"> ERROR!</h1>';
            echo '<p>';
            if ($confirmationMailOK == false) {
                echo '<div class="alert alert-danger"> Confirmation message could not be sent! Mailer Error: ' . $mailRespondent->ErrorInfo . '</div>';
            }
            if ($notificationMailOK == false) {
                echo '<div class="alert alert-danger"> Notification message could not be sent! Mailer Error: ' . $mailRespondent->ErrorInfo . '</div>';
            }
            echo '</p><p>';
            echo '<a href="./">Click here to go back to the request form</a>';
            echo '<p>';
        }
        // -------------------------------------------------
        // just for debug! FIXME: remove this!
        if (!$sendMyMail) {
            echo '<p>';
            echo "mail not send!<br>";
            echo 'FROM: ' . $mailFromAddress . '<br>';
            echo 'rcpTO: ' . $rcpToRDM . '<br>';
            echo 'Subject: ' . $subjectRDM . '<br>';
            echo $messageRDM . '<br>';
            echo '</p>';

            echo '<p>';
            echo "mail not send!<br>";
            echo 'FROM: ' . $mailFromAddress . '<br>';
            echo 'rcpTO: ' . $rcpToRespondent . '<br>';
            echo 'Subject: ' . $subjectRespondent . '<br>';
            echo $messageRespondent . '<br>';
            echo '</p>';
        }
    } else {
        echo '<div class="alert alert-danger">' . $uploadMessage . '</div>';
    } // end
        ?>
        </div>
        <script src="js/jquery.min.js"></script>
        <script src="js/bootstrap.bundle.min.js"></script>
    </body>

    </html>
