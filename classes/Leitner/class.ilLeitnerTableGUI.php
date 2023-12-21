<?php
/**
 * Copyright (c) 2016 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg
 * GPLv2, see LICENSE
 */

/**
* Table showing leitner boxes with training cards
*
* @author Fred Neumann <fred.neumann@fim.uni-erlangen.de>
*
* $Id$
*/
class ilLeitnerTableGUI extends ilTable2GUI
{
    protected string $clock = '';
    
	function __construct($a_parent_gui, $a_parent_cmd)
	{
        global $DIC;

        parent::__construct($a_parent_gui, $a_parent_cmd);
 
		$this->parent = 	$a_parent_gui;
		$this->plugin = 	$a_parent_gui->plugin;
		$this->training =  	$a_parent_gui->training;

        $f = $DIC->ui()->factory();
        $renderer = $DIC->ui()->renderer();
        $this->clock = $renderer->render($f->symbol()->glyph()->time("#"));


        $this->setFormAction($this->ctrl->getFormAction($this->parent));		
		$this->addColumn($this->plugin->txt("leitner_box"), "", "29%");
		$this->addColumn($this->plugin->txt("leitner_capacity"), "", "15%");
		$this->addColumn($this->plugin->txt("count_cards"), "", "15%");
		$this->addColumn($this->plugin->txt("last_trained"), "", "20%");
		$this->addColumn($this->plugin->txt("actions"), "", "20%");
		$this->setEnableNumInfo(false);
		$this->setRowTemplate("tpl.leitner_table_row.html", $this->plugin->getDirectory());
	}
 
  	
	/**
	 * Fill a single data row.
	 */
	protected function fillRow(array $a_set): void
	{
        if (!isset($a_set['box'])) {
            return;
        }
        
		// box title
		if ($a_set["box"] == 0)
		{
			$this->tpl->setVariable("BOX", $this->plugin->txt("leitner_box_0_title"));
		}
		else
		{
			$this->tpl->setVariable("BOX", sprintf($this->plugin->txt("leitner_box_x_title"), $a_set["box"]));
		}
		
		// box data
		$this->tpl->setVariable("CAPACITY", $a_set["capacity"] ?? 0);
		$this->tpl->setVariable("COUNT", $a_set["count"] ?? 0);
        if (isset($a_set["last_trained"])) {
            $this->tpl->setVariable("LAST_TRAINED", ilDatePresentation::formatDate($a_set["last_trained"]));
        }
		
		// actions
		if ($a_set["count"] ?? 0)
		{
			$this->ctrl->setParameter($this->parent, "box", $a_set["box"]);
			$this->tpl->setVariable("LINK_TRAINING", $this->ctrl->getLinkTarget($this->parent,"startTraining"));
			$this->tpl->setVariable("TXT_TRAINING", $this->plugin->txt("start_training"));
            
            // training alert
            if (($a_set["count"] ?? 0) >= ($a_set["capacity"] ?? 0))
            {
                $this->tpl->setVariable("TRAINING_ALERT", $this->clock);
            }
		}
	}
}
?>