<?php
// Retrieve the passed variables from the URL parameters
$vorname = $_GET['vorname'];
$whitchEmail = $_GET['whitchEmail'];
$email = $_GET['email'];
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
} catch(PDOException $e) {
    echo "1 nul";
    exit();
}

$sql = "SELECT id, hash FROM menschen WHERE hash = '".$personHash."';";
$result = $conn->query($sql);

if($result->rowCount() != 1) {
    echo "Der mensch exestiert nicht";
    exit();
}

$row = $result->fetch(PDO::FETCH_ASSOC);
$menschId = $row['id'];

$sql = "SELECT hash, gast1_id, gast2_id, gast3_id, gast4_id, status FROM bestellung WHERE hash = '".$bestellungsHash."' AND (besteller_id = ".$menschId." OR gast1_id = ".$menschId." OR gast2_id = ".$menschId." OR gast3_id = ".$menschId." OR gast4_id = ".$menschId.")";
$result = $conn->query($sql);


if($result->rowCount() == 0) {
    echo "Die bestellung exestiert nicht null";
    exit();
}
if($result->rowCount() > 1) {
    echo "Die bestellung exestiert nicht mehr als eins";
    exit();
}

#echo "JETZT DÜRFTE DIE MAIL GESENDET WERDEN XD";

$to = $email;

switch($whitchEmail) {
    case 1:
        $params = array(
            'vorname' => $vorname,
            'personHash' => $personHash,
            'bestellungsHash' => $bestellungsHash,
        );
        // Bestätigungsmail für AGB und Bildrechte und dass die Karten bestellt werden dürfen.
        $subject = "Reservierung für die AKS EndOfYear Partayy Tickets";
        $getmesage = 'http://localhost/aks-EndOfYear-Partayy-ticket-website/ReservierungfürdieAKSEndOfYreaarPartaayTicket.php?' . http_build_query($params);
        $from = "lars.handwerker@web.de";
        $message = file_get_contents($getmesage);
        #cho $message;

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
            $mail->CharSet   = 'UTF-8';
            $mail->Encoding  = 'base64';
            // Empfänger
            $mail->setFrom($from, 'AKS Karlsruhe');


            $mail->addAddress($email); // Füge einen Empfänger hinzu

            // Inhalt
            $mail->isHTML(true); // Setze das E-Mail-Format auf HTML
            $mail->Subject = 'Reservierung Karten AKS EndOfYear Partayy';
            $mail->Body = $message;
            $mail->send();
           # echo 'Die Nachricht wurde gesendet';
        } catch (Exception $e) {
            #echo "Die Nachricht konnte nicht gesendet werden. Mailer Error: {$mail->ErrorInfo}";
        }
        ?>

<body>
    <div class="container">
        <h1>Die E-Mail wurde erfolgreich verschickt.</h1>
        <p>Nun musst du, und ggf. deine begleitung die daten bestätiegen und den agbs zustimmen. <br>
            Danach erhälst du eine weiter mail mit einem qr codr zum abholen der Karten</p>
        <!-- Hier können Sie den gewünschten Inhalt einfügen, der die erfolgreiche Versendung bestätigt. -->
    </div>
</body>



<?php
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