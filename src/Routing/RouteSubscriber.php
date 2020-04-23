<?php

declare(strict_types = 1);

namespace Drupal\apigee_edge_ui\Routing;

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
    if ($route = $collection->get('entity.developer_app.collection_by_developer')) {
      $route->setDefault('_controller', BetterDeveloperAppListBuilderForDeveloper::class . '::render');
    }
    if ($route = $collection->get('entity.team_app.collection_by_team')) {
      $route->setDefault('_controller', BetterTeamAppListByTeamBuilder::class . '::render');
      $route->setDefault('_title_callback', BetterTeamAppListByTeamBuilder::class . '::pageTitle');
    }
    if ($route = $collection->get('entity.team.members')) {
      $route->setDefault('_controller', BetterTeamMembersList::class . '::overview');
      $route->setDefault('_title_callback', BetterTeamMembersList::class . '::pageTitle');
    }
  }

}
