<?php
return array(
	"#tableName"=>"Permission",
	"#primaryKeys"=>["name"=>"name"],
	"#manyToOne"=>[],
	"#fieldNames"=>["level"=>"level","name"=>"name"],
	"#memberNames"=>["level"=>"level","name"=>"name"],
	"#fieldTypes"=>["level"=>"mixed","name"=>"varchar(100)"],
	"#nullable"=>[],
	"#notSerializable"=>[],
	"#transformers"=>[],
	"#accessors"=>["level"=>"setLevel","name"=>"setName"]
);
