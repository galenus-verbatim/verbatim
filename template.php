<?php
declare(strict_types=1);

require_once(__DIR__ . "/Verbatim.php");

use Oeuvres\Kit\{Route, I18n};


$page = Route::$url_parts[0];
$body_class = $page;
if (@substr_compare($page, 'tlg', 0, strlen('tlg'))==0) {
    $body_class = 'tlg';
}

?><!doctype html>
<html>
    <head>
        <meta charset="utf-8"/>
        <title><?= Route::title('Verbatim') ?></title>
        <link  href="https://cdnjs.cloudflare.com/ajax/libs/viewerjs/1.10.5/viewer.min.css" rel="stylesheet"/>
        <link rel="stylesheet" href="<?= Route::app_href() ?>theme/verbatim.css"/>
    </head>
    <body class="<?=$body_class?>">
<div id="all">
    <header id="header">
        <div class="banner">
            <div class="titles">
                <div class="title">Galenus verbatim</div>
                <div class="titlesub">Γαληνὸς κατὰ λέξιν</div>
            </div>
            <div class="moto">Naviguer dans le texte de Galien de Pergame, éd. Kühn (1821–1833) &amp; al.</div>
            <img class="banner" src="<?= Route::app_href() ?>theme/galenus-verbatim.jpg" />
        </div>
        <nav class="tabs">
            <?= Verbatim::tab('', 'Accueil /<br/>Accès rapide') ?>
            <?= Verbatim::tab('traites', 'Table des <br/> traités') ?>
            <?php 
            if ($page == 'tlg') {
                // if doc visible, add a buttoon search in doc search in doc
                Verbatim::qform(true);
            }
            else {
                Verbatim::qform();
            }
            
            ?>
            <?= Verbatim::tab('table', 'Table <br/>fréquentielle') ?>
            <?= Verbatim::tab('apropos', 'À propos /<br/>Crédits') ?>
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
            <a href="https://www.iufrance.fr/" title="Institut universitaire de France"><img alt="Institut Universitaire de France" src="<?=  Route::app_href() ?>theme/logo_IUF.png"/></a>

            <a href="http://www.orient-mediterranee.com/spip.php?rubrique314" title="UMR 8167 Orient et Méditerranée"><img alt="UMR 8167 Orient et Méditerranée" src="<?=  Route::app_href() ?>theme/logo_UMR8167.png"/></a>

            <a href="https://lettres.sorbonne-universite.fr/faculte-des-lettres/ufr/lettres/grec/" title="Faculté des Lettres de Sorbonne Université"><img alt="Faculté des Lettres de Sorbonne Université" src="<?=  Route::app_href() ?>theme/logo_sorbonne-lettres.png"/></a>



            <a href="https://humanites-biomedicales.sorbonne-universite.fr/" title="Initiative humanités biomédicales de l’Alliance Sorbonne Université"><img alt="Initiative humanités biomédicales de l’Alliance Sorbonne Université" src="<?=  Route::app_href() ?>theme/logo_humabiomed.png"/></a>

            <a href="#" onmouseover="this.href='ma'+'i'+'lto:'+'etymologika' + '\u0040gm' + 'ail.com';"><img style="opacity: 0.7;" src="<?=  Route::app_href() ?>theme/enveloppe.png"/></a>
        </nav>
    </footer>
</div>
<?php
// set variables for bâle / chartier links
while ($body_class == 'tlg') {
    $json = file_get_contents(__DIR__ . '/theme/pages.json');
    $bale_chartier = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
    list($book) = explode('_', $page);
    $eds = $bale_chartier[$book];
    if (!$eds) break;
    echo "<script>\n";
    foreach ($eds as $key => $dat) {
        echo 'const ' . $key .'='.json_encode($dat, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE| JSON_UNESCAPED_SLASHES).";\n";
    }
    echo "</script>\n";
    break;
}
?>
        <script src="<?= Route::app_href() ?>vendor/viewer.js"></script>
        <script src="<?= Route::app_href() ?>theme/galenus.js"></script>
    </body>
</html>
