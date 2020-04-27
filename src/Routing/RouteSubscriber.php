<?php

declare(strict_types = 1);

namespace Drupal\apigee_edge_ui\Routing;

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

use Drupal\Core\Routing\RouteSubscriberBase;
use Drupal\apigee_edge_ui\BetterDeveloperAppListBuilderForDeveloper;
use Drupal\apigee_edge_ui\BetterTeamAppListByTeamBuilder;
use Drupal\apigee_edge_ui\BetterTeamMembersList;
use Symfony\Component\Routing\RouteCollection;

/**
 * Listens to the dynamic route events.
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  public function alterRoutes(RouteCollection $collection) {
    $route = $collection->get('entity.developer_app.collection_by_developer');
    if ($route) {
      $route->setDefault('_controller', BetterDeveloperAppListBuilderForDeveloper::class . '::render');
    }
    $route = $collection->get('entity.team_app.collection_by_team');
    if ($route) {
      $route->setDefault('_controller', BetterTeamAppListByTeamBuilder::class . '::render');
      $route->setDefault('_title_callback', BetterTeamAppListByTeamBuilder::class . '::pageTitle');
    }
    $route = $collection->get('entity.team.members');
    if ($route) {
      $route->setDefault('_controller', BetterTeamMembersList::class . '::overview');
      $route->setDefault('_title_callback', BetterTeamMembersList::class . '::pageTitle');
    }
  }

}
