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
use Donquixote\HastyReflectionParser\Tests\Fixture\IEmpty;
use Donquixote\HastyReflectionParser\Tests\Fixture\IOneMethod;
use Donquixote\HastyReflectionParser\Tests\Fixture\TEmpty;
use Donquixote\HastyReflectionParser\Tests\Fixture\TOneMethod;

/**
 * @see \Donquixote\HastyReflectionCommon\Tests\ClassIndexTest
 */
class ClassIndexTest extends \PHPUnit_Framework_TestCase {

  function testInterfaceIsAbstract() {
    $this->assertFalse((new \ReflectionClass(IEmpty::class))->IsAbstract());
    $this->assertTrue((new \ReflectionClass(IOneMethod::class))->IsAbstract());
    $this->assertTrue((new \ReflectionClass(TEmpty::class))->IsAbstract());
    $this->assertTrue((new \ReflectionClass(TOneMethod::class))->IsAbstract());
  }

  function testInterfaces() {
    $this->assertEquals(array(), (new \ReflectionClass(IEmpty::class))->getInterfaceNames());
  }

  /**
   * @param \Donquixote\HastyReflectionCommon\Canvas\ClassIndex\ClassIndexInterface $classIndex
   * @param string $class
   *
   * @dataProvider provideClassIndexArgs()
   */
  function testClassIndex(ClassIndexInterface $classIndex, $class) {
    $classReflection = $classIndex->classGetReflection($class);
    $reflectionClass = new \ReflectionClass($class);

    // Test identity.
    $this->assertTrue($classReflection === $classIndex->classGetReflection($class));

    // Test class type/info.
    $expectedIsClass = !$reflectionClass->isInterface() && !$reflectionClass->isTrait();
    $this->assertEquals($reflectionClass->getName(), $classReflection->getName());
    $this->assertEquals($reflectionClass->getDocComment(), $classReflection->getDocComment());
    $this->assertEquals($reflectionClass->isInterface(), $classReflection->isInterface());
    $this->assertEquals($reflectionClass->isTrait(), $classReflection->isTrait());
    $this->assertEquals($expectedIsClass, $classReflection->isClass());
    $this->assertEquals($reflectionClass->isAbstract() && $expectedIsClass, $classReflection->isAbstractClass());

    // Test context.
    $this->assertEquals($reflectionClass->getNamespaceName(), $classReflection->getNamespaceUseContext()->getNamespaceName());

    // Test interfaces
    foreach ($classReflection->getOwnInterfaces() as $interfaceName => $interfaceReflection) {
      $this->assertTrue($reflectionClass->implementsInterface($interfaceName));
    }

    foreach ($reflectionClass->getInterfaceNames() as $interfaceName) {
      $this->assertTrue($classReflection->extendsOrImplementsInterface($interfaceName, FALSE));
    }

    $expectedAllInterfaceNames = $expectedAllAndSelfInterfaceNames = $reflectionClass->getInterfaceNames();
    if ($reflectionClass->isInterface()) {
      array_unshift($expectedAllAndSelfInterfaceNames, $class);
    }
    $this->assertEqualSorted($expectedAllAndSelfInterfaceNames, array_keys($classReflection->getAllInterfaces(TRUE)));
    $this->assertEqualSorted($expectedAllInterfaceNames, array_keys($classReflection->getAllInterfaces(FALSE)));

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

    // isAbstract() is a beast, so we test it least.
    $this->assertEquals($reflectionClass->isAbstract(), $classReflection->isAbstract());
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
    $this->assertEquals($this->formatArray($expected), $this->formatArray($actual));
  }

  /**
   * @param array $array
   *
   * @return string
   */
  private function formatArray(array $array) {
    $result = array();
    foreach ($array as $value) {
      $result[] = var_export($value, TRUE);
    }
    return "[\n  " . implode(",\n  ", $result) . "\n]";
  }

  /**
   * @return array[]
   */
  function provideClassIndexArgs() {
    $classes = array(
      IEmpty::class,
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
      ClassIndex_Ast::createWithClassLoader(new ClassLoader_Composer($composerClassLoader), FALSE),
      ClassIndex_Ast::createWithClassLoader(new ClassLoader_Native(), FALSE),
      ClassIndex_Ast::createSemiNative(FALSE),
      ClassIndex_Ast::createWithClassLoader(new ClassLoader_Composer($composerClassLoader), TRUE),
      ClassIndex_Ast::createWithClassLoader(new ClassLoader_Native(), TRUE),
      ClassIndex_Ast::createSemiNative(TRUE),
      new ClassIndex_Native(),
    ) as $classIndex) {
      foreach ($classes as $class) {
        $list[] = array($classIndex, $class);
      }
    }
    return $list;
  }

}
