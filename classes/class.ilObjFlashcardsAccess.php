<?php
/**
 * Copyright (c) 2018 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg
 * GPLv2, see LICENSE
 */

/**
* Access/Condition checking for Flashcards Training object
*
* @author 		Fred Neumann <fred.neumann@fim.uni-erlangen.de>
*/
class ilObjFlashcardsAccess extends ilObjectPluginAccess
{

	/**
	* Checks wether a user may invoke a command or not
	* (this method is called by ilAccessHandler::checkAccess)
	*
	* Please do not check any preconditions handled by
	* ilConditionHandler here. Also don't do usual RBAC checks.
	*
	* @param	string		$a_cmd			command (not permission!)
 	* @param	string		$a_permission	permission
	* @param	int			$a_ref_id		reference id
	* @param	int			$a_obj_id		object id
	* @param	int			$a_user_id		user id (if not provided, current user is taken)
	*
	* @return	boolean		true, if everything is ok
	*/
	function _checkAccess(string $a_cmd, string $a_permission, int $a_ref_id, int $a_obj_id, ?int $a_user_id = null): bool
	{
		global $DIC;

		if (empty($a_user_id))
		{
			$a_user_id = $DIC->user()->getId();
		}

		switch ($a_permission)
		{
			case "visible":
			case "read":
				if (!self::checkOnline($a_obj_id) &&
					!$DIC->access()->checkAccessOfUser($a_user_id, "write", "", $a_ref_id))
				{
					return false;
				}
				break;
		}

		return true;
	}
	
	/**
	* Check online status of example object
	*/
	static function checkOnline($a_id)
	{
		global $DIC;
		$db = $DIC->database();
		
		$set = $db->query("SELECT is_online FROM rep_robj_xflc_data ".
			" WHERE obj_id = ".$db->quote($a_id, "integer")
			);
		$rec  = $db->fetchAssoc($set);
		return (boolean) ($rec["is_online"] ?? false);
	}
	
}

?>
