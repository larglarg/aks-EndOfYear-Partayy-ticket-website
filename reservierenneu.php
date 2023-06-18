<?php
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




include 'Mensch.php';
include 'sqlAuth.php';
include 'hashSeed.php';
$number_of_tickets = $_POST['tickets'];
$agb_check = $_POST['agb'];
$einwilligung_bild_ton = $_POST['einwilligung'];
$name = $_POST['name'];
$vorname = $_POST['vorname'];
$schule = $_POST['schule'];
$gb_datum = $_POST['gb_datum'];
$email = $_POST['email'];
$schul_id = 0;
$params = array(
    'vorname' => $_POST['vorname'],
    'name' => $_POST['name'],
    'schule' => $_POST['schule'],
    'gb_datum' => $_POST['gb_datum'],
    'email' => $_POST['email'],
);
$IdListMain = array(
    "besteller" => 0,
    "gast1" => 0,
    "gast2" => 0,
    "gast3" => 0,
    "gast4" => 0,
);

$besteller = new Mensch($params);

try {
    $conn = new PDO("mysql:host=$servername;dbname=aks-EndOfYear-Partayy-tickets", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
if ($agb_check == 0 || $einwilligung_bild_ton == 0) {
    echo "Es wurden nicht alle Hacken gesetzt!";
    exit();
}
#Schull stuff 
$stmt = $conn->prepare("SELECT id, name FROM schulen WHERE name = :schule;");
$stmt->bindParam(':schule', $schule);
$stmt->execute();
if ($stmt->rowCount() > 0) {
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $schul_id = $row["id"];
        break;
    }
} else {

    #wenn nein neue schule aufnehmen
    $stmt = $conn->prepare("INSERT INTO schulen (name) VALUES (:schule);");
    
    print_r($stmt->bindParam(':schule', $schule));
    $stmt->execute();
    print_r($stmt->fetch(PDO::FETCH_ASSOC));
    $stmt = $conn->prepare("SELECT id, name FROM schulen WHERE name = :schule;");
    $stmt->bindParam(':schule', $schule);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $schul_id = $row["id"];
}





$stmt = $conn->prepare("SELECT id, STATUS FROM main WHERE status = 'frei';");
$stmt->execute();
if ($stmt->rowCount() > 0) {
    $stmtUpdate = $conn->prepare("UPDATE main SET status = 'reserviert' WHERE id = :id;");
    for ($i = 0; $i <= $number_of_tickets; $i++) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $id = $row["id"];
        $stmtUpdate->bindParam(':id', $id);
        $stmtUpdate->execute();
        if ($i == 0) {
            $IdListMain['besteller'] = $id;
        }
        if ($i == 1) {
            $IdListMain['gast1'] = $id;
        }
        if ($i == 2) {
            $IdListMain['gast2'] = $id;
        }
        if ($i == 3) {
            $IdListMain['gast3'] = $id;
        }
        if ($i == 4) {
            $IdListMain['gast4'] = $id;
        }
    }
    #return legend 0-> exestiert nicht in DB 1-> Es exestiert nen user mit der gelichen Mail 2-> es name, vorname und gb date exestieren 3-> ganz passend 
    $problemStatus = $besteller->problemMitInfos($conn);
    if ($problemStatus == 0) {

    } else {
        if ($besteller->activeOrder($conn, $problemStatus)) {
            echo "Es läuft bereitz eine Bestellung mit diesen infos und einer Bestätiegten e-mail addresse";
            CleanEverything($conn, $IdListMain, $number_of_tickets);
            exit();
        }
    }



} else {
    echo "error!!!!!!!";
}


?>