<?php
return array(
	"#tableName" => "Role",
	"#primaryKeys" => array(
		"id" => "id"
	),
	"#manyToOne" => array(),
	"#fieldNames" => array(
		"parents" => "parents",
		"id" => "id",
		"name" => "name",
		"aclelements" => "aclelements"
	),
	"#memberNames" => array(
		"parents" => "parents",
		"id" => "id",
		"name" => "name",
		"aclelements" => "aclelements"
	),
	"#fieldTypes" => array(
		"parents" => "varchar(30)",
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
		"parents" => "setParents",
		"id" => "setId",
		"name" => "setName",
		"aclelements" => "setAclelements"
	),
	"#oneToMany" => array(
		"aclelements" => array(
			"mappedBy" => "role",
			"className" => "models\\acls\\Aclelement"
		)
	)
);
