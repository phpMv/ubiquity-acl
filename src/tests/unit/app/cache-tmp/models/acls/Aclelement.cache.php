<?php
return array(
	"#tableName" => "AclElement",
	"#primaryKeys" => array(
		"id" => "id"
	),
	"#manyToOne" => array(
		"permission",
		"resource",
		"role"
	),
	"#fieldNames" => array(
		"id" => "id",
		"permission" => "idPermission",
		"resource" => "idResource",
		"role" => "idRole"
	),
	"#memberNames" => array(
		"id" => "id",
		"idPermission" => "permission",
		"idResource" => "resource",
		"idRole" => "role"
	),
	"#fieldTypes" => array(
		"id" => "int(11)",
		"permission" => false,
		"resource" => false,
		"role" => false
	),
	"#nullable" => array(),
	"#notSerializable" => array(
		"permission",
		"resource",
		"role"
	),
	"#transformers" => array(),
	"#accessors" => array(
		"id" => "setId",
		"idPermission" => "setPermission",
		"idResource" => "setResource",
		"idRole" => "setRole"
	),
	"#joinColumn" => array(
		"permission" => array(
			"className" => "models\\acls\\Permission",
			"name" => "idPermission",
			"nullable" => false
		),
		"resource" => array(
			"className" => "models\\acls\\Resource",
			"name" => "idResource",
			"nullable" => false
		),
		"role" => array(
			"className" => "models\\acls\\Role",
			"name" => "idRole",
			"nullable" => false
		)
	),
	"#invertedJoinColumn" => array(
		"idPermission" => array(
			"member" => "permission",
			"className" => "models\\acls\\Permission"
		),
		"idResource" => array(
			"member" => "resource",
			"className" => "models\\acls\\Resource"
		),
		"idRole" => array(
			"member" => "role",
			"className" => "models\\acls\\Role"
		)
	)
);
