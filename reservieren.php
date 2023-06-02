<!DOCTYPE html>
<html lang="de">

<head>
  <meta charset="UTF-8">
  <title>Reservierung</title>
  <link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
<?php
#<!-- geposted wird: anzahl der tickets: tickets; Ob dem AGB zugestimmt worden ist: agb; ob dem bild unt ton aufnamen zugestimmt habe: einwiligung;  -->
$number_of_tickets = $_POST['tickets'];
$agb_check = $_POST['agb'];
$einwilligung_bild_ton = $_POST['einwilligung'];
echo $number_of_tickets.$agb_check.$einwilligung_bild_ton;
?>

<div class="popup-container" id="popupContainer" style="display: none;">
      <div class="popup">
        <span class="close-btn" onclick="closePopup()">X</span>
        <h2>Willkommen im Popup!</h2>
        <p>Dies ist ein Beispiel für ein Popup-Fenster.</p>
      </div>
    </div>

    <script>
      // Funktion zum Öffnen des Popups
      function openPopup() {
        document.getElementById("popupContainer").style.display = "flex";
      }

      // Funktion zum Schließen des Popups
      function closePopup() {
        document.getElementById("popupContainer").style.display = "none";
      }
    </script>
  </div>
  </body>

</html>