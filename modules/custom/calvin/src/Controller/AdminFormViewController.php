<?php

namespace Drupal\calvin\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AdminFormViewController.
 */
class AdminFormViewController extends ControllerBase {

  /**
   * Displays submitted data in admin form at route calvin.admin_form
   *
   * Warning: Not suitable for sensitive data
   *
   * @return string
   *   Return Hello string.
   */
  public function view(Request $request) {
    $data = $request->query->all();
    $values = $data['param'];

    $build = [
      '#type' => 'markup',
      '#markup' => $this->t('Implement method: view'),
      '#attached' => [
        'drupalSettings' => [
          'calvin' => [
            'title' => $values['title'] ?? '',
            'body' => $values['body'] ?? '',
            'file_uri' => $values['file_uri'] ?? '',
          ]
        ]
      ]
    ];    
    return $build;
  }
}
