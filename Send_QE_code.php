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

                <img src="<?PHP echo $QRpath; ?>" alt="QR-Code" style="width:auto;">

                <!-- Display the additional text below the QR code -->
                <p><?php echo $additionalText; ?></p>

            </div>
        </div>
        <?php
        include 'footer.php';
        ?>
    </div>
</body>

</html>
