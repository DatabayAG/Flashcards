<?php
/**
 * Copyright (c) 2016 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg
 * GPLv2, see LICENSE
 */

/**
* ListGUI implementation for Flashcards plugin. This one
* handles the presentation in container items (categories, courses, ...)
* together with the corresponfing ...Access class.
*
* PLEASE do not create instances of larger classes here. Use the
* ...Access class to get DB data and keep it small.
*
* @author 		Fred Neumann <fred.neumann@fim.uni-erlangen.de>
*/
class ilObjFlashcardsListGUI extends ilObjectPluginListGUI
{
	
	/**
	* Init type
	*/
	function initType()
	{
		$this->setType("xflc");
		$this->copy_enabled = true;
	}
	
	/**
	* Get name of gui class handling the commands
	*/
	public function getGuiClass(): string
	{
		return "ilObjFlashcardsGUI";
	}
	
	/**
	* Get commands
	*/
	public function initCommands(): array
	{
		return array
		(
			array(
				"permission" => "read",
				"cmd" => "showContent",
				"default" => true),
			array(
				"permission" => "write",
				"cmd" => "editProperties",
				"txt" => $this->txt("edit"),
				"default" => false),
		);
	}

	/**
	* Get item properties
	*
	* @return	array		array of property arrays:
	*						"alert" (boolean) => display as an alert property (usually in red)
	*						"property" (string) => property name
	*						"value" (string) => property value
	*/
	public function getProperties(): array
	{
		$props = array();
		
		if (!ilObjFlashcardsAccess::checkOnline($this->obj_id))
		{
			$props[] = array("alert" => true, "property" => $this->txt("status"),
				"value" => $this->txt("offline"));
		}

		return $props;
	}
}
?>
