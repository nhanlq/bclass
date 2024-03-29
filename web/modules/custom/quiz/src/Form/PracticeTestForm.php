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
    $i = 1;
      foreach ($sections as $sid => $section) {

        $form['section_title_' . $sid] = [
          '#type' => 'markup',
          '#markup' => '<h4>' . $section->get('title')->value . '</h4>',
          '#prefix' => '<div class="test-form-item">'
        ];
        if($quiz->get('quiz_type')->value === 'Reading' || $quiz->get('quiz_type')->value === 'Writing'){
          $form['section_' . $sid] = [
            '#type' => 'markup',
            '#markup' => $section->get('body')->value,
          ];
        }
        if($quiz->get('quiz_type')->value === 'Listening'){
          $form['section_' . $sid] = [
            '#type' => 'markup',
            '#markup' => render($section->audio->entity->field_media_audio_file->view('default')),
          ];
        }
       if($quiz->get('quiz_type')->value !== 'Writing'){
         $questions = $service->getQuestion($sid);
         foreach ($questions as $qid => $question) {
           $form['question_'.$qid] = [
             '#type' => 'radios',
             '#title' => $question['question'],
             '#options' => $question['answers'],
             '#attributes' => ['class'=>['q-number-test'], 'data-quiz'=>$qid]
           ];
         }
       }else{
         $form['writing_'.$sid] = [
           '#type' => 'textarea',
           '#title' => t('Bài viết '.$i),
           '#rows' => 20,
         ];
       }


        $form['close_div_'.$sid] = [
          '#type' => 'markup',
          '#suffix' => '</div>',
        ];

       $i++;
      }
    $form['quiz'] = [
      '#type' => 'hidden',
      '#value' => $quiz_id,
    ];
    $form['quiz_type'] = [
      '#type' => 'hidden',
      '#value' => $quiz->get('quiz_type')->value,
    ];
    $form['collection'] = [
      '#type' => 'hidden',
      '#value' => $quiz->get('collection')->target_id,
    ];
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
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $user = \Drupal::currentUser();
    $node = Node::create([
      'type' => 'result',
      'title' => 'Result of '.$user->id().' quiz '.$form_state->getValue('quiz'),
      'quiz' => ['target_id'=>$form_state->getValue('quiz')],
      'user' => ['target_id' => $user->id()],
      'result' => json_encode($form_state->getValues())
    ]);
    $node->save();
    \Drupal::messenger()
      ->addMessage('Nộp bài thành công.');

  }


}