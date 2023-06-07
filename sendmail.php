<?php
// Retrieve the passed variables from the URL parameters
$name = $_GET['name'];
$vorname = $_GET['vorname'];
$schule = $_GET['schule'];
$gb_datum = $_GET['gb_datum'];
$email = $_GET['email'];
$bestellungsId = $_GET['bestellungs_id'];
$whitchEmail = $_GET['whitchEmail'];
$menschId;
$message;
$servername = "localhost";
$username = "root";
$password = "";
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
} catch(PDOException $e) {
    echo "1 nul";
    exit();
}

$sql = "SELECT id, email FROM menschen WHERE email = :email";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':email', $email);
$stmt->execute();

if($stmt->rowCount() != 1) {
    echo "1 eins";
    exit();
}

$row = $stmt->fetch(PDO::FETCH_ASSOC);
$menschId = $row['id'];

$sql = "SELECT besteller_id, gast1_id, gast2_id, gast3_id, gast4_id, status FROM bestellung WHERE id = :bestellungsId AND (besteller_id = :menschId OR gast1_id = :menschId OR gast2_id = :menschId OR gast3_id = :menschId OR gast4_id = :menschId)";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':bestellungsId', $bestellungsId);
$stmt->bindParam(':menschId', $menschId);
$stmt->execute();

if($stmt->rowCount() != 1) {
    echo "1 zwei";
    exit();
}

echo "JETZT DÜRFTE DIE MAIL GESENDET WERDEN XD";

$to = $email;

switch($whitchEmail) {
    case 1:
        $params = array(
            'name' => $name,
            'vorname' => $vorname,
            'schule' => $schule,
            'gb_datum' => $gb_datum,
            'email' => $email,
        );
        // Bestätigungsmail für AGB und Bildrechte und dass die Karten bestellt werden dürfen.
        $subject = "Reservierung für die AKS EndOfYear Partayy Tickets";
        $getmesage = 'http://localhost/aks-EndOfYear-Partayy-ticket-website/ReservierungfürdieAKSEndOfYreaarPartaayTicket.php?' . http_build_query($params);
        $from = "lars.handwerker@web.de";
        $message = file_get_contents($getmesage);
        echo $message;

        try {
            // Servereinstellungen
            $mail->SMTPDebug = SMTP::DEBUG_SERVER; // Aktiviere detaillierte Debug-Ausgabe
            $mail->isSMTP(); // Sende über SMTP
            $mail->Host = $smtpHost; // Setze den SMTP-Server für den Versand
            $mail->SMTPAuth = true; // Aktiviere SMTP-Authentifizierung
            $mail->Username = $mailusername; // SMTP-Benutzername
            $mail->Password = $mailpassword; // SMTP-Passwort
            $mail->SMTPSecure = "TLS"; // Aktiviere TLS-Verschlüsselung
            $mail->Port = $smtpPort; // TCP-Port zum Verbinden; verwende 587, wenn `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS` gesetzt ist
            $mail->CharSet   = 'UTF-8';
            $mail->Encoding  = 'base64';
            // Empfänger
            $mail->setFrom('lars.handwerker@web.de', 'AKS Karlsruhe');
            $mail->addAddress($email, $name.", ".$vorname); // Füge einen Empfänger hinzu

            // Inhalt
            $mail->isHTML(true); // Setze das E-Mail-Format auf HTML
            $mail->Subject = 'Reservierung Karten AKS EndOfYear Partayy';
            $mail->Body = $message;
            $mail->send();
            echo 'Die Nachricht wurde gesendet';
        } catch (Exception $e) {
            echo "Die Nachricht konnte nicht gesendet werden. Mailer Error: {$mail->ErrorInfo}";
        }

        break;
    case 2:
        echo "<h3>Zweite Begleitung</h3>";
        BegleitungForm($i);
        break;
    case 3:
        echo "<h3>Dritte Begleitung</h3>";
        BegleitungForm($i);
        break;
    case 4:
        echo "<h3>Vierte Begleitung</h3>";
        BegleitungForm($i);
        break;
}
?>
