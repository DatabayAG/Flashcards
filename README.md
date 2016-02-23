ILIAS Flashcards Training plugin
================================

Copyright (c) 2016 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg, GPLv2, see LICENSE

- Author:   Fred Neumann <fred.neumann@ili.fau.de>
- Forum: http://www.ilias.de/docu/goto_docu_frm_3474_1945.html
- Bug Reports: http://www.ilias.de/mantis (Choose project "ILIAS plugins" and filter by category "Flashcards")

Installation
------------
When you download the Plugin as ZIP file from GitHub, please rename the extracted directory to *Flashcards*
(remove the branch suffix, e.g. -master).

1. Copy the Flashcards directory to your ILIAS installation at the followin path (create subdirectories, if neccessary):
Customizing/global/plugins/Services/Repository/RepositoryObject/
2. Go to Administration > Plugins
3. Click "Update" for the FlashCards plugin
4. Click "Activate" for the FlashCards plugin
5. Click "Refresh Languages" for the FlashCards plugin

There is nothing to configure for this plugin.

Usage
-----
This plugin provides a training object for glossary contents.
Therefore you should have glossary with contents avaliable in ILIAS. 
The contents are trained as flashcards according to the training scheme of Sebasian Leitner.

As a Lecturer

1. Add a new "FlashCards Training" object in the repository
2. Edit the properties 
3. Select a glossary from the repository
4. Choose the training mode (asking for term, definition or second definition)
5. Probably enter some extra definitions
6. Set the training online and save the properties
7. Click "Update Cards from Glossary" to fill the training with content

Changes to a term or definition in the glossary will automatically be reflected in the training.
Newly added term in the glossary will not be automatically added to the training. 
Use "Update Cards from Glossary" to add them.

As a Learner

1. Select the training object in the glossary
2. Click "Fill Startbox" to get the first cards in you start box
3. Click "Start Training" to train these cards
4. For each card look at the front side an try to remember the back side
5. For each card choose whether you remembered it

Train the cards in the startbox daily.

* A known card moves one box forward.
* A difficult card stays in its box.
* An unknown card moves back to the startbox.

Train each box as soon as its capacity is reached. You will see that indicated by an icon.

Version History
===============

* All versions for ILIAS 5.1 and higher are maintained in GitHub: https://github.com/ilifau/Flashcards
* Former versions for ILIAS 5.0 and lower are maintained in ILIAS SVN: http://svn.ilias.de/svn/ilias/branches/fau/plugins

Version 1.3.2 (2016-02-23)
--------------------------
* stable version for ILIAS 5.1
* fixed ignored offline setting
* improved German language
* added uninstall support
* added "copy permission" (needs to be initialized in permission system)

Version 1.2.3 (2016-02-19)
------------------------------
* http://svn.ilias.de/svn/ilias/branches/fau/plugins/Flashcards
* stable version for ILIAS 5.0
* fixed ignored offline setting
* added "copy permission" (needs to be initialized in permission system)
* updated version number, icons and compatibility

Version 1.1.2 (2016-02-11)
--------------------------
* http://svn.ilias.de/svn/ilias/branches/fau/plugins/Flashcards-1.1.x
* stable version for ILIAS 4.4
* fixed ignored offline setting
* updated version number and README

Version 1.0.1 (2013-07-04)
--------------------------
* http://svn.ilias.de/svn/ilias/branches/fau/plugins/Flashcards-1.0.x
* stable version for ILIAS 4.3
* Fixed typos in German lang file
* Fixed default glossary mode
* Improved glossary selection
* Added screen ids for online help
* Currently only the Leitner training mode is supported
* Currently cards refer the glossary content but the training filled manually
* ToDo: Import and Export (e.g. for Anki) missing
* ToDo: Learning progress not supported
* ToDo: Timings are disabled
