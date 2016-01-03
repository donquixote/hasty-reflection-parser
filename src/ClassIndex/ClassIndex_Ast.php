<?php

namespace Donquixote\HastyReflectionParser\ClassIndex;

use Donquixote\HastyPhpAst\Util\UtilBase;
use Donquixote\HastyReflectionCommon\Canvas\ClassIndex\ClassIndex_SemiNative;
use Donquixote\HastyReflectionCommon\Canvas\File\FileIndex_PhpToReflection;
use Donquixote\HastyReflectionCommon\ClassLoader\ClassLoaderInterface;
use Donquixote\HastyReflectionCommon\Canvas\ClassIndex\ClassIndex_Loader;
use Donquixote\HastyReflectionParser\PhpToReflection\PhpToReflection_ViaAst;

final class ClassIndex_Ast extends UtilBase {

  /**
   * @param \Donquixote\HastyReflectionCommon\ClassLoader\ClassLoaderInterface $classLoader
   * @param bool $lazy
   *
   * @return \Donquixote\HastyReflectionCommon\Canvas\ClassIndex\ClassIndexInterface
   */
  static function createWithClassLoader(ClassLoaderInterface $classLoader, $lazy = FALSE) {
    $phpToReflection = PhpToReflection_ViaAst::create($lazy);
    $fileIndex = new FileIndex_PhpToReflection($phpToReflection);
    return new ClassIndex_Loader($fileIndex, $classLoader);
  }

  /**
   * @param bool $lazy
   *
   * @return \Donquixote\HastyReflectionCommon\Canvas\ClassIndex\ClassIndexInterface
   */
  static function createSemiNative($lazy = FALSE) {
    $phpToReflection = PhpToReflection_ViaAst::create($lazy);
    $fileIndex = new FileIndex_PhpToReflection($phpToReflection);
    return new ClassIndex_SemiNative($fileIndex);
  }
}
