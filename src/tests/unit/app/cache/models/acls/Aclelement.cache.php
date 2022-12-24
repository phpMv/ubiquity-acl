<?php
return array(
	"#tableName"=>"aclelement",
	"#primaryKeys"=>["id"=>"id"],
	"#manyToOne"=>["role","permission","resource"],
	"#fieldNames"=>["id"=>"id","role"=>"roleName","permission"=>"permissionName","resource"=>"resourceName"],
	"#memberNames"=>["id"=>"id","roleName"=>"role","permissionName"=>"permission","resourceName"=>"resource"],
	"#fieldTypes"=>["id"=>"int(11)","role"=>"mixed","permission"=>"mixed","resource"=>"mixed"],"#nullable"=>["id"],
	"#notSerializable"=>["role","permission","resource"],
	"#transformers"=>[],
	"#accessors"=>["id"=>"setId","roleName"=>"setRole","permissionName"=>"setPermission","resourceName"=>"setResource"],
	"#joinColumn"=>["role"=>["className"=>"Ubiquity\\security\\acl\\models\\Role","name"=>"roleName"],"permission"=>["className"=>"Ubiquity\\security\\acl\\models\\Permission","name"=>"permissionName"],"resource"=>["className"=>"Ubiquity\\security\\acl\\models\\Resource","name"=>"resourceName"]],
	"#invertedJoinColumn"=>["roleName"=>["member"=>"role","className"=>"Ubiquity\\security\\acl\\models\\Role"],"permissionName"=>["member"=>"permission","className"=>"Ubiquity\\security\\acl\\models\\Permission"],"resourceName"=>["member"=>"resource","className"=>"Ubiquity\\security\\acl\\models\\Resource"]]
);
