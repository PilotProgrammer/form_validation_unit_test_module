<?php

namespace Drupal\Tests\form_validation_unit_test_module\Unit;

use Drupal\Tests\UnitTestCase;
use Drupal\Core\Form\FormStateInterface;

abstract class FormValidationUnitTestCase extends UnitTestCase 
{
  use GeneralUnitTestHelperTrait;
  
  // A subclass must implement to tell FormValidationUnitTestCase the class name of the Form that is to be tested.
  protected abstract function getFullyNamespacedFormClassToTest();
  
  // A subclass must provide default values to be used for each form element. The array that is returned from this method
  // must be in the form of [form element name] => [default form value].
  protected abstract function getFormElementNamesAndDefaultValues();
  
  // A subclass should provide an array with the string names of all the utility methods that the Form's validateForm 
  // will call on the same Form class during the unit test.
  // All methods of the Form class will be mocked, except for the validateForm method, and the methods whose names are returned
  // in the following method
  protected abstract function getMethodsToNotMockExcludingValidateFormMethod();
  
  // A subclass can call this method to make sure the a FormStateInterface::setErrorByName is called for a specific
  // form element using a given combination of form element input values. 
  // The input values override the default values that are provided by getFormElementNamesAndDefaultValues.
  protected function assertFormElementCausesErrorMessageWithInvalidFormInput($name_of_form_element_generating_error, $expected_error_message, $form_element_names_and_input_values)
  {
    $mock_form = $this->getMockedFormObject();
    $form_state = $this->getMockedFormState();
    $form_structure = [];

    $form_element_names_and_input_values_to_use_for_test = $this->getFormValuesToUseForTestBasedOnDefaultAndOverrideValues($form_element_names_and_input_values);

    $form_state->expects($this->any())
      ->method('getValues')
      ->will($this->returnValue($form_element_names_and_input_values_to_use_for_test));

    // 1
//      this doesn't work and must do getInvcation technique instead, because setErrorByName also called for other invalid input. 
//    $form_state->expects($form_state_set_error_by_name_spy = $this->any())
//      ->method('setErrorByName')
//      ->with($name_of_form_element_generating_error, $expected_error_message);

    // 2
    $form_state->expects($form_state_set_error_by_name_spy = $this->any())
      ->method('setErrorByName');

    $mock_form->validateForm($form_structure, $form_state);

    $parameters = [
      $name_of_form_element_generating_error, 
      $expected_error_message,
    ];

    // 3
    $method_invoked_with_valid_parameters = $this->checkThatMethodCalledAtLeastOnceWithDesiredParameters($form_state_set_error_by_name_spy, $parameters, 'setErrorByName');
    $this->assertTrue($method_invoked_with_valid_parameters, "Method setErrorByName was never called with valid parameters");
  }

  // A subclass can call this method to make sure that FormStateInterface::setErrorByName is never called for a
  // given combination of form element input values. 
  // The input values override the default values that are provided by getFormElementNamesAndDefaultValues.
  protected function assertFormElementDoesNotCausesErrorMessageWithValidFormInput($form_element_names_and_input_values)
  {
    $mock_form = $this->getMockedFormObject();
    $form_state = $this->getMockedFormState();
    $form_structure = [];

    $form_element_names_and_input_values_to_use_for_test = $this->getFormValuesToUseForTestBasedOnDefaultAndOverrideValues($form_element_names_and_input_values);

    // 1
    $form_state->expects($this->any())
      ->method('getValues')
      ->will($this->returnValue($form_element_names_and_input_values_to_use_for_test));

    // 2
    $form_state->expects($this->exactly(0))
      ->method('setErrorByName');

    $mock_form->validateForm($form_structure, $form_state);
  }

  protected function getFormValuesToUseForTestBasedOnDefaultAndOverrideValues($form_names_and_override_input_values)
  {
    $form_element_names_and_default_values = $this->getFormElementNamesAndDefaultValues();
    $form_element_names_and_values_to_return = array_merge($form_element_names_and_default_values, $form_names_and_override_input_values);
    return $form_element_names_and_values_to_return;
  }

  protected function getMockedFormObject()
  {
    $methods_to_not_mock = ['validateForm']; // obviously, as this is the method we will be testing
    $methods_to_not_mock = array_merge($methods_to_not_mock, $this->getMethodsToNotMockExcludingValidateFormMethod());
    $form_class_name = $this->getFullyNamespacedFormClassToTest();
    $all_methods_to_mock = $this->getAllMethodsExcept($methods_to_not_mock, $form_class_name); 
    
    $fully_namespaced_form_class_to_test = $this->getFullyNamespacedFormClassToTest();

    $mock_form = $this->getMockBuilder($fully_namespaced_form_class_to_test)
      ->setMethods($all_methods_to_mock)
      ->disableOriginalConstructor()
      ->getMock();

    return $mock_form;
  }

  protected function getMockedFormState()
  {
    $form_state = $this->getMockBuilder('\Drupal\Core\Form\FormStateInterface')->getMock(); 
    $this->assertTrue($form_state instanceof \Drupal\Core\Form\FormStateInterface);
    return $form_state;
  }
}