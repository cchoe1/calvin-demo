<?php

namespace Drupal\calvin\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class AboutMeController.
 */
class AboutMeController extends ControllerBase {

  /**
   * View.
   *
   * @return string
   *   Return Hello string.
   */
  public function view() {
    $build = [
      '#theme' => 'calvin',
    ];
    return $build;
  }

}
