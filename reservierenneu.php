<?php
function CleanEverything($conn, $IdListMain, $number_of_tickets, $reservierung_id)
{
    $stmt = $conn->prepare("UPDATE main SET status = 'frei', reservierung_id = NULL WHERE reservierung_id = :reservierung_id;");
    $stmt->bindParam(':reservierung_id', $reservierung_id, PDO::PARAM_INT);
    $stmt->execute();
}

function wannerstelltToChar($input /*2023-06-25 08:50:05*/)
{
    $input = strrev($input);
    $datetime = explode(" ", $input);
    $time = explode(":", $datetime[0]);
    $date = explode("-", $datetime[1]);
    return implode($time) . implode($date);
}
function setBestellungsHashSingel($conn, $besteller, $reservierung_id)
{
    $stmt = $conn->prepare("SELECT id, wann_erstellt FROM bestellung WHERE id = :reservierung_id");
    $stmt->bindParam(':reservierung_id', $reservierung_id, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $wannerstelltchar = wannerstelltToChar($row['wann_erstellt']);
    $Bestlleungs_hash = hash('sha3-512', $besteller->getHash() . $wannerstelltchar, false);
    $stmt = $conn->prepare("UPDATE bestellung set hash = :bestellungs_hash WHERE id = :reservierung_id");
    $stmt->bindParam(':bestellungs_hash', $Bestlleungs_hash, PDO::PARAM_STR);
    $stmt->bindParam(':reservierung_id', $reservierung_id, PDO::PARAM_INT);
    $stmt->execute();
    return $Bestlleungs_hash;
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
    'gb_datum' => $_POST['gb_datum'],
    'email' => $_POST['email'],
);
$IdListMain = array(
    'besteller' => 0,
    'gast1' => 0,
    "gast2" => 0,
    "gast3" => 0,
    "gast4" => 0,
);
$reservierung_id = 0;

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
#create bestellung.

$stmt = $conn->prepare("INSERT INTO bestellung (Anzahl_tickets, status) VALUES (:anzahl_tickets, 'reserviert');");
$stmt->bindParam(':anzahl_tickets', $number_of_tickets, PDO::PARAM_INT);
$stmt->execute();
$reservierung_id = $conn->lastInsertId();
$besteller->setResverierungID($reservierung_id);
#Bekommen und reservieren von den tickets

$stmt = $conn->prepare("UPDATE main
                        SET status = 'reserviert',
                            reservierung_id = :reservierung_id
                        WHERE status = 'frei'
                        LIMIT :number_of_tickets;");
$stmt->bindParam(':reservierung_id', $reservierung_id, PDO::PARAM_INT);
$stmt->bindParam(':number_of_tickets', $number_of_tickets, PDO::PARAM_INT);
$stmt->execute();
$CountReservierterTickets = $stmt->rowCount();
# bekommen der id der tickets aus MAIN
$stmt = $conn->prepare("SELECT id FROM main
                        WHERE status = 'reserviert'
                        AND reservierung_id = :reservierung_id;");
$stmt->bindParam(':reservierung_id', $reservierung_id, PDO::PARAM_INT);
$stmt->execute();

$i = 0;
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $row) {
    $i++;
    switch ($i) {
        case 1:
            $IdListMain['besteller'] = $row;
        case 2:
            $IdListMain['gast1'] = $row;
        case 3:
            $IdListMain['gast2'] = $row;
        case 4:
            $IdListMain['gast3'] = $row;
        case 5:
            $IdListMain['gast4'] = $row;

    }

}
$problemStatus = $besteller->problemMitInfos($conn);
if ($problemStatus == 3) {
    echo "problemstatus ist 3";
    if ($besteller->userExestiertKomplett($conn)) {
        echo "user exestiert kommplet und wird geswitcht";
        $besteller->SwitchIDtoexistig($conn);

    } else {
        echo "ganz komischer fehler meld dich ebim andmit team";
        exit();
    }
}
if ($problemStatus != 0) {
    if ($besteller->activeOrder($conn, $problemStatus)) {
        echo "Es läuft bereitz eine Bestellung mit diesen infos und einer Bestätiegten e-mail addresse";
        CleanEverything($conn, $IdListMain, $number_of_tickets, $reservierung_id);
        exit();
    }
}

if ($CountReservierterTickets == $number_of_tickets) {

    echo "genug tickets stehen bereit";
    if ($besteller->getId() == 0) {
        $besteller->generateHash($NumberOfBytes);
        $besteller->writeMenschInDB($conn);
    } else {
        $besteller->SetHashFromDB($conn);
    }
    $besteller->writeIDInMainDB($conn);
    $besteller->idInBestellung($conn, 0);
    $besteller->setSchulIdByName($conn, $schule);
} else {
    #entweder kein retun oder zu wenige tickets zu bekomen. 

    echo "es tut uns leid es konnten leider nur x tickets reserviert werden, da leider nicht mehr verfügbar sind. Wollen sie die reservierung trozdem weiter führen? bitte bedenken sie, das wenn sie nein drücken ihr anrecht auf die bis jetzt reservierten plätze verfallen!";
}
$bestellungsHash = bin2hex(random_bytes($NumberOfBytes));
$stmt = $conn->prepare("UPDATE bestellung set hash = :hash WHERE id = :id");
$stmt->bindParam(':hash', $bestellungsHash, PDO::PARAM_STR);
$stmt->bindParam(':id', $reservierung_id,   PDO::PARAM_INT);
$stmt->execute();

$zielUrl = './sendmail.php';
$zielUrlMitParametern = $zielUrl . '?personHash=' . urlencode($besteller->getHash()) . '&bestellungsHash=' . urlencode($bestellungsHash);
header('Location: ' . $zielUrlMitParametern);
exit();
?>