<?php
/**
 * Copyright (c) 2018 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg
 * GPLv2, see LICENSE
 */

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

	/**
	 * decides if this repository plugin can be copied
	 *
	 * @return bool
	 */
	public function allowCopy()
	{
		return true;
	}

}
?>
