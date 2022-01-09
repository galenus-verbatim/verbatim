<?php
/**
 * Part of verbatim https://github.com/galenus-verbatim/verbatim
 * Copyright (c) 2021 Nathalie Rousseau
 * MIT License https://opensource.org/licenses/mit-license.php
 */
include(__DIR__ . "/verbatim.php");

$q = null;
if (isset($_REQUEST['q'])) $q = trim($_REQUEST['q']);
$cts = null;
if (isset($_REQUEST['cts'])) $cts = trim($_REQUEST['cts']);

use Oeuvres\Kit\Route;

if (Route::$routed) $href = '%s?q=%s';
else $href = "doc.php?cts=%s&amp;q=%s";


?>
<!doctype html>
<html>
    <head>
        <meta charset="utf-8"/>
        <title>doc</title>
        <link rel="stylesheet" href="theme/verbatim.css"/>
    </head>
    <body>
        <header>
            <?php include(__DIR__ . "/tabs.php"); ?>
        </header>
        <main>
<?php
// get document
$sql = "SELECT * FROM doc WHERE identifier = ?";
$qDoc = Verbatim::$pdo->prepare($sql);
$qDoc->execute(array($cts));
$doc = $qDoc->fetch(PDO::FETCH_ASSOC);
if (!$doc) {
    http_response_code(404);
    echo "<p>Pas de document trouvé pour l’identifiant cts : \"$cts\"</p>\n";
}
else {
echo '
            <div class="reader">
                <div class="prev">';
if (isset($doc['prev']) && $doc['prev']) {
    echo '<a href="' . sprintf($href, $doc['prev'], $q) . '">
        <div>⟨</div>
    <a>';
}
echo '
                </div>
                <div class="doc">
                    ';

    echo "<h3>" . Verbatim::bibl($doc, $q) . "</h3>\n";
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
            echo mb_substr($html, $start, $tok['offset'] - $start);
            echo "<mark>";
            echo mb_substr($html, $tok['offset'], $tok['length']);
            echo "</mark>";
            $start = $tok['offset'] + $tok['length'];
        }
        echo mb_substr($html, $start, mb_strlen($html) - $start);
    }
    else {
        echo $html;
    }
}
echo '
                </div>
                <div class="next">
                    ';
if (isset($doc['next']) && $doc['next']) {
    echo '<a href="' . sprintf($href, $doc['next'], $q) . '">
        <div>⟩</div>
    </a>';
}
echo '
                </div>
            </div>
';
?>
        </main>
    </body>
</html>

