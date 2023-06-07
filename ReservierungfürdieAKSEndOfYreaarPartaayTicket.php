<?php




$name = $_GET['name'];
$vorname = $_GET['vorname'];
$schule = $_GET['schule'];
$gb_datum = $_GET['gb_datum'];
$email = $_GET['email'];


function infos(){
    echo $name.$vorname.$schule.$gb_datum;
    return;
}
$currentDateTime = date("Y-m-d h:i:sa");;

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
        background-image: url('./ig-logo-bw.png');
        /* Pfad zum Instagram-Logo */
        background-size: cover;
        margin-right: 5px;
        vertical-align: middle;
    }

    .link-button {
        background: none;
        border: none;
        color: blue;
        text-decoration: underline;
        cursor: pointer;
        font-size: 1em;
        font-family: serif;
        margin-left: 5px;
        margin-left: 5px;
        padding: 0px 0px;
    }

    .link-button:focus {
        outline: none;
    }

    .link-button:active {
        color: red;
    }
    </style>

</head>

<body>
    <div class="container">
        <h1>Hallo <?php echo $vorname;?>,</h1>

        <p>Es wurde ein Ticket für Ihre E-Mail-Adresse reserviert. Bitte überprüfen Sie die folgenden Daten:</p>
        <form action="http://localhost/aks-EndOfYear-Partayy-ticket-website/besteatigen.php" method="post">
            <ul>
                <li>Name: <?php echo $name; ?></li>
                <li>Vorname: <?php echo $vorname; ?></li>
                <li>Schule: <?php echo $schule; ?></li>
                <li>Geburtsdatum: <?php echo $gb_datum; ?></li>
            </ul>
            <p>Zum bestätiegen oder bearbeiten der daten, sowie dem bestätiegen der reservierung auf weiter klicken.</p>


            <?php echo infos(); ?>
            <button type="button" type="submit">Weiter</button>
        </form>
        <form action="http://localhost/aks-EndOfYear-Partayy-ticket-website/storno.php" method="post">

            <input type="hidden" name="date" value="<?php echo $currentDateTime; ?>">
            <input type="hidden" name="email" value="<?php echo $email; ?>">
            
            <p>Falls Sie die Reservierung stornieren wollen, drücken Sie bitte <button  type="submit" class="link-button">hier</button>. <br></p>
        </form>

        <p>Bei Fragen oder Problemen schreiben Sie uns gerne bei <a href="https://www.instagram.com/aks.karlsruhe/"><span class="instagram-icon"></span>Instagram</a> oder
            schreiben Sie uns eine E-Mail.</p>

        <p>Vielen Dank!</p>

    </div>
</body>

</html>