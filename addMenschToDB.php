
<?php
/*
$params = array(
    'id' => $id,
    'personHash' => $personHash,
    'bestellungsHash' => $bestellungsHash,
);

$getmesage = '/ReservierungfürdieAKSEndOfYreaarPartaayTicket.php?' . http_build_query($params);
*/

$id = $_GET['id'];

include 'statics.php';


try {
    $conn = new PDO("mysql:host=$servername;dbname=aks-EndOfYear-Partayy-tickets", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "1 nul";
    exit();
}
# wenn es nur eine mail gibt wird der hash mit $name.$vorname.$schule.$gb_datum.$email.$row['wann_erstellt'] erstellt unter verwendung von sha3-512
$sql = "SELECT id, wann_erstellt, status From bestellung Where id = " . $id;
$result = $conn->query($sql);
$row = $result->fetch(PDO::FETCH_ASSOC);
#convert classic date to just a nummber
#TODO -> In function verschieben. 
$firstsplit = array();
$firstsplit = explode(" ", $row['wann_erstellt']);
$dateAsNumberString = "";
$wannErstelleHash = "";
foreach (explode("-", $firstsplit[0]) as $i) {
    $dateAsNumberString = $dateAsNumberString . $i;
}
foreach (explode(":", $firstsplit[1]) as $i) {
    $dateAsNumberString = $dateAsNumberString . $i;
}

$Nummberarray = array();
$Nummberarray = str_split($dateAsNumberString);
foreach ($Nummberarray as $i) {
    $i = chr($i);
    $wannErstelleHash = $wannErstelleHash . $i;
}

$bestellungsHash = bin2hex(random_bytes($NumberOfBytes));
$sql = "UPDATE bestellung SET hash = '" . $bestellungsHash . "' WHERE besteller_id = " . $idBestellerMenschen . ";";
$conn->query($sql);
#weiterleiten auf mail seite
$sql = "SELECT id, besteller_id, status From bestellung Where besteller_id = " . $idBestellerMenschen . " AND status = 'reserviert'";
$result = $conn->query($sql);
if ($result->rowCount() == 1) {
    $row = $result->fetch(PDO::FETCH_ASSOC);
    $bestellungsId = $row["id"];
} else {
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
$sendmailURL = $URL.'sendmail.php?' . http_build_query($params); // Mach nen Funktion Call draus kein API CALL
$response = file_get_contents($sendmailURL);
echo $response;
?>