<?php
// Retrieve the passed variables from the URL parameters

$bestellungsHash = $_GET['bestellungsHash'];
include 'statics.php';
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

$stmt = $conn->prepare("SELECT hash, besteller_id, gast1_id, gast2_id, gast3_id, gast4_id FROM bestellung WHERE hash = :hash");
$stmt->bindParam(':hash', $bestellungsHash, PDO::PARAM_STR);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$ids = array();
if ($row['besteller_id'] != NULL) {
    array_push($ids, $row['besteller_id']);
}
if ($row['gast1_id'] != NULL) {
    array_push($ids, $row['gast1_id']);
}
if ($row['gast2_id'] != NULL) {
    array_push($ids, $row['gast2_id']);
}
if ($row['gast3_id'] != NULL) {
    array_push($ids, $row['gast3_id']);
}
if ($row['gast4_id'] != NULL) {
    array_push($ids, $row['gast4_id']);
}


$from = "lars.handwerker@web.de";
#getMSG
$file = 'http://localhost/aks-EndOfYear-Partayy-ticket-website/aks-EndOfYear-Partayy-ticket-website/ReservierungfuerdieAKSEndOfYreaarPartaayTicket.php';

$params = [
    'bestellungsHash' => $bestellungsHash
];
$url = $file . '?&bestellungsHash=' . $bestellungsHash;

$message = file_get_contents($url);
$to = "lars.handwerker@web.de";


try {



    // Servereinstellungen
    #$mail->SMTPDebug = SMTP::DEBUG_SERVER; // Aktiviere detaillierte Debug-Ausgabe
    $mail->isSMTP(); // Sende über SMTP
    $stmt = $conn->prepare("SELECT id, email FROM menschen WHERE id = :id");
    $Bcc = "";
    foreach ($ids as $id) {
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $mail->addBcc($stmt->fetch(PDO::FETCH_ASSOC)['email']); 

    }


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
    $mail->AddAttachment('./beispiel.pdf', $name = 'beispiel.pdf',  $encoding = 'base64', $type = 'application/pdf');
    $mail->addAddress($to); // Füge einen Empfänger hinzu
    //add bcc 
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