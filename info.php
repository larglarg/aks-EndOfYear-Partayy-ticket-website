<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <title>INFOS</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>

    <div class="container">
        <div class="content">
            <div class="inner-content">
                <div class="header skew">
                    <h1>AKS EndOfYear-Partayy</h1>
                    <h3>26.07.2023 von 19:00 bis 23:00 Uhr</h3>
                </div>
            </div>


            <div class="infos">
                <h2>Veranstaltungsinformationen:</h2>
                <ul>
                    <li><strong>Wann:</strong> 26.07.2023 von 19:00 bis 23:00 Uhr</li>
                    <li><strong>Wo:</strong> NCO Club Karlsruhe</li>
                    <li><strong>Wer:</strong> Alle Schülerinnen und Schüler der 7. bis 9. Klasse aus Karlsruhe</li>
                    <li><strong>Eintritt:</strong> Reservierte Karten erforderlich</li>
                </ul>
                <?php
                $eventDate = strtotime('2023-07-17 18:00:00');
                $currentDate = time();
                if ($currentDate >= $eventDate) {
                    ?>
                    <h2>jetzt Reservieren:</h2>

                <a href="./reservieren/index.php" class="button-submit">Jetzt reservieren</a>

                
                <?php
                }
                ?>

                <h2>Getränke:</h2>
                <p>Jeder Besucher erhält zwei Freigetränke, danach kostet jedes weitere Getränk 1,5€.</p>

                <h2>Was muss ich mitbringen?</h2>
                <ul>
                    <li>Gute Laune</li>
                    <li>Schülerausweis</li>
                    <li>Karten / Karten Reservierung</li>
                    <li>Unterschriebene Elternzettel / AGBs</li>
                </ul>

                <h2>Tickets:</h2>
                <p>Die Tickets kosten 3€ pro Person.</p>
                <p>Um die Tickets zu reservieren, benötigen wir folgende Informationen von jedem Teilnehmer:</p>
                <ul>
                    <li>Name</li>
                    <li>Vorname</li>
                    <li>Geburtsdatum</li>
                    <li>Besuchte Schule</li>
                    <li>E-Mail-Adresse</li>
                </ul>
                <p>Die reservierten Tickets werden an der Abendkasse bezahlt.</p>
                <p>Jeder kann maximal 5 Tickets bestellen (sich selbst und 4 Begleitpersonen).</p>
                <p>Nach Bestätigung der Reservierung werden die unterschriebenen Elternzettel / AGBs an jede Person
                    per E-Mail zugeschickt.</p>

            </div>

        </div>
    </div>

    <?php include 'footer.php'; ?>

</body>

</html>
