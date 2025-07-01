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
	private const PLUGIN_PATH = 'public/Customizing/global/plugins/Services/Repository/RepositoryObject/Flashcards';

	function getPluginName(): string
	{
		return "Flashcards";
	}

	/**
	 * Get the title icon
	 * Used for object list, creation, gui
	 * used by info, export and permission tabe
	 */
	public static function _getIcon(string $a_type): string
	{
		return 'assets/images/standard/icon_xflc.svg';
	}

	protected function uninstallCustom(): void
	{
		$this->db->dropTable('rep_robj_xflc_data');
		$this->db->dropTable('rep_robj_xflc_cards');
		$this->db->dropTable('rep_robj_xflc_usage');
	}

	/**
	 * decides if this repository plugin can be copied
	 *
	 * @return bool
	 */
	public function allowCopy(): bool
	{
		return true;
	}

	/**
	 * Get a template of the plugin
	 * @param string $a_template
	 */
	public function getTemplate(string $a_template, bool $a_par1 = true, bool $a_par2 = true): ilTemplate
	{
		return new ilTemplate( $a_template, $a_par1, $a_par2, self::PLUGIN_PATH);
	}
}
?>
