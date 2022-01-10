<?php
declare(strict_types=1);

require_once(__DIR__ . "/php/autoload.php");

use Oeuvres\Kit\Route;


Route::get('/', 'welcome.php');
Route::get('/conc', 'conc.php');
Route::get('/table', 'table.php');
Route::get('/biblio', 'biblio.php');
Route::get('/$cts', 'doc.php', '@^/tlg@');
Route::route('/404','404.php');