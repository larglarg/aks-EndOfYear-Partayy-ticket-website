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
  echo $sql;
  $result = $conn->query($sql);
  if ($result->rowCount() > 0) {
    while($row = $result->fetch(PDO::FETCH_ASSOC)) {
      print $row["id"];
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
  

print_r($id_list);

  $sql = "SELECT id, name FROM schulen WHERE name = '".$schule."';";
  echo $sql;
  $result = $conn->query($sql);
  if ($result->rowCount() > 0) {
    while($row = $result->fetch(PDO::FETCH_ASSOC)) {
      print $row["id"];
     
      $schul_id = $row["id"];
      echo "<br>".$schul_id;
      break;
    }
  } else {
    echo $schule;
    $sql = "INSERT INTO schulen (name) VALUES ('".$schule."');";
    $conn->query($sql);

    $sql = "SELECT id, name FROM schulen WHERE name = '".$schule."';";
    $result = $conn->query($sql);
    while($row = $result->fetch(PDO::FETCH_ASSOC)) {
      print $row["id"];
      
      $schul_id = $row["id"];
      echo "<br>".$schul_id;
      break;
    }
  }

  $sql = "INSERT INTO menschen (name, vorname, gb_datum, schule_id, ticketstatus, email) VALUES ('".$name."', '".$vorname."', '".$gb_datum."', '".$schul_id."', 'reserviert', '".$email."');";
  echo "<br><br> <h1>".$sql."</h1>";
  
  $conn->query($sql);
  $sql = "SELECT id, name, vorname, email FROM menschen WHERE name = '".$name."' AND vorname = '".$vorname."' AND email = '".$email."'";
  $result = $conn->query($sql);
  while($row = $result->fetch(PDO::FETCH_ASSOC)) {
    print $row["id"];
    $id_list['besteller'] = $row["id"];
    $sql = "UPDATE main SET mensch_id = ".$id_list['besteller']." WHERE id = ".$id.";";
    $conn->query($sql);
    break;
  }

  print_r($result);
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
