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

function CleanEverything($conn, $id_list, $number_of_tickets){
  for ($i = 1; $i <= $number_of_tickets; $i++) {
    if($i==1){
      $id = $id_list['besteller'];

    }
    if($i==2){
      $id = $id_list['gast1'];

    }
    if($i==3){
      $id = $id_list['gast2'];

    }
    if($i==4){
      $id = $id_list['gast3'];

    }
    if($i==5){
      $id = $id_list['gast1'];

    }
    $sql = "UPDATE main SET status = 'frei' WHERE id = ".$id.";";
    $conn->query($sql);
  }
}
function TestIdForActivOrders($conn, $id){
  $sql = "SELECT besteller_id, gast1_id, gast2_id, gast3_id, gast4_id, status FROM bestellung WHERE besteller_id = '".$id."' OR gast1_id = '".$id."' OR gast2_id = '".$id."' OR gast3_id = '".$id."' OR gast4_id = '".$id."';";
  $result = $conn->query($sql);
  while($row = $result->fetch(PDO::FETCH_ASSOC)) {
      if ($row['status'] == "reserviert") {
          CleanEverything($conn, $id_list, $number_of_tickets);
          echo "Weiterleiten auf bestellung läuft schon Seite";
          return;
      }
      if ($row['status'] == "verkauft") {
        CleanEverything($conn, $id_list, $number_of_tickets);
        echo "Weiterleiten auf karten wurden schon gekauft Seite";
        return;
    }
}


}
function CheckExcistingBookings($conn, $id_list, $number_of_tickets, $name, $vorname, $gb_datum, $email){
#test ob mail mit nicht abgebrochener bestellung verbunden

$sql = "SELECT id, email FROM menschen WHERE email = '".$email."';";
$result = $conn->query($sql);
if ($result->rowCount() != 0) {
    
  while($row = $result->fetch(PDO::FETCH_ASSOC)) {
    TestIdForActivOrders($conn, $row['id']);
  }
  }
    #testen ob mensch sonst exestiert -> besttellungstest und TODO->doppelte mesnchen


    $sql = "SELECT id, name, vorname, gb_datum FROM menschen WHERE name = '".$name."' AND vorname = '".$vorname."' AND gb_datum = '".$gb_datum."'";
    $result = $conn->query($sql);
    if ($result->rowCount() != 0) {
      while($row = $result->fetch(PDO::FETCH_ASSOC)) {
    
      TestIdForActivOrders($conn, $row['id']);
      }
    }
}

$servername = "localhost";
$username = "root";
$password = "";
$id_besteller;
$id;
$id_list = array(
  "besteller" => 0,
  "gast1" => 0,
  "gast2" => 0,
  "gast3" => 0,
  "gast4" => 0,
);
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
        $id_list['besteller'] = $id;
      }
      if($i == 2){
        $id_list['gast1'] = $id;
      }
      if($i == 3){
        $id_list['gast2'] = $id;
      }
      if($i == 4){
        $id_list['gast3'] = $id;
      }
      if($i == 5){
        $id_list['gast4'] = $id;
      }
      if($i == $number_of_tickets){
        break;
      }
      
    }
 
  } else {
    echo "error!!!!!!!";
  }

  CheckExcistingBookings($conn, $id_list, $number_of_tickets, $name, $vorname, $gb_datum, $email);

  #exestiert schule ? wenn ja get id

  $sql = "SELECT id, name FROM schulen WHERE name = '".$schule."';";
  echo $sql;
  $result = $conn->query($sql);
  if ($result->rowCount() > 0) {
    while($row = $result->fetch(PDO::FETCH_ASSOC)) {
      $schul_id = $row["id"];
      echo "<br>".$schul_id;
      break;
    }
  } else {

  #wenn nein neue schule aufnehmen
    echo $schule;
    $sql = "INSERT INTO schulen (name) VALUES ('".$schule."');";
    $conn->query($sql);

    $sql = "SELECT id, name FROM schulen WHERE name = '".$schule."';";
    $result = $conn->query($sql);
    $row = $result->fetch(PDO::FETCH_ASSOC);
    print $row["id"];
    $schul_id = $row["id"];
    echo "<br>".$schul_id;
  }
  
  
  
  #reservierer mit abgebochenen bestellungen -> nicht neu anlegen
  $sql = "SELECT id, name, vorname, gb_datum, email FROM menschen WHERE name = '".$name."' AND vorname = '".$vorname."' AND gb_datum = '".$gb_datum."' AND email = '".$email."'";
  $result = $conn->query($sql);
  if($result->rowCount() > 0){
  $row = $result->fetch(PDO::FETCH_ASSOC);
  $id_list['besteller'] = $row["id"];
  echo $id_list['besteller']."wurde als id gesetzt";
  
  }else{
    #erstellen von menschen in db
    $sql = "INSERT INTO menschen (name, vorname, gb_datum, schule_id, ticketstatus, email) VALUES ('".$name."','".$vorname."','".$gb_datum."','".$schul_id."', 'reserviert', '".$email."');";
    echo $sql;
    $conn->query($sql);
  }

  $sql = "UPDATE main SET mensch_id = ".$id_list['besteller']." WHERE id = ".$id.";";
  $conn->query($sql);

} else {
  echo "Es wurden nicht alle Hacken gesetzt!";
}
?>

<div class="popup-container" id="popupContainer" style="display: none;">
  <div class="popup">
    <span class="close-btn" onclick="closePopup()">X</span>
    <h2>Willkommen im Popup!</h2>
    <p>Dies ist ein Beispiel für ein Popup-Fenster.</p>
  </div>
</div>

<script>
  function openPopup() {
    document.getElementById("popupContainer").style.display = "flex";
  }

  function closePopup() {
    document.getElementById("popupContainer").style.display = "none";
  }
</script>

</body>

</html>
