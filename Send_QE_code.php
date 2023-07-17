<?php

$QRpath = $_GET['path'];
$bestellungsHash = $_GET['bestellungsHash'];

// Additional text to be included in the email
$additionalText = "Bitte beachten Sie, dass Sie mit dem QR-Code innerhalb der nächsten 5 Tage die Karten im Anne Frank Haus in Karlsruhe abholen und bezahlen können. Vergessen Sie nicht, den unterschriebenen Mutterzettel mitzubringen.";


?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title></title>
    <style>
    <?php
    include 'style.css';
    ?>
    </style>

</head>

<body>

    <div class="container">
        <div class="content">
            <div class="inner-content">
                <div class="header skew">
                    <h1>AKS EndOfYear-Partayy</h1>
                    <h3>Reservierung</h3>
                </div>

                <img src="<?php echo $QRpath; ?>" alt="QR-Code" style="width:auto;">

                <!-- Email content -->
                <div class="email-content">
                    <p>Liebe/r Teilnehmer/in,</p>
                    <p>vielen Dank für Ihre Reservierung für die AKS EndOfYear-Partayy!</p>

                    <p>Bitte beachten Sie, dass alle Tickets am Tag der Party verkauft und bezahlt werden müssen.</p>

                    <p>Wir benötigen von jedem Teilnehmer den ausgefüllten und unterschriebenen Muttizettel. 
                        Ohne diesen können wir dich leider nicht an der Party teilhaben lassen.
                    </p>

                    <p>Vielen Dank, und wir freuen uns auf Ihre Teilnahme!</p>

                    <p>Mit freundlichen Grüßen,<br>
                        Ihr AKS Team</p>
                </div>


            </div>
        </div>
        <?php
        include 'footer.php';
        ?>
    </div>
</body>

</html>
