<?php
/**
 * Part of verbatim https://github.com/galenus-verbatim/verbatim
 * Copyright (c) 2021 Nathalie Rousseau
 * MIT License https://opensource.org/licenses/mit-license.php
 */
include(dirname(__DIR__) . "/verbatim.php");

function main() {
    $href = 'conc?q=';
    echo '
    <table class="freqs">
        <thead>
            <tr>
                <th>Forme</th>
                <th class="nb">Occurrences</th>
                <th>Lemme</th>
            </tr>
        </thead>
        <tbody>';
    $sql = "SELECT orth, COUNT(orth) AS count FROM tok GROUP BY orth ORDER BY count DESC LIMIT 500";
    $qFreqs = Verbatim::$pdo->prepare($sql);
    $qForm = Verbatim::$pdo->prepare("
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
        . "\n  <td class=\"orth\"><a href=\"" . $href . $forms['orth'] . "\">" .  $forms['orth'] . "</a></td>"
        . "\n  <td class=\"nb\">" . number_format($count, 0, ',', ' ') . "</td>"
        . "\n  <td class=\"lem\"><a href=\"" . $href . $forms['lem'] . "\">" . $forms['lem'] . "</a></td>"
        . "\n</tr>";
    }
    echo '
        </tbody>
    </table>';
};
?>
