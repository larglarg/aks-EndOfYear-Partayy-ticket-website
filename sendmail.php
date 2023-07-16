<!DOCTYPE html>
<html lang="de">

<head>
  <meta charset="UTF-8">
  <title>Reservierung</title>
  <link rel="stylesheet" type="text/css" href="style.css">
</head>

<?php


function sendMail($mailer, $sender_name, $recipient, $subject, $message, $isHTML = TRUE)
{
    try {
        include 'mailAuth.php';
        // Servereinstellungen
        #$mailer->SMTPDebug = SMTP::DEBUG_SERVER; // Aktiviere detaillierte Debug-Ausgabe
        $mailer->isSMTP(); // Sende über SMTP
    
        $mailer->Host = $smtpHost; // Setze den SMTP-Server für den Versand
        $mailer->SMTPAuth = true; // Aktiviere SMTP-Authentifizierung
        $mailer->Username = $mailusername; // SMTP-Benutzername
        $mailer->Password = $mailpassword; // SMTP-Passwort
        $mailer->SMTPSecure = "TLS"; // Aktiviere TLS-Verschlüsselung
        $mailer->Port = $smtpPort; // TCP-Port zum Verbinden; verwende 587, wenn `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS` gesetzt ist
        $mailer->CharSet = 'UTF-8';
        $mailer->Encoding = 'base64';

        // 
        $mailer->setFrom($senderMail, $sender_name);// 'AKS Karlsruhe');
                
        // Empfänger
        $mailer->addAddress($recipient); // Füge einen Empfänger hinzu
    
        // Inhalt
        $mailer->isHTML($isHTML); // Setze das E-Mail-Format auf HTML
        $mailer->Subject = $subject;//'Reservierung Karten AKS EndOfYear Partayy';
        $mailer->Body = $message;
        $mailer->send();
    } catch (Exception $e) {
        echo "Die Nachricht konnte nicht gesendet werden. Mailer Error: {$mailer->ErrorInfo}";
        return false;
    }
    return true;
}
// Retrieve the passed variables from the URL parameters
$menschId;
$message;
include 'statics.php';
$personHash = $_GET['personHash'];
$bestellungsHash = $_GET['bestellungsHash'];


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;


require 'includes/PHPMailer/src/Exception.php';

require 'includes/PHPMailer/src/PHPMailer.php';
require 'includes/PHPMailer/src/SMTP.php';


//Lade den Autoloader von Composer


// Erstelle eine Instanz; das Argument `true` ermöglicht das Werfen von Ausnahmen (Exceptions)
$mailer = new PHPMailer(true);

try {
    $conn = new PDO("mysql:host=$servername;dbname=aks-EndOfYear-Partayy-tickets", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "1 nul";
    exit();
}
$stmt = $conn->prepare("SELECT id, hash, email FROM menschen WHERE hash = :personHash;");
$stmt->bindParam(':personHash', $personHash, PDO::PARAM_STR);
$stmt->execute();
if ($stmt->rowCount() != 1) {
    echo "Der mensch exestiert nicht oder mehr als ein mal";
    exit();
}

$row = $stmt->fetch(PDO::FETCH_ASSOC);
$menschId = $row['id'];
$email = $row['email'];

$stmt = $conn->prepare("SELECT hash, gast1_id, gast2_id, gast3_id, gast4_id, status FROM bestellung WHERE hash = :bestellungsHash AND (besteller_id = :menschid OR gast1_id = :menschid OR gast2_id = :menschid OR gast3_id = :menschid OR gast4_id = :menschid)");
$stmt->bindParam(':bestellungsHash', $bestellungsHash, PDO::PARAM_STR);
$stmt->bindParam(':menschid', $menschId, PDO::PARAM_INT);
$stmt->execute();

if ($stmt->rowCount() == 0) {
    echo "Die bestellung exestiert nicht null";
    exit();
}
if ($stmt->rowCount() > 1) {
    echo "Die bestellung exestiert nicht mehr als eins";
    exit();
}
$from = "lars.handwerker@web.de";
$file = 'http://localhost/aks-EndOfYear-Partayy-ticket-website/aks-EndOfYear-Partayy-ticket-website/ReservierungfuerdieAKSEndOfYreaarPartaayTicket.php'; // Why would you do that?

$params = [
    'personHash' => $personHash,
    'bestellungsHash' => $bestellungsHash
];
$url = $file . '?personHash='.$personHash.'&bestellungsHash='.$bestellungsHash;

$message = file_get_contents($url);
//$to = $email;
sendMail($mailer, 'AKS Karlsruhe', $email, 'Reservierung Karten AKS EndOfYear Partayy', $message);




?>

<body>
<div class="container">
    <div class="content">
      <div class="inner-content">
<div class="header skew">
  <h1>AKS EndOfYear-Partayy</h1>
  <h3>Die E-Mail wurde erfolgreich verschickt.</h3>
</div>
        <p>Nun musst du deine E-Mail bestätiegen. <br>
            Danach erhälst du eine weiter mail mit dem qr code zum abholen der Karten und einer einverständinsserklärung
            die von deinen erlern gelesen und bestätigt werden muss.
        </p>
        <!-- Hier können Sie den gewünschten Inhalt einfügen, der die erfolgreiche Versendung bestätigt. -->
    </div>
    </div>
</div>
<?php
include 'footer.php';
?>
</body>



<?php


?>