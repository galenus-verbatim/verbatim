<?php
/**
 * Part of verbatim https://github.com/galenus-verbatim/verbatim
 * Copyright (c) 2021 Nathalie Rousseau
 * MIT License https://opensource.org/licenses/mit-license.php
 */
require_once(dirname(__DIR__) . "/Verbatim.php");

use Oeuvres\Kit\{Route, I18n, Web, Xml};



function main()
{
    $q = Web::par('q');
    // sanitize for display
    $qprint = htmlspecialchars($q);
    if (!$q) {
        echo I18n::_('conc.noq');
        return;
    }
    // transliterate latin letters

    $trans = include(__DIR__ . '/lat_grc.php');
    $q = strtr($q, $trans);
    $words = preg_split("@[\s,]+@", trim($q));
    $in  = str_repeat('?,', count($words) - 1) . '?';

    $field = Web::par('f', 'lem', '/lem|orth/');


    $forms = array();
    foreach(array(
        "SELECT id, form FROM $field WHERE form IN ($in)",
        "SELECT id, form FROM $field WHERE deform IN ($in)",
    ) as $sql) {
        $qForm = Verbatim::$pdo->prepare($sql);
        $qForm->execute($words);
        while ($row = $qForm->fetch(PDO::FETCH_NUM)) {
            $forms[$row[0]] = $row[1];
        }
    }
    $formids = array_keys($forms);

    if (!count($formids)) {
        echo I18n::_('conc.nowords', $qprint);
        return;
    }
    echo '<div class="conc">'."\n";

    $qDoc =  Verbatim::$pdo->prepare("SELECT * FROM doc WHERE id = ?");
    $qEdition = Verbatim::$pdo->prepare("SELECT * FROM edition WHERE id = ?");

    $in  = str_repeat('?,', count($formids) - 1) . '?';
    $sql = "SELECT COUNT(*) FROM tok WHERE $field IN ($in)";
    $qCount =  Verbatim::$pdo->prepare($sql);
    $qCount->execute($formids);
    list($count) = $qCount->fetch();
    $mess = 'conc.lem';
    if ($field == 'orth') $mess = 'conc.orth';
    if (count($forms) > 1 ) $mess .= 's';
    echo "<header>\n";
    echo '<div class="occs">' . I18n::_($mess, $count, implode(', ', $forms)) . '</div>' . "\n";
    echo "</header>\n";


    $sql = "SELECT * FROM tok WHERE $field IN ($in) ORDER BY id ";
    $qTok =  Verbatim::$pdo->prepare($sql);
    $qTok->execute($formids);
    $lastDoc = -1;
    while ($tok = $qTok->fetch(PDO::FETCH_ASSOC)) {
        if ($tok['doc'] != $lastDoc) {
            $qDoc->execute(array($tok['doc']));
            $doc = $qDoc->fetch(PDO::FETCH_ASSOC);

            $qEdition->execute(array($doc['edition']));
            $edition = $qEdition->fetch(PDO::FETCH_ASSOC);
            if (Route::$routed) $href = '%s?q=%s';
            else $href = "doc.php?cts=%s&amp;q=%s";
            echo '<h4 class="doc">'
                . '<a href="' . sprintf($href, $doc['clavis'], $qprint) . '">'
                . Verbatim::bibl($edition, $doc)
                . "</a>"
                . "</h4>\n"
            ;
            $lastDoc = $tok['doc'];
            $html = Xml::detag($doc['html']);
        }
        $start = $tok['charde'] - 50;
        if ($start < 0) $start = 0;
        echo "<div><span class=\"kwicl\">";
        echo mb_substr($html, $start, $tok['charde'] - $start);
        echo "</span>";
        echo "<mark>" . mb_substr($html, $tok['charde'], $tok['charad'] - $tok['charde']) . "</mark>";
        $len = 50;
        echo mb_substr($html, $tok['charad'], $len);
        echo "</div>\n";
    }
    echo "</div>\n";

}
?>