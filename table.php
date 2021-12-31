<?php
/**
 * Part of verbapy https://github.com/galenus-verbatim/verbapy
 * Copyright (c) 2021 Nathalie Rousseau
 * MIT License https://opensource.org/licenses/mit-license.php
 */
mb_internal_encoding("UTF-8");

include_once(dirname(__DIR__) . '/teinte/php/autoload.php');

use Oeuvres\Kit\{Sqlite,Xml};

$pars = include(__DIR__ . "/pars.php");
$pdo = Sqlite::open($pars['corpus.db']);
$conc = "conc.php?q=";

?>
<html>
    <head>
        <meta charset="utf-8"/>
        <title>Fréquences</title>
        <link rel="stylesheet" href="theme/verbatim.css"/>
    </head>
    <body>
        <header>
            <?php include(__DIR__ . "/tabs.php"); ?>
        </header>
        <main>
            <table>
                <thead>
                    <tr>
                        <th>Forme</th>
                        <th class="nb">Occurrences</th>
                        <th>Lemme</th>
                    </tr>
                </thead>
                <tbody>
            <?php

$sql = "SELECT orth, COUNT(orth) AS count FROM tok GROUP BY orth ORDER BY count DESC LIMIT 500";
$qFreqs = $pdo->prepare($sql);
$qForm = $pdo->prepare("
    SELECT orth.form AS orth, lem.form AS lem 
    FROM orth, lem 
    WHERE orth.id = ? AND orth.lem = lem.id
");

$qFreqs->execute();
$i = 100;
while ($freq = $qFreqs->fetch(PDO::FETCH_ASSOC)) {
    $orthId = $freq['orth'];
    $count = $freq['count'];
    $qForm->execute(array($orthId));
    $forms = $qForm->fetch(PDO::FETCH_ASSOC);
    echo "\n<tr>" 
    . "\n  <td><a href=\"" . $conc . $forms['orth'] . "\">" .  $forms['orth'] . "</a></td>"
    . "\n  <td class=\"nb\">" . number_format($count, 0, ',', ' ') . "</td>"
    . "\n  <td><a href=\"" . $conc . $forms['lem'] . "\">" . $forms['lem'] . "</a></td>"
    . "\n</tr>";
}


?>
                </tbody>
            </table>
        </main>
    </body>
</html>

