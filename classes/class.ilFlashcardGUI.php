<?php
/**
 * Copyright (c) 2016 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg
 * GPLv2, see LICENSE
 */

/**
 * GUI class for showing a flashcard
 * 
 * This class
 * 
 * @author Fred Neumann <fred.neumann@fim.uni-erlangen.de>
 * $id$
 */
class ilFlashcardGUI
{
	/**
	 * Constructor
	 * 
	 * @param object	patent gui object
	 * @param object	flashcard object
	 */
	function __construct($a_parent_gui, ilFlashcard $a_card)
	{
		$this->parent_gui = $a_parent_gui;
		$this->object = $a_parent_gui->object;
		$this->plugin = $a_parent_gui->plugin;
		$this->lng = $a_parent_gui->lng;
		$this->ctrl = $a_parent_gui->ctrl;
		$this->tpl = $a_parent_gui->tpl;
		
		$this->card = $a_card;
	}
	
	/**
	 * get the HTML code for showing the card in a training
	 */
	function getCardForTrainingHTML()
	{
        $this->tpl->addCss(ilObjStyleSheet::getContentStylePath(0));
		
		// get the card pages to be displayed
		if ($this->card->getTermId())
		{
			$pages = $this->getGlossaryTermPages();
		}
		else
		{
			// TODO: corrently only glossary cards are supported
			$pages = array();
		}
		
		$question = new ilAccordionGUI();
		$question->setBehaviour(ilAccordionGUI::FORCE_ALL_OPEN);
		$question->setContentClass("xflcFlashcardPage");
		$question->addItem($pages[0]["title"] ?? '', $pages[0]["html"] ?? '');

		$answers = new ilAccordionGUI();
		//$answers->setBehaviour(ilAccordionGUI::FIRST_OPEN);
		$answers->setContentClass("xflcFlashcardPage");
		
		for ($i=1; $i < count($pages); $i++)
		{
			$answers->addItem($pages[$i]["title"] ?? '', $pages[$i]["html"] ?? '');
		}
		
		return $question->getHTML() . $answers->getHTML();
	}
	
	
	/**
	 * get the HTML pages of a card from the glossary
	 * Each page is an assoc array with title and html code
	 * The first page is assumed to be the question
	 * 
	 * @return array ( array ("title" => string, "html" => string), ...)
	 */
	function getGlossaryTermPages()
	{
		$term = new ilGlossaryTerm($this->card->getTermId());
		$defs = ilGlossaryDefinition::getDefinitionList($term->getId());
		
		// get the term page
		$term_page = array(	"title" => $this->plugin->txt("glossary_term"), 
							"html" => $term->getTerm());

		// get the definition pages
		$def_pages = array();
		$def_title = count($defs) > 1 ? 
					$this->plugin->txt("glossary_definition_x") :
					$this->plugin->txt("glossary_definition");
		foreach ($defs as $definition)
		{
			$page_gui = new ilPageObjectGUI("gdf", $definition["id"] ?? 0);			
			$page_gui->setTemplateOutput(false);
			$page_gui->setOutputMode(ilPageObjectGUI::PRESENTATION);
			$page_gui->setEnabledTabs(false);
			
			$def_pages[] = array( "title" => sprintf($def_title, $i++),
								   "html" => $page_gui->getHTML());
		}
		
		// return the pages according to the glossary mode
		switch ($this->object->getGlossaryMode())
		{
			case ilObjFlashcards::GLOSSARY_MODE_TERM_DEFINITIONS:
				return array_merge(array($term_page), $def_pages);

				
			case ilObjFlashcards::GLOSSARY_MODE_DEFINITION_TERM:
				return array_merge($def_pages, array($term_page));
			
			case ilObjFlashcards::GLOSSARY_MODE_DEFINITIONS:
                if (empty($def_pages)) {
                    return array($term_page);
                }
                
				$def_pages[0]["title"] = $this->plugin->txt("question");
				$answer_title = count($def_pages) > 2 ? 
								$this->plugin->txt("answer_x") :
								$this->plugin->txt("answer");
				for ($i = 1; $i < count($def_pages); $i++)
				{
					$def_pages[$i]["title"] = sprintf($answer_title, $i);
				}
				return $def_pages;
		}
	}
	
}