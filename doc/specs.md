# Spécifications (en cours de mise ordre)

Verbatim est une application pour publier, chercher et annoter des corpus de grec ancien conforme au schéma XML/[TEI](https://tei-c.org/release/doc/tei-p5-doc/en/html/REF-ELEMENTS.html)/[Epidoc](http://epidoc.stoa.org/). Il sera en premier lieu dédié à l’oeuvre de Galien pour des recherches linguistiques, mais tout de suite conçu pour s’adapter à d’autres oeuvres, dont notamment, de la correspondance byzantine.

## Fonctionnalités de base

Ces fonctionnalités sont prioritaires et requises pour l’oeuvre de Galien.

* Doit pouvoir s’installer sur un hébergément mutualisé PHP à bas coût (ex : doctorant)
* Possibilité de mise en parallèle de plusieurs éditions et traductions d’une même unité textuelle 
* URL pérenne pour chaque découpage du texte : fichier / livre / chapitre / occurrence | page Kühn, conforme au patrons d’identification cts de Perseus (cf. [Athénée de Naucratis](https://digitalathenaeus.org/))
* Système d’annotation au mot prêt d’une unité textuelle, indépendamment de l’édition ou la langue
* Recherche plein texte lemmatisée du grec (fonctionnalité en cours de réflexion qui doit pouvoir permettre plusieurs approches selon les progrès des lemmatiseurs)
* Navigation claire dans une oeuvre par la table des matières
* Accueil et  WordPress pour ajouter des document rédigé autour de l’édition
* Personnalisation XSLT et CSS pour adapter la présentation du texte en ligne à du balisage supplémentaire (ex: bulle au survol d’une oeuvre cité donnant un référence bibliographique complète)

## Fonctionnalités supplémentaires

D’autres fonctionnalités sont souhaitées et doivent être permises par l’architecture

* Index des noms de personnes
* Index des citations
* Index des oeuvres
* Index verborum memoribilum (???)

## Question concrètes

* Quelle norme de translitération pour chercher dans le grec ? Le BetaCode Perseus ?
