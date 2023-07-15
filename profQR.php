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
$bestellungsHash = $_GET['bestellungsHash'];
$personHash = $_GET['hash'];
$Localpassword;
if ($_GET['password'] != NULL) {
    $Localpassword = $_GET['password'];
}

try {
    $conn = new PDO("mysql:host=$servername;dbname=aks-EndOfYear-Partayy-tickets", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "1 nul";
    exit();
}

if ($Localpassword != NULL) {
    $PwHash = hash("sha3-256", $Localpassword . $hashSeedForPW);
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
        if ($row['status'] != 'besteatigt') {

            echo "Die karten sind entweder nicht fertig reserviert, abgelaufen oder schon abheolt worden.";
        } else {
            #TODO->Einzeld oder zusammen ? 
            # button mit dem man den verkauf besteatiegen muss!?
            #TODO->send mail mit pdf an Ausgabestelle.
            UpdateStatusAbgeholt($conn, $bestellungsHash, $row['id']);
            echo "Die karte wurde als abgeholt markiert und versendet.";
        }


    }


}else{

    ?>

<div class="container">
      <h1>Ticket-Ausgabe</h1>
      <h3>Bitte gib das password ein</h3>

      <form action="profQR.php" method="get">
        <label for="password">Geburztag:</label>
        <input type="password" id="password" name="password" required>
        <input type="hidden" id="bestellungsHash" name="bestellungsHash" value="<?php echo $bestellungsHash ?>" hidden>
        <input type="hidden" id="hash" name="hash" value="<?php echo $personHash ?>" hidden>

        
        <input type="submit" value="Reservieren">
      </form>
    </div>
<?php


}

?>