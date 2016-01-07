<?php

/*
date_default_timezone_set('America/New_York');
$date = new DateTime("2010-07-05T06:00:00Z");
//$date = DateTime::createFromFormat('Y-m-d H:i:s', "2010-07-05T06:00:00Z");
$date = DateTime::createFromFormat('Y-m-d+', "2010-07-05T06:00:00Z");

var_dump($date);
*/

$validationRules = array(
    'email' => 'email'
);
$fields = ['email' => 'gmail.com'];
$validator = Validator::make($fields, $validationRules);
//$validator = \Validator::make($fields, $validationRules);

$passes = $validator->passes();
var_dump($passes);
print_r($validator->messages()->toArray());
