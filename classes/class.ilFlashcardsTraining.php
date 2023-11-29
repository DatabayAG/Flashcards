<?php
/**
 * Copyright (c) 2016 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg
 * GPLv2, see LICENSE
 */

/**
 * Base application class for a flashcards training
 * This class has to be extended for a certain training mode (e.g. leitner)
 *
 * @author Fred Neumann <fred.neumann@fim.uni-erlangen.de>
 * $Id$
 */
class ilFlashcardsTraining
{
	/**
	 * flashcards object
	 * @var object	
	 */
	var $object = null;

	/**
	 * plugin object
	 * @var object	
	 */
	var $plugin = null;
	
	/**
	 * user object
	 * @var object	
	 */
	var $user = null;
	
	
	/**
	 * list of cards used in the training
	 * @var  array 	card_id => ilFlashcardUsage
	 */
	private $usages = array();

	
	/**
	 * Session values related to this object
	 * 
	 * @var array
	 */
	private $session = array();
	
	
	
	/** 
	 * Constructor
	 * 
	 * @param	ilObjFlashCards		$a_object	the flaschcards object
	 * @param	ilObjUser			$a_user		the user
	 */
	function __construct(ilObjFlashCards $a_object, ilObjUser $a_user)
	{
		// initialize references to the mostly used objects
		$this->object = $a_object;
		$this->plugin = $a_object->getMyPlugin();
		$this->user = $a_user;
		
		// read the cards used in this training
		$this->readUsedCards();		
	}
	
	
	/**
	 * Read the card usages
	 */
	private function readUsedCards()
	{
		$this->setUsedCards(ilFlashcardUsage::_getAll($this->object->getId(), $this->user->getId()));
	}
	
	
	/**
	 * get the cards used in the training
	 * @return  array 	card_id => ilFlashcardUsage
	 */
	public function getUsedCards()
	{
		return $this->usages;
	}
	
	
	/**
	 * set the cards used in the training
	 * @param  array 	$a_usages	card_id => ilFlashcardUsage
	 */
	public function setUsedCards($a_usages)
	{
		$this->usages = ($a_usages);
	}
	
	
	/**
	 * Count the crads used in this training
	 */
	public function countUsedCards()
	{
		return count($this->usages);
	}


	/**
	 * Count the crads used in this training
	 */
	public function countUnusedCards()
	{
		return max(0, count($this->object->getCards()) - count($this->getUsedCards()));
	}

	/**
	 * get the usage of a card
	 * 
	 * @param object ilFlashcardUsage
	 */
	public function getCardUsage($a_card_id)
	{
		return $this->usages[$a_card_id];
	}
	
	/**
	 * add a card usage
	 * 
	 * @param	integer		$a_card_id
	 * @param	integer		$a_status	usage status
	 * @return	object		ilFlashcardUsage
	 */
	public function addCardUsage($a_card_id, $a_status = 0)
	{
		$usage = new ilFlashcardUsage($this->object->getId(), $this->user->getId(), $a_card_id);
		$usage->setStatus($a_status);
		$usage->save();
		$this->usages[$a_card_id] = $usage;
		return $usage;
	}
	
	
	/**
	 * reset the training
	 * This deletes all card usages of the user in the training
	 */
	public function reset()
	{
		ilFlashcardUsage::_deleteUsages($this->object->getId(), $this->user->getId());
		$this->usages = array();
		$this->resetSessionValues();
	}


	/**
	 * init the session values
	 */
	private function assignSessionValues()
	{
		// initialize the session values
		if (!isset($_SESSION["rep_robj_xflc_". $this->object->getId()]))
		{
			$_SESSION["rep_robj_xflc_". $this->object->getId()] = array();
		}
		$this->session =& $_SESSION["rep_robj_xflc_". $this->object->getId()];
	}
	
	
	/**
	 * reset the session values for this training
	 */
	public function resetSessionValues()
	{
		unset($_SESSION["rep_robj_xflc_". $this->object->getId()]);
		unset($this->session);
	}
	
	
	/**
	 * get a session value for this training
	 * @param string		$a_name
	 * @param mixed			$a_default
	 * @return mixed
	 */
	public function getSessionValue($a_name, $a_default = null)
	{
		$this->assignSessionValues();
		return isset($this->session[$a_name]) ? $this->session[$a_name] : $a_default;
	}
	
	
	/**
	 * get a session value for this training
	 * @param string		$a_name
	 * @param mixed			$a_value
	 */
	public function setSessionValue($a_name, $a_value)
	{
		$this->assignSessionValues();
		$this->session[$a_name] = $a_value;
	}
}
?>
