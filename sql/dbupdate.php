<#1>
<?php
/**
 * Copyright (c) 2016 Institut fuer Lern-Innovation, Friedrich-Alexander-Universitaet Erlangen-Nuernberg
 * GPLv2, see LICENSE 
 */

/**
 * FlashCards plugin: database update script
 *
 * @author Fred Neumann <fred.neumann@fim.uni-erlangen.de>
 * @version $Id$
 */ 

$fields = array(
	'obj_id' => array(
		'type' => 'integer',
		'length' => 4,
		'notnull' => true
	),
	'is_online' => array(
		'type' => 'integer',
		'length' => 1,
		'notnull' => false
	),
	'glossary_ref_id' => array(
		'type' => 'integer',
		'length' => 4,
		'notnull' => false
	),
	'glossary_mode' => array(
		'type' => 'text',
		'length' => 10,
		'notnull' => false
	),	
	'instructions' => array(
		'type' => 'clob',
		'notnull' => false
	)
);

$ilDB->createTable("rep_robj_xflc_data", $fields);
$ilDB->addPrimaryKey("rep_robj_xflc_data", array("obj_id"));
?>
<#2>
<?php
$fields = array (
	'card_id' => array(
		'type' => 'integer',
		'length' => 4,
		'notnull' => true	
	),
	'obj_id' => array (
		'type' => 'integer',
		'length' => 4,
		'notnull' => true
	),
	'term_id' => array(
		'type' => 'integer',
		'length' => 4,
		'notnull' => false	
	)
);
$ilDB->createTable("rep_robj_xflc_cards", $fields);
$ilDB->addPrimaryKey("rep_robj_xflc_cards", array("card_id"));
$ilDB->addIndex("rep_robj_xflc_cards", array("obj_id"), "i1");
$ilDB->createSequence("rep_robj_xflc_cards");
?>
<#3>
<?php 
$fields = array (
	'obj_id' => array (
		'type' => 'integer',
		'length' => 4,
		'notnull' => true
	),
	'user_id' => array(
		'type' => 'integer',
		'length' => 4,
		'notnull' => true
	),
	'card_id' => array(
		'type' => 'integer',
		'length' => 4,
		'notnull' => true	
	),
	'status' => array(
		'type' => 'integer',
		'length' => 1,
		'notnull' => false
	),
	'last_checked' => array (
		'type' => 'timestamp',
		'notnull' => false
	),
	'last_result' => array (
		'type' => 'integer',
		'length' => 1,
		'notnull' => false
	),
	'times_checked' => array (
		'type' => 'integer',
		'length' => 2,
		'notnull' => false
	),
	'times_known' => array (
		'type' => 'integer',
		'length' => 2,
		'notnull' => false
	)
);
$ilDB->createTable("rep_robj_xflc_usage", $fields);
$ilDB->addPrimaryKey("rep_robj_xflc_usage", array("obj_id", "user_id", "card_id"));
?>
<#4>
<?php 
$ilDB->addTableColumn("rep_robj_xflc_usage", "last_status",  
	array(
		'type' => 'integer',
		'length' => 1,
		'notnull' => false
	));
?>
<#5>
<?php
	/**
	* Check whether type exists in object data, if not, create the type
	* The type is normally created at plugin activation, see ilRepositoryObjectPlugin::beforeActivation()
	*/
	$set = $ilDB->query("SELECT obj_id FROM object_data WHERE type='typ' AND title = 'xflc'");
	if ($rec = $ilDB->fetchAssoc($set))
	{
		$typ_id = $rec["obj_id"];
	}
	else
	{
		$typ_id = $ilDB->nextId("object_data");
		$ilDB->manipulate("INSERT INTO object_data ".
		"(obj_id, type, title, description, owner, create_date, last_update) VALUES (".
		$ilDB->quote($typ_id, "integer").",".
		$ilDB->quote("typ", "text").",".
		$ilDB->quote("xflc", "text").",".
		$ilDB->quote("Plugin Flashcards", "text").",".
		$ilDB->quote(-1, "integer").",".
		$ilDB->quote(ilUtil::now(), "timestamp").",".
		$ilDB->quote(ilUtil::now(), "timestamp").
		")");
	}

	/**
	* Add new RBAC operations
	*/
	$operations = array('copy');
	foreach ($operations as $operation)
	{
		$query = "SELECT ops_id FROM rbac_operations WHERE operation = ".$ilDB->quote($operation, 'text');
		$res = $ilDB->query($query);
		$row = $ilDB->fetchObject($res);
		$ops_id = $row->ops_id;

		$query = "INSERT INTO rbac_ta (typ_id, ops_id) VALUES ("
		.$ilDB->quote($typ_id, 'integer').","
		.$ilDB->quote($ops_id, 'integer').")";
		$ilDB->manipulate($query);
	}
?>

