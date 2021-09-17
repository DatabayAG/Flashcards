<?php
/**
 * Copyright (c) 2016 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg
 * GPLv2, see LICENSE
 */

/**
 * User interface for a flashcards training
 * This class has to be extended for a certain training mode (e.g. leitner)
 *
 * @author Fred Neumann <fred.neumann@fim.uni-erlangen.de>
 * $Id$
 */
abstract class ilFlashcardsTrainingGUI
{
	/** 
	 * Constructor
	 * @param ilObjFlashcardsGUI $a_parent_gui
	 */
	function __construct($a_parent_gui)
	{
		global $ilCtrl, $lng, $tpl;
		// initialize references to the mostly used objects
		$this->parent_gui = $a_parent_gui;
		$this->object = $a_parent_gui->object;
		$this->plugin = $a_parent_gui->getMyPlugin();
		$this->lng = $lng;
		$this->ctrl = $ilCtrl;
		$this->tpl = $tpl;
		$this->training = $this->getTrainingObject();
	}
	
	/**
	 * execute a command
	 * note: permissions are already checked in ilObjFlashcardsGUI
	 */
	abstract protected function executeCommand();
	
	
	/**
	 * Initialize and return the training object
	 * @return	object	descendant of ilFlashcardsTraining
	 */
	abstract protected function getTrainingObject();
	
	
	/**
	 * Show the content of the training
	 * This is the command called from ilObjFlashcardsGUI
	 */
	abstract protected function showContent();
	
	
	/**
	 * get a short status text when user is in training
	 * @return string	pure text (no HTML)
	 */
	abstract protected function getTrainingStatusText();
	
	
	/**
	 * Start the training
	 */
	abstract protected function startTraining();
	
	
	/**
	 * cancel the training
	 */
	abstract protected function cancelTraining();
	
	
	/**
	 * Reset the training
	 */
	protected function resetTraining()
	{
		$this->training->reset();
		ilUtil::SendSuccess($this->txt("resetTraining"));
		$this->ctrl->redirect($this, "showContent");
	}
	
	
	
	/**
	 * Show confirmation screen to reset the training
	 */
	protected function ConfirmResetTraining()
	{
		require_once("./Services/Utilities/classes/class.ilConfirmationGUI.php");	
		$gui = new ilConfirmationGUI();
		$gui->setFormAction($this->ctrl->getFormAction($this));
		$gui->setHeaderText($this->txt("reset_training_confirmation"));
		$gui->setConfirm($this->txt("reset_training"), "resetTraining");
		$gui->setCancel($this->lng->txt("cancel"), "showContent");
		$this->tpl->setContent($gui->getHTML());
	}
	
	
	/**
	 * Shows a card
	 * Implemented here to ensure a common look & feel for all training modes
	 * This method needs $_GET["card_id"] to be set
	 */
	protected function showCard()
	{
		$card_id = (int) $_GET["card_id"];
		$this->ctrl->saveParameter($this, "card_id");

		$tpl = $this->plugin->getTemplate("tpl.training_show_card.html");
		
		// show the status text
		if ($text = $this->getTrainingStatusText())
		$tpl->setVariable("TRAINING_STATUS_TEXT", $text);
		
		// show the flashcard
		$this->plugin->includeClass("class.ilFlashcardGUI.php");

        $card = $this->object->getCard($card_id);
        if (!is_object($card))
        {
            $tpl->setVariable("CARD_PRESENTATION", $this->plugin->txt('card_is_deleted'));
        }
        else
        {
            $card_gui = new ilFlashcardGUI($this, $this->object->getCard($card_id));
            $tpl->setVariable("CARD_PRESENTATION", $card_gui->getCardForTrainingHTML());
        }


		// show the training actions for the card
		require_once("./Services/UIComponent/Toolbar/classes/class.ilToolbarGUI.php");
		$toolbar = new ilToolbarGUI();
		$toolbar->setFormAction($this->ctrl->getFormAction($this));
		$toolbar->setPreventDoubleSubmission(true);

		$actions = $this->getCardActions($card_id);
		foreach($actions as $action)
		{
			$button = ilSubmitButton::getInstance();
			$button->setCaption($action["txt"], false);
			$button->setCommand($action["cmd"]);
			$button->setOmitPreventDoubleSubmission(false);
			$toolbar->addButtonInstance($button);
		}
		if (count($actions) > 1)
		{
			$toolbar->addSeparator();
		}

		$button = ilSubmitButton::getInstance();
		$button->setCaption($this->txt("cancel_training"), false);
		$button->setCommand("cancelTraining");
		$button->setOmitPreventDoubleSubmission(false);
		$toolbar->addButtonInstance($button);

		$tpl->setVariable("CARD_ACTIONS", $toolbar->getHTML());
		
		$this->tpl->setContent($tpl->get());
	}
	
	 
	/**
	 * Get a list of actions available when a crad is shown
	 * (should be overwritten in child class)
	 * 
	 * @param	array	id if the shown card
	 * @return  array 	array( array("txt" => label, "cmd" => command), ...)
	 */
	protected function getCardActions($a_card_id)
	{
		return array();
	}
	
	
	 /**
	  * Get a plugin specific text message
	  * @param string 	lanuage variable
	  */
	 final protected function txt($a_txt)
	 {
	 	return $this->plugin->txt($a_txt);
	 }
	
}
?>
