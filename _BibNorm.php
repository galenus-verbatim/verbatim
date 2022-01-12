<?php
/**
 * A very hugly hack to rewrite some bibliographic fields
 * with data outside of the XML files.
 * If you need it, rename this file to BibNorm.php,
 * and modify.
 */

/** rewrite some properties of a doc record from the database */
class BibNorm
{
    static function opus(&$opus)
    {
        $editions = array(
            'Karl Gottlob Kühn' => 'Kühn',
            'Hermann Schöne' => 'Schöne',
            'Georg Kaibel' => 'Kaibel',
            'Georg Helmreich' => 'Helmreich',
            'Karl Kalbfleisch' => 'Kalbfleisch',
            'Johannes Marquardt' => 'Marquardt',
            'Ioannes Raeder' => 'Raeder',
            'Iwan von Müller' => 'von Müller',
        );
        if (isset($editions[$opus['editor']])) {
            $opus['editor'] = $editions[$opus['editor']];
        }
        $auctors = array(
            'pseudo-Galen' => 'pseudo-Galenus',
        );
        if (isset($auctors[$opus['auctor']])) {
            $opus['auctor'] = $auctors[$opus['auctor']];
        }
    }
    static function doc(&$doc)
    {
    }
}

