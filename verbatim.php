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
    static $bibNorm;
    static public function init()
    {
        if (file_exists($file = __DIR__ . '/BibNorm.php')) {
            include_once($file);
            self::$bibNorm = true;
        }
        self::$pars = include(__DIR__ . "/pars.php");
        $dsn = "sqlite:" . self::$pars['corpus.db'];
        self::$pdo = new PDO($dsn, null, null, array(
            PDO::ATTR_PERSISTENT => true
        ));
        self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        self::$pdo->exec("PRAGMA temp_store = 2;");
        self::$pdo->exec('PRAGMA mmap_size = 1073741824;');
    }

    static public function opus(&$opus)
    {
        if (self::$bibNorm) { // some local rewriting
            BibNorm::opus($opus);
        }
        $line = '';
        $line .= '<span class="auctor">' . $opus['auctor'] . '</span>';
        $line .= ', <em class="titulus">' . $opus['titulus'] . '</em>';
        $line .= ' (';
        $line .= 'ed. <span class="editor">' . $opus['editor'] . '</span>';
        if (isset($opus['volumen']) && $opus['volumen']) {
            $line .= ', <span class="volumen">vol. ' . $opus['volumen'] . '</volumen>';
        }
        if (isset($opus['pagad']) && $opus['pagad']) {
            $line .= ', <span class="pagina">p. ' . $opus['pagde'] . '-' . $opus['pagad'] . '</span>';
        }
        else if (isset($opus['pagde']) && $opus['pagde']){
            $line .= ', <span class="pagina">p. ' . $opus['pagde'] . '</span>';
        }
        $line .= ')';
        return $line;
    }

    static public function num(&$doc)
    {
        $num = array();
        foreach (array('liber', 'capitulum', 'sectio') as $clavis) {
            if (!isset($doc[$clavis])) continue;
            if (!$doc[$clavis]) continue;
            $num[] = $doc[$clavis];
        }
        return implode('.', $num);
    }

    static public function bibl(&$opus, &$doc)
    {
        if (self::$bibNorm) { // some local rewriting
            BibNorm::opus($opus);
            BibNorm::doc($doc);
        }
        // parts
        $line = '';
        $line .= '<span class="auctor">' . $opus['auctor'] . '</span>';
        $line .= ', <em class="titulus">' . $opus['titulus'] . '</em>';
        $num = self::num($doc);
        if ($num) $line .= ', ' . $num;
        $line .= ' (';
        $line .= 'ed. <span class="editor">' . $opus['editor'] . '</span>';
        // if opus on more than one volume
        if (isset($doc['volumen']) && $doc['volumen']) {
            $line .= ', <span class="volumen">vol. ' . $doc['volumen'] . '</span>';
        }
        else if (isset($opus['volumen']) && $opus['volumen']) {
            $line .= ', <span class="volumen">vol. ' . $opus['volumen'] . '</span>';
        }
        // a bug, but could be found
        if (isset($doc['pagad']) && $doc['pagad'] && (isset($doc['pagde']) || !$doc['pagde'])) {
            $line .= ', <span class="pagina">p. ' . $doc['pagad'] . '</span>';
        }
        else if (isset($doc['pagad']) && $doc['pagad']) {
            $line .= ', <span class="pagina">p. ' . $doc['pagde'] . '-' . $doc['pagad'] . '</span>';
        }
        else if (isset($doc['pagde']) && $doc['pagde']) {
            $line .= ', <span class="pagina">p. ' . $doc['pagde'] . '</span>';
        }
        $line .= ')';
        return $line;
    }
}
