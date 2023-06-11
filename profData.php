<?php
$personHash = $_GET['personhash'];
$bestellungsHash = $_GET['bestellungsHash'];
include "sqlAuth.php";
try {
    $conn = new PDO("mysql:host=$servername;dbname=aks-EndOfYear-Partayy-tickets", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connected successfully <br>";
  } catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
  }
$sql = "SELECT id FROM menschen WHERE hash = '".$personHash."';";
$result = $conn->query($sql);
$row = $result->fetch(PDO::FETCH_ASSOC);
$menschID = $row['id'];
$sql = "SELECT besteller_id, gast1_id, gast2_id, gast3_id, gast4_id, besteller_storniert, gast1_storniert, gast2_storniert, gast3_storniert, gast4_storniert FROM bestellung Where hash = '".$bestellungsHash."';";
$result = $conn->query($sql);
$row = $result->fetch(PDO::FETCH_ASSOC);
$flag = 0;
#testen ob die person ihre bestellung nicht schon stoniert hat
if($menschID == $row['besteller_id'] && !$row['besteller_storniert']){
    $flag = 1;
}elseif($menschID == $row['gast1_id'] && !$row['gast1_storniert']){
    $flag = 1;
}elseif($menschID == $row['gast2_id'] && !$row['gast2_storniert']){
    $flag = 1;
}elseif($menschID == $row['gast3_id'] && !$row['gast3_storniert']){
    $flag = 1;
}elseif($menschID == $row['gast4_id'] && !$row['gast4_storniert']){
    $flag = 1;
}else{
    echo "Die bestellung wurde stoniert!!!";
    exit();
}

#auslesen der daten zum überprüfen.
if($flag == 1){
    $sql = "SELECT * FROM menschen WHERE hash = '".$personHash."';";
    $result = $conn->query($sql);
    $Menscharray = $result->fetch(PDO::FETCH_ASSOC);
    $sql = "SELECT name, id FROM schulen WHERE id = ".$Menscharray['schule_id']."; ";
    $result = $conn->query($sql);
    $row = $result->fetch(PDO::FETCH_ASSOC);
    $schulname = $row['name'];
}
?>
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <title>Reservierung</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
<div class="container">
    <h1>
        Hey, bitte Überprüfe bitte einmal diedaten:
    </h1>

    <form action="besteatiegen.php" method="post">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" value="<?php echo $Menscharray['name'] ?>" required>

        <label for="Vorname">Vorname:</label>
        <input type="text" id="Vorname" name="Vorname" value="<?php echo $Menscharray['name'] ?>" required>

        <label for="schule">Deine eigene schule:</label>
        <input type="text" id="schule" name="schule" value="<?php echo $schulname?>" required>

        <label for="email">E-mail:</label>
        <input type="email" id="email" name="email" value="<?php echo $Menscharray['email'] ?>" required>

        <label for="date">Geburztag:</label>
        <input type="date" id="geburztag" name="geburztag" value="<?php echo $Menscharray['gb_datum'] ?>" required>

        <div class="EinzeldAbholen">
                <input type="checkbox" id="agb" name="EinzeldAbholen" value="<?php echo $Menscharray['EinzeldAbholen'] ?>" required>
                <label for="EinzeldAbholen">Ich möchte meine karte selbst abholen!</a></label>
        </div>
        <br>
        <input type="submit" value="Daten Bestätiegen!">


    </form>

</div>
</body>


</html>