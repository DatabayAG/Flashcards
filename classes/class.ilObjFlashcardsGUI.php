<?php
/**
 * Copyright (c) 2016 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg
 * GPLv2, see LICENSE
 */

include_once("./Services/Repository/classes/class.ilObjectPluginGUI.php");

/**
* User Interface class for flashcards object.
*
 * @author Fred Neumann <fred.neumann@fim.uni-erlangen.de>
* $Id$
*
* @ilCtrl_isCalledBy ilObjFlashcardsGUI: ilRepositoryGUI, ilAdministrationGUI, ilObjPluginDispatchGUI
* @ilCtrl_Calls ilObjFlashcardsGUI: ilPermissionGUI, ilInfoScreenGUI, ilObjectCopyGUI, ilPropertyFormGUI
* @ilCtrl_Calls ilObjFlashcardsGUI: ilPropertyFormGUI, ilPageObjectGUI
*
*/
class ilObjFlashcardsGUI extends ilObjectPluginGUI
{
	/**
	* Get type.
	*/
	final function getType()
	{
		return "xflc";
	}
	
	
	/**
	* Handles all commmands of this class, centralizes permission checks
	* 
	* @param	string		command to be executed
	* @param	string		(optional) class that should handle the command
	*/
	function performCommand($cmd, $class = "")
	{
		global $ilAccess, $ilTabs;
		
		// add addtitonal styles
		$this->tpl->addCss($this->plugin->getStyleSheetLocation("flashcards.css"));
		
		// handling forwards to other classes
		$next_class = $class ? $class : $this->ctrl->getNextClass();
		switch ($next_class)
		{
			// glossary selection in properties form
			case "ilpropertyformgui":
				
				$this->checkPermission("write");
				$ilTabs->activateTab("properties");	
							
				$this->initPropertiesForm();
				$this->ctrl->setReturn($this, "updateGlossaryRefId");
				$this->ctrl->forwardCommand($this->form);
				return;	
				
			// fullscreen link in page presentation
			case "ilpageobjectgui":
				
				$this->checkPermission("read");
				$ilTabs->activateTab("content");
				
				require_once("./Services/COPage/classes/class.ilPageObjectGUI.php");
				require_once("./Services/Style/classes/class.ilObjStyleSheet.php");
				$page_gui = new ilPageObjectGUI("gdf", $_GET["pg_id"]);
				$this->ctrl->forwardCommand($page_gui);
				return;
				
			// common checks for all training classes (continues switch)	
			case "illeitnertraininggui":
			case "ilsupermemotraininggui":
				
				$this->checkPermission("read");
				$ilTabs->activateTab("content");

				if (!$ilAccess->checkAccess("read", "", $this->object->getGlossaryRefId()))
				{
					ilUtil::sendFailure($this->txt("glossary_not_readable"));
				}				

				// include the base classes extend by the specific training modes
				$this->plugin->includeClass('class.ilFlashcardsTrainingGUI.php');
				$this->plugin->includeClass('class.ilFlashcardsTraining.php');
				
			// Leitner training 	
			case "illeitnertraininggui":
				$this->plugin->includeClass('Leitner/class.ilLeitnerTrainingGUI.php');
				$training_gui = new ilLeitnerTrainingGUI($this);
				$this->ctrl->setReturn($this, "showContent");	
				$this->ctrl->forwardCommand($training_gui);	
				return;	

			// SuperMemo training (TODO)
			case "ilsupermemotraininggui":
				

			// show unknown next class	
			default: 
				if ($next_class)
				{
					$this->tpl->setContent($next_class);
					return;
				}
		}
		
		// handling commands for this class
		$cmd = $cmd ? $cmd : $this->ctrl->getCmd();
		switch ($cmd)
		{
			// properties (author)
			case "editProperties":		
			case "updateProperties":
			case "updateGlossaryRefId":
			case "updateCardsFromGlossary":

				$this->checkPermission("write");
				$ilTabs->activateTab("properties");
				
				$this->initPropertiesForm();
				$this->$cmd();
				return;
				
			// training (learner)
			case "showContent":
				
				// choose the training mode from users preferences (TODO)
				// currently only the leitner training is implemented
				$class = "illeitnertraininggui";
				
				// call the gui class for the chosen training mode
				$this->performCommand($cmd, $class);			
				return;

			// unknown commands	
			default:
				$this->tpl->setContent($cmd);
				return;
		}
	}

	
	/**
	* After object has been created -> jump to this command
	*/
	function getAfterCreationCmd()
	{
		return "editProperties";
	}

	/**
	* Get standard command
	*/
	function getStandardCmd()
	{
		return "showContent";
	}
	
	
	/**
	* Set tabs
	*/
	function setTabs()
	{
		global $ilAccess, $ilTabs, $ilHelp;

		$ilHelp->setScreenIdComponent("xflc");
		
		// tab for the "show content" command
		if ($ilAccess->checkAccess("read", "", $this->object->getRefId()))
		{
			$ilTabs->addTab("content", $this->txt("content"), $this->ctrl->getLinkTarget($this, "showContent"));
		}

		// standard info screen tab
		$this->addInfoTab();

		// a "properties" tab
		if ($ilAccess->checkAccess("write", "", $this->object->getRefId()))
		{
			$ilTabs->addTab("properties", $this->txt("properties"), $this->ctrl->getLinkTarget($this, "editProperties"));
		}

		// standard permission tab
		$this->addPermissionTab();
	}
	

	/**
	* Edit Properties. This commands uses the form class to display an input form.
	*/
	private function editProperties()
	{
		$this->getPropertiesValues();
		
		if (!$this->object->getGlossaryRefId())
		{
			ilUtil::sendFailure($this->txt("select_glossary"));
		}
		$this->tpl->setContent($this->form->getHTML());
	}
	
	
	/**
	* Init  form.
	*
	* @param        int        $a_mode        Edit Mode
	*/
	private function initPropertiesForm()
	{
		include_once("Services/Form/classes/class.ilPropertyFormGUI.php");
		$this->form = new ilPropertyFormGUI();
	
		// title
		$ti = new ilTextInputGUI($this->txt("title"), "title");
		$ti->setRequired(true);
		$this->form->addItem($ti);
		
		// description
		$ta = new ilTextAreaInputGUI($this->txt("description"), "description");
		$this->form->addItem($ta);
		
		// online
		$cb = new ilCheckboxInputGUI($this->lng->txt("online"), "online");
		$this->form->addItem($cb);
		
		// glossary ref_id
		$this->plugin->includeClass('class.ilGlossarySelectorInputGUI.php');		
		$rs = new ilGlossarySelectorInputGUI($this->txt("glossary_selection"), "glossary_ref_id");
		$rs->setRequired(true);
		$rs->setInfo($this->txt("glossary_selection_info"));
		$rs->setHeaderMessage($this->txt("select_glossary"));
		$this->plugin->includeClass('class.ilFlashcardsTraining.php');
		if ($this->object->countUsers() > 0)
		{
			$rs->setDisabled(true);
		}
		$this->form->addItem($rs);
		
		// training mode
		$rg = new ilRadioGroupInputGUI($this->txt("glossary_mode"), "glossary_mode");
		$rg->setRequired(true);
		$rg->addOption(new ilRadioOption($this->txt('glossary_mode_term_definitions'),
								ilObjFlashcards::GLOSSARY_MODE_TERM_DEFINITIONS,
								$this->txt('glossary_mode_term_definitions_info')));
		$rg->addOption(new ilRadioOption($this->txt('glossary_mode_definition_term'),
								ilObjFlashcards::GLOSSARY_MODE_DEFINITION_TERM,
								$this->txt('glossary_mode_definition_term_info')));
		$rg->addOption(new ilRadioOption($this->txt('glossary_mode_definitions'),
								ilObjFlashcards::GLOSSARY_MODE_DEFINITIONS,
								$this->txt('glossary_mode_definitions_info')));
		$this->form->addItem($rg);
								
		// instructions
		$ta = new ilTextAreaInputGUI($this->txt("instructions"), "instructions");
		$ta->setInfo($this->txt("instructions_info"));
		$this->form->addItem($ta);
		
		$this->form->addCommandButton("updateProperties", $this->txt("save"));
		$this->form->addCommandButton("updateCardsFromGlossary", $this->txt("update_from_glossary"));
		
		
		$this->form->setTitle($this->txt("edit_properties"));
		$this->form->setFormAction($this->ctrl->getFormAction($this));
	}
	
	/**
	* Get values for edit properties form
	*/
	private function getPropertiesValues()
	{
		$values = array();
		$values["title"] = $this->object->getTitle();
		$values["description"] = $this->object->getDescription();
		$values["online"] = $this->object->getOnline();
		$values["glossary_ref_id"] = $this->object->getGlossaryRefId();
		$values["glossary_mode"] = $this->object->getGlossaryMode();
		$values["instructions"] = $this->object->getInstructions();
		$this->form->setValuesByArray($values);
	}
	
	/**
	* Update properties
	*/
    private function updateProperties()
	{
		if ($this->form->checkInput())
		{
			$this->object->setTitle($this->form->getInput("title"));
			$this->object->setDescription($this->form->getInput("description"));
			$this->object->setOnline($this->form->getInput("online"));
			$this->object->setGlossaryRefId($this->form->getInput("glossary_ref_id"));
			$this->object->setGlossaryMode($this->form->getInput("glossary_mode"));
			$this->object->setInstructions($this->form->getInput("instructions"));
			$this->object->update();
			ilUtil::sendSuccess($this->lng->txt("msg_obj_modified"), true);
			$this->ctrl->redirect($this, "editProperties");
		}
		else
		{
			$this->form->setValuesByPost();
			$this->tpl->setContent($this->form->getHtml());
		}
	}
	
	
	/**
	* Update the selected glossary reference
	*/
	private function updateGlossaryRefId()
	{
		$input = $this->form->getItemByPostVar('glossary_ref_id');
		$input->readFromSession();
		$this->object->setGlossaryRefId($input->getValue());
		$this->object->update();
		$this->object->updateCardsFromGlossary();
		ilUtil::sendSuccess($this->lng->txt("msg_obj_modified"), true);
		$this->ctrl->redirect($this, "editProperties");
	}
	
	/**
	 * Update the flashcards from the glossary
	 * Adds new glossary terms to the flashcards
	 */
	private function updateCardsFromGlossary()
	{
		$this->object->updateCardsFromGlossary();
		ilUtil::sendSuccess($this->txt("synchronized_with_glossary"), true);
		$this->ctrl->redirect($this, "editProperties");
	}
}
?>
