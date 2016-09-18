<?php

namespace Drupal\Tests\form_validation_unit_test_module\Unit;

use Drupal\form_validation_unit_test_module\Form\GuessForm;

/**
 * @group form_validation_unit_test_module
 */
class GuessFormValidateMethodTestCase extends FormValidationUnitTestCase 
{
  const form_element_name_key = 'form_element_name_key';
  const form_element_default_value_key = 'form_element_default_value_key';
  const form_element_name_that_should_generate_error_message_key = 'form_element_name_that_should_generate_error_message_key';
  const expected_error_message_key = 'expected_error_message_key';

  protected $form_element_names_and_default_values;
  protected $fully_namespaced_form_class_to_test;
  
  protected function getFullyNamespacedFormClassToTest()
  {
    return 'Drupal\form_validation_unit_test_module\Form\GuessForm';
  }
  
  protected function getFormElementNamesAndDefaultValues()
  {
    $form_element_names_and_default_values_for_test = [
      GuessForm::select_list_key => GuessForm::favorite_number_key,
      GuessForm::text_field_key => GuessForm::favorite_number
    ];
    
    return $form_element_names_and_default_values_for_test;
  }
  
  protected function getMethodsToNotMockExcludingValidateFormMethod()
  {
    return ['getQuestionSelectionList', 'getAnswerToQuestion'];
  }
  
  public function testFormValidationNoErrorThrownForCorrectInput() {
    $form_element_names_and_input_values = [
      GuessForm::select_list_key => GuessForm::favorite_aircraft_make_key,
      GuessForm::text_field_key => 'Cessna'      
    ];

    $this->assertFormElementDoesNotCausesErrorMessageWithValidFormInput($form_element_names_and_input_values);
  }
  
  public function testFormValidationNoAnswerGiven() {
    $form_element_names_and_input_values = [
      GuessForm::text_field_key => ""
    ];

    $expected_error_message = "You didn't guess anything!";

    $this->assertFormElementCausesErrorMessageWithInvalidFormInput(GuessForm::text_field_key, $expected_error_message, $form_element_names_and_input_values);
  }
  
  public function testFormValidationNoQuestionSelected() {
    $form_element_names_and_input_values = [
      GuessForm::select_list_key => GuessForm::question_selection_default_key
    ];

    $expected_error_message = "Please select a question to guess an answer!";

    $this->assertFormElementCausesErrorMessageWithInvalidFormInput(GuessForm::select_list_key, $expected_error_message, $form_element_names_and_input_values);
  }
  
  public function testFormValidationDefaultTextFieldNotChanged() {
    $form_element_names_and_input_values = [
      GuessForm::text_field_key => GuessForm::text_field_default_value
    ];

    $expected_error_message = "You should remove the default guess and enter your own.";

    $this->assertFormElementCausesErrorMessageWithInvalidFormInput(GuessForm::text_field_key, $expected_error_message, $form_element_names_and_input_values);
  }
  
  public function testFormValidationIncorrectAnswerDetectedWhenIncorrectAnswerEntered() {
    $form_element_names_and_input_values = [
      GuessForm::select_list_key => GuessForm::favorite_number_key,
      GuessForm::text_field_key => '-12345'
    ];

    $expected_error_message = "You entered an incorrect answer to the question you were trying to guess.";

    $this->assertFormElementCausesErrorMessageWithInvalidFormInput(GuessForm::text_field_key, $expected_error_message, $form_element_names_and_input_values);
  }
  
  public function testFormValidationCorrectAnswerGivenForNotCurrentlySelectedQuestion() {
    $form_element_names_and_input_values = [
      GuessForm::select_list_key => GuessForm::favorite_aircraft_make_key
    ];

    $expected_error_message = "You entered an incorrect answer to the question you were trying to guess, "
      . "but the answer was ironically correct for a different question. Change the question and then click 'guess again'!";

    $this->assertFormElementCausesErrorMessageWithInvalidFormInput(GuessForm::text_field_key, $expected_error_message, $form_element_names_and_input_values);
  }
}