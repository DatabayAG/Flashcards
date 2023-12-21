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
	 * @var ilDateTime
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
	
	
    /** @var ilDBInterface */
    private $db;
    
	/**
	 * Constructor
	 *
	 * @param mixed 	obj_id or null
	 */
	public function __construct($a_obj_id = null, $a_user_id = null, $a_card_id = null)
	{
        global $DIC;
        
        $this->db = $DIC->database();
        
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
	
	
	public function setLastChecked(ilDateTime $a_checked)
	{
		$this->last_checked = $a_checked;
	}

	public function getLastChecked() : ilDateTime
	{
		return $this->last_checked ?? new ilDateTime();		
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
		$this->db->replace("rep_robj_xflc_usage",
			array( 	"obj_id" 		=> array("integer", $this->getObjId()),
					"user_id"		=> array("integer", $this->getUserId()),
					"card_id"		=> array("integer", $this->getCardId())),
			array(	"status"		=> array("integer", $this->getStatus()),
					"last_status"	=> array("integer", $this->getLastStatus()),
					"last_checked"	=> array("timestamp", $this->getLastChecked()->get(IL_CAL_DATETIME)),
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
		$query = "DELETE FROM rep_robj_xflc_usage"
				." WHERE obj_id = " . $this->db->quote($this->getObjId(), "integer")
				." AND user_id = " . $this->db->quote($this->getUserId(), "integer")
				." AND card_id = " . $this->db->quote($this->getCardId(), "integer");
        $this->db->manipulate($query);
	}
	
	
	/**
	 * read the cards data
	 */
	private function read()
	{
		$query = "SELECT obj_id, user_id, card_id, "
				." status, last_status, last_checked, last_result, times_checked, times_known"
				." FROM rep_robj_xflc_usage" 
				." WHERE obj_id = " . $this->db->quote($this->getObjId(), "integer")
				." AND user_id = " . $this->db->quote($this->getUserId(), "integer")
				." AND card_id = " . $this->db->quote($this->getCardId(), "integer");
		
		$result = $this->db->query($query);
		if ($row = $this->db->fetchAssoc($result))
		{
			$this->setRowData($row);
			return true;
		}
	}
	
	
	/**
	 * Set the properties of this object from a darabase row
	 * 
	 * @param array $a_row
	 */
	private function setRowData($a_row = array())
	{
		$this->setObjId($a_row["obj_id"] ?? 0);
		$this->setUserId($a_row["user_id"] ?? 0);
		$this->setCardId($a_row["card_id"] ?? 0);
		$this->setStatus($a_row["status"] ?? null);
		$this->setLastStatus($a_row["last_status"] ?? null);
		$this->setLastChecked(new ilDateTime(($a_row["last_checked"] ?? null),IL_CAL_DATETIME));
		$this->setLastResult($a_row["last_result"] ?? null);
		$this->setTimesChecked($a_row["times_checked"] ?? null);
		$this->setTimesKnown($a_row["times_known"] ?? null);
	}
	
	
	/**
	 * get all flashcards for an object
	 *
	 * @param 	 int 		$a_obj_id
     * @param 	 int 		$a_user_id
	 * @return   array   	card_id => usage object
	 */
	public static function _getAll($a_obj_id, $a_user_id)
	{
		global $DIC;
        $db = $DIC->database();
		
		$query = "SELECT u.obj_id, u.user_id, u.card_id, "
				." u.status, u.last_status, u.last_checked, u.last_result, u.times_checked, u.times_known "
				." FROM rep_robj_xflc_usage u"
                ." INNER JOIN rep_robj_xflc_cards c ON u.card_id = c.card_id"
				." WHERE u.obj_id = ".$db->quote($a_obj_id, 'integer')
				." AND u.user_id = ".$db->quote($a_user_id, 'integer');
		$result = $db->query($query);
	
		$usages = array();
		while ($row = $db->fetchAssoc($result))
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
        global $DIC;
        $db = $DIC->database();
		
		$query = "SELECT COUNT(DISTINCT user_id) users FROM rep_robj_xflc_usage"
				. " WHERE obj_id = ". $db->quote($a_obj_id, "integer");		
		
		$result = $db->query($query);
		$row = $db->fetchAssoc($result);
		return $row["users"] ?? 0;
	}
	
	
	/**
	 * delete all usage data of a flashcards object
	 * 
	 * @param integer	flashcards object id
	 */
	static function _deleteAll($a_obj_id)
	{
        global $DIC;
        $db = $DIC->database();
        
		$query = "DELETE FROM rep_robj_xflc_usage"
				. " WHERE obj_id = " . $db->quote($a_obj_id, "integer");
        $db->manipulate($query);
	}
	
	/**
	 * delete all usage data of a user
	 * @param integer user id
	 */
	static function _deleteUser($a_user_id)
	{
        global $DIC;
        $db = $DIC->database();
        
		$query = "DELETE FROM rep_robj_xflc_usage"
				. " WHERE user_id = " . $db->quote($a_user_id, "integer");
        $db->manipulate($query);
    }


    /**
	 * delete usages data of an object and user
	 * @param integer	flashcards object id
	 * @param integer 	user id
	 */
	static function _deleteUsages($a_obj_id, $a_user_id)
	{
        global $DIC;
        $db = $DIC->database();
        
		$query = "DELETE FROM rep_robj_xflc_usage"
				. " WHERE obj_id = " . $db->quote($a_obj_id, "integer")
				. " AND user_id = " . $db->quote($a_user_id, "integer");
        $db->manipulate($query);
	}
	
	
	/** 
	 * cleanup the usage of cards
	 * @param object flashcards object
	 */
	static function _cleanup($a_object)
	{
        global $DIC;
        $db = $DIC->database();
		
		$card_ids = array_keys($a_object->getCards());

		$query = "SELECT card_id FROM rep_robj_xflc_usage"
				. " WHERE obj_id = ". $db->quote($a_object->getId(), "integer");		
		
		$result = $db->query($query);
		while ($row = $db->fetchObject($result))
		{
			if (!in_array((int) $row->card_id, $card_ids))
			{
				$query = "DELETE FROM rep_robj_xflc_usage"
						. " WHERE card_id = ". $db->quote($row->card_id, "integer");	
			}
		}
	}
}

?>