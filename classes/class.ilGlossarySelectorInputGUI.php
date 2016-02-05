<?php
/**
 * Copyright (c) 2016 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg
 * GPLv2, see LICENSE
 */

include_once("./Services/Form/classes/class.ilRepositorySelectorInputGUI.php");

/**
 * extension of the respository selector
 * allows selection of glossary objects
 * 
 * @author Fred Neumann <fred.neumann@fim.uni-erlangen.de>
 *
 * $Id$
 * 
 * @ilCtrl_IsCalledBy ilGlossarySelectorInputGUI: ilFormPropertyDispatchGUI
 */
class ilGlossarySelectorInputGUI extends ilRepositorySelectorInputGUI
{
	
	/**
	* Constructor
	* (extended to make glossary objects clickable)
	*
	* @param	string	$a_title	Title
	* @param	string	$a_postvar	Post Variable
	*/
	function __construct($a_title = "", $a_postvar = "")
	{
		global $lng;
		
		parent::__construct($a_title, $a_postvar);
		
		// extension:
		$this->setClickableTypes(array('glo'));
		// extension.
	}
	

	/**
	* Render item
	* (modified class name in links and respect disabled status)
	*/
	function render($a_mode = "property_form")
	{
		global $lng, $ilCtrl, $ilObjDataCache, $tree;
		
		// modification:
		$tpl = new ilTemplate("tpl.prop_glos_select.html", true, true, "Customizing/global/plugins/Services/Repository/RepositoryObject/Flashcards");
		// modification.
		
		$tpl->setVariable("POST_VAR", $this->getPostVar());
		$tpl->setVariable("ID", $this->getFieldId());
		$tpl->setVariable("PROPERTY_VALUE", ilUtil::prepareFormOutput($this->getValue()));

		// modification:
		if (!$this->getDisabled())
		{
			switch ($a_mode)
			{
				case "property_form":
					$parent_gui = "ilpropertyformgui";
					break;
					
				case "table_filter":
					$parent_gui = get_class($this->getParent());
					break;
			}
	
			$ilCtrl->setParameterByClass("ilglossaryselectorinputgui",
				"postvar", $this->getPostVar());
			
			$tpl->setVariable("TXT_SELECT", $this->getSelectText());
			$tpl->setVariable("HREF_SELECT",
				$ilCtrl->getLinkTargetByClass(array($parent_gui, "ilformpropertydispatchgui", "ilglossaryselectorinputgui"),
				"showRepositorySelection"));
			if ($this->getValue() > 0)
			{
				$tpl->setVariable("TXT_RESET", $lng->txt("reset"));
				$tpl->setVariable("HREF_RESET",
					$ilCtrl->getLinkTargetByClass(array($parent_gui, "ilformpropertydispatchgui", "ilglossaryselectorinputgui"),
				"reset"));
				
			}  	
		}
		// modification.

		if ($this->getValue() > 0 && $this->getValue() != ROOT_FOLDER_ID)
		{
			// modification:
			require_once("Services/Locator/classes/class.ilLocatorGUI.php");
			$loc_gui = new ilLocatorGUI();
			$loc_gui->addContextItems($this->getValue());			
			$tpl->setVariable("TXT_ITEM", $loc_gui->getHTML());
			// modification.
		}
		else
		{
			$nd = $tree->getNodeData(ROOT_FOLDER_ID);
			$title = $nd["title"];
			if ($title == "ILIAS")
			{
				$title = $lng->txt("repository");
			}
			if (in_array($nd["type"], $this->getClickableTypes()))
			{
				$tpl->setVariable("TXT_ITEM", $title);
			}
		}
		return $tpl->get();
	}
	
	
	
	/**
	* Select repository item
	* (extended to allow glossary selection)
	*/
	function showRepositorySelection()
	{
		global $tpl, $lng, $ilCtrl, $tree, $ilUser;
		
		include_once 'Services/Search/classes/class.ilSearchRootSelector.php';
		$ilCtrl->setParameter($this, "postvar", $this->getPostVar());

		ilUtil::sendInfo($this->getHeaderMessage());
		
		$exp = new ilSearchRootSelector($ilCtrl->getLinkTarget($this,'showRepositorySelection'));
		$exp->setExpand($_GET["search_root_expand"] ? $_GET["search_root_expand"] : $tree->readRootId());
		$exp->setExpandTarget($ilCtrl->getLinkTarget($this,'showRepositorySelection'));
		$exp->setTargetClass(get_class($this));
		$exp->setCmd('selectRepositoryItem');
		$exp->setClickableTypes($this->getClickableTypes());
		
		// extension:
		$exp->addFilter('glo');
		// extension.

		// build html-output
		$exp->setOutput(0);
		$tpl->setContent($exp->getOutput());
	}
	
}

?>