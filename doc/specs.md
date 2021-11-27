Spécifications (en cours de mise ordre)

Verbatim est une application pour publier et chercher dans des corpus de grec ancien conforme au schéma XML/TEI/Epidoc.

* URL pérenne pour chaque découpage du texte : fichier / livre / chapitre / occurrence | page Kühn (cf. Athénée de Naucratis).


Pouvoir lire chaque texte avec une mise en valeur par changement de police (italique, surlignage, gras, couleurs) qui permet de distinguer des entités nommées ou des citations au sein du texte.
    Lemmatisation du texte permettant d’effectuer une recherche au sein de tout le corpus ou du texte lu (pour Galien du traité ou d’une lettre en particulier)
    Lien entre les personnes qui permet de cliquer sur une personne et de voir si par exemple elle écrit une lettre ou est mentionné dans une autre lettre
    Moteur de recherche qui peut proposer à partir du lemme des suggestions pour les homonymes (définir si on recherche en grec ou en translitérant. Si oui, selon qu’elle norme ? Bon exemple, le modèle de Perseus ).
    Un index pour chaque texte des personnes et des citations répliquer à un index global pour l’ensemble des textes. Si pas trop compliqué à coder, un index verborum memoribilum peut être intéressant comme complément.
    Menu de navigation qui permet une recherche dans le texte ou dans le corpus global un lien vers l’index global et la page d’accueil et un menu déroulant pour des textes. Définir si les listes des personnes et des citations par texte sont ajoutées en dessus du texte ou sur une page tierce.
    Possibilité de survoler un mot encodé et d’obtenir directement les informations disponibles dans l’XML. Exemple sur cette balise :

<bibl> <title type=“poetry” ref=”Theriaka"></title> <author> <name type=” poet” nymRef=”Nicander_of_Colophon"> </name> </author> Θηριακὰ </bibl>

Si on survole le mot Thêriaka sur le site, on pourra voir « Therriaka, poetry, written by Nicander of Colophon »
