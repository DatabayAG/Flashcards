<?php
/**
 * Copyright (c) 2018 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg
 * GPLv2, see LICENSE
 */

/**
* Application class for flashcards training object.
*
* @author Fred Neumann <fred.neumann@fim.uni-erlangen.de>
*
* $Id$
*/
class ilObjFlashcards extends ilObjectPlugin
{
	/**
	 * Mode: show term ans ask for definitions 
	 */
	const GLOSSARY_MODE_TERM_DEFINITIONS = 'term_def';
	
	/**
	 * Mode: show definition 1 and ask for term
	 */
	const GLOSSARY_MODE_DEFINITION_TERM = 'def_term';
	
	/**
	 * Mode: show first definition and ask for others
	 */
	const GLOSSARY_MODE_DEFINITIONS = 'defs';

	/**
	 * online status of the training
	 * @var boolean
	 */
	private $online = false;
	
	/**
	 * ref_id of the glossary used for the training
	 * @var integer
	 */
	private $glossary_ref_id = 0;
	
	/**
	 * Initial training mode
	 * @var integer
	 */
	private $glossary_mode = self::GLOSSARY_MODE_TERM_DEFINITIONS;
	
	/**
	 * Instructions given to the student
	 * @var string
	 */
	private $instructions = "";
	
	
	/**
	 * List of flashcards in this training
	 * 
	 * @var 	array		card_id => ilFlashcard	
	 */
	private $cards = array();
	
	/**
	* Constructor
	*
	* @access	public
	*/
	function __construct($a_ref_id = 0)
	{
		parent::__construct($a_ref_id);
	}
	

	/**
	* Get type.
	*/
	final function initType(): void
	{
		$this->setType("xflc");
	}

	/**
	 * Get the plugin (made public)
	 * @return object
	 * @throws ilPluginException
	 */
	public function getMyPlugin()
	{
		return $this->plugin;
	}

	/**
	* Create object
	*/
	function doCreate(bool $clone_mode = false): void
	{
		$this->db->insert('rep_robj_xflc_data', 
			array(	'obj_id' => array('integer', $this->getId()),
					'is_online' => array('integer', $this->getOnline()),
					'glossary_ref_id' => array('integer', $this->getGlossaryRefId()),
					'glossary_mode' => array('text', self::GLOSSARY_MODE_TERM_DEFINITIONS),
					'instructions' => array('clob', $this->getInstructions())
			));
	}
	
	/**
	* Read data from db
	*/
	function doRead(): void
	{
		$set = $this->db->query(
			'SELECT obj_id, is_online, glossary_ref_id, glossary_mode, instructions '.
			'FROM rep_robj_xflc_data '.
			'WHERE obj_id = '. $this->db->quote($this->getId(), "integer")
			);
		while ($rec = $this->db->fetchAssoc($set))
		{
			$this->setOnline($rec["is_online"] ?? false);
			$this->setGlossaryRefId($rec["glossary_ref_id"] ?? null);
			$this->setGlossaryMode($rec["glossary_mode"] ?? self::GLOSSARY_MODE_TERM_DEFINITIONS);
			$this->setInstructions($rec["instructions"] ?? null);
		}
		
		// read the cards of this object
		$this->readCards();
	}
	
	/**
	* Update data
	*/
	function doUpdate(): void
	{
		$this->db->update('rep_robj_xflc_data', 
			array(	'obj_id' => array('integer', $this->getId()),
					'is_online' => array('integer', $this->getOnline()),
					'glossary_ref_id' => array('integer', $this->getGlossaryRefId()),
					'glossary_mode' => array('text', $this->getGlossaryMode()),
					'instructions' => array('clob', $this->getInstructions())
			),
			array(	'obj_id' => array('integer', $this->getId()))
		);
	}
	
	/**
	* Delete data from db
	*/
	function doDelete(): void
	{
		$this->db->manipulate(
			'DELETE FROM rep_robj_xflc_data '.
			'WHERE obj_id = '.$this->db->quote($this->getId(), 'integer')
			);
			
		ilFlashcard::_deleteAll($this->getId());
		ilFlashcardUsage::_deleteAll($this->getId());	
	}
	
	/**
	* Do Cloning
	*/
	protected function doCloneObject(ilObject2 $new_obj, int $a_target_id, ?int $a_copy_id = null): void
	{
		$new_obj->setOnline($this->getOnline());
		$new_obj->setGlossaryRefId($this->getGlossaryRefId());
		$new_obj->setGlossaryMode($this->getGlossaryMode());
		$new_obj->setInstructions($this->getInstructions());
		$new_obj->update();
		
		ilFlashcard::_cloneAll($this->getId(), $new_obj->getId());
	}

	/**
	 * Clone dependencies
	 * This updates the glossary ref_id and term_ids
	 * It needs a patch in ilObjGlossary::cloneObject()
	 *
	 * @param integer $a_target_id
	 * @param integer $a_copy_id
	 * @return bool
	 */
	public function cloneDependencies(int $a_target_id, int $a_copy_id): bool
	{
		parent::cloneDependencies($a_target_id,$a_copy_id);

		// note: $this is the original object

		$cp_options = ilCopyWizardOptions::_getInstance($a_copy_id);
		if(!$cp_options->isRootNode($this->getRefId()))
		{
			$mappings = $cp_options->getMappings();
			if (isset($mappings[$this->getGlossaryRefId()]))
			{
				$new_obj_id = ilObject::_lookupObjId($a_target_id);
				$this->db->update('rep_robj_xflc_data',
					array('glossary_ref_id' => array('integer', $mappings[$this->getGlossaryRefId()])),
					array('obj_id' => array('integer', $new_obj_id))
				);

				if (!empty($mappings[$this->getGlossaryRefId().'_glo_terms']))
				{
					ilFlashcard::_updateTermIds($new_obj_id, $mappings[$this->getGlossaryRefId().'_glo_terms']);
				}
			}
		}
		return true;
	}


	/**
	* Set online
	*
	* @param	boolean		online
	*/
	function setOnline($a_val)
	{
		$this->online = (boolean) $a_val;
	}
	
	/**
	* Get online
	*
	* @return	boolean		online
	*/
	function getOnline()
	{
		return $this->online;
	}
	
	/**
	* Set glossary ref_id
	*
	* @param	int		glossary ref_id
	*/
	function setGlossaryRefId($a_val)
	{
		$this->glossary_ref_id = (int) $a_val;
	}
	
	/**
	* Get glossary ref_id
	*
	* @return	int		glossary ref_id
	*/
	function getGlossaryRefId()
	{
		return (int) $this->glossary_ref_id;
	}
	
	/**
	* Set glossary mode
	*
	* @param	string		glossary mode
	*/
	function setGlossaryMode($a_val)
	{
		$this->glossary_mode = (string) $a_val;
	}
	
	/**
	* Get glossary mode
	*
	* @return	string		glossary mode
	*/
	function getGlossaryMode()
	{
		return (string) $this->glossary_mode;
	}
		
	/**
	* Set instructions
	*
	* @param	string		instructions
	*/
	function setInstructions($a_val)
	{
		$this->instructions = (string) $a_val;
	}
	
	/**
	* Get instructions
	*
	* @return	string		instructions
	*/
	function getInstructions()
	{
		return (string) $this->instructions;
	}

	/** 
	 * Set the cards
	 * @param array 	$card_id => card object
	 */
	function setCards($a_cards = array())
	{
		$this->cards = $a_cards;
	}
	
	/** 
	 * Get the cards
	 * @return array 	$card_id => card object
	 */
	function getCards()
	{
		return $this->cards;
	}
	
	/**
	 * Read the flash cards assigned to this object
	 */
	function readCards()
	{
		$this->setCards(ilFlashcard::_getAll($this->getId()));
	}
	   
	/**
	 * get a single card
	 * @param integer card_id
	 */
	function getCard($card_id)
	{
		return $this->cards[$card_id] ?? null;
	}
	
	
	/**
	 * Update the cards from the glossary
	 * delete all cards
	 * 
	 * @param integer $a_obj_id
	 * @param integer $a_glossary_ref_id
	 */
	public function updateCardsFromGlossary()
	{	
		// get all term_ids from the glossaries involved
		$all_terms = array();
		if ($this->getGlossaryRefId())
		{
			$glossary = ilObjectFactory::getInstanceByRefId($this->getGlossaryRefId());
			$glo_ids = $glossary->getAllGlossaryIds();
			if (!is_array($glo_ids))
			{
				$glo_ids = array($glo_ids);	
			}
			
			foreach ($glo_ids as $glo_id)
			{
				$terms = ilGlossaryTerm::getTermsOfGlossary($glo_id);
				$all_terms = array_merge($all_terms, $terms);
			}
		}
		
		// delete all cards without terms
		$found_terms = array();
		foreach ($this->cards as $card_id => $card)
		{
			if (!in_array($card->getTermId(), $all_terms))
			{
				$card->delete();
				if (isset($this->cards[$card_id])) {
					unset($this->cards[$card_id]);
				}
			}
			else
			{
				$found_terms[] = $card->getTermId();
			}
		}
		
		// add new cards for new terms
		$missing_terms = array_diff($all_terms, $found_terms);
		foreach ($missing_terms as $term_id)
		{
			$card = new ilFlashCard();
			$card->setObjId($this->getId());
			$card->setTermId($term_id);
			$card->save();
			$this->cards[$card->getCardId()] = $card;
		}
		
		// cleanup the trainings
		ilFlashcardUsage::_cleanup($this);
	}
		
	
	/**
	 * Count the number of users of this object (cached)
	 * @return	number of users
	 */
	function countUsers()
	{
		static $users = null;

		if (!isset($users))
		{
			$users = ilFlashcardUsage::_countUsers($this->getId());
		}
		return $users;
	}
	
	/**
	 * delete all data of a user
	 * @param integer user id
	 */
	static function _deleteUser($a_user_id)
	{
		ilFlashcardUsage::_deleteUser($a_user_id);
	}	
}
?>
