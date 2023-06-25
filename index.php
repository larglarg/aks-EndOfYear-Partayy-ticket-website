<!DOCTYPE html>
<html lang="de">

<head>
  <meta charset="UTF-8">
  <title>Reservierung</title>
  <link rel="stylesheet" type="text/css" href="style.css">
</head>

<?php
include 'sqlAuth.php';
$max_ausleihe = 5;
#git ls-files | %{ Get-Content -Path $_ } | measure
try {
  $conn = new PDO("mysql:host=$servername;dbname=aks-EndOfYear-Partayy-tickets", $username, $password);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  // echo "Connected successfully <br>";
} catch (PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
}

$sql = "SELECT COUNT(*) AS free FROM main WHERE status = 'frei';";
$result = $conn->query($sql);

if ($result->rowCount() > 0) {
  // Ausgabe der Buchungen in einer Tabelle
  while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $count_free = $row["free"];
  }
} else {
  echo "error!!!!!!!";
}

if ($count_free >= 1) {
  if ($count_free >= 5) {

  } else {
    $max_ausleihe = $count_free;
  }
  ?>

  <body>
    <div class="container">
      <h1>Ticket-Reservierung</h1>
      <h3>Es sind noch
        <?php echo $count_free ?> Plätze frei
      </h3>
      <form action="reservierenneu.php" method="post">

        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>

        <label for="vorname">Vorname:</label>
        <input type="text" id="vorname" name="vorname" required>

        <label for="schule">Deine eigene schule:</label>
        <input type="text" id="schule" name="schule" required>

        <label for="email">E-mail:</label>
        <input type="email" id="email" name="email" required>


        <label for="tickets">Anzahl der Tickets (1-
          <?php echo $max_ausleihe ?>):
        </label>
        <input type="number" id="tickets" name="tickets" min="1" max="<?php echo $max_ausleihe ?>" required>

        <label for="gb_datum">Geburztag:</label>
        <input type="date" id="gb_datum" name="gb_datum" min="2007-01-01" max="2011-05-01" required>


        <div class="agb">
          <input type="checkbox" id="agb" name="agb" required>
          <label for="agb">Ich stimme den AGB zu. <a href="agb.html" target="_blank">AGB anzeigen</a></label>
        </div>

        <div class="einwilligung">
          <input type="checkbox" id="einwilligung" name="einwilligung" required>
          <label for="einwilligung">Ich stimme zu, dass Bild- und Tonaufnahmen gemacht werden und auf den Websites
            des STJA und den Social Media Kanälen des AKS veröffentlicht werden dürfen, solange mindestens 10
            weitere Personen darauf zu erkennen sind.</label>
        </div>
        <div class="EinzeldAbholen">
          <input type="checkbox" id="EinzeldAbholen" name="EinzeldAbholen" required>
          <label for="EinzeldAbholen">Jeder soll seine karten selbst abholen.</label>
        </div>

        <input type="submit" value="Reservieren">
      </form>
    </div>
  </body>

  </html>

  <!-- geposted wird: anzahl der tickets: tickets; Ob dem AGB zugestimmt worden ist: agb; ob dem bild unt ton aufnamen zugestimmt habe: einwiligung;  -->

  <?php
} elseif ($count_free == 0) {
  echo "Es sind grade keine Karten vorhanden. Es gibt aber noch NUMMER Karten, die bestellt aber noch nicht abgeholt worden sind. Du kannst es also später nochmal probieren.";
} else {
}
$conn = null;
?>