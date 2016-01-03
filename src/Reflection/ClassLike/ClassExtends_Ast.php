<?php

namespace Donquixote\HastyReflectionParser\Reflection\ClassLike;

use Donquixote\HastyPhpAst\Ast\ClassLike\AstClassLikeInterface;
use Donquixote\HastyReflectionCommon\Canvas\ClassIndex\ClassIndexInterface;
use Donquixote\HastyReflectionCommon\NamespaceUseContext\NamespaceUseContextInterface;
use Donquixote\HastyReflectionCommon\Reflection\ClassLike\ClassExtends\ClassExtends_ByName;
use Donquixote\HastyReflectionCommon\Reflection\ClassLike\ClassExtends\ClassExtends_None;
use Donquixote\HastyReflectionCommon\Util\UtilBase;

final class ClassExtends_Ast extends UtilBase {

  /**
   * @param \Donquixote\HastyPhpAst\Ast\ClassLike\AstClassLikeInterface $astClassLike
   * @param \Donquixote\HastyReflectionCommon\Canvas\ClassIndex\ClassIndexInterface $classIndex
   * @param \Donquixote\HastyReflectionCommon\NamespaceUseContext\NamespaceUseContextInterface $namespaceUseContext
   *
   * @return \Donquixote\HastyReflectionCommon\Reflection\ClassLike\ClassExtends\ClassExtendsInterface
   */
  static function createFromAst(AstClassLikeInterface $astClassLike, ClassIndexInterface $classIndex, NamespaceUseContextInterface $namespaceUseContext) {

    $extendsAliases = $astClassLike->getExtendsAliases();
    if ($astClassLike->hasModifier(T_INTERFACE)) {
      return new ClassExtends_None();
    }
    $parentClassAlias = reset($extendsAliases);
    if (FALSE === $parentClassAlias) {
      return new ClassExtends_None();
    }
    return new ClassExtends_ByName($classIndex, $namespaceUseContext->aliasGetName($parentClassAlias));
  }

}
