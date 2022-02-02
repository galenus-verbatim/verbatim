<?php
declare(strict_types=1);

require_once(__DIR__ . "/php/autoload.php");

use Oeuvres\Kit\{Route, I18n};

$q = "";
if (isset($_REQUEST['q'])) {
    $q = filter_var(trim($_REQUEST['q']), FILTER_SANITIZE_STRING);
}


?><!doctype html>
<html>
    <head>
        <meta charset="utf-8"/>
        <title><?= Route::title('Verbatim') ?></title>
        <link rel="stylesheet" href="theme/verbatim.css"/>
    </head>
    <body>
        <header>
            <img id="banner" />
            <nav class="tabs">
                <a href="." title="Présentation"><strong>Verbatim, en dev</strong></a>
                <a href="biblio" title="Bibliographie" class="tab">Bibliographie</a>
                <a href="table" title="Fréquences par mots" class="tab">Table</a>
                <a href="conc<?= (isset($q) && $q)?"?q=$q":'' ?>" title="Recherche de mot" class="tab">Concordance</a>
                <!--
                <a href="doc.jsp" title="Lire un texte" class="tab">Liseuse</a>
                -->
            </nav>
        </header>
        <div id="page" class="container">
            <?php Route::main(); ?>
        </div>
    </body>
</html>
