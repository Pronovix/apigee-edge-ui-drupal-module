<?php

declare(strict_types = 1);

namespace Drupal\apigee_edge_ui;

use Drupal\Core\Entity\EntityInterface;
use Drupal\apigee_edge_teams\Entity\ListBuilder\TeamAppListBuilder;

/**
 * Advanced list builder for developer apps.
 */
class BetterTeamAppListBuilder extends TeamAppListBuilder {

  use BetterAppListTrait;

  /**
   * {@inheritdoc}
   */
  public function render(): array {
    $build = parent::render();
    $this->buildAppListContent($build);
    return $build;
  }

  /**
   * {@inheritdoc}
   */
  protected function getDefaultOperations(EntityInterface $entity): array {
    return $this->getBetterOperations($entity);
  }

}
