moodle-filter-filtermenu
=====================
Ce filtre construire une beau (?) menu dynamique affichant les cours de l'utilisateur ou tous les cours (au choix).

Attention:
Nous avons seulement mis à jour le code source original de filtre "filtermenu" pour Moodle 1.x.
Nous ne sommes pas les mainteneurs officiels, parce que :
- nous n'avons pas écrit le code source original, nous l'avons seulement mis à jour pour Moodle 2.3
- nous pensons que ce n'est pas la meilleure solution pour afficher la liste des cours (nous l'utilisons comme une solution temporaire)

Cependant, nous lirons les commentaires et répondrons aux questions sur le forum, mais sans aucune garantie.
N'hésitez pas à réutiliser / améliorer le code. Nous sommes prêt à discuter au sujet d'un outil d'affichage plus générique et plus érgonomique.

Requiert :
- Moodle 2.3 (nous n'avons pas pu le tester avec les autres versions)
- Maximum 1 sous-catégorie par catégorie

Nous n'avons pas testé tous les cas de d'organisation de catégorie, alors testez-le avant de l'utiliser !


installation
------------

From `moodledir/filter`:

	git clone git://github.com/comete-upo/moodle-filter-filtermenu.git filtermenu
	copier le dossier filtermenu dans /yourmoodle/filter/
	
* Se connecter en tant qu'admin pour installer le filtre.
* Aller à la page `http://yourmoodle/admin/filters.php` et mettez _filtermenu_  à **Désactiver mais disponible**.
* Aller à la page `http://yourmoodle/admin/settings.php?section=commonfiltersettings` et mettez "Durée de vie du cache texte" à **Non**.
* Aller à la page `Administration du site > Page d'accueil > Filtres de la page d'accueil` et mettez _filtermenu_ **Activé**.
* Editer la page d'accueil et ajouter le texte `[-MENU-]`.

(Vous pourrez personnaliser le menu via cette page : `http://yourmoodle/admin/settings.php?section=filtersettingfilterfiltermenu`.)
=> Ceci n'est pas encore implémenté.

Si vous mettez à jour Moodle via git, pensez à ajouter `/filter/filtermenu` au fichier `.git/info/exclude`.


credits
-------

* DefaultIcon ver 2.0 by Apostolos Paschalidis interactivemania
* Browser Detect Lite modified by Chris Nott 
* dynMenu by Bieler Batiste
* update for Moodle 2.3 by Jean-François Lemoine, Université Paris Ouest Nanterre La Défense

licence
-------

This code is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.
 
It is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
