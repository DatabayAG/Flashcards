<?php
/**
 * Copyright (c) 2016 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg
 * GPLv2, see LICENSE
 */
 
/**
 * Flashcards configuration user interface class
 *
 * @author Fred Neumann <fred.neumann@gmx.de>
 * @version $Id$
 *
 * @ilCtrl_IsCalledBy ilFlashcardsConfigGUI: ilObjComponentSettingsGUI
 */
class ilFlashcardsConfigGUI extends ilPluginConfigGUI
{
	protected ilGlobalTemplateInterface $tpl;
	
	public function __construct()
	{
		global $DIC;
		$this->tpl = $DIC->ui()->mainTemplate();
	}
	
	/**
	* Handles all commmands, default is "configure"
	*/
	function performCommand(string $cmd): void
	{
		switch ($cmd)
		{
			case "configure":
				$this->$cmd();
				break;
		}
	}

	/**
	 * Configure screen
	 */
	function configure()
	{
		$pl = $this->getPluginObject();
		$this->tpl->setOnScreenMessage('info', $pl->txt("nothing_to_configure"), false);
	}
}
?>
