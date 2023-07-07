<?php
  function BegleitungForm($i)
  {
    ?>
    <label for="name<?php echo $i ?>">Name:</label>
    <input type="text" id="name<?php echo $i ?>" name="name<?php echo $i ?>" required>

    <label for="vorname<?php echo $i ?>">Vorname:</label>
    <input type="text" id="vorname<?php echo $i ?>" name="vorname<?php echo $i ?>" required>

    <label for="schule<?php echo $i ?>">Schule:</label>
    <input type="text" id="schule<?php echo $i ?>" name="schule<?php echo $i ?>" required>

    <label for="email<?php echo $i ?>">E-mail:</label>
    <input type="email" id="email<?php echo $i ?>" name="email<?php echo $i ?>" required>

    <label for="gb_datum<?php echo $i ?>">Geburztag:</label>
    <input type="date" id="gb_datum<?php echo $i ?>" min="2007-01-01" max="2011-05-01" name="gb_datum<?php echo $i ?>"
      required>

    <?php

  }









$personHash = $_GET['personhash'];
$bestellungsHash = $_GET['bestellungsHash'];
include 'statics.php';
try {
    $conn = new PDO("mysql:host=$servername;dbname=aks-EndOfYear-Partayy-tickets", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connected successfully <br>";
  } catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
  }

$stmt = $conn->prepare("SELECT id FROM menschen WHERE hash = :personHash;");
$stmt->bindParam(':personHash', $personHash, PDO::PARAM_STR);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$menschID = $row['id'];
$stmt = $conn->prepare("SELECT besteller_id, gast1_id, gast2_id, gast3_id, gast4_id, besteller_storniert, gast1_storniert, gast2_storniert, gast3_storniert, gast4_storniert FROM bestellung Where hash = :bestellungsHash;");
$stmt->bindParam(':bestellungsHash', $bestellungsHash, PDO::PARAM_STR);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
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

    $stmt = $conn->prepare("SELECT * FROM menschen WHERE hash = :personHash;");
    $stmt->bindParam(':personHash', $personHash, PDO::PARAM_STR);
    $stmt->execute();
    $Menscharray = $stmt->fetch(PDO::FETCH_ASSOC);
    $stmt = $conn->prepare("SELECT name, id FROM schulen WHERE id = :schul_id; ");
    $stmt->bindParam(':schul_id',$Menscharray['schule_id'], PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
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
        Hey, bitte Überprüfe bitte einmal deine daten:
    </h1>

    <form action="besteatiegen.php" method="post">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" value="<?php echo $Menscharray['name'] ?>" required>

        <label for="Vorname">Vorname:</label>
        <input type="text" id="vorname" name="vorname" value="<?php echo $Menscharray['vorname'] ?>" required>

        <label for="schule">Deine eigene schule:</label>
        <input type="text" id="schule" name="schule" value="<?php echo $schulname?>" required>


        <input type="hidden" id="personHash" name="personHash" value="<?php echo $personHash ?>">
        <input type="hidden" id="bestellungsHash" name="bestellungsHash" value="<?php echo $bestellungsHash ?>">
        <label for="date">Geburztag:</label>
        <input type="date" id="gb_datum" name="gb_datum" value="<?php echo $Menscharray['gb_datum'] ?>" required>




    <?php

    $stmt = $conn->prepare("SELECT hash, Anzahl_tickets FROM bestellung WHERE hash = :hash;");
    $stmt->bindParam(':hash',$bestellungsHash, PDO::PARAM_STR);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $number_of_tickets = $row['Anzahl_tickets'];
 
    $stmt = $conn->prepare("SELECT besteller_id, hash FROM bestellung WHERE hash = :hash");
    $stmt->bindParam(':hash', $bestellungsHash, PDO::PARAM_STR);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $istBestller = false;
    if($row['besteller_id'] == $menschID){
        $istBestller = true;
    }

    if($number_of_tickets != 1 && $istBestller){



   
    ?>
    
      <h1>Infos für die weiteren Tickets</h1>
      <p>
      <h3>Jede person muss sein eigene Mail bestätigen und den AGBs zustimmen.</h3>
  
  
      </p>
        <?php
        for ($i = 1; $i < $number_of_tickets; $i++) {
          switch ($i) {
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





    }
        ?>
    
    
    <br>
        <input type="submit" value="Daten Bestätiegen!">





    </form>

</div>
</body>


</html>