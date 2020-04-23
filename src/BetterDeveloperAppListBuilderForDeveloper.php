<?php

declare(strict_types = 1);

namespace Drupal\apigee_edge_ui;

use Drupal\Core\Entity\EntityInterface;
use Drupal\apigee_edge\Entity\ListBuilder\DeveloperAppListBuilderForDeveloper;

/**
 * Advanced list builder for developer apps.
 */
class BetterDeveloperAppListBuilderForDeveloper extends DeveloperAppListBuilderForDeveloper {

  use BetterAppListTrait;

  /**
   * {@inheritdoc}
   */
  public function render(): array {
    $build = parent::render();
    $this->buildAppListContent($build, 'canonical-by-developer');
    return $build;
  }

  /**
   * {@inheritdoc}
   */
  protected function getDefaultOperations(EntityInterface $entity): array {
    return $this->getBetterOperations($entity, 'canonical-by-developer');
  }

}
