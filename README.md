# advanced_search
Advanced Search Mod for Eve Development Killboard

By Sonya Rayner, fixed by Redhouse, enhanced for better compatibility and maintained by Salvoxia

##Installation
Upload the advanced_search folder to your killboard/mods folder. Activate the mod in ACP -> Modules.

##Changelog

#####Version 2.7.3
* fixes for compatibility with PHP7

#####Version 2.7.2
* typo fix in the template

#####Version 2.7.1
* searching by system name now actually works
* some deprecated symbols moved to non-deprecated

#####Version 2.7.0
* updated for EDK 4.0
* added "share" link function (you have to copy the url)
* fixed paging bug that crept in during some killboard revision

#####Version 2.6.2
* updated for EDK 3.2 (thanks to Sir Quentin and Kovell for additional info)

#####Version 2.6.1
* plugged possible SQL injection holes (thanks to tbma for tip and Kovell for help)

#####Version 2.6:
* bugfixes for EDK 3.1
* no feature changes

#####Version 2.5:
* rewritten as EDK3 mod that extends standard search functionality (instead of plainly replacing it)
* also now has option to fully replace standard search (off by default)
* added date filter field
* some more bugs nailed

#####Version 2.1:
* minor bugfixes

#####Version 2.0:
* fully rewritten as an EDK 2.0+ mod
* now when activated replaces basic search
* built to use ready-available EDK classes: KillListTable, DBQuery, Kill, PageSplitter
* uses sessions to save search parameters between reloads and to enable paging control
* now uses TPL for search fields - almost no html code inside!
* object-oriented design (mostly) - creates a replica of KillList class to enable compatibility with PageSplitter and KillListTable
* performance improvements
* bugfixes
* settings panel - enables to set display options for results (edk 2.0 combined view enabled/disabled - will be more)
* no more haxx to mimic killboards kill list - now uses default killboard's list view
* compatible with region name haxxed and non-haxxed #####Versions of KillList classes (checks for available #####Versions)
* possibly compatible with edk 1.5 or earlier (not tested)

#####Version 1.1b
* bugfixes

#####Version 1.1a
* added "Clear" button

#####Version 1.1
* performance fixes
* bugfixes

#####Version 1.0
* initial release
* as stand-alone php-script that mimicks killboard's layout
* required some manual integration
