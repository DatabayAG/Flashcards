# ILIAS Flashcards Training plugin


Copyright (c) 2017 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg, GPLv2, see LICENSE

**Further maintenance can be offered by [Databay AG](https://www.databay.de).**

- Forum: http://www.ilias.de/docu/goto_docu_frm_3474_1945.html
- Bug Reports: http://www.ilias.de/mantis (Choose project "ILIAS plugins" and filter by category "Flashcards")

## Note for ILIAS 9

When you update from ILIAS 8 to ILIAS 9, please be sure to run the following migrations in the ilias setup:

- glossary.ilGlossaryDefinitionMigration
- glossary.ilGlossaryCollectionMigration

Otherwise you will get errors when the training pages are shown.

ILIAS 9 has a flashcards functionality in the core glossary object that is very similar to this plugin. The Add-on [FlashcardsConverter](https://github.com/DatabayAG/FlashcardsConverter) for ILIAS 9 is able to convert existing flashcard training objects into glossaries with flashcard presentation, keeping the training state of the users.

If you plan to use this conversion when updating from ILIAS 7 or 8 to ILIAS 9, please follow these steps:

1. Deactivate the plugin in ILIAS 7 but keep it installed
2. Update ILIAS to ILIAS 8 and the plugin to version 1.8 for ILIAS 8
3. Deactivate the plugin in ILIAS 8 (or keep it deactivated), but keep it installed
4. Update to ILIAS 9 (the plugin for ILIAS 8 will be ignored)
5. Install the [FlashcardsConverter](https://github.com/DatabayAG/FlashcardsConverter)
6. Do the conversion as described in the add-on
7. Check the converted objects
8. Uninstall the plugin, this should remove its database tables
9. Remove the code of the plugin and the converter

If you want to keep using the Flashcards Training plugin in ILIAS 9 instead, please contact [Databay AG](https://www.databay.de) to get an offer for a plugin update.

## Installation

When you download the Plugin as ZIP file from GitHub, please rename the extracted directory to *Flashcards*
(remove the branch suffix, e.g. -master).

1. Copy the Flashcards directory to your ILIAS installation at the followin path (create subdirectories, if neccessary):
Customizing/global/plugins/Services/Repository/RepositoryObject/
2. Run `composer du` in the main directory of your ILIAS installation
3. Go to Administration > Extending ILIAS > Plugins
3. Click "Install" for the Flashcards plugin
4. Click "Activate" for the Flashcards plugin

There is nothing to configure for this plugin.

## Usage

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

## Version History

Plugin versions for different ILIAS releases are provided in separate branches of this repository.

Version 1.8.0 (2023-12-22)
* Compatibility with ILIAS 8

