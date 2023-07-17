<?php
$personHash = $_GET['personHash'];
$bestellungsHash = $_GET['bestellungsHash'];#
include 'statics.php'
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title></title>
    <style>
    <?php include 'style.css';
    ?>
    </style>
</head>

<body>

    <div class="container" >
        <div class="content">
            <div class="inner-content">
                <div class="header skew">
                    <h1>AKS EndOfYear-Partayy</h1>
                    <h3>Ticketreservierung</h3>
                </div>
                <p>Es wurde ein Ticket für deine E-Mail-Adresse reserviert.</p>
                <form action="<?php echo $URL; ?>besteatigen.php" method="post">
                    <input type="hidden" name="personhash" value="<?php echo $personHash; ?>">
                    <input type="hidden" name="bestellungsHash" value="<?php echo $bestellungsHash; ?>">
                    <input
                        style="justify-content: center; display: flex; align-items: center; margin-top: 20px; background-color: #63007F; color: #FFFFFF; border: none; padding: 10px 20px; cursor: pointer;"
                        type="submit" value="Bestätigen">
                    <p>hier kommt test <a
                            href="<?php echo $URL; ?>profData.php?personhash=<?php echo urlencode($personHash); ?>&bestellungsHash=<?php echo urlencode($bestellungsHash); ?>"
                            style="color: blue; text-decoration: underline; font-size: 1em; font-family: serif; margin-left: 5px; padding: 0px 0px;">hier</a>.
                    </p>


                </form>
                <form action="<?php echo $URL; ?>storno.php" method="post">
                    <input type="hidden" name="personhash" value="<?php echo $personHash; ?>">
                    <input type="hidden" name="bestellungsHash" value="<?php echo $bestellungsHash; ?>">
                    <p>Falls Sie die Reservierung stornieren wollen, drücken Sie bitte <a
                            href="<?php echo $URL; ?>storno.php?personhash=<?php echo urlencode($personHash); ?>&bestellungsHash=<?php echo urlencode($bestellungsHash); ?>"
                            style="color: blue; text-decoration: underline; font-size: 1em; font-family: serif; margin-left: 5px; padding: 0px 0px;">hier</a>.
                    </p>


                </form>
                <p>Bei Fragen oder Problemen schreib uns doch gerne bei <a
                        href="https://www.instagram.com/aks.karlsruhe/" style="color: #63007F;"><span
                            style="display: inline-block; width: 16px; height: 16px; background-image: url('<?php echo $URL; ?>ig-logo-bw.png'); background-size: cover; margin-right: 5px; vertical-align: middle;"></span>Instagram</a>
                    oder per E-Mail.</p>
                <p>Vielen Dank!</p>
            </div>
        </div>
    </div>
</body>

</html>