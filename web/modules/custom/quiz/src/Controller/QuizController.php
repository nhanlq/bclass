<?php

namespace Drupal\quiz\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;

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
      foreach ($term->parents as $parent) {
        if ($parent == 0) {
          $data[$term->tid] = [
            'parent' => \Drupal\taxonomy\Entity\Term::load($term->tid),
            'children' => $this->getChildren($term->tid),
          ];
        }
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

}
