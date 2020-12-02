<?php
return array(
	"#tableName" => "Permission",
	"#primaryKeys" => array(
		"id" => "id"
	),
	"#manyToOne" => array(),
	"#fieldNames" => array(
		"level" => "level",
		"id" => "id",
		"name" => "name",
		"aclelements" => "aclelements"
	),
	"#memberNames" => array(
		"level" => "level",
		"id" => "id",
		"name" => "name",
		"aclelements" => "aclelements"
	),
	"#fieldTypes" => array(
		"level" => "int(11)",
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
		"level" => "setLevel",
		"id" => "setId",
		"name" => "setName",
		"aclelements" => "setAclelements"
	),
	"#oneToMany" => array(
		"aclelements" => array(
			"mappedBy" => "permission",
			"className" => "models\\acls\\Aclelement"
		)
	)
);
