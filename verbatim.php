<?php
/**
 * Part of verbatim https://github.com/galenus-verbatim/verbatim
 * Copyright (c) 2021 Nathalie Rousseau
 * MIT License https://opensource.org/licenses/mit-license.php
 */

mb_internal_encoding("UTF-8");

include_once(__DIR__ . '/php/autoload.php');

use Oeuvres\Kit\Route;


Verbatim::init();
class Verbatim
{
    static $pars;
    static $pdo;
    static public function init()
    {
        self::$pars = include(__DIR__ . "/pars.php");
        $dsn = "sqlite:" . self::$pars['corpus.db'];
        self::$pdo = new PDO($dsn, null, null, array(
            PDO::ATTR_PERSISTENT => true
        ));
        self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        self::$pdo->exec("PRAGMA temp_store = 2;");
        self::$pdo->exec('PRAGMA mmap_size = 1073741824;');
    }

    static public function opus($opus)
    {
        $line = '';
        $line .= $opus['author']; // TODO author
        $line .= ', <em>' . $opus['title'] . '</em>';
        $line .= ' (';
        $line .= $opus['editor'];
        if (isset($opus['volume']) && $opus['volume']) {
            $line .= ' ' . $opus['volume'] . '.';
        }
        else {
            $line .= ' ';
        }
        if (isset($opus['pageto']) && $opus['pageto']) {
            $line .= $opus['pagefrom'] . '-' . $opus['pageto'];
        }
        else if (isset($opus['pagefrom']) && $opus['pagefrom']){
            $line .= $opus['pagefrom'];
        }
        $line .= ')';
        return $line;
    }


    static public function bibl($opus, $doc, $q)
    {
        if (Route::$routed) $href = '%s?q=%s';
        else $href = "doc.php?cts=%s&amp;q=%s";
        // parts
        $line = '';
        $line .= '<a href="' . sprintf($href, $doc['identifier'], $q) . '">';
        $line .= 'Galien'; // TODO author
        $line .= ', <em>' . $opus['title'] . '</em>';
        if (isset($doc['book']) && isset($doc['chapter'])) {
            $line .= ', ' . $doc['book'] . '.' . $doc['chapter'] . '';
        }
        else if (isset($doc['chapter'])) {
            $line .= ', ' . $doc['chapter'] . '';
        }
        $line .= ' (';
        $line .= $opus['editor'] . ' ';
        // TODO, something here for opus on more than one volume
        if (isset($opus['volume']) && $opus['volume']) {
            $line .= $opus['volume'] . '.';
        }
        if (isset($doc['pageto']) && $doc['pageto']) {
            $line .= $doc['pagefrom'] . '-' . $doc['pageto'];
        }
        else {
            $line .= $doc['pagefrom'];
        }
        $line .= ')';
        $line .= '</a>';
        return $line;
    }
}
