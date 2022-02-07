<?php
/**
 * Part of verbatim https://github.com/galenus-verbatim/verbatim
 * Copyright (c) 2021 Nathalie Rousseau
 * MIT License https://opensource.org/licenses/mit-license.php
 */
require_once(dirname(__DIR__) . "/Verbatim.php");

use Oeuvres\Kit\{I18n,Web};

function main() {
    $q = Web::par('q');
    $cts = Web::par('cts');
    // get document
    $sql = "SELECT * FROM doc WHERE clavis = ?";
    $qDoc = Verbatim::$pdo->prepare($sql);
    $qDoc->execute(array($cts));
    $doc = $qDoc->fetch(PDO::FETCH_ASSOC);
    if (!$doc) {
        http_response_code(404);
        echo I18n::_('doc.notfound', $cts);
        return;
    }
    $clavis = $doc['clavis'];
    $sql = "SELECT * FROM edition WHERE id = ?";
    $qEdition = Verbatim::$pdo->prepare($sql);
    $qEdition->execute(array($doc['edition']));
    $edition = $qEdition->fetch(PDO::FETCH_ASSOC);

    /*
    if (isset($doc['prev']) && $doc['prev']) {
        echo '<a href="' . sprintf($href, $doc['prev'], $q) . '">
            <div>⟨</div>
        <a>';
    }
    */
    /*
    if (isset($doc['next']) && $doc['next']) {
        echo '<a href="' . sprintf($href, $doc['next'], $q) . '">
            <div>⟩</div>
        </a>';
    }
    */
    $forms = array();
    if ($q) {
        $field = Web::par('f', 'lem', '/lem|orth/');
        $forms = Verbatim::forms($q, $field);
    }
    $formids = array_keys($forms);

    echo '
<div class="reader">
<div class="toc">';
    // no nav
    if (!isset($edition['nav']) || ! $edition['nav']) {
    }
    // no word searched
    else if (!count($formids)) {
        echo $edition['nav'];
    }
    // calculate occurrences by chapter
    else {
        $in  = str_repeat('?,', count($formids) - 1) . '?';
        $sql = "SELECT COUNT(*) FROM tok, doc WHERE $field IN ($in) AND doc = doc.id AND clavis = ?";
        $qTok =  Verbatim::$pdo->prepare($sql);
        $params = $formids;
        $i = count($params);
        // occurrences by chapter ?
        $html = preg_replace_callback(
            array(
                '@<a href="([^"]+)">([^<]+)</a>@',
            ),
            function ($matches) use ($clavis, $q, $qTok, $params, $i){
                $params[$i] = $matches[1];
                $qTok->execute($params);
                list($count) = $qTok->fetch();
                $ret = '';
                $ret .= '<a';
                if ($matches[1] == $clavis) {
                    $ret .= ' class="selected"';
                }
                $ret .= ' href="' . $matches[1] . '?q=' . $q . '"';
                $ret .= '>';
                $ret .= $matches[2];
                if ($count) {
                    $ret .= ' <small>(' . $count . ' o.)</small>';
                }
                $ret .= '</a>';
                return $ret;
            },
            $edition['nav']
        );
        echo $html;
    }
    echo '
</div>';

echo '
<div class="doc">';
    echo '<h1 class="title">' . Verbatim::bibl($edition, $doc, $q) . "</h1>\n";
    echo '
                    <div class="text">';

    $html = $doc['html'];
    // if a word to find, get lem_id or orth_id
    // A word to hilite
    if (count($forms)) {
        $in  = str_repeat('?,', count($formids) - 1) . '?';
        $sql = "SELECT * FROM tok WHERE $field IN ($in) AND doc = {$doc['id']}";
        $qTok =  Verbatim::$pdo->prepare($sql);
        $qTok->execute($formids);
        $start = 0;
        while ($tok = $qTok->fetch(PDO::FETCH_ASSOC)) {
            echo mb_substr($html, $start, $tok['charde'] - $start);
            echo "<mark>";
            echo mb_substr($html, $tok['charde'], $tok['charad'] - $tok['charde']);
            echo "</mark>";
            $start = $tok['charad'];
        }
        echo mb_substr($html, $start, mb_strlen($html) - $start);
    }
    else {
        echo $html;
    }
    echo '
    </div>
</div>';
}
?>

