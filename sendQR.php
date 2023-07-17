<?php

function getqrcodepath($NumberOfBytes, $URL)
{
    $QRPath = $URL."/qrcodes/";
    $filename = "qrcode" .bin2hex(random_bytes($NumberOfBytes)).".png";
    return $QRPath . $filename;
}

// Retrieve the passed variables from the URL parameters
$bestellungsHash = $_GET['bestellungsHash'];

include "statics.php";
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;


require 'includes/PHPMailer/src/Exception.php';
require 'includes/PHPMailer/src/PHPMailer.php';
require 'includes/PHPMailer/src/SMTP.php';
require 'includes/phpqrcode/qrlib.php';


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

if ($row['einzeld_oder_zusammen'] == FALSE || $Anzahl_tickets == 1) {
    $stmt = $conn->prepare("SELECT id, hash, email FROM menschen WHERE id = :id");
    $stmt->bindParam(':id', $row['besteller_id'], PDO::PARAM_INT);
    $stmt->execute();
    $mensch = $stmt->fetch(PDO::FETCH_ASSOC);
    $email = $mensch['email'];
    $hash = $mensch['hash'];
    $QRPath = getqrcodepath($NumberOfBytes, $URL);
    QRcode::png($URL."profQR.php?bestellungsHash=" . urlencode($bestellungsHash) . "&hash=" . urlencode($hash), $QRPath);
    #get contett of mail 
    $file = $URL.'Send_QE_code.php';
    $QRPath = $URL.$QRPath;
    $params = [
        'bestellungsHash' => $bestellungsHash,
        'path' => $QRPath
    ];
    $UrlFromFile = $file . '?' . http_build_query($params);
    $message = file_get_contents($UrlFromFile);
    $to = $email;
    $from = "lars.handwerker@web.de";

    try {
        $mail->isSMTP();
        // Servereinstellungen
        #$mail->SMTPDebug = SMTP::DEBUG_SERVER; // Aktiviere detaillierte Debug-Ausgabe
        $mail->isSMTP(); // Sende über SMTP

        $mail->Host = $smtpHost; // Setze den SMTP-Server für den Versand
        $mail->SMTPAuth = true;
        $mail->Username = $mailusername;
        $mail->Password = $mailpassword;
        $mail->SMTPSecure = "TLS";
        $mail->Port = $smtpPort;
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';

        $mail->setFrom($from, 'AKS Karlsruhe');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'QR für die Abholung der Tickets';
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

    $stmt = $conn->prepare("SELECT id, hash FROM menschen WHERE id = :id;");
    for ($i = 1; $i <= $Anzahl_tickets; $i++) {
        $stmt->bindParam(':id', $ids["gast" . $i . "_id"], PDO::PARAM_INT);
        $stmt->execute();
        $mensch = $stmt->fetch(PDO::FETCH_ASSOC);
        $email = $mensch['email'];
        $hash = $mensch['hash'];
        $QRPath = getqrcodepath($NumberOfBytes, $URL);
        QRcode::png($URL."profQR.php?bestellungsHash=" . urlencode($bestellungsHash) . "&hash=" . urlencode($hash), $QRPath);

        $file = $URL.'Send_QE_code.php';
        $params = [
            'bestellungsHash' => $bestellungsHash,
            'path' => $QRPath
        ];
        echo $QRPath;
        $url = $file . '?' . http_build_query($params);
        $message = file_get_contents($url);

        $to = $email;
        $from = "lars.handwerker@web.de";

        try {
            $mail->isSMTP();
            $mail->Host = $smtpHost;
            $mail->SMTPAuth = true;
            $mail->Username = $mailusername;
            $mail->Password = $mailpassword;
            $mail->SMTPSecure = "TLS";
            $mail->Port = $smtpPort;
            $mail->CharSet = 'UTF-8';
            $mail->Encoding = 'base64';

            $mail->setFrom($from, 'AKS Karlsruhe');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'QR für die Abholung der Tickets';
            $mail->Body = $message;

            $mail->send();
        } catch (Exception $e) {
            echo "Die Nachricht konnte nicht gesendet werden. Mailer Error: {$mail->ErrorInfo}";
        }

    }
}


?>
