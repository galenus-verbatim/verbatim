<?php
declare(strict_types=1);

require_once(__DIR__ . "/php/autoload.php");

use Oeuvres\Kit\Route;

// set template
Route::$template = __DIR__ . '/template.php';
/*
Route::get('/conc', 'pages/conc.php');
Route::get('/table', 'pages/table.php');
Route::get('/biblio', 'pages/biblio.php');
*/
Route::get('/', 'pages/welcome.html');
Route::get('/(tlg.*)', 'pages/doc.php', array('cts' => '$1')); // a tlg content
Route::get('/(.*)', 'pages/$1.php'); // try if a php content is available
Route::get('/(.*)', 'pages/$1.html'); // try if an html content is available
Route::route('/404','pages/404.html'); // catch all