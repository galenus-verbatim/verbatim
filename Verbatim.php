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
    /** Sqlite connection */
    static $pdo;
    /** Something nice for betacode conversion */
    static $lat_grc;
    /** Name of the app */
    static private $name;
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
     * Name the app, used in some generated messages
     */
    static public function name($name=null)
    {
        if ($name !== null) self::$name = $name;
        return self::$name;
    }

    /**
     * Database should be connected if something is desired to be displayed
     */
    static public function connect($sqlite, $persistent=false)
    {
        if (!file_exists($sqlite)) {
            echo "<p>Database ".$sqlite." not found</p>";
            exit();
        }
        $dsn = "sqlite:" . $sqlite;
        if ($persistent) {
            self::$pdo = new PDO($dsn, null, null, array(
                PDO::ATTR_PERSISTENT => true
            ));
            self::$pdo->exec('PRAGMA mmap_size = 1073741824;');
        }
        else {
            self::$pdo = new PDO($dsn);
        }
        self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        self::$pdo->exec("PRAGMA temp_store = 2;");
    }

    static public function ante(&$doc, &$pars=array('q'))
    {
        return self::antepost('ante', $doc, $pars);
    }

    static public function post(&$doc, &$pars=array('q'))
    {
        return self::antepost('post', $doc, $pars);
    }
    /**
     * a prev / next link from database 
     */
    static private function antepost($key, &$doc, &$pars=array('q'))
    {
        if (!isset($doc[$key]) || !$doc[$key]) return; 
        $qstring = Web::qstring($pars);
        $chars = array('ante' => '⟨', 'post' => '⟩');
        echo '<a class="prevnext antepost ' . $key .'" href="' . $doc[$key] . $qstring . '">' . $chars[$key] .'</a>';
    }

    /**
     * Hilite doc
     */
    static public function hidoc(&$doc,&$formids)
    {
        $html = $doc['html'];
        if (!count($formids)) {
            return $html;
        }
        // http param a bit hard coded here
        $f = Web::par('f', 'lem', '/lem|orth/');
        $out = '';
        // Words to hilite
        $in  = str_repeat('?,', count($formids) - 1) . '?';
        $sql = "SELECT * FROM tok WHERE $f IN ($in) AND doc = {$doc['id']}";
        $qTok =  Verbatim::$pdo->prepare($sql);
        $qTok->execute($formids);
        $start = 0;
        while ($tok = $qTok->fetch(PDO::FETCH_ASSOC)) {
            $out .= mb_substr($html, $start, $tok['charde'] - $start);
            $out .= "<mark>";
            $out .= mb_substr($html, $tok['charde'], $tok['charad'] - $tok['charde']);
            $out .= "</mark>";
            $start = $tok['charad'];
        }
        $out .= mb_substr($html, $start, mb_strlen($html) - $start);
        return $out;
    }

    /**
     * Normalize a greek form to lower with no accents
     */
    static public function deform($form)
    {
        $form = Normalizer::normalize($form, Normalizer::FORM_D);
        $form = preg_replace( '@\pM@u', "", $form);
        $form = mb_strtolower($form);
        return $form;
    }

    /**
     * $q = "kai"
     */
    public static function forms(string $q, string $field)
    {
        $limit = 500; // search more than 100 words ?
        $qform = Verbatim::$pdo->prepare("SELECT id, form, cat FROM $field WHERE form LIKE ?");
        $qdeform = Verbatim::$pdo->prepare("SELECT id, form, cat FROM $field WHERE deform LIKE ?");
        $forms = array();
        $words = preg_split("@[\s,]+@", trim($q));
        for ($i = 0; $i < count($words); $i++) {
            $w = $words[$i];
            $w = Normalizer::normalize($w, Normalizer::FORM_KC);
            /*
            if ($field == 'lem' && $w == 'NUM');
            // maybe latin letters to translitterate
            else $w = strtr($w, self::$lat_grc);
            */
            $qform->execute(array($w));
            // rowcount do not work
            while ($row = $qform->fetch(PDO::FETCH_NUM)) {
                $forms[$row[0]] = $row[1]; // .' ' . I18n::_('pos.' . $row[2]) ;
                if (--$limit <= 0) return $forms;
            }
            // nothing found in form, try deform (without accents)
            if (!$row) {
                // decompose letters and accents
                $w = Normalizer::normalize($w, Normalizer::FORM_D);
                // strip non letter (accents)
                $w = preg_replace("/\PL/u", '', $w);
                // lower case folding, should regule final ς
                $w = mb_convert_case($w, MB_CASE_FOLD, "UTF-8");
                // translitterate possible beta code
                $w = strtr($w, self::$lat_grc);
                $qdeform->execute(array($w));
                while ($row = $qdeform->fetch(PDO::FETCH_NUM)) {
                    $forms[$row[0]] = $row[1]; // .' ' . I18n::_('pos.' . $row[2]) ;
                    if (--$limit <= 0) return $forms;
                }
            }
        }
        return $forms;
    }

    /**
     * Display nav with possible freqs by chapter
     */
    public static function nav(&$editio, &$doc, &$formids)
    {
        // http param a bit hard coded here
        $q = Web::par('q');
        $f = Web::par('f', 'lem', '/lem|orth/');
        $clavis = $doc['clavis'];
        if (!isset($editio['nav']) || ! $editio['nav']) return '';
        // no word searched
        if (!count($formids)) {
            $html = $editio['nav'];
            $html = preg_replace(
                '@ href="' . $clavis . '"@',
                '$1 class="selected"',
                $html
            );
            return $html;
        }
        $in  = str_repeat('?,', count($formids) - 1) . '?';
        $sql = "SELECT COUNT(*) FROM tok, doc WHERE $f IN ($in) AND doc = doc.id AND clavis = ?";
        $qTok =  Verbatim::$pdo->prepare($sql);
        $params = $formids;
        $i = count($params);
        // occurrences by chapter ?
        $html = preg_replace_callback(
            array(
                '@<a href="([^"]+)">([^<]+)</a>@',
            ),
            function ($matches) use ($clavis, $q, $qTok, $params, $i){
                $params[$i] = $matches[1];
                $qTok->execute($params);
                list($count) = $qTok->fetch();
                $ret = '';
                $ret .= '<a';
                if ($matches[1] == $clavis) {
                    $ret .= ' class="selected"';
                }
                $ret .= ' href="' . $matches[1] . '?q=' . $q . '"';
                $ret .= '>';
                $ret .= $matches[2];
                if ($count) {
                    $ret .= ' <small>(' . $count . ' occ.)</small>';
                }
                $ret .= '</a>';
                return $ret;
            },
            $editio['nav']
        );
        return $html;
    }

    /**
     * Draw an html tab for a navigation with test if selected 
     */
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
        . ' href="'. Route::home_href(). $href . '"' 
        . '>' . $text . '</a>';
    }

    /**
     * A search form
     */
    public static function qform($down=false, $route='conc')
    {
        $selected = Route::match($route)?' selected':'';
        $q = Web::par('q');
        $radio = new Radio('f');
        $radio->add('lem', I18n::_('Lem'));
        $radio->add('orth', I18n::_('Form'));
        echo '
<form action="' . Route::home_href() . $route . '" class="qform' . $selected . '">
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

    /**
     * Build a kind of biblio record, not perfect
     */
    static public function editio(&$editio)
    {
        $line = '';
        $line .= '<span class="auctor">' . $editio['auctor'] . '</span>';
        $line .= ', <em class="titulus">' . $editio['titulus'] . '</em>';
        $line .= ' (';
        $line .= 'ed. <span class="editor">' . $editio['editor'] . '</span>';
        if (isset($editio['volumen']) && $editio['volumen']) {
            $line .= ', <span class="volumen">vol. ' . $editio['volumen'] . '</volumen>';
        }
        if (isset($editio['pagad']) && $editio['pagad']) {
            $line .= ', <span class="pagina">p. ' . $editio['pagde'] . '-' . $editio['pagad'] . '</span>';
        }
        else if (isset($editio['pagde']) && $editio['pagde']){
            $line .= ', <span class="pagina">p. ' . $editio['pagde'] . '</span>';
        }
        $line .= ')';
        return $line;
    }

    /**
     * best numerotation of a section
     */
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

    /**
     * Give a displayable scope (vol., p.) for a section of an edition
     */
    static public function scope(&$doc)
    {
        $line = '';
        if (isset($doc['volumen']) && $doc['volumen']) {
            $line .= ', <span class="volumen">vol. ' . $doc['volumen'] . '</span>';
        }
        /*
        else if (isset($edition['volumen']) && $edition['volumen']) {
            $line .= ', <span class="volumen">vol. ' . $edition['volumen'] . '</span>';
        }
        */
        // a bug, but could be found
       if (isset($doc['pagad']) && $doc['pagad']) {
            $line .= ', <span class="pagina">p. ' . $doc['pagde'] . '-' . $doc['pagad'] . '</span>';
        }
        else if (isset($doc['pagde']) && $doc['pagde']) {
            $line .= ', <span class="pagina">p. ' . $doc['pagde'] . '</span>';
        }
        return $line;
    }

    /**
     * All this bib, not well optimized
     */
    static public function bibl(&$editio, &$doc)
    {
        // parts
        $line = '';
        $line .= '<span class="auctor">' . $editio['auctor'] . '</span>';
        $line .= ', <em class="titulus">' . $editio['titulus'] . '</em>';
        $num = self::num($doc);
        if ($num) $line .= ', ' . $num;
        $line .= ', ed. <span class="editor">' . $editio['editor'] . '</span>';
        $line .= self::scope($doc);
        return $line;
    }
}
