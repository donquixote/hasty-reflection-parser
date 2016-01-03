<?php

namespace Donquixote\HastyReflectionParser\Reflection\ClassLike;

use Donquixote\HastyPhpAst\Util\UtilBase;
use Donquixote\HastyPhpAst\Ast\ClassLike\AstClassLikeInterface;
use Donquixote\HastyReflectionCommon\Canvas\ClassIndex\ClassIndexInterface;
use Donquixote\HastyReflectionCommon\NamespaceUseContext\NamespaceUseContextInterface;
use Donquixote\HastyReflectionCommon\Reflection\ClassLike\Body\ClassLikeBody_Composite;
use Donquixote\HastyReflectionCommon\Reflection\ClassLike\ClassExtends\ClassExtends_ByName;
use Donquixote\HastyReflectionCommon\Reflection\ClassLike\ClassLikeReflection_Composite;
use Donquixote\HastyReflectionCommon\Reflection\ClassLike\AllInterfaces\AllInterfaces_Recursive;
use Donquixote\HastyReflectionCommon\Reflection\ClassLike\OwnInterfaces\OwnInterfaces_FromNames;
use Donquixote\HastyReflectionCommon\Reflection\Declaration;

final class ClassLikeReflection_Ast extends UtilBase {

  /**
   * @param \Donquixote\HastyReflectionCommon\NamespaceUseContext\NamespaceUseContextInterface $namespaceUseContext
   * @param \Donquixote\HastyPhpAst\Ast\ClassLike\AstClassLikeInterface $astNode
   * @param \Donquixote\HastyReflectionCommon\Canvas\ClassIndex\ClassIndexInterface $autoloadSource
   *
   * @return \Donquixote\HastyReflectionCommon\Reflection\ClassLike\ClassLikeReflectionInterface
   */
  static function create(
    NamespaceUseContextInterface $namespaceUseContext,
    AstClassLikeInterface $astNode,
    ClassIndexInterface $autoloadSource
  ) {
    $declaration = new Declaration($astNode->getDocComment(), $namespaceUseContext);
    $name = $astNode->getName();
    if (NULL !== $namespace = $namespaceUseContext->getNamespaceName()) {
      $name = $namespace . '\\' . $name;
    }
    $parentClassQcn = (NULL !== $parentAlias = $astNode->getParentClassName())
      ? $namespaceUseContext->aliasGetName($parentAlias)
      : NULL;
    $header = new ClassLikeHeader_Ast($name, $astNode);
    $extends = new ClassExtends_ByName($autoloadSource, $parentClassQcn);
    $ownInterfaceNames = array();
    foreach ($astNode->getInterfaceNames() as $interfaceAlias) {
      $ownInterfaceNames[] = $namespaceUseContext->aliasGetName($interfaceAlias);
    }
    $ownInterfaces = new OwnInterfaces_FromNames($autoloadSource, $ownInterfaceNames);
    if ($header->isInterface()) {
      $interfacesAll = new AllInterfaces_Recursive($extends, $ownInterfaces, $name);
    }
    else {
      $interfacesAll = new AllInterfaces_Recursive($extends, $ownInterfaces, NULL);
    }
    $ownBody = new OwnBody_Ast($astNode->getBody(), $autoloadSource, $name);
    $body = ClassLikeBody_Composite::createFromOwnBody($extends, $interfacesAll, $ownBody);
    return new ClassLikeReflection_Composite($declaration, $header, $extends, $interfacesAll, $ownInterfaces, $body);
  }

}
