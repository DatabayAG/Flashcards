<?php
/**
 * Copyright (c) 2016 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg
 * GPLv2, see LICENSE
 */

/**
* Application class for a flashcards training based on leitner
* According to the leitner principle the index is divided into serveral boxes holding the cards
*
* @author Fred Neumann <fred.neumann@fim.uni-erlangen.de>
* $Id$
*/
class ilLeitnerTraining extends ilFlashcardsTraining 
{
	/**
	 * Maximum number of cards in the start box	(may be configurable later)
	 * @var integer	
	 */
	private $start_max = 20;
	
	/**
	 * Number of the last box (startbox has 0)
	 * @var integer	
	 */
	private $last_box = 4;
	
			
	/**
	 * arrays of 
	 * @var array 	array(box => array(card_id => ilFlashcardUsage)
	 */
	private $boxes = array();
	
	
	
	/**
	 * get the maximum size of the start box
	 * @return	integer
	 */
	public function getStartMax()
	{
		return $this->start_max;
	}
	
	/**
	 * get the last available box
	 * @return integer
	 */
	public function getLastBox()
	{
		return $this->last_box;
	}
	
	
	
	/**
	 * set the used cards
	 * (overwritten from parent to fill the boxes)
	 * 
	 * @param  array 	card_id => ilFlashcardUsage
	 */
	public function setUsedCards($a_cards)
	{
		parent::setUsedCards($a_cards);
		
		// fill the boxes
		foreach ($this->getUsedCards() as $card_id => $usage)
		{
			$box = min($usage->getStatus(), $this->getLastBox());
			$this->boxes[$box][$card_id] = $usage;
		}
	}


	/**
	 * add a card usage
	 * (overwritten to add box association)
	 * 
	 * @param	integer		card id
	 * @param	integer		usage status
	 */
	public function addCardUsage($a_card_id, $a_status = 0)
	{
		$usage = parent::addCardUsage($a_card_id, $a_status);
		$box = min($usage->getStatus(), $this->getLastBox());
		$this->boxes[$box][$a_card_id] = $usage;
	}
	
	
	/**
	 * Get the box capacity
	 * 
	 * @param 	integer 	box index
	 * @return	integer		number oc cards this box should hold
	 */
	public function getBoxCapacity($a_box)
	{
		return pow(2, $a_box) * $this->getStartMax();
	}
	
	
	/**
	 * count the cards in a specific box
	 * 
	 * @param 	integer		box index (0 for start box)
	 * @return	integer		number of cards in the box
	 */
	public function countCardsInBox($a_box)
	{
		return count((array) $this->boxes[$a_box]);
	}
		
	
	/**
	 * get the maximum size of the start box
	 */
	public function isStartMaxReached()
	{
		return $this->countCardsInBox(0) >= $this->getStartMax();	
	}
	
	
	/**
	 * get the data for a users overview table
	 * 
	 * @return	array	list of box data
	 */
	public function getOverviewData()
	{
		$data = array();
		
		// get the basic box data
		for ($box = 0; $box <= $this->getLastBox(); $box++)
		{			
			$data[$box] = array("box" => (int) $box,
								"capacity" => (int) $this->getBoxCapacity($box),
								"count" => (int) $this->countCardsInBox($box),
								"last_trained" => null);
		}
		
		
		// get the last trained date of all boxes
		// for each box this is taken from the latest last trained date 
		// of all cards with box as last status
		foreach ((array) $this->getUsedCards() as $usage)
		{
			$box = $usage->getLastStatus();
			if (isset($data[$box]))
			{
				if (!isset($data[$box]['last_trained']) || ilDateTime::_before($data[$box]["last_trained"], $usage->getLastChecked()))
				{
					$data[$box]["last_trained"] = $usage->getLastChecked();
				}
			}
		}

		for ($box = 0; $box <= $this->getLastBox(); $box++)
		{
			if (!isset($data[$box]['last_trained']))
			{
				$data[$box]['last_trained'] = new ilDateTime();
			}
		}
		
		return array_values($data);
	}
			
	
	/**
	 * fill the startbox randomly with remaining terms
	 * 
	 * @return integer	number of new cards
	 */
	public function fillStartbox()
	{
		$filled = 0;
		$free = max(0, $this->getStartMax() - $this->countCardsInBox(0));
		
		$all_cards = array_keys($this->object->getCards());
		$used_cards = array_keys($this->getUsedCards());
		
		$unused_cards = array_diff($all_cards, $used_cards);
		shuffle($unused_cards);
		
		while ($free > 0 and count($unused_cards) > 0)
		{
			$card_id = array_pop($unused_cards);
			$this->addCardUsage($card_id, 0);
			$filled++;
			$free--;
		}
		return $filled;
	}
	
	
	/**
	 * initialize the training of a box
	 * 
	 * @param 	integer 	box index
	 * @return 	bool		initialization successfull	
	 */
	public function initTrainingSession($a_box)
	{
		// always reset the training session
		$this->resetSessionValues();
		
		// check if there is something to train 
		if (is_array($this->boxes[$a_box]) and count($this->boxes[$a_box]) > 0)
		{
			$to_train = array_keys($this->boxes[$a_box]);
			shuffle($to_train);
			$this->setSessionValue("to_train", $to_train);
			$this->setSessionValue("trained", array());
			return true;
		}
		else
		{	
			return false;
		}
	}
	
	
	/**
	 * Set the checed status of the presented card
	 * 
	 * @param 	integer		card id
	 * @param 	string		"known", "difficult" or "not_known"
	 * @return	boolean		setting successful
	 */
	public function setCardChecked($a_card_id, $a_result)
	{
		// get the card usage
		if (!$usage = $this->getCardUsage($a_card_id))
		{
			return false;
		}
		$old_box = $usage->getStatus();
		
		// store the result
		switch($a_result)
		{
			case "known":	// move one box forward
				$new_box = min($old_box + 1, $this->getLastBox());
				$usage->setStatus($new_box);
				$usage->setLastResult(true);
				$usage->setTimesKnown($usage->getTimesKnown() + 1);
				break;
				
			case "difficult":	// stay in box
				$new_box = $old_box;
				$usage->setLastResult(true);
				$usage->setTimesKnown($usage->getTimesKnown() + 1);
				break;
				
			case "not_known":	// back to inbox
				$new_box = 0;
				$usage->setStatus($new_box);
				$usage->setLastResult(false);
				break;
				
			default:
				return false;
		}
		$usage->setLastStatus($old_box);
		$usage->setLastChecked(time(), IL_CAL_UNIX);
		$usage->setTimesChecked($usage->getTimesChecked() +1 );
		$usage->save();
		
		// adjust the box association
		if ($new_box != $old_box)
		{
			unset($this->boxes[$old_box][$a_card_id]);
			$this->boxes[$new_box][$a_card_id] = $usage;
		}
		
		// change the training status
		$to_train = $this->getSessionValue("to_train", array());
		$to_train = array_diff($to_train, array($a_card_id));
		$this->setSessionValue("to_train", $to_train);
		
		$trained = $this->getSessionValue("trained", array());
		array_push($trained, $a_card_id);
		$this->setSessionValue("trained", $trained);
	}
	
	
	/**
	 * get the next card to be presented
	 * 
	 * @return	integer	card id or 0 (if training is finished)
	 */
	public function getNextCardId()
	{
		$to_train = $this->getSessionValue("to_train", array());
		if (count($to_train))
		{
			return array_pop($to_train);
		}
		else
		{
			return 0;
		}
	}
	
	
	/**
	 * count the number of cards to train in the current training session
	 * @return 	integer		number
	 */
	public function countSessionToTrain()
	{
		return count($this->getSessionValue('to_train', array()));
	}
	
	
	/**
	 * count the number of cards alreaty trained in the current training session
	 * @return 	integer		number
	 */
	public function countSessionTrained()
	{
		return count($this->getSessionValue('trained', array()));
	}
}
