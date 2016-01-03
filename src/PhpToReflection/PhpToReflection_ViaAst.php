<?php

namespace Donquixote\HastyReflectionParser\PhpToReflection;

use Donquixote\HastyPhpAst\PhpToAst\PhpToAstInterface;
use Donquixote\HastyPhpParser\PhpToAst\PhpToAst_Parser;
use Donquixote\HastyReflectionCommon\Canvas\ClassIndex\ClassIndexInterface;
use Donquixote\HastyReflectionCommon\PhpToReflection\PhpToReflectionInterface;
use Donquixote\HastyReflectionParser\AstToFileReflection\AstToFileReflection;
use Donquixote\HastyReflectionParser\AstToFileReflection\AstToFileReflectionInterface;

class PhpToReflection_ViaAst implements PhpToReflectionInterface {

  /**
   * @var \Donquixote\HastyPhpAst\PhpToAst\PhpToAstInterface
   */
  private $phpToAst;

  /**
   * @var \Donquixote\HastyReflectionParser\AstToFileReflection\AstToFileReflectionInterface
   */
  private $astToFileReflection;

  /**
   * @param bool $lazy
   *
   * @return \Donquixote\HastyReflectionParser\PhpToReflection\PhpToReflection_ViaAst
   */
  static function create($lazy = FALSE) {
    return new self(PhpToAst_Parser::create($lazy), new AstToFileReflection());
  }

  /**
   * @param \Donquixote\HastyPhpAst\PhpToAst\PhpToAstInterface $phpToAst
   * @param \Donquixote\HastyReflectionParser\AstToFileReflection\AstToFileReflectionInterface $astToFileReflection
   */
  function __construct(PhpToAstInterface $phpToAst, AstToFileReflectionInterface $astToFileReflection) {
    $this->phpToAst = $phpToAst;
    $this->astToFileReflection = $astToFileReflection;
  }

  /**
   * @param string $php
   *   Entire contents of a PHP file.
   * @param \Donquixote\HastyReflectionCommon\Canvas\ClassIndex\ClassIndexInterface $autoloadSource
   *
   * @return \Donquixote\HastyReflectionCommon\Reflection\File\FileReflection|null
   */
  function phpGetReflection($php, ClassIndexInterface $autoloadSource) {
    $fileAst = $this->phpToAst->phpGetAst($php);
    if (NULL === $fileAst) {
      return NULL;
    }
    return $this->astToFileReflection->astGetFileReflection($fileAst, $autoloadSource);
  }
}
