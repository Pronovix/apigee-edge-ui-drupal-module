<?php

declare(strict_types = 1);

namespace Drupal\apigee_edge_ui;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\apigee_edge_teams\Entity\ListBuilder\TeamAppListByTeam;
use Drupal\apigee_edge_teams\Entity\TeamInterface;

/**
 * Advanced list builder for developer apps.
 */
class BetterTeamAppListByTeamBuilder extends TeamAppListByTeam {

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

  /**
   * Returns the title of the "team app list by team" page.
   *
   * @return \Drupal\Core\StringTranslation\TranslatableMarkup
   *   The title of the page.
   */
  public function pageTitle(): TranslatableMarkup {
    /** @var \Drupal\apigee_edge_teams\Entity\TeamInterface $team */
    $team = $this->routeMatch->getParameter('team');

    if ($team instanceof TeamInterface) {
      $label = $this->entityTypeManager->getDefinition('team_app')
        ->getPluralLabel();
      $args = [
        '@label' => $label,
        '@team' => $team->getName(),
      ];
      $title = $this->t('@label of @team', $args);
    }
    else {
      $title = parent::pageTitle();
    }

    return $title;
  }

}
