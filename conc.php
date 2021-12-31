<?php
/**
 * Part of verbapy https://github.com/galenus-verbatim/verbapy
 * Copyright (c) 2021 Nathalie Rousseau
 * MIT License https://opensource.org/licenses/mit-license.php
 */

include_once(dirname(__DIR__) . '/teinte/php/autoload.php');

use Oeuvres\Kit\{Sqlite,Xml};

$pars = include(__DIR__ . "/pars.php");
$pdo = Sqlite::open($pars['corpus.db']);

mb_internal_encoding("UTF-8");

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
        <main>
            <form>
                <input name="q" value="<?= htmlspecialchars($q) ?>"/>
                <button type="submit">Go</button>
            </form>
<?php

foreach(array('lem', 'orth') as $field) {
    $qForm = $pdo->prepare("SELECT id FROM $field WHERE form = ?");
    $qForm->execute(array($q));
    $res = $qForm->fetchAll();
    if (count($res)) break;
}

if (!$q);
else if (!count($res)) {
    echo "$q, pas de documents trouvés.";
}
else {
    $qDoc =  $pdo->prepare("SELECT * FROM doc WHERE id = ?");

    $formId = $res[0][0];
    $qTok =  $pdo->prepare("SELECT * FROM tok WHERE $field = ? LIMIT 10000");
    $qTok->execute(array($formId));
    $lastDoc = -1;
    $html;
    while ($tok = $qTok->fetch(PDO::FETCH_ASSOC)) {
        if ($tok['doc'] != $lastDoc) {
            $qDoc->execute(array($tok['doc']));
            $doc = $qDoc->fetch(PDO::FETCH_ASSOC);
            $lastDoc = $tok['doc'];
            $html = Xml::detag($doc['html']);
            echo "<h4 class=\"kwict\">" . $doc['title'] . "</h4>";
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
        </main>
    </body>
</html>

