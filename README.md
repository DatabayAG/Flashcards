ILIAS Flashcards Training plugin
================================

Copyright (c) 2017 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg, GPLv2, see LICENSE

**Further maintenance can be offered by [Databay AG](https://www.databay.de).**

- Forum: http://www.ilias.de/docu/goto_docu_frm_3474_1945.html
- Bug Reports: http://www.ilias.de/mantis (Choose project "ILIAS plugins" and filter by category "Flashcards")

Installation
------------
When you download the Plugin as ZIP file from GitHub, please rename the extracted directory to *Flashcards*
(remove the branch suffix, e.g. -master).

1. Copy the Flashcards directory to your ILIAS installation at the followin path (create subdirectories, if neccessary):
Customizing/global/plugins/Services/Repository/RepositoryObject/
2. Run `composer du` in the main directory of your ILIAS installation
3. Go to Administration > Extending ILIAS > Plugins
4. Click "Update" for the Flashcards plugin
5. Click "Activate" for the Flashcards plugin

There is nothing to configure for this plugin.

Usage
-----
This plugin provides a training object for glossary contents.
Therefore you should have glossary with contents avaliable in ILIAS. 
The contents are trained as flashcards according to the training scheme of Sebasian Leitner.

As a Lecturer

1. Add a new "Flashcards Training" object in the repository
2. Edit the properties 
3. Select a glossary from the repository
4. Choose the training mode (asking for term, definition or second definition)
5. Set the training online and save the properties
6. Click "Update Cards from Glossary" to fill the training with content

Changes to a term or definition in the glossary will automatically be reflected in the training.
Newly added terms in the glossary will not automatically be added to the training.
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

Plugin versions for different ILIAS releases are provided in separate branches of this repository.

Version 1.7.1 (2021-09-17)
* Compatibility with ILIAS 7
