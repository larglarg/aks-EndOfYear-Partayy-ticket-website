<!DOCTYPE html>
<html lang="de">

<head>
  <meta charset="UTF-8">
  <title>Reservierung</title>
  <link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
  <?php
  function CheckAll($agb_check, $einwilligung_bild_ton)
  {
    $flag = true;

    if ($agb_check != "on") {
      $flag = false;
      echo "agb false";
    }

    if ($einwilligung_bild_ton != "on") {
      $flag = false;
      echo "agbbbb false";
    }

    return $flag;
  }

  function CleanEverything($conn, $IdListMain, $number_of_tickets)
  {
    #echo "CleanEverything wird<br>";
    for ($i = 1; $i <= $number_of_tickets; $i++) {
      if ($i == 1) {
        $id = $IdListMain['besteller'];

      }
      if ($i == 2) {
        $id = $IdListMain['gast1'];

      }
      if ($i == 3) {
        $id = $IdListMain['gast2'];

      }
      if ($i == 4) {
        $id = $IdListMain['gast3'];

      }
      if ($i == 5) {
        $id = $IdListMain['gast1'];

      }
      $sql = "UPDATE main SET status = 'frei' WHERE id = " . $id . ";";
      $conn->query($sql);
    }
  }
  function TestIdForActivOrders($conn, $idBestellerMenschen, $IdListMain, $number_of_tickets, $isMail)
  {

    echo "TestIdForActivOrders wird<br>";
    $sql = "SELECT besteller_id, gast1_id, gast2_id, gast3_id, gast4_id, status FROM bestellung WHERE besteller_id = '" . $idBestellerMenschen . "' OR gast1_id = '" . $idBestellerMenschen . "' OR gast2_id = '" . $idBestellerMenschen . "' OR gast3_id = '" . $idBestellerMenschen . "' OR gast4_id = '" . $idBestellerMenschen . "';";
    $result = $conn->query($sql);
    $FirstTime = 1;
    $status = array("reserviert", "storno", "besteatigt");
    print_r($status);
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
      foreach ($status as $currentStatus) {
        echo "TestIdForActivOrders while loop<br>";
        $msg = "es fehlt der standart fehler ";
        if ($isMail == 1 && $i == 1) {
          $msg = "Errorcode 409 Mail already taken.";
          $FirstTime--;
        } else {
          $msg = "darf ich nicht sagen bruder xD";
          if ($row['status'] == $currentStatus) {
            CleanEverything($conn, $IdListMain, $number_of_tickets);
            echo $msg;
            exit();
          }
        }


      }
    }


  }
  function CheckExcistingBookings($conn, $IdListMain, $idBestellerMenschen, $number_of_tickets, $name, $vorname, $gb_datum, $email)
  {
    $isMail = 0;
    #test ob mail mit nicht abgebrochener bestellung verbunden
#echo "CheckExcistingBookings wird<br>";
    $sql = "SELECT id, email FROM menschen WHERE email = '" . $email . " AND ';";
    $result = $conn->query($sql);
    if ($result->rowCount() != 0) {
      $isMail = 1;
      while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $idBestellerMenschen = $row['id'];
        TestIdForActivOrders($conn, $idBestellerMenschen, $IdListMain, $number_of_tickets, $isMail);
      }
    }
    #testen ob mensch sonst exestiert -> besttellungstest und TODO->doppelte mesnchen
  

    $sql = "SELECT id, name, vorname, gb_datum FROM menschen WHERE name = '" . $name . "' AND vorname = '" . $vorname . "' AND gb_datum = '" . $gb_datum . "'";
    $result = $conn->query($sql);
    if ($result->rowCount() != 0) {
      while ($row = $result->fetch(PDO::FETCH_ASSOC)) {

        TestIdForActivOrders($conn, $IdListMain['besteller'], $IdListMain, $number_of_tickets, $isMail);
      }
    }
  }

  function BegleitungForm($i)
  {
    ?>
    <label for="name<?php echo $i ?>">Name:</label>
    <input type="text" id="name<?php echo $i ?>" name="name<?php echo $i ?>" required>

    <label for="Vorname<?php echo $i ?>">Vorname:</label>
    <input type="text" id="Vorname<?php echo $i ?>" name="Vorname<?php echo $i ?>" required>

    <label for="schule<?php echo $i ?>">Schule:</label>
    <input type="text" id="schule<?php echo $i ?>" name="schule<?php echo $i ?>" required>

    <label for="email<?php echo $i ?>">E-mail:</label>
    <input type="email" id="email<?php echo $i ?>" name="email<?php echo $i ?>" required>

    <label for="start<?php echo $i ?>">Geburztag:</label>
    <input type="date" id="geburztag<?php echo $i ?>" min="2007-01-01" max="2011-05-01" name="geburztag<?php echo $i ?>"
      required>

    <?php

  }
  function nichtStonier($conn, $number_of_tickets, $bestellungsId)
  {
    $sql = "UPDATE bestellung SET besteller_stoniert = false, ";
    if ($number_of_tickets > 1) {
      $sql = $sql . "gast1_stoniert = false";
    }
    if ($number_of_tickets > 2) {
      $sql = $sql . ", gast1_stoniert = false";
    }
    if ($number_of_tickets > 3) {
      $sql = $sql . ", gast1_stoniert = false";
    }
    if ($number_of_tickets > 4) {
      $sql = $sql . ", gast1_stoniert = false";
    }
    $sql = $sql . ";";
  }
  include 'Mensch.php';
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
  $params = array(
    $name = $_POST['name'],
    $vorname = $_POST['Vorname'],
    $schule = $_POST['schule'],
    $gb_datum = $_POST['geburztag'],
    $email = $_POST['email'],
  );
  $besteller = new Mensch($params);


  try {
    $conn = new PDO("mysql:host=$servername;dbname=aks-EndOfYear-Partayy-tickets", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  } catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
  }

  if (CheckAll($agb_check, $einwilligung_bild_ton)) {


    $i = 0;

    #bekomen von freien ids von main 
  
    $sql = "SELECT id, STATUS FROM main WHERE status = 'frei';";
    $result = $conn->query($sql);
    if ($result->rowCount() > 0) {
      while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $i = $i + 1;
        $id = $row["id"];
        $sql = "UPDATE main SET status = 'reserviert' WHERE id = " . $id . ";";
        $conn->query($sql);
        if ($i == 1) {
          $IdListMain['besteller'] = $id;
        }
        if ($i == 2) {
          $IdListMain['gast1'] = $id;
        }
        if ($i == 3) {
          $IdListMain['gast2'] = $id;
        }
        if ($i == 4) {
          $IdListMain['gast3'] = $id;
        }
        if ($i == 5) {
          $IdListMain['gast4'] = $id;
        }
        if ($i == $number_of_tickets) {
          break;
        }

      }

    } else {
      echo "error!!!!!!!";
    }
    if($besteller->problemMitInfos($conn)){
      if($besteller->doseUserExist()){

      }


    }
    
    $besteller->activeOrder($conn);
    CheckExcistingBookings($conn, $IdListMain, $idBestellerMenschen, $number_of_tickets, $name, $vorname, $gb_datum, $email);

    #exestiert schule ? wenn ja get id
  
    $sql = "SELECT id, name FROM schulen WHERE name = '" . $schule . "';";
    $result = $conn->query($sql);
    if ($result->rowCount() > 0) {
      while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $schul_id = $row["id"];
        break;
      }
    } else {

      #wenn nein neue schule aufnehmen
      $sql = "INSERT INTO schulen (name) VALUES ('" . $schule . "');";
      $conn->query($sql);

      $sql = "SELECT id, name FROM schulen WHERE name = '" . $schule . "';";
      $result = $conn->query($sql);
      $row = $result->fetch(PDO::FETCH_ASSOC);
      $schul_id = $row["id"];
    }
    $besteller->setHash(hash('sha3-512', $name . $vorname . $schule . $gb_datum . $email . $idBestellerMenschen . $hashseed, false));


    #reservierer mit abgebochenen bestellungen -> nicht neu anlegen 
    $sql = "SELECT id, name, vorname, gb_datum, hash FROM menschen WHERE name = '" . $name . "' AND vorname = '" . $vorname . "' AND gb_datum = '" . $gb_datum . "';";
    $result = $conn->query($sql);
    if ($result->rowCount() > 0) {
      $row = $result->fetch(PDO::FETCH_ASSOC);
      $idBestellerMenschen = $row["id"];
      $personHash = $row["hash"];

    } else {
      
     #TODO Somthing is wrong here I can feel IT 
      $sql2 = "SELECT id, email, hash FROM bestellung WHERE email = '" . $email . "'";
      $result2 = $conn->query($sql);
      if ($result2->rowCount() > 0) {
        $row = $result2->fetch(PDO::FETCH_ASSOC);
        $idBestellerMenschen = $row["id"];
        $personHash = $row["hash"];

      } else {
        #erstellen von menschen in db
        $besteller->writeInDB($conn);
      }
    }



    $sql = "UPDATE main SET mensch_id = " . $idBestellerMenschen . " WHERE id = " . $IdListMain['besteller'] . ";";
    $conn->query($sql);

    #erstellen von bestellung in db
    $sql = "INSERT INTO bestellung (Anzahl_tickets, besteller_id, status) VALUES ('" . $number_of_tickets . "', '" . $idBestellerMenschen . "','reserviert');";
    $conn->query($sql);
    $sql = "SELECT id,besteller_id, status FROM bestellung WHERE besteller_id = " . $idBestellerMenschen . " AND status = 'reserviert';";
    $result = $conn->query($sql);
    $row = $result->fetch(PDO::FETCH_ASSOC);
    $bestellungsId = $row['id'];
  } else {
    echo "Es wurden nicht alle Hacken gesetzt!";
    exit();
  }
  nichtStonier($conn, $number_of_tickets, $bestellungsId);
  $params = array(
    'id' => $id,
    'personHash' => $personHash,
  );

  $getmesage = '/ReservierungfürdieAKSEndOfYreaarPartaayTicket.php?' . http_build_query($params);

  if ($number_of_tickets == 1) {

    exit();
  }

  ?>
  <div class="container">
    <h1>Infos für die weiteren Tickets</h1>
    <p>
    <h3>Jeder person muss sein eigene Mail bestätigen und den AGBs zustimmen.</h3>
    Dennoch können alle Tickets von Dir abgeholt und bezahlt werden.


    </p>
    <form action=".php" method="post">
      <div class="EinzeldAbholen">
        <input type="checkbox" id="EinzeldAbholen" name="EinzeldAbholen" required>
        <label for="EinzeldAbholen">Alle tickets sollen einzelnd abgeholt werden.</label>
      </div>
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