<?php

function stornieren($whitch, $conn, $bestellungsHash, $stonierer){
    $stmt = $conn->prepare("UPDATE bestellung SET $whitch = 1 WHERE hash = :bestellungsHash;");
    $stmt->bindParam(':bestellungsHash', $bestellungsHash, PDO::PARAM_STR);
    $stmt->execute();

    $stmt = $conn->prepare("SELECT id FROM bestellung WHERE hash = :bestellungsHash;");
    $stmt->bindParam(':bestellungsHash', $bestellungsHash, PDO::PARAM_STR);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row !== false) {
        $reservierungs_id = $row['id'];
        echo "Die Bestellung wurde storniert. Aktualisierte Zeilen-ID: $reservierungs_id";
    } else {
        echo "Es wurden keine Zeilen aktualisiert.";
    }

    
    $stmt = $conn->prepare("UPDATE main SET status = 'frei', mensch_id = null, reservierung_id = null WHERE mensch_id = :besteller_id AND reservierung_id = :reservierungs_id;");
    $stmt->bindParam(':reservierungs_id', $reservierungs_id, PDO::PARAM_INT);
    $stonierer_id = $stonierer->getId();
    $stmt->bindParam(':besteller_id', $stonierer_id, PDO::PARAM_INT);
    $stmt->execute();
    echo "die bestellung wurde stuniert";
    $stmt = $conn->prepare("SELECT Anzahl_tickets, besteller_id, gast1_id, gast2_id, gast3_id, gast4_id, besteller_storniert, gast1_storniert, gast2_storniert, gast3_storniert, gast4_storniert FROM bestellung WHERE hash = :bestellungsHash;");
    $stmt->bindParam(':bestellungsHash', $bestellungsHash, PDO::PARAM_STR);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $number_of_tickets = $row['Anzahl_tickets'];
    $flag = 1;
    if($row['besteller_storniert'] == false && $row['besteller_id'] != $stonierer_id){
        $flag = 0;
        echo "besteller";
    }
    if($number_of_tickets > 1){

        if($row['gast1_storniert'] == false && $row['gast1_id'] != $stonierer_id){
            $flag = 0;
            echo "gast1";
        }
    }
    if($number_of_tickets > 2 ){
        if($row['gast2_storniert'] == false && $row['gast2_id'] != $stonierer_id){
            $flag = 0;
            echo "gast2";
        }
    }
    if($number_of_tickets > 3 ){
        if($row['gast3_storniert'] == false && $row['gast3_id'] != $stonierer_id){
            $flag = 0;
            echo "gast3";
        }
    }
    if($number_of_tickets > 4 ){
        if($row['gast4_storniert'] == false && $row['gast4_id'] != $stonierer_id){
            $flag = 0;
            echo "gast4";
        }
    }
    if($flag == 1){
        echo "flag war 1";
        $stmt = $conn->prepare("UPDATE bestellung SET status = 'storno' WHERE hash = :bestellungsHash;");
        $stmt->bindParam('bestellungsHash', $bestellungsHash, PDO::PARAM_STR);
        $stmt->execute();
    }
}

include 'statics.php';
include 'Mensch.php';
$personHash = $_GET['personhash'];
$bestellungsHash = $_GET['bestellungsHash'];


$annzahlTickets;
$menschid;
$karteGekauft = 'Wenn due die karten zurückgeben willst, schreib und bitte eine <a href="https://www.instagram.com/aks.karlsruhe/" style="color: #63007F;"><span style="display: inline-block; width: 16px; height: 16px; background-image: url("https://example.com/ig-logo-bw.png"); background-size: cover; margin-right: 5px; vertical-align: middle;"></span>Instagram</a> oder per E-Mail.';


try {
    $conn = new PDO("mysql:host=$servername;dbname=aks-EndOfYear-Partayy-tickets", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connected successfully <br>";
  } catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
  }

  #creat besteller to del him
  $stonierer = new Mensch();
  if(!$stonierer->loadViaHash($conn, $personHash)){
      exit();
  }
  if(!$stonierer->loadreseRvierungIDViabestellungsHash($conn, $bestellungsHash)){
      exit();
  }

$stmt = $conn->prepare("SELECT id, hash From menschen Where hash = :personHash;");
$stmt->bindParam(':personHash', $personHash,PDO::PARAM_STR);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$menschid= $row['id'];
$stmt = $conn->prepare("UPDATE menschen set email_verified = 1 WHERE hash = :personHash");
$stmt->bindParam(':personHash', $personHash,PDO::PARAM_STR);
$stmt->execute();

$stmt = $conn->prepare("SELECT hash, besteller_id, gast1_id, gast2_id, gast3_id, gast4_id, besteller_storniert, gast1_storniert, gast2_storniert, gast3_storniert, gast4_storniert From bestellung Where hash = :bestellungsHash;");
$stmt->bindParam(':bestellungsHash', $bestellungsHash, PDO::PARAM_STR);
$stmt->execute();
if($stmt->rowCount() == 1) {
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if($menschid == $row['besteller_id'] && !$row['besteller_storniert'] ){
        $whitch = "besteller_storniert";
    }
    if($menschid == $row['gast1_id'] && !$row['gast1_storniert'] ){
        $whitch = "gast1_storniert";
    }
    if($menschid == $row['gast2_id'] && !$row['gast2_storniert'] ){
        $whitch = "gast2_storniert";
    }
    if($menschid == $row['gast3_id'] && !$row['gast3_storniert'] ){
        $whitch = "gast3_storniert";
    }
    if($menschid == $row['gast4_id'] && !$row['gast4_storniert'] ){
        $whitch = "gast4_storniert";
    }
    stornieren($whitch, $conn, $bestellungsHash, $stonierer);
    
    


    $row = $stmt->fetch(PDO::FETCH_ASSOC);

}elseif($row['status'] == 'Gekauft'){
    echo $karteGekauft;
    } else {

    Echo "bestellung exestiert nicht !!!";
}


?>