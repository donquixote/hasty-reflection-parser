<?php

namespace Donquixote\HastyReflectionParser\Reflection\ClassLike;

use Donquixote\HastyPhpAst\Ast\ClassLike\AstClassLikeInterface;
use Donquixote\HastyReflectionCommon\NamespaceUseContext\NamespaceUseContextInterface;
use Donquixote\HastyReflectionCommon\Reflection\ClassLike\Header\ClassLikeHeaderInterface;

class ClassLikeHeader_Ast implements ClassLikeHeaderInterface {

  /**
   * @var string
   */
  private $qcn;

  /**
   * @var \Donquixote\HastyPhpAst\Ast\ClassLike\AstClassLikeInterface
   */
  private $astNode;

  /**
   * @param \Donquixote\HastyReflectionCommon\NamespaceUseContext\NamespaceUseContextInterface $namespaceUseContext
   * @param \Donquixote\HastyPhpAst\Ast\ClassLike\AstClassLikeInterface $astNode
   *
   * @return \Donquixote\HastyReflectionParser\Reflection\ClassLike\ClassLikeHeader_Ast
   */
  static function create(NamespaceUseContextInterface $namespaceUseContext, AstClassLikeInterface $astNode) {
    $qcn = $astNode->getShortName();
    if (NULL !== $namespaceQcn = $namespaceUseContext->getNamespaceName()) {
      $qcn = $namespaceQcn . '\\' . $qcn;
    }
    return new self($qcn, $astNode);
  }

  /**
   * @param string $qcn
   * @param \Donquixote\HastyPhpAst\Ast\ClassLike\AstClassLikeInterface $astNode
   */
  function __construct($qcn, AstClassLikeInterface $astNode) {
    $this->qcn = $qcn;
    $this->astNode = $astNode;
  }

  /**
   * @return string|null
   */
  function getDocComment() {
    return $this->astNode->getDocComment();
  }

  /**
   * @return string
   */
  function getName() {
    return $this->qcn;
  }

  /**
   * @return bool
   */
  function isInterface() {
    return $this->astNode->hasModifier(T_INTERFACE);
  }

  /**
   * @return bool
   */
  function isClass() {
    return $this->astNode->hasModifier(T_CLASS);
  }

  /**
   * @return bool
   */
  function isTrait() {
    return $this->astNode->hasModifier(T_TRAIT);
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
  function isFinal() {
    return $this->astNode->hasModifier(T_FINAL);
  }
}
