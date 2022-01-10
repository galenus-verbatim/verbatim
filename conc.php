<?php
/**
 * Part of verbatim https://github.com/galenus-verbatim/verbatim
 * Copyright (c) 2021 Nathalie Rousseau
 * MIT License https://opensource.org/licenses/mit-license.php
 */
include(__DIR__ . "/verbatim.php");

use Oeuvres\Kit\{Route, Xml};


$q = null;
if (isset($_REQUEST['q'])) $q = trim($_REQUEST['q']);

?>
<html>
    <head>
        <meta charset="utf-8"/>
        <title>Test moteur de recherche Sqlite</title>
        <link rel="stylesheet" href="theme/verbatim.css"/>
    </head>
    <body>
        <header>
            <?php include(__DIR__ . "/tabs.php"); ?>
        </header>
        <div class="container" id="page">
            <form>
                <input name="q" value="<?= htmlspecialchars($q) ?>"/>
                <button type="submit">Go</button>
            </form>
            <div class="reader">
                <div class="conc">

<?php


foreach(array('lem', 'orth') as $field) {
    $qForm = Verbatim::$pdo->prepare("SELECT id FROM $field WHERE form = ?");
    $qForm->execute(array($q));
    $res = $qForm->fetchAll();
    if (count($res)) break;
}

if (!$q);
else if (!count($res)) {
    echo "$q, pas de documents trouvés.";
}
else {
    $qDoc =  Verbatim::$pdo->prepare("SELECT * FROM doc WHERE id = ?");
    $qOpus = Verbatim::$pdo->prepare("SELECT * FROM opus WHERE id = ?");

    $formId = $res[0][0];
    $qTok =  Verbatim::$pdo->prepare("SELECT * FROM tok WHERE $field = ? LIMIT 10000");
    $qTok->execute(array($formId));
    $lastDoc = -1;
    $html;
    while ($tok = $qTok->fetch(PDO::FETCH_ASSOC)) {
        if ($tok['doc'] != $lastDoc) {
            $qDoc->execute(array($tok['doc']));
            $doc = $qDoc->fetch(PDO::FETCH_ASSOC);

            $qOpus->execute(array($doc['opus']));
            $opus = $qOpus->fetch(PDO::FETCH_ASSOC);
            if (Route::$routed) $href = '%s?q=%s';
            else $href = "doc.php?cts=%s&amp;q=%s";
            echo '<h4 class="doc">'
             . '<a href="' . sprintf($href, $doc['identifier'], $q) . '">'
             . Verbatim::bibl($opus, $doc)
             . "</a>"
             . "</h4>\n";


            $lastDoc = $tok['doc'];



            $html = Xml::detag($doc['html']);
        }
        $start = $tok['offset'] - 50;
        if ($start < 0) $start = 0;
        echo "<div><span class=\"kwicl\">";
        echo mb_substr($html, $start, $tok['offset'] - $start);
        echo "</span>";
        echo "<mark>" . mb_substr($html, $tok['offset'], $tok['length']) . "</mark>";
        $len = 50;
        echo mb_substr($html, $tok['offset']+$tok['length'], $len);
        echo "</div>\n";
    }

}
?>
                </div>
            </div>
        </div>
    </body>
</html>

