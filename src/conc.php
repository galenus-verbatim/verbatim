<?php
/**
 * Part of verbatim https://github.com/galenus-verbatim/verbatim
 * Copyright (c) 2021 Nathalie Rousseau
 * MIT License https://opensource.org/licenses/mit-license.php
 */
require_once(__DIR__ . "/Verbatim.php");

use Oeuvres\Kit\{Route, I18n, Http, Xml};
use GalenusVerbatim\Verbatim\{Verbatim};



function main()
{
    $q = Http::par('q');
    // sanitize for display
    $qprint = htmlspecialchars($q);
    if (!$q) {
        echo I18n::_('conc.noq');
        return;
    }
    $field = Http::par('f', 'lem', '/lem|orth/');
    $forms = Verbatim::forms($q, $field);
    $formids = array_keys($forms);

    if (!count($formids)) {
        echo I18n::_('conc.nowords', $qprint);
        return;
    }
    echo '<div class="conc">'."\n";

    $qDoc =  Verbatim::$pdo->prepare("SELECT * FROM doc WHERE id = ?");
    $qed = Verbatim::$pdo->prepare("SELECT * FROM editio WHERE id = ?");

    $in  = str_repeat('?,', count($formids) - 1) . '?';
    $sql = "SELECT COUNT(*) FROM tok WHERE $field IN ($in)";
    $qCount =  Verbatim::$pdo->prepare($sql);
    $qCount->execute($formids);
    list($count) = $qCount->fetch();
    $mess = 'conc.lem';
    if ($field == 'orth') $mess = 'conc.orth';
    if (count($forms) > 1 ) $mess .= 's';
    echo "<header>\n";
    echo '<div class="occs">' . I18n::_('conc.search', $count, "<span title=" . json_encode($q) .">$q</span>");
    $first = true;
    echo ' (';
    // unify words
    $words = array_keys(array_flip($forms));
    $words = array_combine($words, $words);
    array_walk($words, function(&$value) {
        $value = Verbatim::deform($value);
        return $value;
    });
    asort($words);

    foreach ($words as $w => $deform) {
        if ($first) $first = false;
        else echo ', ';
        echo '<span title=' . json_encode($w) . '>' . $w . '</span>';
    }
    echo ')';
    echo '</div>' . "\n";
    echo "</header>\n";

    // order by needed, natural order is by the form search
    $sql = "SELECT * FROM tok WHERE $field IN ($in) ORDER BY id LIMIT 2000";
    $qTok =  Verbatim::$pdo->prepare($sql);
    $qTok->execute($formids);
    $lastDoc = -1;
    while ($tok = $qTok->fetch(PDO::FETCH_ASSOC)) {
        if ($tok['doc'] != $lastDoc) {
            $qDoc->execute(array($tok['doc']));
            $doc = $qDoc->fetch(PDO::FETCH_ASSOC);

            $qed->execute(array($doc['editio']));
            $editio = $qed->fetch(PDO::FETCH_ASSOC);
            if (Route::found()) $href = '%s?q=%s';
            else $href = "doc.php?cts=%s&amp;q=%s";
            echo '<h4 class="doc">'
                . '<a href="' . sprintf($href, $doc['clavis'], $qprint) . '">'
                . Verbatim::bibl($editio, $doc)
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