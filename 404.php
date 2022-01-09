<?php
http_response_code(404);
?>
<html>
    <head>
        <meta charset="utf-8"/>
        <title>404 NON Found, Verbatim</title>
        <link rel="stylesheet" href="theme/verbatim.css"/>
    </head>
    <body>
        <header>
            <?php include(__DIR__ . "/tabs.php"); ?>
        </header>
        <main>
            <h1>Perdu ?</h1>
            <div>Essayez un lien ci-dessus ou tentez une recherche dans le corpus :</div>
            <form action="conc">
                <input name="q" value="ὁ"/>
                <button type="submit">Go</button>
            </form>
        </main>
    </body>
</html>