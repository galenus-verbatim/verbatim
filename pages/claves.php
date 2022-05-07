<?php
/**
 * Part of verbatim https://github.com/galenus-verbatim/verbatim
 * Copyright (c) 2021 Nathalie Rousseau
 * MIT License https://opensource.org/licenses/mit-license.php
 */
require_once(dirname(__DIR__) . "/Verbatim.php");

use Oeuvres\Kit\{I18n};

function main() {
    $href = 'conc?f=lem&amp;q=%s';
    echo '
<article style="background-color: #fff">
<table>
    <caption>Liste des textes en base</caption>
    <thead>
        <tr>
            <th></th>
            <th>Code</th>
            <th>Auteur</th>
            <th>Titre</th>
            <th>Éditeur</th>
            <th>Volume</th>
            <th>Année</th>
        </tr>
    <thead>
    <tbody>
';
    $sql = "SELECT clavis, auctor, titulus, editor, volumen, annuspub FROM edition";
    $q = Verbatim::$pdo->prepare($sql);
    $q->execute();
    $i = 1;
    while ($row = $q->fetch(PDO::FETCH_ASSOC)) {
        echo  '
        <tr>
            <td class="no">' . $i++ . '</td>
            <td class="clavis"><a href="' . $row['clavis'] . '">' . $row['clavis'] . '</a></td> 
            <td>' . $row['auctor'] . '</td>
            <td>' . $row['titulus'] . '</td>
            <td>' . $row['editor'] . '</td>
            <td>' . $row['volumen'] . '</td>
            <td>' . $row['annuspub'] . '</td>
        </tr>';
    }
    echo '
    </tbody>
</table>
';
    };
?>
