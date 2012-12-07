moodle-filter-filtermenu
=====================
Ce filtre construire une beau (?) menu dynamique affichant les cours de l'utilisateur ou tous les cours (au choix).

Attention:
Nous avons seulement mis � jour le code source original de filtre "filtermenu" pour Moodle 1.x.
Nous ne sommes pas les mainteneurs officiels, parce que :
- nous n'avons pas �crit le code source original, nous l'avons seulement mis � jour pour Moodle 2.3
- nous pensons que ce n'est pas la meilleure solution pour afficher la liste des cours (nous l'utilisons comme une solution temporaire)

Cependant, nous lirons les commentaires et r�pondrons aux questions sur le forum, mais sans aucune garantie.
N'h�sitez pas � r�utiliser / am�liorer le code. Nous sommes pr�t � discuter au sujet d'un outil d'affichage plus g�n�rique et plus �rgonomique.

Requiert :
- Moodle 2.3 (nous n'avons pas pu le tester avec les autres versions)
- Maximum 1 sous-cat�gorie par cat�gorie

Nous n'avons pas test� tous les cas de d'organisation de cat�gorie, alors testez-le avant de l'utiliser !


installation
------------

From `moodledir/filter`:

	git clone git://github.com/comete-upo/moodle-filter-filtermenu.git filtermenu
	copier le dossier filtermenu dans /yourmoodle/filter/
	
* Se connecter en tant qu'admin pour installer le filtre.
* Aller � la page `http://yourmoodle/admin/filters.php` et mettez _filtermenu_  � **D�sactiver mais disponible**.
* Aller � la page `http://yourmoodle/admin/settings.php?section=commonfiltersettings` et mettez "Dur�e de vie du cache texte" � **Non**.
* Aller � la page `Administration du site > Page d'accueil > Filtres de la page d'accueil` et mettez _filtermenu_ **Activ�**.
* Editer la page d'accueil et ajouter le texte `[-MENU-]`.

(Vous pourrez personnaliser le menu via cette page : `http://yourmoodle/admin/settings.php?section=filtersettingfilterfiltermenu`.)
=> Ceci n'est pas encore impl�ment�.

Si vous mettez � jour Moodle via git, pensez � ajouter `/filter/filtermenu` au fichier `.git/info/exclude`.


credits
-------

* DefaultIcon ver 2.0 by Apostolos Paschalidis interactivemania
* Browser Detect Lite modified by Chris Nott 
* dynMenu by Bieler Batiste
* update for Moodle 2.3 by Jean-Fran�ois Lemoine, Universit� Paris Ouest Nanterre La D�fense

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
