<?php

namespace Donquixote\HastyReflectionParser\Tests;

use Donquixote\HastyReflectionCommon\Canvas\ClassIndex\ClassIndex_IncludeBase;
use Donquixote\HastyReflectionCommon\Canvas\ClassIndex\ClassIndex_Native;
use Donquixote\HastyReflectionCommon\Canvas\ClassIndex\ClassIndex_SemiNative;
use Donquixote\HastyReflectionCommon\Canvas\ClassIndex\ClassIndexBase;
use Donquixote\HastyReflectionCommon\Canvas\ClassIndex\ClassIndexInterface;
use Donquixote\HastyReflectionCommon\ClassLoader\ClassLoader_Composer;
use Donquixote\HastyReflectionCommon\ClassLoader\ClassLoader_Native;
use Donquixote\HastyReflectionCommon\Reflection\ClassLike\Body\Own\OwnBody_DecoratorTrait;
use Donquixote\HastyReflectionCommon\Reflection\ClassLike\ClassLikeReflectionInterface;
use Donquixote\HastyReflectionCommon\Reflection\FunctionLike\MethodReflectionInterface;
use Donquixote\HastyReflectionParser\ClassIndex\ClassIndex_Ast;
use Donquixote\HastyReflectionParser\Tests\Fixture\B;
use Donquixote\HastyReflectionParser\Tests\Fixture\C;
use Donquixote\HastyReflectionParser\Tests\Fixture\D;

/**
 * @see \Donquixote\HastyReflectionCommon\Tests\ClassIndexTest
 */
class ClassIndexTest extends \PHPUnit_Framework_TestCase {

  /**
   * @param \Donquixote\HastyReflectionCommon\Canvas\ClassIndex\ClassIndexInterface $classIndex
   * @param string $class
   *
   * @dataProvider provideClassIndexArgs()
   */
  function testClassIndex(ClassIndexInterface $classIndex, $class) {
    $classReflection = $classIndex->classGetReflection($class);
    $reflectionClass = new \ReflectionClass($class);

    // Test name.
    $this->assertEquals($reflectionClass->getName(), $classReflection->getName());

    // Test context.
    $this->assertEquals($reflectionClass->getNamespaceName(), $classReflection->getNamespaceUseContext()->getNamespaceName());

    // Test interfaces
    foreach ($classReflection->getOwnInterfaces() as $interfaceName => $interfaceReflection) {
      $this->assertTrue($reflectionClass->implementsInterface($interfaceName));
    }

    foreach ($reflectionClass->getInterfaceNames() as $interfaceName) {
      $this->assertTrue($classReflection->extendsOrImplementsInterface($interfaceName, FALSE));
    }

    $expectedInterfaceNames = $reflectionClass->getInterfaceNames();
    if ($reflectionClass->isInterface()) {
      array_unshift($expectedInterfaceNames, $class);
    }
    $this->assertEqualSorted($expectedInterfaceNames, array_keys($classReflection->getAllInterfaces(FALSE)));

    $expectedMethodNames = array();
    $expectedOwnMethodNames = array();
    foreach ($reflectionClass->getMethods() as $method) {
      $expectedMethodNames[] = $method->getName();
      if ($method->getDeclaringClass()->getName() === $reflectionClass->getName()) {
        $expectedOwnMethodNames[] = $method->getName();
      }
    }

    $this->assertEquals($expectedOwnMethodNames, array_keys($classReflection->getOwnMethods()));
    $this->assertEqualSorted($expectedMethodNames, array_keys($classReflection->getMethods()));

    $methodReflections = $classReflection->getMethods();
    foreach ($reflectionClass->getMethods() as $reflectionMethod) {
      $methodReflection = $methodReflections[$reflectionMethod->getShortName()];
      $this->assertEqualMethods($reflectionMethod, $methodReflection);
    }
  }

  /**
   * @param \ReflectionMethod $reflectionMethod
   * @param \Donquixote\HastyReflectionCommon\Reflection\FunctionLike\MethodReflectionInterface $methodReflection
   */
  private function assertEqualMethods(\ReflectionMethod $reflectionMethod, MethodReflectionInterface $methodReflection) {
    $this->assertEquals($reflectionMethod->isAbstract(), $methodReflection->isAbstract());
    $this->assertEquals($reflectionMethod->getDeclaringClass()->getName(), $methodReflection->getDeclaringClassName());
    $this->assertEquals($reflectionMethod->getDocComment(), $methodReflection->getDocComment());
    $this->assertEquals($reflectionMethod->getShortName(), $methodReflection->getName());
    $this->assertEquals($reflectionMethod->getName(), $methodReflection->getName());
    $this->assertEquals($reflectionMethod->class . '::' . $reflectionMethod->getName(), $methodReflection->getQualifiedName());
    $this->assertEquals($reflectionMethod->returnsReference(), $methodReflection->isByReference());
    $this->assertEquals($reflectionMethod->isPrivate(), $methodReflection->isPrivate());
    $this->assertEquals($reflectionMethod->isProtected(), $methodReflection->isProtected());
    $this->assertEquals($reflectionMethod->isPublic(), $methodReflection->isPublic());
    $this->assertEquals($reflectionMethod->isStatic(), $methodReflection->isStatic());
  }

  /**
   * @param string[] $expected
   * @param string[] $actual
   */
  private function assertEqualSorted(array $expected, array $actual) {
    sort($expected);
    sort($actual);
    $this->assertEquals($expected, $actual);
  }

  /**
   * @return array[]
   */
  function provideClassIndexArgs() {
    $classes = array(
      D::class,
      C::class,
      B::class,
      ClassIndexBase::class,
      ClassIndex_IncludeBase::class,
      ClassIndex_SemiNative::class,
      ClassLikeReflectionInterface::class,
      OwnBody_DecoratorTrait::class,
    );
    $composerClassLoader = include dirname(dirname(__DIR__)) . '/vendor/autoload.php';
    $list = array();
    foreach (array(
      ClassIndex_Ast::createWithClassLoader(new ClassLoader_Composer($composerClassLoader)),
      ClassIndex_Ast::createWithClassLoader(new ClassLoader_Native()),
      ClassIndex_Ast::createSemiNative(),
      new ClassIndex_Native(),
    ) as $classIndex) {
      foreach ($classes as $class) {
        $list[] = array($classIndex, $class);
      }
    }
    return $list;
  }

}
