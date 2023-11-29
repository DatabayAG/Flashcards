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
    
    /** @var ilDBInterface */
    private $db;
	
	/**
	 * Constructor
	 *
	 * @param mixed 	card_id or null
	 */
	public function __construct($a_card_id = null)
	{
        global $DIC;
        $this->db = $DIC->database();
        
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
		if (!$this->getCardId())
		{
			$this->setCardId($this->db->nextId("rep_robj_xflc_cards"));		
		}

        $this->db->replace("rep_robj_xflc_cards",
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
		$query = "DELETE from rep_robj_xflc_cards"
				." WHERE card_id = " . $this->db->quote($this->getCardId(), "integer");
        $this->db->manipulate($query);
	}
	
	/**
	 * read the cards data
	 */
	private function read()
	{
		$query = "SELECT card_id, obj_id, term_id FROM rep_robj_xflc_cards ".
		"WHERE card_id = ".$this->db->quote($this->card_id, 'integer');
		$result = $this->db->query($query);
		
		$result = $this->db->query($query);
		if ($row = $this->db->fetchAssoc($result))
		{
			$this->setRowData($row);
			return true;
		}
        return false;
	}
	
	
	/**
	 * Set the properties of this object from a darabase row
	 * 
	 * @param array $a_row
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
	 * @param 	 int 		$a_obj_id
	 * @return   array   	card_id => card object
	 */
	public static function _getAll($a_obj_id)
	{
        global $DIC;
        
		$query = "SELECT card_id, obj_id, term_id FROM rep_robj_xflc_cards ".
		"WHERE obj_id = ". $DIC->database()->quote($a_obj_id, 'integer');
		$result = $DIC->database()->query($query);
	
		$cards = array();
		while ($row = $DIC->database()->fetchAssoc($result))
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
		global $DIC;
		
		$query = "DELETE FROM rep_robj_xflc_cards"
				." WHERE obj_id = " . $DIC->database()->quote($a_obj_id, "integer");
        $DIC->database()->manipulate($query);
		
	}
	
	
	/**
	 * clone all cards
	 * 
	 * @param	integer		source object id
	 * @param	integer		target object id
	 */
	public static function _cloneAll($a_source_id, $a_target_id)
	{
		global $DIC;
		
		$query = " SELECT card_id, obj_id, term_id"
				. " FROM rep_robj_xflc_cards"
				. " WHERE obj_id = " . $DIC->database()->quote($a_source_id, "integer");
		$result = $DIC->database()->query($query);
		while($row = $DIC->database()->fetchAssoc($result))
		{
            $DIC->database()->insert("rep_robj_xflc_cards",
				array( 	"card_id" 	=> array("integer", $DIC->database()->nextId("rep_robj_xflc_cards")),
						"obj_id"	=> array("integer", $a_target_id),
						"term_id"	=> array("integer", $row["term_id"])));
		}
	}

	/**
	 * Map the ids of glossary terms when the object is cloned with its glossary
	 * @param integer	$a_obj_id
	 * @param array		$a_mapping		old_term_id => new_term_id
	 */
	public static function _updateTermIds($a_obj_id, $a_mapping)
	{
		global $DIC;

		foreach ($a_mapping as $old_term_id => $new_term_id)
		{
            $DIC->database()->update('rep_robj_xflc_cards',
				array(
					'term_id' => array('integer', $new_term_id)
				),
				array(
					'obj_id' => array('integer', $a_obj_id),
					'term_id' => array('integer', $old_term_id)
				)
			);
		}
	}
}

?>