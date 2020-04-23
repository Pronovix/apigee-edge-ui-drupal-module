<?php

declare(strict_types = 1);

namespace Drupal\apigee_edge_ui;

/**
 * Copyright (C) 2020 PRONOVIX GROUP BVBA.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301,
 * USA.
 */

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\apigee_edge_teams\Entity\ListBuilder\TeamAppListByTeam;
use Drupal\apigee_edge_teams\Entity\TeamInterface;

/**
 * Advanced list builder for team apps by team.
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
