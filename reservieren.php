<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <title>Reservierung</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
    <?php
function CheckAll($agb_check, $einwilligung_bild_ton){
  $flag = true;
  
  if($agb_check != "on"){
    $flag = false;
    echo "agb false";
  }
  
  if($einwilligung_bild_ton != "on"){
    $flag = false;
    echo "agbbbb false";
  }

  return $flag;
}

function CleanEverything($conn, $IdListMain, $number_of_tickets){
  #echo "CleanEverything wird<br>";
  for ($i = 1; $i <= $number_of_tickets; $i++) {
    if($i==1){
      $id = $IdListMain['besteller'];

    }
    if($i==2){
      $id = $IdListMain['gast1'];

    }
    if($i==3){
      $id = $IdListMain['gast2'];

    }
    if($i==4){
      $id = $IdListMain['gast3'];

    }
    if($i==5){
      $id = $IdListMain['gast1'];

    }
    $sql = "UPDATE main SET status = 'frei' WHERE id = ".$id.";";
    $conn->query($sql);
  }
}
function TestIdForActivOrders($conn, $idBestellerMenschen, $IdListMain, $number_of_tickets){
  #echo "TestIdForActivOrders wird<br>";
  $sql = "SELECT besteller_id, gast1_id, gast2_id, gast3_id, gast4_id, status FROM bestellung WHERE besteller_id = '".$idBestellerMenschen."' OR gast1_id = '".$idBestellerMenschen."' OR gast2_id = '".$idBestellerMenschen."' OR gast3_id = '".$idBestellerMenschen."' OR gast4_id = '".$idBestellerMenschen."';";
  $result = $conn->query($sql);
  while($row = $result->fetch(PDO::FETCH_ASSOC)) {
      if ($row['status'] == "reserviert") {
          CleanEverything($conn, $IdListMain, $number_of_tickets);
          echo "Weiterleiten auf bestellung läuft schon Seite";
          exit();
      }
      if ($row['status'] == "verkauft") {
        CleanEverything($conn, $IdListMain, $number_of_tickets);
        echo "Weiterleiten auf karten wurden schon gekauft Seite";
        exit();
    }
}


}
function CheckExcistingBookings($conn, $IdListMain, $idBestellerMenschen, $number_of_tickets, $name, $vorname, $gb_datum, $email){
#test ob mail mit nicht abgebrochener bestellung verbunden
#echo "CheckExcistingBookings wird<br>";
$sql = "SELECT id, email FROM menschen WHERE email = '".$email."';";
$result = $conn->query($sql);
if ($result->rowCount() != 0) {
    
  while($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $idBestellerMenschen = $row['id'];
    TestIdForActivOrders($conn, $idBestellerMenschen, $IdListMain, $number_of_tickets);
  }
  }
    #testen ob mensch sonst exestiert -> besttellungstest und TODO->doppelte mesnchen


    $sql = "SELECT id, name, vorname, gb_datum FROM menschen WHERE name = '".$name."' AND vorname = '".$vorname."' AND gb_datum = '".$gb_datum."'";
    $result = $conn->query($sql);
    if ($result->rowCount() != 0) {
      while($row = $result->fetch(PDO::FETCH_ASSOC)) {
    
      TestIdForActivOrders($conn, $IdListMain['besteller'], $IdListMain, $number_of_tickets);
      }
    }
}

function BegleitungForm($i){
  ?>
    <label for="name<?php echo $i?>">Name:</label>
    <input type="text" id="name<?php echo $i?>" name="name<?php echo $i?>" required>

    <label for="Vorname<?php echo $i?>">Vorname:</label>
    <input type="text" id="Vorname<?php echo $i?>" name="Vorname<?php echo $i?>" required>

    <label for="schule<?php echo $i?>">Schule:</label>
    <input type="text" id="schule<?php echo $i?>" name="schule<?php echo $i?>" required>

    <label for="email<?php echo $i?>">E-mail:</label>
    <input type="email" id="email<?php echo $i?>" name="email<?php echo $i?>" required>

    <label for="start<?php echo $i?>">Geburztag:</label>
    <input type="date" id="geburztag<?php echo $i?>" name="geburztag<?php echo $i?>" required>

    <?php

}
function nichtStonier($conn, $number_of_tickets, $bestellungsId){
    $sql = "UPDATE bestellung SET besteller_stoniert = false, ";
    if($number_of_tickets > 1){
      $sql = $sql."gast1_stoniert = false";
    }
    if($number_of_tickets > 2){
      $sql = $sql.", gast1_stoniert = false";
    }
    if($number_of_tickets > 3){
      $sql = $sql.", gast1_stoniert = false";
    }
    if($number_of_tickets > 4){
      $sql = $sql.", gast1_stoniert = false";
    }
    $sql = $sql.";";
}

include 'sqlAuth.php';
include 'hashSeed.php';
$idBestellerMenschen = 0;
$id;
$bestellungsHash;
$personHash;
$IdListMain = array(
  "besteller" => 0,
  "gast1" => 0,
  "gast2" => 0,
  "gast3" => 0,
  "gast4" => 0,
);
$bestellungsId;
$number_of_tickets = $_POST['tickets'];
$agb_check = $_POST['agb'];
$einwilligung_bild_ton = $_POST['einwilligung'];
$name = $_POST['name'];
$vorname = $_POST['Vorname'];
$schule = $_POST['schule'];
$gb_datum = $_POST['geburztag'];
$email = $_POST['email'];
$schul_id = 0;

try {
  $conn = new PDO("mysql:host=$servername;dbname=aks-EndOfYear-Partayy-tickets", $username, $password);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
}

if(CheckAll($agb_check, $einwilligung_bild_ton)){

  
  $i = 0;
  
  #bekomen von freien ids von main 
  
  $sql = "SELECT id, STATUS FROM main WHERE status = 'frei';";
  $result = $conn->query($sql);
  if ($result->rowCount() > 0) {
    while($row = $result->fetch(PDO::FETCH_ASSOC)) {
      $i = $i +1;
      $id = $row["id"];
      $sql = "UPDATE main SET status = 'reserviert' WHERE id = ".$id.";";
      $conn->query($sql);
      if($i == 1){
        $IdListMain['besteller'] = $id;
      }
      if($i == 2){
        $IdListMain['gast1'] = $id;
      }
      if($i == 3){
        $IdListMain['gast2'] = $id;
      }
      if($i == 4){
        $IdListMain['gast3'] = $id;
      }
      if($i == 5){
        $IdListMain['gast4'] = $id;
      }
      if($i == $number_of_tickets){
        break;
      }
      
    }
 
  } else {
    echo "error!!!!!!!";
  }

  CheckExcistingBookings($conn, $IdListMain, $idBestellerMenschen, $number_of_tickets, $name, $vorname, $gb_datum, $email);

  #exestiert schule ? wenn ja get id

  $sql = "SELECT id, name FROM schulen WHERE name = '".$schule."';";
  $result = $conn->query($sql);
  if ($result->rowCount() > 0) {
    while($row = $result->fetch(PDO::FETCH_ASSOC)) {
      $schul_id = $row["id"];
      break;
    }
  } else {

  #wenn nein neue schule aufnehmen
    $sql = "INSERT INTO schulen (name) VALUES ('".$schule."');";
    $conn->query($sql);

    $sql = "SELECT id, name FROM schulen WHERE name = '".$schule."';";
    $result = $conn->query($sql);
    $row = $result->fetch(PDO::FETCH_ASSOC);
    $schul_id = $row["id"];
  }
  
  
  
  #reservierer mit abgebochenen bestellungen -> nicht neu anlegen 
  $sql = "SELECT id, name, vorname, gb_datum, hash FROM menschen WHERE name = '".$name."' AND vorname = '".$vorname."' AND gb_datum = '".$gb_datum."';";
  $result = $conn->query($sql);
  if($result->rowCount() > 0){
  $row = $result->fetch(PDO::FETCH_ASSOC);
  $idBestellerMenschen = $row["id"];
  $personHash  = $row["hash"];

  }else{
    $sql2 = "SELECT id, email, hash FROM bestellung WHERE email = '".$email."'";
    $result2 = $conn->query($sql);
    if($result2->rowCount() > 0){
      $row = $result2->fetch(PDO::FETCH_ASSOC);
      $idBestellerMenschen = $row["id"];
      $personHash =  $row["hash"];

    }else{
      # creat hash vor person
      $personHash = hash('sha3-512',$name.$vorname.$schule.$gb_datum.$email.$idBestellerMenschen.$hashseed, false);
      #erstellen von menschen in db
      $sql = "INSERT INTO menschen (name, vorname, gb_datum, schule_id, email, hash) VALUES ('".$name."','".$vorname."','".$gb_datum."','".$schul_id."', '".$email."','".$personHash."');";
      $conn->query($sql);
      $sql = "SELECT id, name, vorname, gb_datum, email FROM menschen WHERE name = '".$name."' AND vorname = '".$vorname."' AND gb_datum = '".$gb_datum."' AND email = '".$email."'";
      $result = $conn->query($sql);
      $row = $result->fetch(PDO::FETCH_ASSOC);
      $idBestellerMenschen = $row["id"];
    }
}
  


  $sql = "UPDATE main SET mensch_id = ".$idBestellerMenschen." WHERE id = ".$IdListMain['besteller'].";";
  $conn->query($sql);
  
  #erstellen von bestellung in db
  $sql = "INSERT INTO bestellung (Anzahl_tickets, besteller_id, status) VALUES ('".$number_of_tickets."', '".$idBestellerMenschen."','reserviert');";
  $conn->query($sql);
  $sql = "SELECT id,besteller_id, status FROM bestellung WHERE besteller_id = ".$idBestellerMenschen." AND status = 'reserviert';";
  $result = $conn->query($sql);
  $row = $result->fetch(PDO::FETCH_ASSOC);
  $bestellungsId = $row['id'];
} else {
  echo "Es wurden nicht alle Hacken gesetzt!";
  exit();
}
nichtStonier($conn, $number_of_tickets, $bestellungsId);

if($number_of_tickets == 1){
  
  # wenn es nur eine mail gibt wird der hash mit $name.$vorname.$schule.$gb_datum.$email.$row['wann_erstellt'] erstellt unter verwendung von sha3-512
  $sql = "SELECT id, wann_erstellt, status From bestellung Where id = ".$id." AND status = 'reserviert'";
  $result = $conn->query($sql);
  $row = $result->fetch(PDO::FETCH_ASSOC);
  #convert classic date to just a nummber
  
  $firstsplit = array();
  $firstsplit = explode(" ", $row['wann_erstellt']);
  $dateAsNumberString = "";
  $wannErstelleHash = "";
  foreach(explode("-", $firstsplit[0] ) as $i){
    $dateAsNumberString = $dateAsNumberString.$i;
  }
  foreach(explode(":", $firstsplit[1] ) as $i){
    $dateAsNumberString = $dateAsNumberString.$i;
  }
  $Nummberarray = array();
  $Nummberarray = str_split($dateAsNumberString);
  foreach($Nummberarray as $i){
    $i = chr($i);
    $wannErstelleHash = $wannErstelleHash.$i;
  }
  $key = $wannErstelleHash.$name.$vorname.$schule.$gb_datum.$email.$hashseed;
  $bestellungsHash = hash('sha3-512',$key , false);
  $sql = "UPDATE bestellung SET hash = '".$bestellungsHash."' WHERE besteller_id = ".$idBestellerMenschen.";";
  $conn->query($sql);
  #weiterleiten auf mail seite
  $sql = "SELECT id, besteller_id, status From bestellung Where besteller_id = ".$idBestellerMenschen." AND status = 'reserviert'";
  $result = $conn->query($sql);
  if($result->rowCount() == 1){
    $row = $result->fetch(PDO::FETCH_ASSOC);
    $bestellungsId = $row["id"];
    }else{
      echo "<h1>Es gab einen unbekanntenfehle bitte wennden sie sich an Support@mail.de</h1>";
      exit();
    }

  $params = array(
    'vorname' => $vorname,
    'whitchEmail' => 1,
    'personHash' => $personHash,
    'bestellungsHash' => $bestellungsHash,
    'email' => $email,
  );
  $sendmailURL = 'http://localhost/aks-EndOfYear-Partayy-ticket-website/sendmail.php?' . http_build_query($params);
  $response = file_get_contents($sendmailURL);
  echo $response;
  exit();
}

?>
    <div class="container">
        <h1>Infos für die weiteren Tickets</h1>
        <p>
        <h3>Jeder person muss sein eigene Mail bestätigen und den AGBs zustimmen.</h3>
        Dennoch können alle Tickets von Dir abgeholt und bezahlt werden.


        </p>
        <form action="reservieren.php" method="post">
            <div class="EinzeldAbholen">
                <input type="checkbox" id="EinzeldAbholen" name="EinzeldAbholen" required>
                <label for="EinzeldAbholen">Alle tickets sollen einzelnd abgeholt werden.</label>
            </div>
            <?php 
  for($i = 1; $i < $number_of_tickets; $i++){
    switch($i){
      case 1:
        echo "<h3>Erste begleitung</h3>";
        BegleitungForm($i);
        break;
      case 2:
        echo "<h3>Zweite begleitung</h3>";
        BegleitungForm($i);
        break;
      case 3:
        echo "<h3>Dritte begleitung</h3>";
        BegleitungForm($i);
        break;
      case 4:
        echo "<h3>Vierte begleitung</h3>";
        BegleitungForm($i);
        break;
    }

  }
?>
            <div class="EinzeldAbholen">
                <input type="checkbox" id="EinzeldAbholen" name="EinzeldAbholen" required>
                <label for="EinzeldAbholen">Alle tickets sollen einzelnd abgeholt werden.</label>
            </div>
            <br>
            <input type="submit" value="Reservieren">
        </form>
    </div>


</body>

</html>