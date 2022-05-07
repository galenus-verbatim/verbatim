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
    static $lat_grc;
    /**
     * Init static fields
     */
    static public function init()
    {
        mb_internal_encoding("UTF-8");
        self::$lat_grc = include(__DIR__ . '/pages/lat_grc.php');
        // test needed extension
        foreach (array('intl', 'pdo_sqlite') as $ext) {
            if (!extension_loaded($ext)) {
                $mess = "<h1>Installation problem, check your php.ini, needed extension: " . $ext . "</h1>";
                throw new Exception($mess);
            }
        }
    }

    /**
     * Database should be connected if something is desired to be displayed
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

    /**
     * $q = "kai"
     */
    public static function forms(string $q, string $field)
    {
        $limit = 100; // search more than 100 words ?
        $qform = Verbatim::$pdo->prepare("SELECT id, form, cat FROM $field WHERE form LIKE ?");
        $qdeform = Verbatim::$pdo->prepare("SELECT id, form, cat FROM $field WHERE deform LIKE ?");
        $forms = array();
        $words = preg_split("@[\s,]+@", trim($q));
        for ($i = 0; $i < count($words); $i++) {
            $w = $words[$i];
            $w = Normalizer::normalize($w, Normalizer::FORM_KD);
            if ($field == 'lem' && $w == 'NUM');
            // maybe latin letters to translitterate
            else $w = strtr($q, self::$lat_grc);




            $qform->execute(array($w));
            // rowcount do not work
            while ($row = $qform->fetch(PDO::FETCH_NUM)) {
                $forms[$row[0]] = $row[1] .' ' . I18n::_('pos.' . $row[2]) ;
                if (--$limit <= 0) return $forms;
            }
            // nothing found in form, try deform (without accents)
            if (!$row) {
                $qdeform->execute(array($w));
                while ($row = $qdeform->fetch(PDO::FETCH_NUM)) {
                    $forms[$row[0]] = $row[1] .' ' . I18n::_('pos.' . $row[2]) ;
                    if (--$limit <= 0) return $forms;
                }
            }
        }
        return $forms;
    }

    public static function tab($href, $text)
    {
        $page = Route::$url_parts[0];
        $selected = '';
        if ($page == $href) {
            $selected = " selected";
        }
        if(!$href) {
            $href = '.';
        }
        return '<a class="tab'. $selected . '"'
        . ' href="'. Route::home(). $href . '"' 
        . '>' . $text . '</a>';
    }

    public static function qform($down=false, $route='conc')
    {
        $selected = Route::match($route)?' selected':'';
        $q = Web::par('q');
        $radio = new Radio('f');
        $radio->add('lem', I18n::_('Lem'));
        $radio->add('orth', I18n::_('Form'));
        echo '
<form action="' . Route::home() . $route . '" class="qform' . $selected . '">
    <div class="radios">' . $radio . '    </div>
    <div  class="input">';
        if ($down) {
            echo '
        <button type="submit" title="' . I18n::_('search.indoc') . '" onclick="this.form.action=\'\'">▼</button>';
        }
        echo'
        <input name="q" class="q" value="' . htmlspecialchars($q) . '" />
        <button type="submit">▶</button>
    </div>
</form>
';    
    }

    static public function edition(&$edition)
    {
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
       if (isset($doc['pagad']) && $doc['pagad']) {
            $line .= ', <span class="pagina">p. ' . $doc['pagde'] . '-' . $doc['pagad'] . '</span>';
        }
        else if (isset($doc['pagde']) && $doc['pagde']) {
            $line .= ', <span class="pagina">p. ' . $doc['pagde'] . '</span>';
        }
        $line .= ')';
        return $line;
    }
}
