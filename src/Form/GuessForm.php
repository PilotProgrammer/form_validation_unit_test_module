<?php

namespace Drupal\form_validation_unit_test_module\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class GuessForm extends FormBase 
{
  const select_list_key = 'select_list_key';
  const text_field_key = 'text_field_key';
  const submit_button_key = 'form_select_list_key';
  
  const question_selection_default_key = 'question_selection_default_key';
  const favorite_number_key = 'favorite_number_key';
  const favorite_color_key = 'favorite_color_key';
  const favorite_aircraft_make_key = 'favorite_aircraft_make_key';
    
  const favorite_number = '14.5';
  const favorite_color = 'light cyan';
  const favorit_aircraft_make = 'Cessna';
    
  const text_field_default_value = 'Enter your guess here';
  
  public function getFormId()
  {
    return 'form_test';
  }
  
  public function getAnswerToQuestion($question_key)
  {
    $questions_and_answers = [
      GuessForm::question_selection_default_key => null,
      GuessForm::favorite_number_key => GuessForm::favorite_number,
      GuessForm::favorite_color_key => GuessForm::favorite_color,
      GuessForm::favorite_aircraft_make_key => GuessForm::favorit_aircraft_make,
    ];

    return $questions_and_answers[$question_key];
  }
  
  public function getQuestionSelectionList()
  {
    $questions = [
      GuessForm::question_selection_default_key => 'Select a question to answer...',
      GuessForm::favorite_number_key => 'What\'s my favorite number?',
      GuessForm::favorite_color_key => 'What\'s my favorite color?',
      GuessForm::favorite_aircraft_make_key => 'What\'s my favorite aircraft make?',
    ];
    return $questions;
  }
  
  public function buildForm(array $form, FormStateInterface $form_state)
  {
    $form = [];
    
    $form[GuessForm::select_list_key] = [
      '#type' => 'select',
      '#options' => $this->getQuestionSelectionList(),
      '#default_value' => GuessForm::question_selection_default_key,
    ];
    
    $form[GuessForm::text_field_key] = [
      '#type' => 'textfield',
      '#default_value' => GuessForm::text_field_default_value,
    ];
    
    $form[GuessForm::submit_button_key] = [
      '#type' => 'submit',
      '#value' => 'Guess!',
    ];
    
    return $form;
  }
  
  public function validateForm(array &$form, FormStateInterface $form_state)
  {
    $values = $form_state->getValues();

    $selected_question_key = $values[GuessForm::select_list_key];
    $correct_answer_to_selected_question = $this->getAnswerToQuestion($selected_question_key);
    $entered_answer_value = $values[GuessForm::text_field_key];
        
    if (empty($entered_answer_value))
      $form_state->setErrorByName(GuessForm::text_field_key, "You didn't guess anything!");

    if ($selected_question_key == GuessForm::question_selection_default_key)
      $form_state->setErrorByName(GuessForm::select_list_key, "Please select a question to guess an answer!");

    if ($entered_answer_value == GuessForm::text_field_default_value)
      $form_state->setErrorByName(GuessForm::text_field_key, "You should remove the default guess and enter your own.");
    
    if ($entered_answer_value != $correct_answer_to_selected_question)
    {
      $entered_answer_is_correct_for_a_question_other_than_what_was_selected = false;
      $question_selection_list = $this->getQuestionSelectionList();

      foreach ($question_selection_list as $question_selection_key => $question_selection_value)
      {
        $correct_answer_to_currently_iterated_question = $this->getAnswerToQuestion($question_selection_key);
        
        if ($correct_answer_to_currently_iterated_question === $entered_answer_value)
        {
          $entered_answer_is_correct_for_a_question_other_than_what_was_selected = true;
          break;
        }
      }

      if ($entered_answer_is_correct_for_a_question_other_than_what_was_selected)
      {
        $form_state->setErrorByName(
          GuessForm::text_field_key, "You entered an incorrect answer to the question you were trying to guess, "
          . "but the answer was ironically correct for a different question. Change the question and then click 'guess again'!"
        );
      }
      else
      {
        $form_state->setErrorByName(GuessForm::text_field_key, "You entered an incorrect answer to the question you were trying to guess.");
      }
    }
  }
  
  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    drupal_set_message("Thanks for playing! You Won!! Sorry no prizes though.");
  }
}

