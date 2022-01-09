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

    static public function bibl($doc, $q)
    {
        if (Route::$routed) $href = '%s?q=%s';
        else $href = "doc.php?cts=%s&amp;q=%s";
        // parts
        preg_match('@\.(\d+(\.\d+)*)$@', $doc['identifier'], $matches);
        $no = $matches[1];

        $line = '';
        $line .= '<a href="' . sprintf($href, $doc['identifier'], $q) . '">';
        $line .= 'Galien'; // TODO author
        $line .= ', <em>' . $doc['title'] . '</em>';
        if (isset($doc['book']) && isset($doc['chapter'])) {
            $line .= ', ' . $doc['book'] . '.' . $doc['chapter'] . '';
        }
        else if (isset($doc['chapter'])) {
            $line .= ', ' . $doc['chapter'] . '';
        }
        $line .= ' (';
        $line .= $doc['edition'] . ' ';
        if (isset($doc['volume']) && $doc['volume']) {
            $line .= $doc['volume'] . '.';
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
