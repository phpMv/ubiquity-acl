<?php
return array(
	"#tableName" => "Resource",
	"#primaryKeys" => array(
		"id" => "id"
	),
	"#manyToOne" => array(),
	"#fieldNames" => array(
		"value" => "value",
		"id" => "id",
		"name" => "name",
		"aclelements" => "aclelements"
	),
	"#memberNames" => array(
		"value" => "value",
		"id" => "id",
		"name" => "name",
		"aclelements" => "aclelements"
	),
	"#fieldTypes" => array(
		"value" => "varchar(30)",
		"id" => "int(11)",
		"name" => "varchar(30)",
		"aclelements" => "mixed"
	),
	"#nullable" => array(),
	"#notSerializable" => array(
		"aclelements"
	),
	"#transformers" => array(),
	"#accessors" => array(
		"value" => "setValue",
		"id" => "setId",
		"name" => "setName",
		"aclelements" => "setAclelements"
	),
	"#oneToMany" => array(
		"aclelements" => array(
			"mappedBy" => "resource",
			"className" => "models\\acls\\Aclelement"
		)
	)
);
