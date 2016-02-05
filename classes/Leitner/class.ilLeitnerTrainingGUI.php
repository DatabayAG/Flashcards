<?php
/**
 * Copyright (c) 2016 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg
 * GPLv2, see LICENSE
 */

/**
 * User interface for a flashcards training based on leitner 
 *
 * @author Fred Neumann <fred.neumann@fim.uni-erlangen.de>
 * $Id$
 * 
 * @ilCtrl_isCalledBy ilLeitnerTrainingGUI: ilObjFlashcardsGUI
 */
class ilLeitnerTrainingGUI extends ilFlashcardsTrainingGUI
{
	/**
	 * execute a command
	 * note: permissions are already checked in ilObjFlashcardsGUI
	 */
	public function executeCommand()	
	{
		$cmd = $this->ctrl->getCmd();
		$cmd = $cmd ? $cmd : "showContent";
		$this->$cmd();
	}
	
	
	/**
	 * Init the training object
	 */
	protected function getTrainingObject()
	{
		global $ilUser;
		$this->plugin->includeClass("Leitner/class.ilLeitnerTraining.php");
		return new ilLeitnerTraining($this->object, $ilUser);
	}
	
	
	/**
	* Show content overview
	*/
	protected function showContent()
	{
		// preparation
		$content = $this->plugin->getTemplate("tpl.leitner_overview.html");
		
		// instructions
		if ($instructions = $this->object->getInstructions())
		{
			$content->setVariable("INSTRUCTIONS", nl2br(ilUtil::makeClickable($instructions, true)));
		}
		
		// table with boxes and commands
		$this->plugin->includeClass("Leitner/class.ilLeitnerTableGUI.php");
		$table = new ilLeitnerTableGUI($this, "showContent");
		$table->setData($this->training->getOverviewData());
		
		$unused_cards = $this->training->countUnusedCards();
		if ($unused_cards > 0 and !$this->training->isStartMaxReached())
		{
			$table->addCommandButton("fillTraining", $this->txt("leitner_fill_training"));
		}
		if ($used_cards = $this->training->countUsedCards())
		{
			$table->addCommandButton("confirmResetTraining", $this->txt("reset_training"));
		}
		$content->setVariable("TABLE", $table->getHTML());

		// message for untrained cards
		if ($unused_cards > 0)
		{
			$content->setVariable("UNTRAINED", 
					sprintf($this->txt($unused_cards == 1 ? 
								"untrained_1" : "untrained_x"), $unused_cards));
			
			$content->setVariable("UNTRAINED_HINT", 
					$this->txt($this->training->isStartMaxReached() ? 
								"leitner_hint_train_startbox" : "leitner_hint_fill_startbox"));
		}
		
		// explanation
		$content->setVariable("EXPLANATION", $this->txt("leitner_explanation"));
		
		// set the output
		$this->tpl->setContent($content->get());
	}

	
	/**
	 * Fill the training with items from the glossary
	 */
	protected function fillTraining()
	{
		if ($this->training->isStartMaxReached())
		{
			ilUtil::sendInfo(sprintf($this->txt("leitner_startbox_full"), $this->training->getStartMax()), false);
		}
		else
		{
			$filled = $this->training->fillStartbox();
			ilUtil::sendSuccess(sprintf($this->txt("leitner_startbox_filled"), $filled), false);
		}
		
		$this->showContent();
	}	
	
	
	/**
	 * Start the training
	 */
	protected function startTraining()
	{
		$box = (int) $_GET["box"];
		if ($this->training->initTrainingSession($box))
		{
			$card_id = $this->training->getNextCardId();
			$this->ctrl->setParameter($this, "card_id", $card_id);
			$this->ctrl->redirect($this, "showCard");
		}
		else
		{
			ilUtil::sendFailure(sprintf($this->txt("leitner_training_not_available"), $box), false);
			$this->showContent();
		}
	}
	

	/** 
	 * Cancel the training
	 */ 
	protected function cancelTraining()
	{
		ilUtil::sendInfo($this->txt("leitner_training_canceled"), false);
		$this->showContent();
	}
	
	/**
	 * Get the status text in a training session
	 */
	protected function getTrainingStatusText()
	{
		$to_train = $this->training->countSessionToTrain();
		$trained = $this->training->countSessionTrained();
		
		return sprintf($this->txt("leitner_status_text"), $trained + 1, $trained + $to_train); 
	}
	
	
	/**
	 * Get the actions available for a card
	 */
	protected function getCardActions($a_card_id)
	{	
		return array(
			array("txt" => $this->txt("leitner_set_known"), "cmd" => "setCardKnown"),
			array("txt" => $this->txt("leitner_set_difficult"), "cmd" => "setCardDifficult"),
			array("txt" => $this->txt("leitner_set_not_known"), "cmd" => "setCardNotKnown")
		);
	}
	

	/**
	 * set the presented card as known
	 */
	protected function setCardKnown()
	{
		$this->setCardChecked((int) $_GET["card_id"], "known");	
	}
	
	
	/**
	 * set the presented card as difficult
	 */
	protected function setCardDifficult()
	{
		$this->setCardChecked((int) $_GET["card_id"], "difficult");	
	}
	
	
	/**
	 * set the presented card as not_known
	 */
	protected function setCardNotKnown()
	{
		$this->setCardChecked((int) $_GET["card_id"], "not_known");	
	}
	
	
	/**
	 * Set the checed status of the presented card and show the next card
	 * 
	 * @param integer	card id
	 * @param string	"known", "difficult" or "not_known"
	 */
	protected function setCardChecked($a_card_id, $a_result)
	{
		$this->training->setCardChecked($a_card_id, $a_result);
		
		if ($card_id = $this->training->getNextCardId())
		{
			$this->ctrl->setParameter($this, "card_id", $card_id);
			$this->ctrl->redirect($this, "showCard");
		}
		else
		{
			ilUtil::sendSuccess($this->txt("leitner_training_finished"), true);
			$this->ctrl->redirect($this, "showContent");
		}
	}
	
	
	
}
?>