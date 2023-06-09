<?php

function stornieren($whitch, $conn, $bestellungsHash){
    $sql = "UPDATE bestellung SET ".$whitch." = true WHERE hash = '".$bestellungsHash."';";
    $conn->query($sql);

    echo "die bestellung wurde stuniert";
    $sql = "SELECT Anzahl_tickets, besteller_storniert, gast1_storniert, gast2_storniert, gast3_storniert, gast4_storniert From bestellung WHERE hash = '".$bestellungsHash."';";
    $result = $conn->query($sql);
    $row = $result->fetch(PDO::FETCH_ASSOC);
    $number_of_tickets = $row['Anzahl_tickets'];
    $flag = 1;
    if($row=['besteller_storniert'] == false){
        $flag = 0;

    }
    if($number_of_tickets < 1 ){

        if($row=['gast1_storniert'] == false){
            $flag = 0;
    
        }
    }
    if($number_of_tickets < 2 ){
        if($row=['gast2_storniert'] == false){
            $flag = 0;
    
        }
    }
    if($number_of_tickets < 3 ){
        if($row=['gast3_storniert'] == false){
            $flag = 0;
    
        }
    }
    if($number_of_tickets < 4 ){
        if($row=['gast4_storniert'] == false){
            $flag = 0;
    
        }
    }
    if($flag == 1){
        echo "status = storno";
        $sql = "UPDATE bestellung SET status = 'storno' WHERE hash = '".$bestellungsHash."';";
        $conn->query($sql);
    }
}
$personHash = $_GET['personhash'];
$bestellungsHash = $_GET['bestellungsHash'];


$annzahlTickets;
$menschid;
$karteGekauft = 'Wenn due die karten zurückgeben willst, schreib und bitte eine <a href="https://www.instagram.com/aks.karlsruhe/" style="color: #63007F;"><span style="display: inline-block; width: 16px; height: 16px; background-image: url("https://example.com/ig-logo-bw.png"); background-size: cover; margin-right: 5px; vertical-align: middle;"></span>Instagram</a> oder per E-Mail.';
include 'sqlAuth.php';
include 'hashSeed.php';

try {
    $conn = new PDO("mysql:host=$servername;dbname=aks-EndOfYear-Partayy-tickets", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connected successfully <br>";
  } catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
  }
$sql = "SELECT id, hash From menschen Where hash = '".$personHash."';";

$result = $conn->query($sql);
$row = $result->fetch(PDO::FETCH_ASSOC);
$menschid= $row['id'];

$sql = "SELECT hash, besteller_id, gast1_id, gast2_id, gast3_id, gast4_id, besteller_storniert, gast1_storniert, gast2_storniert, gast3_storniert, gast4_storniert From bestellung Where hash = '".$bestellungsHash."';";
$result = $conn->query($sql);
if($result->rowCount() == 1) {
    $row = $result->fetch(PDO::FETCH_ASSOC);
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
    stornieren($whitch, $conn,$bestellungsHash);
    
    


    $row = $result->fetch(PDO::FETCH_ASSOC);

}elseif($row['status'] == 'Gekauft'){
    echo $karteGekauft;
    } else {

    Echo "bestellung exestiert nicht !!!";
}


?>