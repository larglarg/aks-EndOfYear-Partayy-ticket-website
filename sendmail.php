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
$file = 'http://localhost/aks-EndOfYear-Partayy-ticket-website/aks-EndOfYear-Partayy-ticket-website/ReservierungfuerdieAKSEndOfYreaarPartaayTicket.php';

$params = [
    'personHash' => $personHash,
    'bestellungsHash' => $bestellungsHash
];
$url = $file . '?' . http_build_query($params);

$message = file_get_contents($url);
$to = $email;




try {
    // Servereinstellungen
    #$mail->SMTPDebug = SMTP::DEBUG_SERVER; // Aktiviere detaillierte Debug-Ausgabe
    $mail->isSMTP(); // Sende über SMTP

    $mail->Host = $smtpHost; // Setze den SMTP-Server für den Versand
    $mail->SMTPAuth = true; // Aktiviere SMTP-Authentifizierung
    $mail->Username = $mailusername; // SMTP-Benutzername
    $mail->Password = $mailpassword; // SMTP-Passwort
    $mail->SMTPSecure = "TLS"; // Aktiviere TLS-Verschlüsselung
    $mail->Port = $smtpPort; // TCP-Port zum Verbinden; verwende 587, wenn `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS` gesetzt ist
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';
    // Empfänger
    $mail->setFrom($from, 'AKS Karlsruhe');


    $mail->addAddress($email); // Füge einen Empfänger hinzu

    // Inhalt
    $mail->isHTML(true); // Setze das E-Mail-Format auf HTML
    $mail->Subject = 'Reservierung Karten AKS EndOfYear Partayy';
    $mail->Body = $message;
    $mail->send();
} catch (Exception $e) {
    echo "Die Nachricht konnte nicht gesendet werden. Mailer Error: {$mail->ErrorInfo}";
}
?>

<body>
    <div class="container">
        <h1>Die E-Mail wurde erfolgreich verschickt.</h1>
        <p>Nun musst du deine E-Mail bestätiegen. <br>
            Danach erhälst du eine weiter mail mit dem qr code zum abholen der Karten und einer einverständinsserklärung
            die von deinen erlern gelesen und bestätigt werden muss.
        </p>
        <!-- Hier können Sie den gewünschten Inhalt einfügen, der die erfolgreiche Versendung bestätigt. -->
    </div>
</body>



<?php


?>