<?php
// Retrieve the passed variables from the URL parameters
$menschId;
$message;
include 'sqlAuth.php';
$personHash = $_GET['personHash'];
$bestellungsHash = $_GET['bestellungsHash'];
include 'mailAuth.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;


require 'includes/PHPMailer/src/Exception.php';

require 'includes/PHPMailer/src/PHPMailer.php';
require 'includes/PHPMailer/src/SMTP.php';


//Lade den Autoloader von Composer


// Erstelle eine Instanz; das Argument `true` ermöglicht das Werfen von Ausnahmen (Exceptions)
$mail = new PHPMailer(true);

try {
    $conn = new PDO("mysql:host=$servername;dbname=aks-EndOfYear-Partayy-tickets", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "1 nul";
    exit();
}