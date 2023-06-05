<?php
    // Retrieve the passed variables from the URL parameters
    $name = $_GET['name'];
    $vorname = $_GET['vorname'];
    $schule = $_GET['schule'];
    $gb_datum = $_GET['gb_datum'];
    $email = $_GET['email'];
    $bestellungsId = $_GET['bestellungs_id'];
    $whitchEmail = $_GET['whitchEmail'];
    $menschId;
    $message;
    $servername = "localhost";
    $username = "root";
    $password = ""; 
    try {
        $conn = new PDO("mysql:host=$servername;dbname=aks-EndOfYear-Partayy-tickets", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      } catch(PDOException $e) {
        echo "1";
        exit();
        #echo "Connection failed: " . $e->getMessage();
      }
      $sql = "SELECT id, email From menschen Where email = '".$email."'";
      $result = $conn->query($sql);
      if($result->rowCount() != 1 ){
        echo "1";
        exit();
      }
      $row = $result->fetch(PDO::FETCH_ASSOC);
      $menschId = $row['id'];
      $sql = "SELECT besteller_id, gast1_id, gast2_id, gast3_id, gast4_id, status FROM bestellung WHERE id = ".$bestellungsId." AND besteller_id = '".$menschId."' OR gast1_id = '".$menschId."' OR gast2_id = '".$menschId."' OR gast3_id = '".$menschId."' OR gast4_id = '".$menschId."';";
      $result = $conn->query($sql);
      if($result->rowCount() != 1 ){
        echo "1";
        exit();
      }
      echo "JETZT DÜRFTE DIE MAIL GESENDET WERDEN XD";
      $to = $email;
      switch($whitchEmail){
        case 1:
            $params = array(
                'name' => $name,
                'vorname' => $vorname,
                'schule' => $schule,
                'gb_datum' => $gb_datum,
              );
            #bestätigungsmail für agb und bildrechte und das die karten bestellet werden dürfen.
            $subject = "Reservierung für die AKS EndOfYreaar Partaay Tickets";
            $getmesage = 'http://localhost/aks-EndOfYear-Partayy-ticket-website/ReservierungfürdieAKSEndOfYreaarPartaayTicket.php?' . http_build_query($params);
            
            $message = file_get_contents($getmesage);
            echo $message;
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
     
?>