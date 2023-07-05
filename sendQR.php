<?php

function getqrcodepath($bestellungsHash, $hash)
{


    $QRPath = "./qrcodes/";
    $filename = "qrcode" . $bestellungsHash . $hash . ".png";
    $codeContents = "http://localhost/aks-EndOfYear-Partayy-ticket-website/aks-EndOfYear-Partayy-ticket-website/profQR.php?bestellungsHash=" . urlencode($bestellungsHash) . "&hash=" . urlencode($hash);



    return $QRPath . $filename . $codeContents;

}
// Retrieve the passed variables from the URL parameters
$menschId;
$message;
include 'sqlAuth.php';
$bestellungsHash = $_GET['bestellungsHash'];
include 'mailAuth.php';





use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;
use PHPQRCode\QRcode;


require 'includes/PHPMailer/src/Exception.php';

require 'includes/PHPMailer/src/PHPMailer.php';
require 'includes/PHPMailer/src/SMTP.php';
require 'includes/phpqrcode/qrlib.php';

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
$stmt = $conn->prepare("SELECT Anzahl_tickets, einzeld_oder_zusammen, besteller_id, gast1_id, gast2_id, gast3_id, gast4_id FROM bestellung WHERE hash = :hash");
$stmt->bindParam(':hash', $bestellungsHash, PDO::PARAM_STR);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$Anzahl_tickets = $row['Anzahl_tickets'];
if ($row['einzeld_oder_zusammen'] == 0 || $Anzahl_tickets == 1) {
    #einfach einmal die mail an den besteller
    $stmt = $conn->prepare("SELECT id, hash, email FROM menschen WHERE id = :id");
    $stmt->bindParam(':id', $row['besteller_id'], PDO::PARAM_INT);
    $stmt->execute();
    $mensch = $stmt->fetch(PDO::FETCH_ASSOC);
    $email = $mensch['email'];

    $hash = $mensch['hash'];

    $QRPath = getqrcodepath($bestellungsHash, $hash);

    QRcode::png("http://localhost/aks-EndOfYear-Partayy-ticket-website/aks-EndOfYear-Partayy-ticket-website/profQR.php?bestellungsHash=" . urlencode($bestellungsHash) . "&hash=" . urlencode($hash), $QRPath);
    $file = 'http://localhost/aks-EndOfYear-Partayy-ticket-website/aks-EndOfYear-Partayy-ticket-website/Send_QE_code.php';

    $params = [
        'personHash' => $personHash,
        'bestellungsHash' => $bestellungsHash,
        'path' => $QRPath
    ];
    $url = $file . '?' . http_build_query($params);


    $message = file_get_contents($url);
    $to = $email;
    $from = "lars.handwerker@web.de";
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
        $mail->Subject = 'QR für die abholung der Tickets';
        $mail->Body = $message;
        $mail->send();
    } catch (Exception $e) {
        echo "Die Nachricht konnte nicht gesendet werden. Mailer Error: {$mail->ErrorInfo}";
    }
} else {

    $ids = array(
        'besteller_id' => $row['besteller_id'],
        'gast1_id' => $row['gast1_id'],
        'gast2_id' => $row['gast2_id'],
        'gast3_id' => $row['gast3_id'],
        'gast4_id' => $row['gast4_id'],
    );
    #wenn ein stmt in for muss es auch wieder rein
    $stmt = $conn->prepare("SELECT id, hash FROM menschen WHERE id = :id;");
    for($i = 1; $i <= $Anzahl_tickets; $i++ ){
        #sollte warschgeinlich in eine functuin und mit der von drüber verbunden werden xD mal schauen ob ich das noch mache xD
        
        $stmt->bindParam(':id', $ids[$i], PDO::PARAM_INT);
        $mensch = $stmt->fetch(PDO::FETCH_ASSOC);
        $email = $mensch['email'];
    
        $hash = $mensch['hash'];
        $QRPath = getqrcodepath($bestellungsHash, $hash);

        QRcode::png("http://localhost/aks-EndOfYear-Partayy-ticket-website/aks-EndOfYear-Partayy-ticket-website/profQR.php?bestellungsHash=" . urlencode($bestellungsHash) . "&hash=" . urlencode($hash), $QRPath);
        $file = 'http://localhost/aks-EndOfYear-Partayy-ticket-website/aks-EndOfYear-Partayy-ticket-website/Send_QE_code.php';
    
        $params = [
            'personHash' => $personHash,
            'bestellungsHash' => $bestellungsHash,
            'path' => $QRPath
        ];
        $url = $file . '?' . http_build_query($params);
    
    
        $message = file_get_contents($url);
        $to = $email;
        $from = "lars.handwerker@web.de";
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
            $mail->Subject = 'QR für die abholung der Tickets';
            $mail->Body = $message;
            $mail->send();
        } catch (Exception $e) {
            echo "Die Nachricht konnte nicht gesendet werden. Mailer Error: {$mail->ErrorInfo}";
        }

    }
}


?>