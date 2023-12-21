<?php
/**
 * Copyright (c) 2016 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg
 * GPLv2, see LICENSE
 */

/**
* Event listener. Listens to events of other components.
* TODO: in 4.2 it was not possible to register this handler
*
* @author Fred Neumann <fred.neumann@fim.uni-erlangen.de>
* @version $Id$
*/
class ilFlashcardsAppEventListener
{
	/**
	* Handle an event in a listener.
	*
	* @param	string	$a_component	component, e.g. "Modules/Forum" or "Services/User"
	* @param	string	$a_event		event e.g. "createUser", "updateUser", "deleteUser", ...
	* @param	array	$a_parameter	parameter array (assoc), array("name" => ..., "phone_office" => ...)
	*/
	static function handleEvent($a_component, $a_event, $a_parameter)
	{
		switch($a_component)
		{
			case "Services/User":
				switch ($a_event)
				{
					case "deleteUser":
						$user_id = $a_parameter['usr_id'] ?? 0;
						ilFlashcardUsage::_deleteUser($user_id);
						break;
				}
				break;
			}
	}
}
?>
