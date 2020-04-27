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
use Drupal\Core\Template\Attribute;
use Drupal\apigee_edge\Element\StatusPropertyElement;
use Drupal\apigee_edge_teams\Entity\ListBuilder\TeamListBuilder;
use Drupal\apigee_edge_teams\Entity\TeamInterface;

/**
 * Advanced list builder for teams.
 */
final class BetterTeamListBuilder extends TeamListBuilder {

  /**
   * {@inheritdoc}
   */
  public function render(): array {
    $build = parent::render();
    // Use custom template instead of table.
    unset($build['table']['#type']);
    if (isset($this->entityTypeId)) {
      $build['table']['#type'] = $this->entityTypeId;
    }
    $build['table']['#theme'] = 'apigee_edge_ui_list';

    // Build the team rows from scratch.
    $build['table']['#items'] = [];
    foreach ($this->load() as $entity) {
      /** @var \Drupal\apigee_edge_teams\Entity\TeamInterface $entity */
      if ($entity->access('view')) {
        $name = $entity->toLink()->toRenderable();
      }
      else {
        $name = ['#markup' => $entity->label()];
      }
      $team_row = [
        '#attributes' => new Attribute([
          'class' => 'row--info',
        ]),
        'name' => $name,
        'status' => [
          '#type' => 'status_property',
          '#value' => $entity->getStatus(),
          '#indicator_status' => $entity->getStatus() === TeamInterface::STATUS_ACTIVE ? StatusPropertyElement::INDICATOR_STATUS_OK : StatusPropertyElement::INDICATOR_STATUS_ERROR,
        ],
        'operations' => $this->buildOperations($entity),
      ];

      $build['table']['#items'][] = $team_row;
    }

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  protected function getDefaultOperations(EntityInterface $entity): array {
    $operations = parent::getDefaultOperations($entity);
    if ($entity->access('view')) {
      $operations['view'] = [
        'title' => $this->t('View'),
        'weight' => -150,
        'url' => $entity->toUrl(),
      ];
    }
    return $operations;
  }

}
