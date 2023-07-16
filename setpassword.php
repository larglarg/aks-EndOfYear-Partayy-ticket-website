<!DOCTYPE html>
<html lang="de">

<head>
  <meta charset="UTF-8">
  <title>Reservierung</title>
  <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<div class="container">
    <div class="content">
      <div class="inner-content">
<div class="header skew">
  <h1>AKS EndOfYear-Partayy</h1>
  <h3>Set Password</h3>
</div>
<?php

include "statics.php";
if($_POST['password'] != NULL){
    $localpassword = $_POST['password'];

}
try {
    $conn = new PDO("mysql:host=$servername;dbname=aks-EndOfYear-Partayy-tickets", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "1 nul";
    exit();
}
$stmt = $conn->prepare("SELECT PwHash FROM password");

$stmt->execute();
if($stmt->rowCount() == 0){
    if($localpassword != ""){
        $PwHash = hash("sha3-256", $localpassword . $hashSeedForPW);
        $stmt = $conn->prepare("INSERT INTO password (PwHash) VALUES (:PwHash);");
        $stmt->bindParam(':PwHash', $PwHash, PDO::PARAM_STR);
        $stmt->execute();
    }else{

            ?>


        <h1>Set Password</h1>
        <h3>Bitte gib das password ein</h3>

        <form action="setpassword.php" method="post">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            
            <input type="submit" value="Reservieren">
        </form>
        </div>
    <?php
    }


}else{
    ?>
    <h1>was willst du hier junge was ???</h1>
 </div>
    <?php
   

}


include 'footer.php';

?>
</body>