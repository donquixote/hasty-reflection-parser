[![Build Status](https://secure.travis-ci.org/donquixote/hasty-reflection-parser.png)](https://travis-ci.org/donquixote/hasty-reflection-parser)

# hasty-reflection-parser

A library that parses PHP code to generate reflection objects.

It is called "hasty", because it skips and omits and ignores a lot of stuff, and only focuses on what one usually wants for e.g. annotation discovery.

Currently it ignores variable declarations, regular statements, function calls, object variables, function declarations, and more.

Use cases:  
It is meant for stuff like Annotation discovery like in Doctrine or Drupal 8.

### Partial libraries (as dependencies):

hasty-php-ast:  
Abstract syntax tree. This is an incomplete representation of PHP !

hasty-php-parser:  
The parser. Turns a PHP string into an AST graph.

hasty-reflection-common:  
Classes and interfaces similar to the core reflection API. Contains a working "native" implementation that uses core reflection instead of parsing.

hasty-reflection-parser:  
Uses the parser to generate the reflection objects.

