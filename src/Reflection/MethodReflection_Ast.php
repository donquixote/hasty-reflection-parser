<?php

namespace Donquixote\HastyReflectionParser\Reflection;

use Donquixote\HastyPhpAst\Ast\FunctionLike\AstFunctionLikeInterface;
use Donquixote\HastyReflectionCommon\Reflection\ClassLike\ClassLikeReflectionInterface;
use Donquixote\HastyReflectionCommon\Reflection\FunctionLike\MethodReflectionBase;

class MethodReflection_Ast extends MethodReflectionBase {

  /**
   * @var \Donquixote\HastyPhpAst\Ast\FunctionLike\AstFunctionLikeInterface
   */
  private $astNode;

  /**
   * @param \Donquixote\HastyReflectionCommon\Reflection\ClassLike\ClassLikeReflectionInterface $declaringClass
   * @param \Donquixote\HastyPhpAst\Ast\FunctionLike\AstFunctionLikeInterface $astNode
   */
  function __construct(ClassLikeReflectionInterface $declaringClass, AstFunctionLikeInterface $astNode) {
    parent::__construct($declaringClass);
    $this->astNode = $astNode;
  }


  /**
   * @return string
   */
  function getName() {
    return $this->astNode->getName();
  }

  /**
   * @return string
   */
  function getDocComment() {
    return $this->astNode->getDocComment();
  }

  /**
   * @return bool
   */
  function isByReference() {
    return $this->astNode->hasModifier('&');
  }

  /**
   * @return bool
   */
  function isStatic() {
    return $this->astNode->hasModifier(T_STATIC);
  }

  /**
   * @return bool
   */
  function isAbstract() {
    return $this->astNode->hasModifier(T_ABSTRACT);
  }

  /**
   * @return bool
   */
  function isPrivate() {
    return $this->astNode->hasModifier(T_PRIVATE);
  }

  /**
   * @return bool
   */
  function isPublic() {
    return $this->astNode->hasModifier(T_PUBLIC);
  }

  /**
   * @return bool
   */
  function isProtected() {
    return $this->astNode->hasModifier(T_PROTECTED);
  }
}
