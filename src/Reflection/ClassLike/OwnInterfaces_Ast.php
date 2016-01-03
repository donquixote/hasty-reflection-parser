<?php

namespace Donquixote\HastyReflectionParser\Reflection\ClassLike;

use Donquixote\HastyPhpAst\Ast\ClassLike\AstClassLikeInterface;
use Donquixote\HastyReflectionCommon\Canvas\ClassIndex\ClassIndexInterface;
use Donquixote\HastyReflectionCommon\NamespaceUseContext\NamespaceUseContextInterface;
use Donquixote\HastyReflectionCommon\Reflection\ClassLike\OwnInterfaces\OwnInterfaces_FromNames;
use Donquixote\HastyReflectionCommon\Util\UtilBase;

final class OwnInterfaces_Ast extends UtilBase {

  /**
   * @param \Donquixote\HastyPhpAst\Ast\ClassLike\AstClassLikeInterface $astClassLike
   * @param \Donquixote\HastyReflectionCommon\Canvas\ClassIndex\ClassIndexInterface $classIndex
   * @param \Donquixote\HastyReflectionCommon\NamespaceUseContext\NamespaceUseContextInterface $namespaceUseContext
   *
   * @return \Donquixote\HastyReflectionCommon\Reflection\ClassLike\OwnInterfaces\OwnInterfacesInterface
   */
  static function createFromAst(AstClassLikeInterface $astClassLike, ClassIndexInterface $classIndex, NamespaceUseContextInterface $namespaceUseContext) {
    if ($astClassLike->hasModifier(T_INTERFACE)) {
      $interfaceAliases = $astClassLike->getExtendsAliases();
    }
    else {
      $interfaceAliases = $astClassLike->getImplementsAliases();
    }
    $ownInterfaceNames = array();
    foreach ($interfaceAliases as $interfaceAlias) {
      $ownInterfaceNames[] = $namespaceUseContext->aliasGetName($interfaceAlias);
    }
    return new OwnInterfaces_FromNames($classIndex, $ownInterfaceNames);
  }

}
