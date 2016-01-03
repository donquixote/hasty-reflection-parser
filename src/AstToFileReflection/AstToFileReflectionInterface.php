<?php

namespace Donquixote\HastyReflectionParser\AstToFileReflection;

use Donquixote\HastyPhpAst\Ast\File\AstFileInterface;
use Donquixote\HastyReflectionCommon\Canvas\ClassIndex\ClassIndexInterface;

interface AstToFileReflectionInterface {

  /**
   * @param \Donquixote\HastyPhpAst\Ast\File\AstFileInterface $astFile
   * @param \Donquixote\HastyReflectionCommon\Canvas\ClassIndex\ClassIndexInterface $autoloadSource
   *
   * @return \Donquixote\HastyReflectionCommon\Reflection\File\FileReflection
   */
  function astGetFileReflection(AstFileInterface $astFile, ClassIndexInterface $autoloadSource);

}
