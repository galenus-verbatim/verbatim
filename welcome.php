<html>
    <head>
        <meta charset="utf-8"/>
        <title>Test moteur de recherche Sqlite</title>
        <link rel="stylesheet" href="theme/verbatim.css"/>
    </head>
    <body>
        <header>
            <?php include(__DIR__ . "/tabs.php"); ?>
        </header>
        <main>
            <h1>Verbatim</h1>
            <p>Un moteur de recherche lemmatisé sur de textes en grec ancien.</p>
            <p>Chercher un mot ?</p>
            <form action="conc.php">
                <input name="q" value="ὁ"/>
                <button type="submit">Go</button>
            </form>
        </main>
    </body>
</html>