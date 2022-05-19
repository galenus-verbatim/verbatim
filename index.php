<?php
/**
 * Edit this file to control your app of 
 * cts greek texts publication
 */
declare(strict_types=1);

// Change this path if your site is outside verbatim
$verbadir = __DIR__ . "/";

// load the master class of the app
require_once($verbadir . "Verbatim.php");

use Oeuvres\Kit\{Route,I18n};

// connect to a database prepared with verbapie
// https://github.com/galenus-verbatim/verbapie
Verbatim::connect($verbadir . 'corpus.db');

// Register messages for the app
I18n::load(require_once($verbadir .'fr.php'));
// register the template in which include content
Route::template($verbadir . 'template.php');


// try a redirection to a Kühn reference
Route::get('/([\dIVX].*)', $verbadir . 'pages/kuhn.php', array('kuhn' => '$1'), null);
// try an urn:cts redirection like 
// https://www.digitalathenaeus.org/tools/KaibelText/cts_urn_retriever.php
// urn:cts:greekLit:tlg0008.tlg001.perseus-grc2:3.7
// some server may 403 on ':' in url, support '_'
Route::get(
    'urn[:_].*', 
    $verbadir . 'pages/cts.php', 
    array('URN' => '$0'), 
    null
);
// welcome page
Route::get('/', $verbadir . 'pages/welcome.html');
// a tlg opus
Route::get('/(tlg\d+\.tlg\d+)', $verbadir . 'pages/opus.php', array('cts' => '$1'));
// a tlg content, array to pass params extracted from url path
Route::get('/(tlg.*)', $verbadir . 'pages/doc.php', array('cts' => '$1'));
// try if a php content is available
Route::get('/(.*)', $verbadir . 'pages/$1.php'); 
// try if an html content is available
Route::get('/(.*)', $verbadir . 'pages/$1.html');
// catch all
Route::route('/404', $verbadir . 'pages/404.html');
// No Route has worked
echo "Bad routage, 404.";