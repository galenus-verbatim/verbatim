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
// connect to a database prepared with verbapie
// https://github.com/galenus-verbatim/verbapie
Verbatim::connect($verbadir . 'corpus.db');

use Oeuvres\Kit\{Route,I18n};

// Register messages for the app
I18n::load(require_once($verbadir .'fr.php'));
// register the template in which include content
Route::template($verbadir . 'template.php');
// welcome page
Route::get('/', $verbadir . 'pages/welcome.html');
// a tlg content, array to pass params extracted from url path
Route::get('/(tlg.*)', $verbadir . 'pages/doc.php', array('cts' => '$1'));
// try if a php content is available
Route::get('/(.*)', $verbadir . 'pages/$1.php'); 
// try if an html content is available
Route::get('/(.*)', $verbadir . 'pages/$1.html');
// try a redirection to a Kühn reference
Route::get('/(.*)', $verbadir . 'pages/kuhn.php', array('kuhn' => '$1'));
// catch all
Route::route('/404', $verbadir . 'pages/404.html');
// No Route has worked
echo "Bad routage, 404.";