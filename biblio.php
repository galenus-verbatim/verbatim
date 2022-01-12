<?php
/**
 * Part of verbatim https://github.com/galenus-verbatim/verbatim
 * Copyright (c) 2021 Nathalie Rousseau
 * MIT License https://opensource.org/licenses/mit-license.php
 */
include(__DIR__ . "/verbatim.php");


?>
<html>
    <head>
        <meta charset="utf-8"/>
        <title>Test moteur de recherche Sqlite</title>
        <link rel="stylesheet" href="theme/verbatim.css"/>
    </head>
    <body>
        <header>
            <?php include(__DIR__ . "/tabs.php"); ?>
        </header>
        <div id="page" class="container">
            <div class="doc">
                <p> </p>

<?php

/*
foreach(array('lem', 'orth') as $field) {
    $qForm = Verbatim::$pdo->prepare("SELECT id FROM $field WHERE form = ?");
    $qForm->execute(array($q));
    $res = $qForm->fetchAll();
    if (count($res)) break;
}
*/

$sql = "SELECT * FROM opus";
$qOpus = Verbatim::$pdo->prepare($sql);
$qOpus->execute(array());
$sql = "SELECT * FROM doc WHERE opus = ? LIMIT 1";
$qDoc = Verbatim::$pdo->prepare($sql);
while ($opus = $qOpus->fetch(PDO::FETCH_ASSOC)) {
    $qDoc->execute(array($opus['id']));
    $doc = $qDoc->fetch(PDO::FETCH_ASSOC);

    echo '<div class="opus"><a href="'
    . $doc['clavis'] . '">'
    . Verbatim::opus($opus)
    . "</a></div>\n";
}
?>
                <p> </p>
            </div>
        </div>
    </body>
</html>

