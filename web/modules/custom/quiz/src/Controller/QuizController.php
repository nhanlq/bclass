<?php

namespace Drupal\quiz\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;
use \Drupal\taxonomy\Entity\Term;

/**
 * Contains the callback handler used by the quiz Module.
 */
class QuizController extends ControllerBase {
  /**
   * @inheritDoc
   */
  public function index() {
    return [
      '#theme' => ['b1_practice_test'],
      '#terms' => $this->getTerms(),
    ];
  }

  /**
   * @return array
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function getTerms() {
    $data = [];
    $vid = 'category';
    $terms = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->loadTree($vid);

    foreach ($terms as $term) {
        if (reset($term->parents) == 0) {
          $data[$term->tid] = [
            'parent' => Term::load($term->tid),
            'children' => $this->getChildren($term->tid),
          ];
        }
    }
    return $data;
  }

  /**
   * @param $parent
   *
   * @return mixed
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function getChildren($parent) {
    $vid = 'category';
    $terms = \Drupal::entityTypeManager()
      ->getStorage('taxonomy_term')
      ->loadTree($vid, $parent);
    foreach ($terms as $term) {
      if ($this->getCollectionByTid($term->tid)) {
        $term->collection = $this->getCollectionByTid($term->tid);
      }
      else {
        $term->collection = 0;
      }
    }
    return $terms;
  }

  /**
   * @param $tid
   *
   * @return false|mixed
   */
  public function getCollectionByTid($tid) {
    $id = FALSE;
    $ids = \Drupal::entityQuery('node')
      ->condition('type', 'collection')
      ->condition('student',0)
      ->condition('status', 1)
      ->condition('category', $tid)
      ->execute();
    if ($ids) {
      $id = reset($ids);
    }
    return $id;
  }

  /**
   * @param $nid
   */
  public function practice_test($nid) {
    $node = Node::load($nid);
  }

  /**
   * @return array
   */
  public function testTitle($nid): array {
    $node = Node::load($nid);
    return [
      '#markup' => $node->getTitle(),
      '#allowed_tags' => \Drupal\Component\Utility\Xss::getHtmlTagList(),
    ];
  }

  public function result($nid){
    $currentUser = \Drupal::currentUser();
    $node = Node::load($nid);
    $user_id = $node->get('user')->target_id;
    $result = $this->GetResult($node);
    $answers = [];
    $origin_user = false;
    if ($currentUser->id() == $user_id){
      $origin_user = true;
    }
    $right = 0;
    $count = 0;
    $score = 0;
    $quiz = Node::load($node->get('quiz')->target_id);
    $collection = Node::load($node->get('collection')->target_id);
    foreach($result as $key => $r){
      $question = null;
      $ans = null;
      if(strpos($key,"question_") !== false){

        $question = Paragraph::load(str_replace('question_','',$key));
        if($r){
          $ans = Paragraph::load($r);
        }

        if($ans && $ans->get('correct')->value == 1){
          $right += 1;
        }
        $answers[] = ['question' => $question,'answer' => $ans];
        $count += 1;
      }
    }
    if ($node->get('quiz_type')->value == 'Reading' || $node->get('quiz_type')->value == 'Listening') {
      $evag = $quiz->get('total_score')->value / $count;
      $score = number_format($evag * $right,2);
    }


    return [
      '#theme' => ['b1_practice_test_result'],
      '#node' => $node,
      '#quiz' => $quiz,
      '#collection' => $collection,
      '#results' => $answers,
      '#sections' => $this->getSectionByQuiz($node->get('quiz')->target_id),
      '#score' => $score,
      '#origin_user' => $origin_user,
      '#result' => $result,
    ];
  }

  public function GetResult($node){
    $result = json_decode($node->get('result')->value);
    return $result;
  }

  /**
   * @return array
   */
  public function resultTitle($nid): array {
    $node = Node::load($nid);
    $quiz = Node::load($node->get('quiz')->target_id);
    return [
      '#markup' => $quiz->getTitle(),
      '#allowed_tags' => \Drupal\Component\Utility\Xss::getHtmlTagList(),
    ];
  }

  public function getSectionByQuiz($qid){
      $ids = \Drupal::entityQuery('node')
        ->condition('status', 1)
        ->condition('type','section')
        ->condition('quiz', $qid)
        ->execute();
      $result = Node::loadMultiple($ids);
      return $result;
  }

}
