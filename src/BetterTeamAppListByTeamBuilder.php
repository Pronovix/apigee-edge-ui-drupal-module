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
use Drupal\apigee_edge\Entity\AppInterface;
use Drupal\apigee_edge_teams\Entity\ListBuilder\TeamAppListByTeam;
use Drupal\apigee_edge_teams\Entity\TeamInterface;

/**
 * Advanced list builder for team apps by team.
 */
final class BetterTeamAppListByTeamBuilder extends TeamAppListByTeam {

  use BetterAppListTrait;

  /**
   * {@inheritdoc}
   */
  public function render(): array {
    $build = parent::render();
    // Use custom template instead of table.
    unset($build['table']['#type']);
    $build['table']['#theme'] = 'apigee_edge_ui_list';
    if (isset($this->entityTypeId)) {
      $build['table']['#type'] = $this->entityTypeId;
    }
    $build['table']['#items'] = [];
    foreach ($this->load() as $entity) {
      if (!($entity instanceof AppInterface)) {
        return [];
      }
      $app_row = $this->buildAppRow($entity);
      $app_row['operations'] = $this->buildOperations($entity);
      if ($entity->getStatus() === AppInterface::STATUS_APPROVED) {
        $warningText = $this->getWarningList($this->checkAppCredentialWarnings($entity));
        if ($warningText) {
          $app_row['warning_message'] = $warningText;
        }
      }
      $build['table']['#items'][] = $app_row;
    }
    return $build;
  }

  /**
   * {@inheritdoc}
   */
  protected function getDefaultOperations(EntityInterface $entity): array {
    $operations = parent::getDefaultOperations($entity);
    if ($operation = $this->getViewOperation($entity)) {
      $operations += ['view' => $operation];
    }
    return $operations;
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
