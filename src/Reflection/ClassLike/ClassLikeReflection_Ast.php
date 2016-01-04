<?php

namespace Donquixote\HastyReflectionParser\Reflection\ClassLike;

use Donquixote\HastyPhpAst\Ast\ClassLike\AstClassLikeInterface;
use Donquixote\HastyPhpAst\Util\UtilBase;
use Donquixote\HastyReflectionCommon\Canvas\ClassIndex\ClassIndexInterface;
use Donquixote\HastyReflectionCommon\NamespaceUseContext\NamespaceUseContextInterface;
use Donquixote\HastyReflectionCommon\Reflection\ClassLike\AllInterfaces\AllInterfaces_FromOwn;
use Donquixote\HastyReflectionCommon\Reflection\ClassLike\Body\ClassLikeBody_Composite;
use Donquixote\HastyReflectionCommon\Reflection\ClassLike\ClassLikeReflection_Composite;
use Donquixote\HastyReflectionCommon\Reflection\Declaration;

final class ClassLikeReflection_Ast extends UtilBase {

  /**
   * @param \Donquixote\HastyReflectionCommon\NamespaceUseContext\NamespaceUseContextInterface $namespaceUseContext
   * @param \Donquixote\HastyPhpAst\Ast\ClassLike\AstClassLikeInterface $astNode
   * @param \Donquixote\HastyReflectionCommon\Canvas\ClassIndex\ClassIndexInterface $classIndex
   *
   * @return \Donquixote\HastyReflectionCommon\Reflection\ClassLike\ClassLikeReflectionInterface
   */
  static function create(
    NamespaceUseContextInterface $namespaceUseContext,
    AstClassLikeInterface $astNode,
    ClassIndexInterface $classIndex
  ) {
    $declaration = new Declaration($astNode->getDocComment(), $namespaceUseContext);
    $name = (NULL !== $namespace = $namespaceUseContext->getNamespaceName())
      ? $namespace . '\\' . $astNode->getShortName()
      : $astNode->getShortName();

    $header = new ClassLikeHeader_Ast($name, $astNode);

    $extends = ClassExtends_Ast::createFromAst($astNode, $classIndex, $namespaceUseContext);

    $ownInterfaces = OwnInterfaces_Ast::createFromAst($astNode, $classIndex, $namespaceUseContext);

    $interfacesAll = new AllInterfaces_FromOwn($extends, $ownInterfaces);
    if ($header->isInterface()) {
      $interfacesAll = $interfacesAll->withSelfInterfaceName($name, $classIndex);
    }

    $ownBody = new OwnBody_Ast($astNode->getBody(), $classIndex, $name);

    $body = ClassLikeBody_Composite::createFromOwnBody($extends, $interfacesAll, $ownBody);

    return new ClassLikeReflection_Composite($declaration, $header, $extends, $interfacesAll, $ownInterfaces, $body);
  }

}
