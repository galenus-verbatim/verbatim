<?php
/**
 * Part of verbatim https://github.com/galenus-verbatim/verbatim
 * Copyright (c) 2021 Nathalie Rousseau
 * MIT License https://opensource.org/licenses/mit-license.php
 */
require_once(dirname(__DIR__) . "/verbatim.php");

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
    $field = Web::par('f', 'lem', '/lem|orth/');
    $qForm = Verbatim::$pdo->prepare("SELECT id FROM $field WHERE form = ?");
    $qForm->execute(array($q));
    $res = $qForm->fetchAll();
    if (!count($res)) {
        echo I18n::_('conc.nowords', $qprint);
        return;
    }
    echo '<div class="conc">'."\n";

    $qDoc =  Verbatim::$pdo->prepare("SELECT * FROM doc WHERE id = ?");
    $qOpus = Verbatim::$pdo->prepare("SELECT * FROM opus WHERE id = ?");

    $formId = $res[0][0];
    $qTok =  Verbatim::$pdo->prepare("SELECT * FROM tok WHERE $field = ? LIMIT 10000");
    $qTok->execute(array($formId));
    $lastDoc = -1;
    while ($tok = $qTok->fetch(PDO::FETCH_ASSOC)) {
        if ($tok['doc'] != $lastDoc) {
            $qDoc->execute(array($tok['doc']));
            $doc = $qDoc->fetch(PDO::FETCH_ASSOC);

            $qOpus->execute(array($doc['opus']));
            $opus = $qOpus->fetch(PDO::FETCH_ASSOC);
            if (Route::$routed) $href = '%s?q=%s';
            else $href = "doc.php?cts=%s&amp;q=%s";
            echo '<h4 class="doc">'
                . '<a href="' . sprintf($href, $doc['clavis'], $qprint) . '">'
                . Verbatim::bibl($opus, $doc)
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