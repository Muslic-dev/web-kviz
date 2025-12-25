<?php

session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once "vendor/autoload.php";
$mail = new PHPMailer(true);

$mail->isSMTP();
$mail->Host       = 'smtp.gmail.com';
$mail->SMTPAuth   = true;
$mail->Username   = 'sender.americanfood@gmail.com';
$mail->Password   = 'cids srfl opzo fuvi';
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$mail->Port       = 587;

$data = $_SESSION['mail_data'];
$email = $data['email'];
$kod = $data['kod'];
$purpose = $data['purpose'];
if($purpose == 'register') 
{
    $subject = 'Verifikacioni kod za registraciju';
    $message = "Vaš verifikacioni kod je: <b>" . substr($kod,0, 3) . "-" . substr($kod,3,3) . "</b>";
} else if($purpose == 'reset') 
{
    $subject = 'Verifikacioni kod za resetovanje lozinke';
} else 
{
    header("Location: index.php");
    exit();
}

if(isset($_SESSION['last_mail_time']) && (time() - $_SESSION['last_mail_time'] < 15)) {
    $_SESSION['error'] = "Molimo sačekajte pre nego što zatražite novi kod.";
}

$mail->setFrom('sender.americanfood@gmail.com');
$mail->addAddress($email);


$mail->isHTML(true);
$mail->Subject = $subject;
$mail->Body    = $message;
if($mail->send()) {
    echo "Email poslat!";
    $_SESSION['last_mail_time'] = time();
    if($purpose == 'register') 
    {
        header("Location: verifikacija.php?email=");
    } else if($purpose == 'reset') 
    {
        header("Location: resetLozinke.php?email=" . urlencode($email));
    }
} else {
    echo "Došlo je do greške prilikom slanja emaila.";
    $mail->ErrorInfo;
}

$mail->smtpClose();
?>