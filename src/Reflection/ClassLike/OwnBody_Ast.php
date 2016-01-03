<?php

namespace Donquixote\HastyReflectionParser\Reflection\ClassLike;

use Donquixote\HastyPhpAst\Ast\ClassLikeBody\AstClassLikeBodyInterface;
use Donquixote\HastyPhpAst\Ast\FunctionLike\AstFunctionLikeInterface;
use Donquixote\HastyReflectionCommon\Canvas\ClassIndex\ClassIndexInterface;
use Donquixote\HastyReflectionCommon\Reflection\ClassLike\Body\Own\OwnBodyBase;
use Donquixote\HastyReflectionParser\Reflection\MethodReflection_Ast;

class OwnBody_Ast extends OwnBodyBase {

  /**
   * @var \Donquixote\HastyPhpAst\Ast\ClassLikeBody\AstClassLikeBodyInterface
   */
  private $astBody;

  /**
   * @var \Donquixote\HastyReflectionCommon\Canvas\ClassIndex\ClassIndexInterface
   */
  private $autoloadSource;

  /**
   * @var string
   */
  private $name;

  /**
   * @param \Donquixote\HastyPhpAst\Ast\ClassLikeBody\AstClassLikeBodyInterface $astBody
   * @param \Donquixote\HastyReflectionCommon\Canvas\ClassIndex\ClassIndexInterface $autoloadSource
   * @param string $name
   */
  function __construct(AstClassLikeBodyInterface $astBody, ClassIndexInterface $autoloadSource, $name) {
    $this->astBody = $astBody;
    $this->autoloadSource = $autoloadSource;
    $this->name = $name;
  }

  /**
   * @param string $name
   *
   * @return null|\Donquixote\HastyReflectionCommon\Reflection\FunctionLike\MethodReflectionInterface
   */
  protected function findOwnMethod($name) {
    foreach ($this->astBody->getMemberNodes() as $memberNode) {
      if ($memberNode instanceof AstFunctionLikeInterface) {
        if ($name === $memberNode->getShortName()) {
          return $this->methodNodeBuildMethod($memberNode);
        }
      }
    }
    return NULL;
  }

  /**
   * @return \Donquixote\HastyReflectionCommon\Reflection\FunctionLike\MethodReflectionInterface[]
   */
  protected function findOwnMethods() {
    $methods = array();
    foreach ($this->astBody->getMemberNodes() as $memberNode) {
      if ($memberNode instanceof AstFunctionLikeInterface) {
        $methods[$memberNode->getShortName()] = $this->methodNodeBuildMethod($memberNode);
      }
    }
    return $methods;
  }

  /**
   * @param \Donquixote\HastyPhpAst\Ast\FunctionLike\AstFunctionLikeInterface $methodNode
   *
   * @return \Donquixote\HastyReflectionCommon\Reflection\FunctionLike\MethodReflectionInterface
   */
  private function methodNodeBuildMethod(AstFunctionLikeInterface $methodNode) {
    $declaringClass = $this->autoloadSource->classGetReflection($this->name);
    return new MethodReflection_Ast($declaringClass, $methodNode);
  }
}
