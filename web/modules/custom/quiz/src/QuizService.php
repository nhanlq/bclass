<?php

namespace Drupal\quiz;

use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;
use Drupal\paragraphs\Entity\Paragraph;

class QuizService {

  /**
   * @param $quiz_id
   *
   * @return \Drupal\Core\Entity\EntityBase[]|\Drupal\Core\Entity\EntityInterface[]|\Drupal\node\Entity\Node[]|false
   */
  public function getSection($quiz_id) {
    $ids = \Drupal::entityQuery('node')
      ->condition('type', 'section')
      ->condition('quiz', $quiz_id)
      ->condition('status', NodeInterface::PUBLISHED)
      ->execute();
    if ($ids) {
      return Node::loadMultiple($ids);
    }
    return FALSE;

  }

  /**
   * @param $section_id
   *
   * @return array
   */
  public function getQuestion($section_id) {
    $node = Node::load($section_id);
    $questions = $node->get('questions')->getValue();
    $data = [];
    foreach ($questions as $q) {
      $answers = [];
      $paragraph = Paragraph::load($q['target_id']);
      foreach ($paragraph->get('answers')->getValue() as $ans) {
        $para_ans = Paragraph::load($ans['target_id']);
        $answers[$ans['target_id']] = '<span>'.$para_ans->get('answer_label')->value . '</span> ' . $para_ans->get('answer')->value;
      }
      $data[$q['target_id']] = [
        'question' => '<div class="q-number">'.$paragraph->get('number')->value . '</div> <div class="q-desc">' . $paragraph->get('question')->value.'</div>',
        'answers' => $answers,
      ];
    }
    return $data;
  }

  /**
   * @param $cid
   *
   * @return array
   */
  public function getLinks($cid){
    $ids = \Drupal::entityQuery('node')
      ->condition('type', 'quiz')
      ->condition('collection', $cid)
      ->condition('status', NodeInterface::PUBLISHED)
      ->execute();
    $data = [];
    if ($ids) {
      $nodes = Node::loadMultiple($ids);
      foreach($nodes as $node){
        if($node->get('quiz_type')->value == 'Reading'){
          $data['reading'] = $node->id();
        }
        if($node->get('quiz_type')->value == 'Listening'){
          $data['listening'] = $node->id();
        }
        if($node->get('quiz_type')->value == 'Writing'){
          $data['writing'] = $node->id();
        }
      }
    }
    return $data;
  }



}