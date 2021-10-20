<?php

namespace Drupal\quiz\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;

class PracticeTestForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'practice_test_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $quiz_id = NULL): array {
    $service = \Drupal::service('quiz.quiz_services');
    $sections = $service->getSection($quiz_id);
    $quiz = Node::load($quiz_id);
    if($quiz->get('quiz_type')->value === 'Reading' || $quiz->get('quiz_type')->value === 'Listening'){
      foreach ($sections as $sid => $section) {
        $form['section_title_' . $sid] = [
          '#type' => 'markup',
          '#markup' => '<h4>' . $section->get('title')->value . '</h4>',
          '#prefix' => '<div class="test-form-item">'
        ];
        if($quiz->get('quiz_type')->value === 'Reading'){
          $form['section_' . $sid] = [
            '#type' => 'markup',
            '#markup' => $section->get('body')->value,
          ];
        }else{
          $form['section_' . $sid] = [
            '#type' => 'markup',
            '#markup' => render($section->audio->entity->field_media_audio_file->view('default')),
          ];
        }

        $questions = $service->getQuestion($sid);
        foreach ($questions as $qid => $question) {
          $form['question_'.$qid.'_'.$sid] = [
            '#type' => 'radios',
            '#title' => $question['question'],
            '#options' => $question['answers'],
          ];
        }
        $form['close_div_'.$sid] = [
          '#type' => 'markup',
          '#suffix' => '</div>',
        ];
      }
    }
    if($quiz->get('quiz_type')->value === 'Writing'){
      $form['writing_test'] = [
        '#type' => 'text_format',
        '#title' => t('Bài Thi Viết'),
        '#prefix' => '<div class="test-form-item">',
        '#suffix' => '</div>',
      ];
    }

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Nộp Bài'),
      '#weight' => '4',
      '#prefix' => '<div class="test-form-item-submit">',
      '#suffix' => '</div>',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    foreach ($form_state->getValues() as $key => $value) {
      // @TODO: Validate fields.
      //      if ($key == 'email' && empty($value)) {
      //        $form_state->setErrorByName('email', t('Email is required.'));
      //      }
    }
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    \Drupal::messenger()
      ->addMessage('Enrolled success for ' . $user->getEmail());
  }


}