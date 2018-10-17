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
   * Constructs a new NodeQueryController object.
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

    try {

      if ($nid) {

        if (!$this->isWholeNumber($nid)) {
          // Ideally throw custom exception to differentiate between invalid node id vs invalid param type
          throw new \Exception("The URL param must be a valid whole number");
          // TODO: throw new InvalidApiParamTypeException 
        }

        $node = $this->entityTypeManager->getStorage('node')->load($nid);
        //$nodes = array_pop($node) ?? [$node];
        $nodes = isset($node) ? [$node] : [];
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
      $code = 200;

      if (empty($data)) {
        $message = 'none found';
      }
      else {
        $message = 'success';
      }
    }
    catch (\Exception $e) {
      $code = 500;
      $message = $e->getMessage();
      $data = [];

    }
    // TODO: Create standard responder which takes 3 args possibly from a base class if we have multiple API endpoints
    //   so we don't have to return same body over and over
    return new JsonResponse([
      'data' => $data,
      'code' => $code,
      'message' => $message
    ]);
  }

  /**
   * Performs truthy check to see if value is valid whole number
   *
   * @param $value
   *   Any value
   *
   * @return bool
   *   Whether or not value is integer
   */
  private function isWholeNumber($value) {
    return (string)((int)$value) == $value;
  }

  private function isString($value) {
    // Check for string value
  }

}
