<?php
/**
 * Copyright (c) 2016 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg
 * GPLv2, see LICENSE
 */

/**
 * Class representing a flashcard
 * 
 * 
 * @author Fred Neumann <fred.neumann@fim.uni-erlangen.de>
 * $id$
 */
class ilFlashcardUsage
{
	/**
	 * id of the flashcards object
	 * @var integer
	 */
	private $obj_id;
	
	/**
	 * id of the user
	 * @var integer
	 */
	private $user_id;
	
	/**
	 * id of the flash card
	 * @var integer
	 */
	private $card_id;
	
	/**
	 * Status of the usage (depending on training mode)
	 * @var integer
	 */
	private $status;
	
	
	/**
	 * last of the usage (depending on training mode)
	 * @var integer
	 */
	private $last_status;
	
	/**
	 * Time of the last check
	 * @var object	ilDateTime
	 */
	private $last_checked;
	
	/**
	 * Result of the last check
	 * @var boolean	
	 */
	private $last_result;
	
	
	/**
	 * How many times was the card checked
	 * @var integer
	 */
	private $times_checked;
	
	
	/**
	 * How many times was the card known
	 * @var integer
	 */
	private $times_known;
	
	
	/**
	 * Constructor
	 *
	 * @param mixed 	obj_id or null
	 */
	public function __construct($a_obj_id = null, $a_user_id = null, $a_card_id = null)
	{
		if ($a_obj_id)
		{
			$this->obj_id = $a_obj_id;
		}
		if ($a_user_id)
		{
			$this->user_id = $a_user_id;
		}
		
		if ($a_card_id)
		{
			$this->card_id = $a_card_id;
		}
		
		if ($a_obj_id > 0 and $a_user_id > 0 and $a_card_id > 0)
		{
			$this->read();
		}
	}

	public function setObjId($a_obj_id)
	{
		$this->obj_id = (int) $a_obj_id;
	}
	public function getObjId()
	{
		return (int) $this->obj_id;		
	}
	
	public function setUserId($a_user_id)
	{
		$this->user_id = (int) $a_user_id;
	}
	public function getUserId()
	{
		return (int) $this->user_id;		
	}
	
	public function setCardId($a_card_id)
	{
		$this->card_id = (int) $a_card_id;
	}
	public function getCardId()
	{
		return (int) $this->card_id;		
	}
	
	public function setStatus($a_status)
	{
		$this->status = (int) $a_status;
	}
	public function getStatus()
	{
		return (int) $this->status;		
	}
	
	public function setLastStatus($a_status)
	{
		$this->last_status = (int) $a_status;
	}
	public function getLastStatus()
	{
		return (int) $this->last_status;		
	}
	
	
	public function setLastChecked($a_checked, $a_format = null)
	{
		$this->last_checked = isset($a_format) ? new ilDateTime($a_checked, $a_format): $a_checked;
	}
	public function getLastChecked($a_format = null)
	{
		$checked = isset($this->last_checked) ? $this->last_checked : new ilDateTime();		
		return isset($a_format) ? $checked->get($a_format) : $checked;
	}
	
	public function setLastResult($a_result)
	{
		$this->last_result = (bool) $a_result;
	}
	public function getLastResult()
	{
		return (bool) $this->last_result;		
	}

	public function setTimesChecked($a_times)
	{
		$this->times_checked = (int) $a_times;
	}
	public function getTimesChecked()
	{
		return (int) $this->times_checked;		
	}
	
	public function setTimesKnown($a_times)
	{
		$this->times_known = (int) $a_times;
	}
	public function getTimesKnown()
	{
		return (int) $this->times_known;		
	}
	
	
	/**
	 * save the card 
	 */
	public function save()
	{
		global $ilDB;
		
		$ilDB->replace("rep_robj_xflc_usage",
			array( 	"obj_id" 		=> array("integer", $this->getObjId()),
					"user_id"		=> array("integer", $this->getUserId()),
					"card_id"		=> array("integer", $this->getCardId())),
			array(	"status"		=> array("integer", $this->getStatus()),
					"last_status"	=> array("integer", $this->getLastStatus()),
					"last_checked"	=> array("timestamp", $this->getLastChecked(IL_CAL_DATETIME)),
					"last_result"	=> array("integer",	$this->getLastResult()),
					"times_checked" => array("integer", $this->getTimesChecked()),
					"times_known"	=> array("integer", $this->getTimesKnown()))
			);
	}
	
	
	/**
	 * delete the card
	 */
	public function delete()
	{
		global $ilDB;
		
		$query = "DELETE FROM rep_robj_xflc_usage"
				." WHERE obj_id = " . $ilDB->quote($this->getObjId(), "integer")
				." AND user_id = " . $ilDB->quote($this->getUserId(), "integer")
				." AND card_id = " . $ilDB->quote($this->getCardId(), "integer");
		$ilDB->manipulate($query);
	}
	
	
	/**
	 * read the cards data
	 */
	private function read()
	{
		global $ilDB;
		
		$query = "SELECT obj_id, user_id, card_id, "
				." status, last_status, last_checked, last_result, times_checked, times_known"
				." FROM rep_robj_xflc_usage" 
				." WHERE obj_id = " . $ilDB->quote($this->getObjId(), "integer")
				." AND user_id = " . $ilDB->quote($this->getUserId(), "integer")
				." AND card_id = " . $ilDB->quote($this->getCardId(), "integer");
		
		$result = $ilDB->query($query);
		if ($row = $ilDB->fetchAssoc($result))
		{
			$this->setRowData($row);
			return true;
		}
	}
	
	
	/**
	 * Set the properties of this object from a darabase row
	 * 
	 * @param unknown_type $a_row
	 */
	private function setRowData($a_row = array())
	{
		$this->setObjId($a_row["obj_id"]);
		$this->setUserId($a_row["user_id"]);
		$this->setCardId($a_row["card_id"]);
		$this->setStatus($a_row["status"]);
		$this->setLastStatus($a_row["last_status"]);
		$this->setLastChecked($a_row["last_checked"],IL_CAL_DATETIME);
		$this->setLastResult($a_row["last_result"]);
		$this->setTimesChecked($a_row["times_checked"]);
		$this->setTimesKnown($a_row["times_known"]);
	}
	
	
	/**
	 * get all flashcards for an object
	 *
	 * @param 	 int 		obj_id
	 * @return   array   	card_id => usage object
	 */
	public static function _getAll($a_obj_id, $a_user_id)
	{
		global $ilDB;
		
		$query = "SELECT u.obj_id, u.user_id, u.card_id, "
				." u.status, u.last_status, u.last_checked, u.last_result, u.times_checked, u.times_known "
				." FROM rep_robj_xflc_usage u"
                ." INNER JOIN rep_robj_xflc_cards c ON u.card_id = c.card_id"
				." WHERE u.obj_id = ".$ilDB->quote($a_obj_id, 'integer')
				." AND u.user_id = ".$ilDB->quote($a_user_id, 'integer');
		$result = $ilDB->query($query);
	
		$usages = array();
		while ($row = $ilDB->fetchAssoc($result))
		{
			$usage = new ilFlashcardUsage();
			$usage->setRowData($row);
			$usages[$usage->getCardId()] = $usage;
		}
		return $usages;
	}	


	/**
	 * Count the users who have cards in usage
	 * 
	 * @param 	integer 	flashcard object id
	 * @return 	integer 	number of users 
	 */
	static function _countUsers($a_obj_id)
	{		
		global $ilDB;
		
		$query = "SELECT COUNT(DISTINCT user_id) users FROM rep_robj_xflc_usage"
				. " WHERE obj_id = ". $ilDB->quote($a_obj_id, "integer");		
		
		$result = $ilDB->query($query);
		$row = $ilDB->fetchAssoc($result);
		return $row["users"];
	}
	
	
	/**
	 * delete all usage data of a flashcards object
	 * 
	 * @param integer	flashcards object id
	 */
	static function _deleteAll($a_obj_id)
	{
		global $ilDB;
		$query = "DELETE FROM rep_robj_xflc_usage"
				. " WHERE obj_id = " . $ilDB->quote($a_obj_id, "integer");
		$ilDB->manipulate($query);
	}
	
	/**
	 * delete all usage data of a user
	 * @param integer user id
	 */
	static function _deleteUser($a_user_id)
	{
		global $ilDB;
		$query = "DELETE FROM rep_robj_xflc_usage"
				. " WHERE user_id = " . $ilDB->quote($a_user_id, "integer");
        $ilDB->manipulate($query);
    }


    /**
	 * delete usages data of an object and user
	 * @param integer	flashcards object id
	 * @param integer 	user id
	 */
	static function _deleteUsages($a_obj_id, $a_user_id)
	{
		global $ilDB;
		$query = "DELETE FROM rep_robj_xflc_usage"
				. " WHERE obj_id = " . $ilDB->quote($a_obj_id, "integer")
				. " AND user_id = " . $ilDB->quote($a_user_id, "integer");
		$ilDB->manipulate($query);
	}
	
	
	/** 
	 * cleanup the usage of cards
	 * @param object flashcards object
	 */
	static function _cleanup($a_object)
	{
		global $ilDB;
		
		$card_ids = array_keys($a_object->getCards());

		$query = "SELECT card_id FROM rep_robj_xflc_usage"
				. " WHERE obj_id = ". $ilDB->quote($a_object->getId(), "integer");		
		
		$result = $ilDB->query($query);
		while ($row = $ilDB->fetchObject($result))
		{
			if (!in_array((int) $row->card_id, $card_ids))
			{
				$query = "DELETE FROM rep_robj_xflc_usage"
						. " WHERE card_id = ". $ilDB->quote($row->card_id, "integer");	
			}
		}
	}
}

?>