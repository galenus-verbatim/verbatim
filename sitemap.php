<?php
/**
 * Part of verbatim https://github.com/galenus-verbatim/verbatim
 * Copyright (c) 2021 Nathalie Rousseau
 * MIT License https://opensource.org/licenses/mit-license.php
 */
require_once(__DIR__ . "/Verbatim.php");
$pars = require_once(__DIR__ . "/pars.php");
// regenerate sitemap.xml if needed
$sitemap_file = __DIR__ . '/sitemap.xml';
if (
       file_exists($sitemap_file) 
    && filemtime($sitemap_file) > filemtime($pars['corpus_db'])
) {
    return;
}

$write = fopen($sitemap_file, "w");
fwrite($write, '<?xml version="1.0" encoding="UTF-8"?>');
fwrite($write, '
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'); 

Verbatim::connect($pars['corpus_db']);
$sql = "SELECT clavis FROM doc ORDER BY ;";
$q = Verbatim::$pdo->prepare($sql);
$q->execute(array());
while($row = $q->fetch(PDO::FETCH_ASSOC)) {
    fwrite($write, '
  <url><loc>' . $pars['url_base'] . $row['clavis'] . '</loc></url>');
}
fwrite($write, '
</urlset>
');
