<?php
/**
 * Part of verbatim https://github.com/galenus-verbatim/verbatim
 * Copyright (c) 2021 Nathalie Rousseau
 * MIT License https://opensource.org/licenses/mit-license.php
 */
include(dirname(__DIR__) . "/verbatim.php");

use Oeuvres\Kit\{I18n};

function main() {
    $q = null;
    if (isset($_REQUEST['q'])) $q = trim($_REQUEST['q']);
    $cts = null;
    if (isset($_REQUEST['cts'])) $cts = trim($_REQUEST['cts']);
        // get document
    $sql = "SELECT * FROM doc WHERE clavis = ?";
    $qDoc = Verbatim::$pdo->prepare($sql);
    $qDoc->execute(array($cts));
    $doc = $qDoc->fetch(PDO::FETCH_ASSOC);
    if (!$doc) {
        http_response_code(404);
        echo I18n::_('doc.notfound', $cts);
    }
    else {
        $sql = "SELECT * FROM opus WHERE id = ?";
        $qOpus = Verbatim::$pdo->prepare($sql);
        $qOpus->execute(array($doc['opus']));
        $opus = $qOpus->fetch(PDO::FETCH_ASSOC);

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

        echo '
                <div class="reader">
                    <div class="toc">';
        if (isset($opus['nav']) && $opus['nav']) {


            echo preg_replace(
                array(
                    '@<li>(\s*.*?href="'. $doc['clavis'] .'")@',
                    '@href="[^"]+@',
                ),
                array(
                    '<li class="selected">$1',
                    '$0?q=' . $q,
                ),
                $opus['nav']
            );
        }
        echo '
                    </div>';

    echo '
                    <div class="doc">';
        echo '<h1 class="title">' . Verbatim::bibl($opus, $doc, $q) . "</h1>\n";
        echo '
                        <div class="text">';

        $html = $doc['html'];
        // if a word to find, get lem_id or orth_id
        $form = array();
        if ($q) {
            foreach(array('lem', 'orth') as $field) {
                $qForm = Verbatim::$pdo->prepare("SELECT id FROM $field WHERE form = ?");
                $qForm->execute(array($q));
                $form = $qForm->fetchAll();
                if (count($form)) break;
            }
        }
        // A word to hilite
        if (count($form)) {
            $formId = $form[0][0];
            $qTok =  Verbatim::$pdo->prepare("SELECT * FROM tok WHERE $field = ? AND doc = {$doc['id']}");
            $qTok->execute(array($formId));
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
    }
    echo '
                        </div>
                    </div>
                </div>
    ';
}
?>

