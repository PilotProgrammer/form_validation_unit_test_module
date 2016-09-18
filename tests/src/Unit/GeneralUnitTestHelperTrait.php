<?php

namespace Drupal\Tests\form_validation_unit_test_module\Unit;

use \PHPUnit_Framework_MockObject_Matcher_InvokedRecorder;

trait GeneralUnitTestHelperTrait
{
  protected function getAllMethodsExcept(array $methods_to_exclude, $class_name)
  {
    $class = new \ReflectionClass($class_name);
    $methods = $class->getMethods();
    $method_names = [];

    for ($method_index = 0; $method_index < count($methods); $method_index++)
    {
      $method = $methods[$method_index];
      $method_name = $method->getName();

      if (!in_array($method_name, $methods_to_exclude))
        $method_names[] = $method_name;
    }

    return $method_names;
  }

  protected function checkThatMethodCalledAtLeastOnceWithDesiredParameters(PHPUnit_Framework_MockObject_Matcher_InvokedRecorder $invocation_spy, array $desired_parameters, $desired_method_name)
  {    
    $invocations = $invocation_spy->getInvocations();
    $method_invoked_with_valid_parameters = false;

    foreach ($invocations as $invocation)
    {
      $invoked_method_name = $invocation->methodName;
      $invoked_parameters = $invocation->parameters;

      $parameters_invoked_but_not_desired = array_diff($invoked_parameters, $desired_parameters);
      $parameters_desired_but_not_invoked = array_diff($desired_parameters, $invoked_parameters);

      if (count($parameters_invoked_but_not_desired) === 0
          && count($parameters_desired_but_not_invoked) === 0
          && $invoked_method_name == $desired_method_name)
      {
        $method_invoked_with_valid_parameters = true;
        break;
      }      
    }

    return $method_invoked_with_valid_parameters;
  }
}