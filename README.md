# aks-EndOfYear-Partayy-ticket-website
SCHAU CODE JO SCHAU EINFACH CODE 

install


-> alle datein die geliefert werden in den ordner 
-> PHP version: 8.2.4 
-> unter includes/ php mailer ordner einbauen
->url in code anpassen
-> mailAuth.php mit 
#<?php
$mailusername = '';
$mailpassword = '';
$smtpHost = '';
$smtpPort = '';
#?>
-> hashSeed.php
#<?php
$hashseed = "t2l0xIbIwyRTsqyER9m4";
/*
            Generierung hash
$length = 20;
$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
$charactersLength = strlen($characters);
$randomString = '';
for ($i = 0; $i < $length; $i++) {
    $randomString .= $characters[random_int(0, $charactersLength - 1)];
}
echo $randomString;
*/

#?>

Einmal unauskomentiert laufen lassen 
-> sqlAuth.php

#<?php
$servername = "localhost";
$username = "root";
$password = "";
$sqlPort = "";
#?>


create.sql ausführen auf db server 
