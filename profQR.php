<!DOCTYPE html>
<html lang="de">

<head>
  <meta charset="UTF-8">
  <title>Reservierung</title>
  <link rel="stylesheet" type="text/css" href="style.css">
</head>

<?php
function UpdateStatusAbgeholt($conn, $bestellungsHash, $bestellungsID)
{
    $stmt = $conn->prepare('UPDATE bestellung SET status = "gekauft" WHERE hash = :bestellungsHash');
    $stmt->bindParam(':bestellungsHash', $bestellungsHash, PDO::PARAM_STR);
    $stmt->execute();
    $stmt = $conn->prepare('UPDATE main SET status = "verkauft" WHERE reservierung_id = :bestellungsID');
    $stmt->bindParam(':bestellungsID', $bestellungsID, PDO::PARAM_INT);
    $stmt->execute();
}

include "statics.php";
#wenn von link aufgerufen ist es get und wenn selbst aufruf post
if(isset($_GET['bestellungsHash'])) {
    $bestellungsHash = $_GET['bestellungsHash'];
}elseif (isset($_POST['bestellungsHash'])) {
    $bestellungsHash = $_POST['bestellungsHash'];
}else{
    echo "fatal error bestellungsHash";
    exit();
}
if(isset($_GET['hash'])) {
    $personHash = $_GET['hash'];
}elseif (isset($_POST['hash'])) {
    $personHash = $_POST['hash'];
}else{
    echo "fatal error hash";
    exit();
}
$localpassword;
if (isset($_POST['password'])) {
    $localpassword = $_POST['password'];
}

try {
    $conn = new PDO("mysql:host=$servername;dbname=aks-EndOfYear-Partayy-tickets", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "1 nul";
    exit();
}

if ($localpassword != NULL) {
    $PwHash = hash("sha3-256", $localpassword . $hashSeedForPW);
    $stmt = $conn->prepare('SELECT PwHash FROM password WHERE PwHash = :PwHash;');
    $stmt->bindParam(':PwHash', $PwHash, PDO::PARAM_STR);
    $stmt->execute();
    if ($stmt->rowCount() != 1) {
        echo "Das eingegebene Password stimmt leider nicht!";

    } else {

        $stmt = $conn->prepare("SELECT id, besteller_id, status, hash, einzeld_oder_zusammen FROM bestellung WHERE hash = :bestellungsHash");
        $stmt->bindParam(':bestellungsHash', $bestellungsHash, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row['status' != 'besteatigt']) {

            echo "Die karten sind entweder nicht fertig reserviert, abgelaufen oder schon abheolt worden.";
        } else {
            #TODO->Einzeld oder zusammen ? 

            #TODO->send mail mit pdf an Ausgabestelle.
            UpdateStatusAbgeholt($conn, $bestellungsHash, $row['id']);
            echo "Die karte wurde als abgeholt markiert und versendet.";
        }


    }


}else{

    ?>

<div class="container">
    <div class="content">
      <div class="inner-content">
<div class="header skew">
  <h1>AKS EndOfYear-Partayy</h1>
  <h3>Ticket-Ausgabe</h3>
</div>
      <h3>Bitte gib das password ein</h3>

      <form action="profQR.php" method="POST">
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <input type="hidden" id="bestellungsHash" name="bestellungsHash" value="<?php echo $bestellungsHash ?>" hidden>
        <input type="hidden" id="hash" name="hash" value="<?php echo $personHash ?>" hidden>

        
        <input type="submit" value="Reservieren">
      </form>
    </div>
</div>
</div>

<?php
include 'footer.php';

}

?>