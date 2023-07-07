 <?php


 include 'Mensch.php';
 include 'statics.php';

  $params = array(
    'name' => 'name',
    'Vorname' => 'Vorname',
    'gb_datum' => 'gb_datum',
    'email' => 'email',
  );
  $besteller = new Mensch($params);
  $besteller->setSchul_id(1);
  $besteller->generateHash("t2l0xIbIwyRTsqyER9m4");

 try {
    $conn = new PDO("mysql:host=$servername;dbname=aks-EndOfYear-Partayy-tickets", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  } catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
  }
  $besteller->writeInDB($conn);
  $firstname = $params['vorname'];
  $lastname = $params['name'];
  $email = $params['email'];


  $stmt = $conn->prepare("INSERT INTO MyGuests (firstname, lastname, email)
  VALUES (:firstname, :lastname, :email)");
  $stmt->bindParam(':firstname', $firstname);
  $stmt->bindParam(':lastname', $lastname);
  $stmt->bindParam(':email', $email);
  

  $stmt->execute();
  $row = $stmt->fetch(PDO::FETCH_ASSOC);

?>