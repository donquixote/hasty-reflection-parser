<?php

namespace Donquixote\HastyReflectionParser\AutoloadSource;

use Donquixote\HastyPhpAst\Util\UtilBase;
use Donquixote\HastyReflectionCommon\Canvas\File\FileIndex_PhpToReflection;
use Donquixote\HastyReflectionCommon\ClassLoader\ClassLoaderInterface;
use Donquixote\HastyReflectionCommon\Canvas\ClassIndex\ClassIndex_Loader;
use Donquixote\HastyReflectionParser\PhpToReflection\PhpToReflection_ViaAst;

final class AutoloadSource_Ast extends UtilBase {

  /**
   * @param \Donquixote\HastyReflectionCommon\ClassLoader\ClassLoaderInterface $classLoader
   * @param bool $lazy
   *
   * @return \Donquixote\HastyReflectionCommon\Canvas\ClassIndex\ClassIndex_Loader
   */
  static function createWithClassLoader(ClassLoaderInterface $classLoader, $lazy = FALSE) {
    $phpToReflection = PhpToReflection_ViaAst::create($lazy);
    $fileIncludeCanvas = new FileIndex_PhpToReflection($phpToReflection);
    return new ClassIndex_Loader($fileIncludeCanvas, $classLoader);
  }
}
