<?php
/**
 * Part of verbatim https://github.com/galenus-verbatim/verbatim
 * Copyright (c) 2021 Nathalie Rousseau
 * MIT License https://opensource.org/licenses/mit-license.php
 */
require_once(dirname(__DIR__) . "/Verbatim.php");

use Oeuvres\Kit\{I18n,Web};

$sql = "SELECT id, clavis, volumen, pagde, pagad, liber, capitulum,  editor, titulus FROM doc WHERE clavis LIKE ?;";
$q = Verbatim::$pdo->prepare($sql);
$q->execute(array('tlg0057.tlg076.1st1K-grc1%'));
echo "<pre>\n";
while($row = $q->fetch(PDO::FETCH_ASSOC)) {
    print_r($row);
}
echo "</pre>\n";
