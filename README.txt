moodle-filter-filtermenu
=====================

This filter build a nice (?) dynamic menu containing courses.

Warning :
We only updated the original source code of "filter menu" for Moodle 1.x. 
We are not the officials maintainers, because : 
- we did not write the original source code, we only updated it for Moodle 2.3 
- we think that it is not the best solution to display the courses list (we use it as a temporary solution)

However, we will read the comments and will answer to the questions in the forum but with no warranty.
Feel free to reuse/improve the code. We are open to discuss about a more generique and ergonomique display tool.

Requires : 
- Moodle 2.3 (we did not test it with the others versions)
- maximum 1 subcategory by category

We did not test all organization category cases. So test it before use it !


installation
------------

From `moodledir/filter`:

	git clone git://github.com/comete-upo/moodle-filter-filtermenu.git filtermenu
	copy the filtermenu directory in /yourmoodle/filter/
	
* Connect to moodle as an administrator in order to install the filter.
* Go to `http://yourmoodle/admin/filters.php` and set _filtermenu_ **off but available**.
* Go to `http://yourmoodle/admin/settings.php?section=commonfiltersettings` and set the _cachetext_ to **No**.
* Go to `Site administration > Front page > Front page` and set _filtermenu_ **On**.
* Edit the front page and add `[-MENU-]`.

(You'll be able to tweak the menu using the filter settings: `http://yourmoodle/admin/settings.php?section=filtersettingfilterfiltermenu`.)
=> Not yet implemented.

If you're using git to update moodle, don't forget to add `/filter/filtermenu` to `.git/info/exclude`.


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
