<?php

namespace Donquixote\HastyReflectionParser\AstToFileReflection;

use Donquixote\HastyPhpAst\Ast\ClassLike\AstClassLikeInterface;
use Donquixote\HastyPhpAst\Ast\File\AstFileInterface;
use Donquixote\HastyPhpAst\Ast\Namespace_\AstNamespaceDeclarationInterface;
use Donquixote\HastyPhpAst\Ast\Use_\AstUseStatementInterface;
use Donquixote\HastyReflectionCommon\Canvas\ClassIndex\ClassIndexInterface;
use Donquixote\HastyReflectionCommon\NamespaceUseContext\NamespaceUseContext;
use Donquixote\HastyReflectionParser\Reflection\ClassLike\ClassLikeReflection_Ast;
use Donquixote\HastyReflectionCommon\Reflection\File\FileReflection;

class AstToFileReflection implements AstToFileReflectionInterface {

  /**
   * @param \Donquixote\HastyPhpAst\Ast\File\AstFileInterface $astFile
   * @param \Donquixote\HastyReflectionCommon\Canvas\ClassIndex\ClassIndexInterface $autoloadSource
   *
   * @return \Donquixote\HastyReflectionParser\AstToFileReflection\AstToFileReflection
   */
  function astGetFileReflection(AstFileInterface $astFile, ClassIndexInterface $autoloadSource) {
    $namespace = NULL;
    $useFqcnsByAlias = array();
    $classLikeNodes = array();
    foreach ($astFile->getNodes() as $node) {
      if ($node instanceof AstNamespaceDeclarationInterface) {
        $namespace = $node->getFqcn();
      }
      elseif ($node instanceof AstUseStatementInterface) {
        $useFqcnsByAlias += $node->getFqcnsByAlias();
      }
      elseif ($node instanceof AstClassLikeInterface) {
        $classLikeNodes[] = $node;
      }
    }
    $namesByAlias = array();
    foreach ($useFqcnsByAlias as $alias => $fqcn) {
      $namesByAlias[$alias] = $fqcn->getQualifiedName();
    }
    $context = new NamespaceUseContext($namespace->getQualifiedName(), $namesByAlias);
    $classesByQcn = array();
    foreach ($classLikeNodes as $node) {
      $class = ClassLikeReflection_Ast::create($context, $node, $autoloadSource);
      $classesByQcn[$class->getName()] = $class;
    }
    return new FileReflection($classesByQcn, $context);
  }
}
