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
Based on `token_get_all()`.

hasty-reflection-common:  
Classes and interfaces similar to the core reflection API. Contains a working "native" implementation that uses core reflection instead of parsing.

hasty-reflection-parser:  
Uses the parser to generate the reflection objects.

### How to use

First, choose one of the various implementations of `ClassIndexInterface`. E.g.
```php
// Use native reflection and native class loader. This means, all files will be *really* included in PHP.
$classIndex = new ClassIndex_Native();

// Files will be parsed AND included.
$classIndex = ClassIndex_Ast::createSemiNative();

// Class files will be parsed, and NOT included.
// Using the currently active Composer class loader/finder. Obviously you need to make sure to get the path right.
$composerClassLoader = include dirname(dirname(__DIR__)) . '/vendor/autoload.php';
$classIndex = ClassIndex_Ast::createWithClassLoader(new ClassLoader_Composer($composerClassLoader));
```

Now you can get class reflection objects as you want.

```php
$classReflection = $classIndex->classGetReflection(C::class);
print $classReflection->getDocComment();
```

The ClassIndex makes sure that only one reflection object exists for each class/interface/trait/method.

```php
assert($classIndex->classGetReflection(C::class) === $classIndex->classGetReflection(C::class));
print $classReflection->getDocComment();
```

### Performance

This thing should be quite fast, because the parser skips a lot of stuff. For long method or function bodies, it will simply fast-forward through the tokens, and only count opening and closing brackets!

It does currently NOT do the trick of cutting out the class body PHP. This is because it was made for a use case where we are really interested in the class body.

However, the class body will initially be read in fast mode, to be picked up later, if/when requested. (This is the "lazy" parameter for some classes).

The token array from `token_get_all()` is never sliced or manipulated, which means PHP only needs to pass around the array pointer, not create different versions the array itself.

The length of the token array is not remembered either, instead it is terminated by a unique symbol.


### Status of this library

This is very fresh. Mabye some names will change in the near future!
