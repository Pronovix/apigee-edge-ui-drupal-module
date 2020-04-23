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
use Drupal\apigee_edge\Entity\AppInterface;
use Drupal\apigee_edge_teams\Entity\TeamAppInterface;

/**
 * List builder additions for apps.
 */
trait BetterAppListTrait {

  /**
   * Creates a row for an app.
   *
   * @param \Drupal\apigee_edge\Entity\AppInterface $app
   *   The developer- or team app entity.
   * @param string $rel
   *   The app name's link relationship type, defaults to 'canonical'.
   * @param bool $includeTeam
   *   Whether the team link should be included or not.
   *
   * @return array
   *   The app row's render array.
   */
  private function buildAppRow(AppInterface &$app, string $rel = 'canonical', bool $includeTeam = FALSE): array {
    if ($app->hasLinkTemplate($rel) && ($link = $app->toLink(NULL, $rel))->getUrl()->access()) {
      $name = $link->toRenderable();
    }
    else {
      $name = ['#markup' => $app->label()];
    }
    $app_row = [
      '#attributes' => new Attribute([
        'class' => 'row--info',
      ]),
      'name' => $name,
      'status' => [
        '#type' => 'status_property',
        '#value' => $app->getStatus(),
        '#indicator_status' => $app->getStatus() === AppInterface::STATUS_APPROVED ? StatusPropertyElement::INDICATOR_STATUS_OK : StatusPropertyElement::INDICATOR_STATUS_ERROR,
      ],
      '#warning_attributes' => new Attribute([
        'class' => 'row--warning',
      ]),
    ];
    if ($app instanceof TeamAppInterface && $includeTeam) {
      $team_storage = $this->entityTypeManager
          ? $this->entityTypeManager->getStorage('team')
          : \Drupal::entityTypeManager()->getStorage('team');
      /** @var \Drupal\apigee_edge_teams\Entity\TeamInterface $team */
      $team = $team_storage->load($app->getAppOwner());
      if ($team) {
        $app_row = ['team' => $team->access('view') ? $team->toLink()->toRenderable() : $team->label()] + $app_row;
      }
    }

    return $app_row;
  }

  /**
   * Returns the warning list for an app.
   *
   * @param array $warnings
   *   The warnings of the App.
   *
   * @return array|null
   *   The render array of warnings.
   */
  private function getWarningList(array $warnings): ?array {
    $items = [];
    // Display warning sign next to the status if the app's status is
    // approved, but:
    // - any credentials of the app is in revoked status
    // - any products of any credentials of the app is in revoked or
    //   pending status.
    if ($warnings['revokedCred'] || $warnings['revokedOrPendingCredProduct'] || $warnings['expiredCred']) {
      if ($warnings['revokedCred']) {
        $items[] = $warnings['revokedCred'];
      }
      elseif ($warnings['revokedOrPendingCredProduct']) {
        $items[] = $warnings['revokedOrPendingCredProduct'];
      }
      if ($warnings['expiredCred']) {
        $items[] = $warnings['expiredCred'];
      }
    }
    return !empty($items) ? [
      '#theme' => 'item_list',
      '#items' => $items,
    ] : NULL;
  }

  /**
   * Creates link for the 'View' operation.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The App entity.
   * @param string $rel
   *   The view link relationship type, defaults to 'canonical'.
   *
   * @return null|array
   *   The render array of 'View' operation.
   */
  private function getViewOperation(EntityInterface $entity, string $rel = 'canonical'): ?array {
    if ($entity->hasLinkTemplate($rel) && ($url = $entity->toUrl($rel))->access()) {
      $operation = [
        'title' => method_exists($this, 't') ? $this->t('View') : t('View'),
        'weight' => -150,
        'url' => $url,
      ];
    }
    return $operation ?? NULL;
  }

}
