<?php

namespace Drupal\banner\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Displays a Social Login block.
 *
 * @Block(
 *   id = "banner_home",
 *   admin_label = @Translation("Banner Home Slider"),
 * )
 */
class BannerHome extends BlockBase {

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
     '#theme' =>['slider_home'],
     '#nodes' =>$this->getBanner(),
   ];
  }

  /**
   * @return \Drupal\Core\Entity\EntityBase[]|\Drupal\Core\Entity\EntityInterface[]|\Drupal\node\Entity\Node[]
   */
  public function getBanner(){
    $ids = \Drupal::entityQuery('node')
      ->condition('type','banner')
      ->condition('status',1)
      ->sort('created','ASC')
      ->execute();
    $nodes = \Drupal\node\Entity\Node::loadMultiple($ids);
    return $nodes;
  }
}
