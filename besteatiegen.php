<?php


function alleHabenBesteatigt($conn, $reservierung_id){


  $stmt = $conn->prepare("SELECT id from main where status = 'reserviert' AND reservierung_id = :reservierung_id;");
  $stmt->bindParam(':reservierung_id', $reservierung_id, PDO::PARAM_INT);
  $stmt->execute();
  if($stmt->rowCount() != 0){
    return false;
  }else{
    return true;
  }

}
function newMensch($conn, $mensch, $schulname, $bestellungsHash, $hashseed, $i){

$problemStatus = $mensch->problemMitInfos($conn);

if($problemStatus == 3){
    echo "problemstatus ist 3";
    if($mensch->userExestiertKomplett($conn)){   
        echo "user exestiert kommplet und wird geswitcht";
        $mensch->SwitchIDtoexistig($conn);
        
    }else{
        echo "ganz komischer fehler meld dich ebim andmit team";
        exit();
    }
}
if ($problemStatus != 0) {
    if ($mensch->activeOrder($conn, $problemStatus)) {
        $stmt = $conn->prepare("SELECT id, hash FROM bestellung WHERE hash = :hash");
        $stmt->bindParam(':hash', $bestellungsHash, PDO::PARAM_STR);
        $stmt->execute();
        $bestellungsID = $stmt->fetch(PDO::FETCH_ASSOC)['id'];
        echo "Es läuft bereitz eine Bestellung mit diesen infos und einer Bestätiegten e-mail addresse für die mail ".$mensch->getMail;
        $stmt = $conn->prepare("SELECT id, status, mensch_id, reservierung_id FROM main WHERE reserverung_id = :reserverung_id AND mensch_id = NULL AND  status = 'reserverit;'");
        $stmt->bindParam(':reservierung_id', $bestellungsID, PDO::PARAM_INT);
        $stmt->execute();
        
        foreach($stmt->fetch(PDO::FETCH_ASSOC) as $row){
            $idtoFREE = $row['id'];
        }
        $stmt = $conn->prepare("UPDATE main SET status = 'frei' WHERE id = :id ");
        $stmt->bindParam(':id', $idtoFREE, PDO::PARAM_INT);
        return;


    }

    }
    $mensch->setResverierungIDviaHash($conn, $bestellungsHash);
    $mensch->generateHash($hashseed);
    $mensch->writeMenschInDB($conn);
    $mensch->writeIDInMainDB($conn);
    $mensch->idInBestellung($conn, $i);
    $mensch->setSchulIdByName($conn, $schulname);
    #hier nächstes mal wieter 
    #das könnte helfen https://stackoverflow.com/questions/20842208/run-php-script-without-output-to-browser
    
  #  $zielUrl = './sendmail.php';
  #  $zielUrlMitParametern = $zielUrl . '?personHash=' . urlencode($mensch->getHash()) . '&bestellungsHash=' . urlencode($bestellungsHash) . '&whitchEmail=' . urlencode(1);
  #  header('Location: ' . $zielUrlMitParametern);
  #sendern besteatigungsmail an gast.
  $file = 'http://localhost/aks-EndOfYear-Partayy-ticket-website/aks-EndOfYear-Partayy-ticket-website/sendmail.php';

  $params = [
      'personHash' => $mensch->getHash(),
      'bestellungsHash' => $bestellungsHash,
  ];
  $url = $file . '?' . http_build_query($params);
  
  $message = file_get_contents($url);
  echo $message;

  
}




include 'hashSeed.php';
include 'sqlAuth.php';
$personHash = $_POST['personHash'];
$bestellungsHash = $_POST['bestellungsHash'];
include 'Mensch.php';
$params = array(
    'vorname' => $_POST['vorname'],
    'name' => $_POST['name'],
    'gb_datum' => $_POST['gb_datum'],
    'email' => "",
);
$schule = $_POST['schule'];
$istBestller = 0;
try {
    $conn = new PDO("mysql:host=$servername;dbname=aks-EndOfYear-Partayy-tickets", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connected successfully <br>";
  } catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
  }


$stmt = $conn->prepare("SELECT email, hash FROM menschen WHERE hash = :hash");
$stmt->bindParam(':hash', $personHash, PDO::PARAM_STR);
$stmt->execute();
$params['email'] = $stmt->fetch(PDO::FETCH_ASSOC)['email'];

$besteatieger = new Mensch($params);


$besteatieger->setSchulIdByName($conn, $schule);

$besteatieger->updateMenschviaHash($conn, $personHash);
$besteatieger->setIDviaHash($conn, $personHash);
$besteatieger->SetHashFromDB($conn);





$stmt = $conn->prepare("SELECT id, Anzahl_tickets, besteller_id, hash from bestellung WHERE hash = :hash");
$stmt->bindParam(':hash', $bestellungsHash, PDO::PARAM_STR);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$reservierung_id = $row['id'];
$number_of_tickets = $row['Anzahl_tickets'];
if($row['besteller_id'] == $besteatieger->getId()){
    $istBestller = true;
}
$besteatieger->besteatigInMain($conn, $reservierung_id);
$besteatieger->verifieMailinDB($conn);
if(alleHabenBesteatigt($conn, $reservierung_id)){
  $stmt = $conn->prepare("UPDATE bestellung set status = 'besteatigt' WHERE id = :reservierung_id");
  $stmt->bindParam(':reservierung_id', $reservierung_id, PDO::PARAM_INT);
  $stmt->execute();

  #senden mail für agb etc. 
  $file = 'http://localhost/aks-EndOfYear-Partayy-ticket-website/aks-EndOfYear-Partayy-ticket-website/sendmail.php';


  $url = $file . '?' . http_build_query($params);
  
  $message = file_get_contents($url);
  #senden abhol qr code
  $file = 'http://localhost/aks-EndOfYear-Partayy-ticket-website/aks-EndOfYear-Partayy-ticket-website/sendmail.php';


  $url = $file . '?' . http_build_query($params);
  
  $message = file_get_contents($url);

}


?>
Falls eine passende reservierung gefunden worden ist wurde diese bestätigt.
Sobald alle karten besteatigt sind oder stoniert sind die zu dieser bestellung gehören werden die karten verschickt.

<?php


if($number_of_tickets != 1 && $istBestller){

    for ($i = 1; $i < $number_of_tickets; $i++) {
        switch ($i) {
          case 1:
            $params = array(
                'vorname' => $_POST['vorname1'],
                'name' => $_POST['name1'],
                'gb_datum' => $_POST['gb_datum1'],
                'email' => $_POST['email1'],
            );
                $gast1 = new Mensch($params);
                newMensch($conn, $gast1, $_POST['schule1'], $bestellungsHash, $hashseed, $i);
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