
<nav class="tabs">
    <a href="." title="Présentation"><strong>Verbatim, en dev</strong></a>
    <a href="biblio" title="Bibliographie" class="tab">Bibliographie</a>
    <a href="table" title="Fréquences par mots" class="tab">Table</a>
    <a href="conc<?= (isset($q) && $q)?"?q=$q":'' ?>" title="Recherche de mot" class="tab">Concordance</a>
    <!--
    <a href="doc.jsp" title="Lire un texte" class="tab">Liseuse</a>
    -->
</nav>
