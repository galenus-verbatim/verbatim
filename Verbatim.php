<?php
/**
 * Part of verbatim https://github.com/galenus-verbatim/verbatim
 * Copyright (c) 2021 Nathalie Rousseau
 * MIT License https://opensource.org/licenses/mit-license.php
 */


include_once(__DIR__ . '/php/autoload.php');

use Oeuvres\Kit\{I18n, Radio, Route, Web};

Verbatim::init();
class Verbatim
{
    static $pdo;
    static $bibNorm;
    /**
     * Init static fields
     */
    static public function init()
    {
        mb_internal_encoding("UTF-8");
        if (file_exists($file = __DIR__ . '/BibNorm.php')) {
            include_once($file);
            self::$bibNorm = true;
        }
    }

    /**
     * Database should be connected if sommething is desired to be displayed
     */
    static public function connect($sqlite)
    {
        if (!file_exists($sqlite)) {
            echo "<p>Database ".$sqlite." not found</p>";
            exit();
        }
        $dsn = "sqlite:" . $sqlite;
        self::$pdo = new PDO($dsn, null, null, array(
            PDO::ATTR_PERSISTENT => true
        ));
        self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        self::$pdo->exec("PRAGMA temp_store = 2;");
        self::$pdo->exec('PRAGMA mmap_size = 1073741824;');
    }

    public static function qform($route='conc')
    {
        $selected = Route::match($route)?' selected':'';
        $q = Web::par('q');
        $radio = new Radio('f');
        $radio->add('lem', I18n::_('Lem'));
        $radio->add('orth', I18n::_('Form'));
        echo '
<form action="' . Route::home() . $route . '" class="qform' . $selected . '">
    <div class="radios">' . $radio->html() . '    </div>
    <div  class="input">
        <input name="q" class="q" value="' . htmlspecialchars($q) . '" />
        <button type="submit">▶</button>
    </div>
</form>
';    
    }

    static public function edition(&$edition)
    {
        if (self::$bibNorm) { // some local rewriting
            BibNorm::edition($edition);
        }
        $line = '';
        $line .= '<span class="auctor">' . $edition['auctor'] . '</span>';
        $line .= ', <em class="titulus">' . $edition['titulus'] . '</em>';
        $line .= ' (';
        $line .= 'ed. <span class="editor">' . $edition['editor'] . '</span>';
        if (isset($edition['volumen']) && $edition['volumen']) {
            $line .= ', <span class="volumen">vol. ' . $edition['volumen'] . '</volumen>';
        }
        if (isset($edition['pagad']) && $edition['pagad']) {
            $line .= ', <span class="pagina">p. ' . $edition['pagde'] . '-' . $edition['pagad'] . '</span>';
        }
        else if (isset($edition['pagde']) && $edition['pagde']){
            $line .= ', <span class="pagina">p. ' . $edition['pagde'] . '</span>';
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

    static public function bibl(&$edition, &$doc)
    {
        if (self::$bibNorm) { // some local rewriting
            BibNorm::edition($edition);
            BibNorm::doc($doc);
        }
        // parts
        $line = '';
        $line .= '<span class="auctor">' . $edition['auctor'] . '</span>';
        $line .= ', <em class="titulus">' . $edition['titulus'] . '</em>';
        $num = self::num($doc);
        if ($num) $line .= ', ' . $num;
        $line .= ' (';
        $line .= 'ed. <span class="editor">' . $edition['editor'] . '</span>';
        // if edition on more than one volume
        if (isset($doc['volumen']) && $doc['volumen']) {
            $line .= ', <span class="volumen">vol. ' . $doc['volumen'] . '</span>';
        }
        else if (isset($edition['volumen']) && $edition['volumen']) {
            $line .= ', <span class="volumen">vol. ' . $edition['volumen'] . '</span>';
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
