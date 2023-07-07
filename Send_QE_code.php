<?php

$QRpath = $_GET['path'];
$bestellungsHash = $_GET['bestellungsHash'];
$QRpath = "http://localhost/aks-EndOfYear-Partayy-ticket-website/aks-EndOfYear-Partayy-ticket-website/".substr($QRpath, 1);
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
    <link rel="icon" href="https://bestellungstest.larglarg.com/aks-EndOfYear-Partayy-ticket-website-test/images.png">
    <!-- Ersetze "https://example.com" durch die tatsächliche URL deines Servers -->
</head>

<body>
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h1 style="color: #63007F;">QR code Mail Wurde gesendet</h1>
        <img src="<?PHP echo $QRpath; ?>" alt="QR-Code" style="width:auto;">
    </div>
</body>

</html>