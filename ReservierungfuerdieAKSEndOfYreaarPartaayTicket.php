<?php
$personHash = $_GET['personHash'];
$bestellungsHash = $_GET['bestellungsHash'];#
include 'statics.php'
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

    label {
        display: block;
        margin-top: 10px;
    }

    input[type="number"] {
        width: 100%;
        padding: 5px;
        margin-top: 5px;
        border: 1px solid #E0E0E0;
    }

    .agb,
    .EinzeldAbholen,
    .einwilligung {
        margin-top: 10px;
    }

    input[type="checkbox"] {
        margin-right: 5px;
    }

    input[type="submit"] {
        margin-top: 20px;
        background-color: #63007F;
        color: #FFFFFF;
        border: none;
        padding: 10px 20px;
        cursor: pointer;
    }

    a {
        color: #63007F;
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

    .button-submit {
        justify-content: center;
        display: flex;
        align-items: center;
        margin-top: 20px;
        background-color: #63007F;
        color: #FFFFFF;
        border: none;
        padding: 10px 20px;
        cursor: pointer;
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
    <link rel="icon" href="<?php echo $URL; ?>images.png">
    <!-- Ersetze "https://example.com" durch die tatsächliche URL deines Servers -->
</head>

<body>
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h1 style="color: #63007F;">Hallo,</h1>

        <p>Es wurde ein Ticket für deine E-Mail-Adresse reserviert.</p>
        <form action="<?php echo $URL; ?>besteatigen.php" method="post">
            <input type="hidden" name="personhash" value="<?php echo $personHash; ?>">
            <input type="hidden" name="bestellungsHash" value="<?php echo $bestellungsHash; ?>">
            <input
                style="justify-content: center; display: flex; align-items: center; margin-top: 20px; background-color: #63007F; color: #FFFFFF; border: none; padding: 10px 20px; cursor: pointer;"
                type="submit" value="Bestätigen">
                <p>hier kommt test <a href="<?php echo $URL; ?>profData.php?personhash=<?php echo urlencode($personHash); ?>&bestellungsHash=<?php echo urlencode($bestellungsHash); ?>" style="color: blue; text-decoration: underline; font-size: 1em; font-family: serif; margin-left: 5px; padding: 0px 0px;">hier</a>.</p>


        </form>
        <form action="<?php echo $URL; ?>storno.php" method="post">
            <input type="hidden" name="personhash" value="<?php echo $personHash; ?>">
            <input type="hidden" name="bestellungsHash" value="<?php echo $bestellungsHash; ?>">
            <p>Falls Sie die Reservierung stornieren wollen, drücken Sie bitte <a href="<?php echo $URL; ?>storno.php?personhash=<?php echo urlencode($personHash); ?>&bestellungsHash=<?php echo urlencode($bestellungsHash); ?>" style="color: blue; text-decoration: underline; font-size: 1em; font-family: serif; margin-left: 5px; padding: 0px 0px;">hier</a>.</p>


        </form>
        <p>Bei Fragen oder Problemen schreib uns doch gerne bei <a href="https://www.instagram.com/aks.karlsruhe/"
                style="color: #63007F;"><span
                    style="display: inline-block; width: 16px; height: 16px; background-image: url('<?php echo $URL; ?>ig-logo-bw.png'); background-size: cover; margin-right: 5px; vertical-align: middle;"></span>Instagram</a>
            oder per E-Mail.</p>
        <p>Vielen Dank!</p>
    </div>
</body>

</html>