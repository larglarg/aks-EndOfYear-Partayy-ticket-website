<?php
$name = $_GET['name'];
$vorname = $_GET['vorname'];
$schule = $_GET['schule'];
$gb_datum = $_GET['gb_datum'];
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Ticketreservierung</title>
    <style>
    .container {
        max-width: 600px;
        margin: 0 auto;
        padding: 20px;
    }

    h1 {
        color: #63007F;
    }

    ul {
        margin-top: 10px;
    }

    li {
        margin-top: 5px;
    }

    button {
        margin-top: 20px;
        background-color: #63007F;
        color: #FFFFFF;
        border: none;
        padding: 10px 20px;
        cursor: pointer;
    }

    a {
        color: #63007F;
        display: inline-block;
        margin-top: 10px;
    }
    
    .instagram-icon {
        display: inline-block;
        width: 16px;
        height: 16px;
        background-image: url('./ig-logo-bw.png'); /* Pfad zum Instagram-Logo */
        background-size: cover;
        margin-right: 5px;
        vertical-align: middle;
    }
    </style>

</head>

<body>
    <div class="container">
        <h1>Hallo <?php echo $vorname; ?>,</h1>

        <p>Es wurde ein Ticket für Ihre E-Mail-Adresse reserviert. Bitte überprüfen Sie die folgenden Daten:</p>

        <ul>
            <li>Name: <?php echo $name; ?></li>
            <li>Vorname: <?php echo $vorname; ?></li>
            <li>Schule: <?php echo $schule; ?></li>
            <li>Geburtsdatum: <?php echo $gb_datum; ?></li>
        </ul>

        <p>Wenn die Daten korrekt sind, klicken Sie bitte auf den unten stehenden Button, um die Reservierung zu
            bestätigen:</p>

        <button type="button">Reservierung bestätigen</button>

        <p>Wenn die Daten nicht korrekt sind, klicken Sie hier, um sie anzupassen.</p>

        <p>Falls Sie die Reservierung stornieren wollen, drücken Sie bitte <a href="#">hier</a>.</p>

        <p>Bei Fragen oder Problemen schreiben Sie uns gerne bei <a href="https://www.instagram.com/aks.karlsruhe/"><span class="instagram-icon"></span>Instagram</a> oder schreiben Sie uns eine E-Mail.</p>

        <p>Vielen Dank!</p>
    </div>
</body>

</html>
