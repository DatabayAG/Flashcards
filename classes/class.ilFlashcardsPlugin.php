<?php
/**
 * Copyright (c) 2016 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg
 * GPLv2, see LICENSE
 */


include_once("./Services/Repository/classes/class.ilRepositoryObjectPlugin.php");
 
/**
* Flashcards training object plugin
*
* @author Fred Neumann <frd.neumann@gmx.de>
* @version $Id$
*
*/
class ilFlashcardsPlugin extends ilRepositoryObjectPlugin
{
	function getPluginName()
	{
		return "Flashcards";
	}


	protected function uninstallCustom()
	{
		global $ilDB;

		$ilDB->dropTable('rep_robj_xflc_data');
		$ilDB->dropTable('rep_robj_xflc_cards');
		$ilDB->dropTable('rep_robj_xflc_usage');
	}
}
?>
