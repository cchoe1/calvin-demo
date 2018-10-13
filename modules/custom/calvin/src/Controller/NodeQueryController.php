<?php

namespace Drupal\calvin\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class NodeQueryController.
 */
class NodeQueryController extends ControllerBase {

  /**
   * Drupal\Core\Entity\EntityTypeManagerInterface definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a new testController object.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  /**
   * Query.
   *
   * @return JsonResponse
   */
  public function query($nid=NULL) {
    $data = [];

    if ($nid) {
      $node = $this->entityTypeManager->getStorage('node')->load($nid);
      $nodes = [$node];
    }
    else {
      $nodes = $this->entityTypeManager->getStorage('node')->loadMultiple();
    }

    foreach ($nodes as $node) {
      $data[] = [
        'nid' => $node->id(),
        'title' => $node->getTitle(),
        'body' => $node->body->value,
        'created' => $node->getCreatedTime()
      ];
    }
    return new JsonResponse([
      'data' => $data,
    ]);
  }

}
