<?php
/**
 * Part of verbatim https://github.com/galenus-verbatim/verbatim
 * Copyright (c) 2021 Nathalie Rousseau
 * MIT License https://opensource.org/licenses/mit-license.php
 */

mb_internal_encoding("UTF-8");

include_once(dirname(__DIR__) . '/teinte/php/autoload.php');


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
        // parts
        preg_match('@\.(\d+(\.\d+)*)$@', $doc['identifier'], $matches);
        $no = $matches[1];
        $href = "doc.php?cts=%s&amp;q=%s";
        return '<a href="' . sprintf($href, $doc['identifier'], $q) . '">'
        . '<em>' . $doc['title'] . '</em>' 
        . ' (' . $no . ')'
        . ', « ' . $doc['chapter'] . ' »'
        . '</a>';

    }
}
Verbatim::init();