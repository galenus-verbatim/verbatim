<?php
/**
 * Part of verbatim https://github.com/galenus-verbatim/verbatim
 * Copyright (c) 2021 Nathalie Rousseau
 * MIT License https://opensource.org/licenses/mit-license.php
 */
include(dirname(__DIR__) . "/verbatim.php");

function main()
{
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
    
}
