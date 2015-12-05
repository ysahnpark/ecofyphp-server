//option namespace:App\Ecofy\Support
//option class:SRQLParser

/* lexical grammar */
/* http://stackoverflow.com/questions/8467150/how-to-get-abstract-syntax-tree-ast-out-of-jison-parser */
%lex
%%

\s+                   /* skip whitespace */
[0-9]+("."[0-9]+)?\b  return 'NUMBER';
'AND'                 return 'AND';
'OR'                  return 'OR';
'NOT'                 return 'NOT';
'BETWEEN'             return 'BETWEEN';
'LIKE'                return 'LIKE';
L?\"(\\.|[^\\"])*\"   return 'STRING_LITERAL';
'('                   return 'LPAREN';
')'                   return 'RPAREN';
'!='                  return 'NEQ';
'>='                  return 'GE';
'<='                  return 'LE';
'='                   return 'EQ';
'>'                   return 'GT';
'<'                   return 'LT';
'IN'                  return 'IN';
'NIN'                 return 'NIN';
'+'                   return 'PLUS';
'-'                   return 'MINUS';
','                   return 'COMMA';
[_a-zA-Z][_\.a-zA-Z0-9]{0,30}            return 'IDEN';
<<EOF>>               return 'EOF';
.                     return 'INVALID';

/lex

%left OR
%left AND
%right NOT
%left NEQ EQ
%left GT LE LT GE
$left PLUS MINUS

%start start
%% /* language grammar */

start
    :  query_expression EOF
        {
        return $1;
        }
    ;

query_expression
    : query_expression OR boolean_term
        {
        /*php
        $$ = [
        	'op' => 'or',
        	'args' => [ $1->text, $3->text ]
        ];
        */
        }
    | boolean_term
    ;

boolean_term
	: boolean_factor
	| boolean_term AND boolean_factor
		{
        /*php
        $$ = [
			'op' => 'and',
        	'args' => [ $1->text, $3->text ]
        ];
        */
        }
    ;

boolean_factor
	: boolean_test
	;

boolean_test
	: boolean_primary
	;

boolean_primary
	: predicate
	| LPAREN query_expression RPAREN
		{
            //php $$ = $2->text;
        }
    ;

predicate
	: comparison_predicate
	| in_predicate
	| nin_predicate
	| like_predicate
	| between_predicate
	;

comparison_predicate
	: IDEN comp_op value_expression
		{
        /*php
        $$ = [
        	'var' => $1->text,
        	'op' => $2->text,
        	'val' => $3->text
        ];
        */
        }
    ;

value_expression
	: NUMBER
		{
            //php $$ = floatval($yy->text);
        }
	| STRING_LITERAL
		{
            //php $$ = substr($yy->text, 1, -1);
        }
	;

comp_op
	: EQ
	| NEQ
	| GT
	| GE
	| LT
	| LE
	;

in_predicate
	: IDEN IN in_predicate_value
	{
        /*php
        $$ = [
			'var' => $1->text,
			'op' => 'in',
        	'args' => $3->text
        ];
        */
    }

    ;

nin_predicate
	: IDEN NIN in_predicate_value
	{
        /*php
        $$ = [
			'var' => $1->text,
			'op' => 'nin',
        	'args' => $3->text
        ];
        */
    }
    ;

in_predicate_value
	: LPAREN in_value_list RPAREN
	{
        //php $$ = $2->text;
    }
    ;

in_value_list
	: in_value_list_element
	{
        /*php
        $$ = [$1->text];
        */
    }
	| in_value_list COMMA in_value_list_element
	{
        /*php
        array_push($1->text, $3->text); $$ = $1->text;
        */
    }
	;

in_value_list_element
	: value_expression
		{
            //php $$ = $1->text;
        }
	;

like_predicate
	: IDEN LIKE value_expression
	{
        /*php
        $$ = [
			'var' => $1->text,
        	'op' => 'like',
    		'val' => $3->text,
        ];
        */
    }
    ;

between_predicate
	: IDEN BETWEEN value_expression AND value_expression
	{
        /*php
        $$ = [
			'var' => $1->text,
            'op' => 'between',
        	'args' => [
        		'from' => $3->text,
        		'to' => $5->text
        	]
        ];
        */
    }
    ;
