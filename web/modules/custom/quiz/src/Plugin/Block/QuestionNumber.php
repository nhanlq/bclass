<?php

namespace Drupal\quiz\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;
use Drupal\paragraphs\Entity\Paragraph;

/**
 * Displays a Social Login block.
 *
 * @Block(
 *   id = "question_number",
 *   admin_label = @Translation("Question Number Block"),
 * )
 */
class QuestionNumber extends BlockBase {

  /**
   * Disables the cache for this block.
   */
  public function getCacheMaxAge() {
    return 0;
  }

  /**
   * Returns the Social Login Block.
   */
  public function build() {
   return[
     '#theme' =>['question_number'],
     '#questions' =>$this->getNumberList(),
     '#attached' => [
       'library' => ['quiz/quiz-action']
     ]
   ];
  }

  public function getNumberList(){
    $data = [];
    $node = \Drupal::routeMatch()->getParameter('node');
    if ($node instanceof NodeInterface) {
      $service = \Drupal::service('quiz.quiz_services');
      $sections = $service->getSection($node->id());
      $i =1;
      foreach($sections as $section){
        $data [$section->id()] = [];
        $node = Node::load($section->id());
        $questions = $node->get('questions')->getValue();
        foreach ($questions as $q) {
          $answers = [];
          $paragraph = Paragraph::load($q['target_id']);
          foreach ($paragraph->get('answers')->getValue() as $ans) {
            $para_ans = Paragraph::load($ans['target_id']);
            $answers[$ans['target_id']] = '<span class="label-'.$ans['target_id'].'">'.$para_ans->get('answer_label')->value . '</span> ';
          }
          $data [$section->id()][] = [
            'question' => '<div class="number-qt q-number-'.$q['target_id'].'">'.$paragraph->get('number')->value . '</div> ',
            'answers' => $answers,
          ];
        }
        $i ++;
      }
      return $data;
    }
  }

}
