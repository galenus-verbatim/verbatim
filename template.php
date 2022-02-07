<?php
declare(strict_types=1);

require_once(__DIR__ . "/Verbatim.php");

use Oeuvres\Kit\{Route, I18n};


$page = Route::$url_parts[0];
$start = 'tlg';
if (@substr_compare($page, $start, 0, strlen($start))==0) {
    $page = 'tlg';
}

?><!doctype html>
<html>
    <head>
        <meta charset="utf-8"/>
        <title><?= Route::title('Verbatim') ?></title>
        <link rel="stylesheet" href="<?= Route::app_href() ?>theme/verbatim.css"/>
    </head>
    <body class="<?=$page?>">
<div id="all">
    <header id="header">
        <div class="banner">
            <div class="title">Galenus verbatim</div>
            <div class="moto">Naviguer dans le texte de Galien de Pergame, éd. Kühn (1821–1833) &amp; al.</div>
            <img class="banner" src="<?= Route::app_href() ?>theme/galenus-verbatim.jpg" />
        </div>
        <nav class="tabs">
            <a class="tab <?=(!$page)?' selected':'' ?>" 
                href="<?= Route::home() ?>." 
                >Accueil /<br/>Accès rapide</a>
            <a class="tab<?=($page == 'biblio')?' selected':''?>" 
                href="<?= Route::home() ?>biblio" 
                >Table des <br/> traités</a>
            <?php 
            if ($page == 'tlg') {
                // if doc visible, add a buttoon search in doc search in doc
                Verbatim::qform(true);
            }
            else {
                Verbatim::qform();
            }
            
            ?>
            <a class="tab<?=($page == 'table')?' selected':''?>" 
                href="<?= Route::home() ?>table"
                >Table <br/>fréquentielle</a>
            <a  class="tab<?=($page == 'about')?' selected':''?>" 
                href="<?= Route::home() ?>about"
                >À propos /<br/>Crédits</a>
        </nav>
    </header>
    <div id="content">
        <div class="container">
            <main>
                <?php Route::main(); ?>
            </main>
        </div>
    </div>
    <footer id="footer">
        <nav id="logos">
            <a href="https://github.com/hipster-philology/nlp-pie-taggers">Textes grecs lemmatisés avec<br/> <em>Pie Extended</em> de Thibault Clérice</a>
            <a>Verbapie</a>
        </nav>
    </footer>
</div>
    </body>
</html>
