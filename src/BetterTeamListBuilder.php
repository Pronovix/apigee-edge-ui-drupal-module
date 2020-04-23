<?php

declare(strict_types = 1);

namespace Drupal\apigee_edge_ui;

use Drupal\Core\Entity\EntityInterface;
use Drupal\apigee_edge_teams\Entity\ListBuilder\TeamListBuilder;

/**
 * Advanced list builder for developer apps.
 */
class BetterTeamListBuilder extends TeamListBuilder {

  use BetterTeamListTrait;

  /**
   * {@inheritdoc}
   */
  public function render(): array {
    $build = parent::render();
    $this->buildTeamListContent($build);
    return $build;
  }

  /**
   * {@inheritdoc}
   */
  protected function getDefaultOperations(EntityInterface $entity): array {
    return $this->getBetterOperations($entity);
  }

}
