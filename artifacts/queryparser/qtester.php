<?php
require('queryparser.php');

use App\Ecofy\Support\SRQLParser;

$srqlParser = new SRQLParser();

//$criteria = $srqlParser->parse('a>4');
//$criteria = $srqlParser->parse('a=22 OR b=33 OR c = 44');
//$criteria = $srqlParser->parse('a=11 AND b=22 OR (c IN (33,44,55))');
//$criteria = $srqlParser->parse('(a=11 AND b=22) OR (c=33 AND d=44)');
$criteria = $srqlParser->parse('a=11 AND b=22 OR (c IN (33,44,55)) OR ( c LIKE "%ello") AND be BETWEEN 1 AND 3');

//var_dump($q);
print_r($criteria);
