<?php
/**
 * Part of verbatim https://github.com/galenus-verbatim/verbatim
 * Copyright (c) 2021 Nathalie Rousseau
 * MIT License https://opensource.org/licenses/mit-license.php
 */
require_once(dirname(__DIR__) . "/Verbatim.php");

use Oeuvres\Kit\{I18n};

function main() {
    $href = 'conc?q=%s&amp;f=%s';
    echo '
    <table class="freqs">
        <thead>
            <tr>
                <th>' . I18n::_('Form') . '</th>
                <th class="nb">' . I18n::_('Occs') . '</th>
                <th>' . I18n::_('Lem') . '</th>
            </tr>
        </thead>
        <tbody>';
    $sql = "SELECT orth, COUNT(orth) AS count FROM tok GROUP BY orth ORDER BY count DESC LIMIT 500";
    $qFreqs = Verbatim::$pdo->prepare($sql);
    $qForm = Verbatim::$pdo->prepare("
        SELECT orth.form AS orth, lem.form AS lem
            , orth.deform AS deorth, lem.deform AS delem 
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
        // bug not found
        if (!isset($forms['orth'])) {
            continue;
        }
        $forms['orth'] = htmlspecialchars($forms['orth']);
        $forms['lem'] = htmlspecialchars($forms['lem']);
        echo "\n<tr>"
        . "\n  <td class=\"orth\">"
        . "<a href=\"" 
        . sprintf($href, $forms['orth'], 'orth') 
        . "\">" .  $forms['orth'] 
        // . "   " . $forms['deorth']
        . "</a>"
        . "</td>"
        . "\n  <td class=\"nb\">" . number_format($count, 0, ',', ' ') . "</td>"
        . "\n  <td class=\"lem\">"
        . "<a href=\"" 
        . sprintf($href, $forms['lem'], 'lem') 
        . "\">" . $forms['lem'] 
        // . "  " . $forms['delem']
        . "</a>"
        . "</td>"
        . "\n</tr>";
    }
    echo '
        </tbody>
    </table>';
};
?>
