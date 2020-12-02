<?php
return array("#tableName"=>"AclElement","#primaryKeys"=>array("id"=>"id"),"#manyToOne"=>array("role","permission","resource"),"#fieldNames"=>array("id"=>"id","role"=>"idRole","permission"=>"idPermission","resource"=>"idResource"),"#memberNames"=>array("id"=>"id","idRole"=>"role","idPermission"=>"permission","idResource"=>"resource"),"#fieldTypes"=>array("id"=>"int(11)","role"=>false,"permission"=>false,"resource"=>false),"#nullable"=>array(),"#notSerializable"=>array("role","permission","resource"),"#transformers"=>array(),"#accessors"=>array("id"=>"setId","idRole"=>"setRole","idPermission"=>"setPermission","idResource"=>"setResource"),"#joinColumn"=>array("role"=>array("className"=>"Ubiquity\\security\\acl\\models\\Role","name"=>"idRole","nullable"=>false),"permission"=>array("className"=>"Ubiquity\\security\\acl\\models\\Permission","name"=>"idPermission","nullable"=>false),"resource"=>array("className"=>"Ubiquity\\security\\acl\\models\\Resource","name"=>"idResource","nullable"=>false)),"#invertedJoinColumn"=>array("idRole"=>array("member"=>"role","className"=>"Ubiquity\\security\\acl\\models\\Role"),"idPermission"=>array("member"=>"permission","className"=>"Ubiquity\\security\\acl\\models\\Permission"),"idResource"=>array("member"=>"resource","className"=>"Ubiquity\\security\\acl\\models\\Resource")));
