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
class ilFlashcard
{
	/**
	 * id of the flash card
	 * @var integer
	 */
	private $card_id;
	
	/**
	 * id if the flashcards object
	 * @var integer
	 */
	private $obj_id;
	
	
	/**
	 * id of a glossary term used for this card
	 * @var integer
	 */
	private $term_id;
	
	/**
	 * Constructor
	 *
	 * @param mixed 	card_id or null
	 */
	public function __construct($a_card_id = null)
	{
		if ($a_card_id)
		{
			$this->card_id = $a_card_id;
			$this->read();
		}
	}

	public function setCardId($a_card_id)
	{
		$this->card_id = (int) $a_card_id;
	}
	public function getCardId()
	{
		return (int) $this->card_id;		
	}
	
	
	public function setObjId($a_obj_id)
	{
		$this->obj_id = (int) $a_obj_id;
	}
	public function getObjId()
	{
		return (int) $this->obj_id;		
	}
	
	
	public function setTermId($a_term_id)
	{
		$this->term_id = (int) $a_term_id;
	}
	public function getTermId()
	{
		return (int) $this->term_id;		
	}

	
	/**
	 * save the card 
	 */
	public function save()
	{
		global $ilDB;
		
		if (!$this->getCardId())
		{
			$this->setCardId($ilDB->nextId("rep_robj_xflc_cards"));		
		}
		
		$ilDB->replace("rep_robj_xflc_cards",
			array( 	"card_id" 	=> array("integer", $this->getCardId())),
			array(	"obj_id"	=> array("integer", $this->getObjId()),
					"term_id"	=> array("integer", $this->getTermId()))
		);
	}
	
	/**
	 * delete the card
	 */
	public function delete()
	{
		global $ilDB;
		
		$query = "DELETE from rep_robj_xflc_cards"
				." WHERE card_id = " . $ilDB->quote($this->getCardId(), "integer");
		$ilDB->manipulate($query);
	}
	
	/**
	 * read the cards data
	 */
	private function read()
	{
		$query = "SELECT card_id, obj_id, term_id FROM rep_robj_xflc_cards ".
		"WHERE card_id = ".$ilDB->quote($this->card_id, 'integer');
		$result = $ilDB->query($query);
		
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
		$this->setCardId($a_row["card_id"]);
		$this->setObjId($a_row["obj_id"]);
		$this->setTermId($a_row["term_id"]);
	}
	
	
	/**
	 * get all flashcards for an object
	 *
	 * @param 	 int 		obj_id
	 * @return   array   	card_id => card object
	 */
	public static function _getAll($a_obj_id)
	{
		global $ilDB;
		
		$query = "SELECT card_id, obj_id, term_id FROM rep_robj_xflc_cards ".
		"WHERE obj_id = ".$ilDB->quote($a_obj_id, 'integer');
		$result = $ilDB->query($query);
	
		$cards = array();
		while ($row = $ilDB->fetchAssoc($result))
		{
			$card = new ilFlashcard();
			$card->setRowData($row);
			$cards[$card->card_id] = $card;
		}
		return $cards;
	}	


	/**
	 * delete all cards of an object
	 * 
	 * @param integer obj_id
	 */
	public static function _deleteAll($a_obj_id)
	{
		global $ilDB;
		
		$query = "DELETE FROM rep_robj_xflc_cards"
				." WHERE obj_id = " . $ilDB->quote($a_obj_id, "integer");
		$ilDB->manipulate($query);
		
	}
	
	
	/**
	 * clone all cards
	 * 
	 * @param	integer		source object id
	 * @param	integer		target object id
	 */
	public static function _cloneAll($a_source_id, $a_target_id)
	{
		global $ilDB;
		
		$query = " SELECT card_id, obj_id, term_id"
				. " FROM rep_robj_xflc_cards"
				. " WHERE obj_id = " . $ilDB->quote($a_source_id, "integer");
				
		$result = $ilDB->query($query);
		while($row = $ilDB->fetchAssoc($result))
		{
			$ilDB->insert("rep_robj_xflc_cards",
				array( 	"card_id" 	=> array("integer", $ilDB->nextId("rep_robj_xflc_cards")),
						"obj_id"	=> array("integer", $a_target_id),
						"term_id"	=> array("integer", $row["term_id"])));

			$ilDB->manipulate($query);
		}
	}
}

?>