<?php
/**
 * Part of verbatim https://github.com/galenus-verbatim/verbatim
 * Copyright (c) 2021 Nathalie Rousseau
 * MIT License https://opensource.org/licenses/mit-license.php
 */
require_once(dirname(__DIR__) . "/Verbatim.php");

function main()
{
    echo '<div class="text">'."\n";
    $sql = "SELECT * FROM edition";
    $qEdition = Verbatim::$pdo->prepare($sql);
    $qEdition->execute(array());
    $sql = "SELECT * FROM doc WHERE edition = ? LIMIT 1";
    $qDoc = Verbatim::$pdo->prepare($sql);
    while ($edition = $qEdition->fetch(PDO::FETCH_ASSOC)) {
        $qDoc->execute(array($edition['id']));
        $doc = $qDoc->fetch(PDO::FETCH_ASSOC);
    
        echo '<div class="edition"><a href="'
        . $doc['clavis'] . '">'
        . Verbatim::edition($edition)
        . "</a></div>\n";
    }
    echo '</div>'."\n";
}
