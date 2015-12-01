/* lexical grammar */
/* http://stackoverflow.com/questions/8467150/how-to-get-abstract-syntax-tree-ast-out-of-jison-parser */
%lex
%%

\s+                   /* skip whitespace */
[0-9]+("."[0-9]+)?\b  return 'NUMBER'
'AND'                 return 'AND'
'OR'                  return 'OR'
'NOT'                 return 'NOT'
'BETWEEN'             return 'BETWEEN'
'LIKE'                return 'LIKE'
L?\"(\\.|[^\\"])*\"   return 'STRING_LITERAL'
'('                   return 'LPAREN'
')'                   return 'RPAREN'
'!='                  return 'NEQ'
'>='                  return 'GE'
'<='                  return 'LE'
'='                   return 'EQ'
'>'                   return 'GT'
'<'                   return 'LT'
'IN'                  return 'IN'
'NIN'                 return 'NIN'
'+'                   return 'PLUS'
'-'                   return 'MINUS'
','                   return 'COMMA'
[_a-zA-Z][_\.a-zA-Z0-9]{0,30}            return 'IDEN'
<<EOF>>               return 'EOF'
.                     return 'INVALID'

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
    :  comparison_predicate EOF
        {return $1;}
    ;


value_expression
	: NUMBER
		{$$ = Number(yytext);}
	| STRING_LITERAL
		{$$ = yytext.substring(1, yytext.length -1);}
	;

comparison_predicate
	: IDEN comp_op value_expression
		{$$ = {
        	var: $1,
        	op: $2,
        	val: $3
        	};
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
