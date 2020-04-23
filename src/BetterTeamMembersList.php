<?php

declare(strict_types = 1);

namespace Drupal\apigee_edge_ui;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Link;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Template\Attribute;
use Drupal\apigee_edge_teams\Controller\TeamMembersList;
use Drupal\apigee_edge_teams\Entity\TeamInterface;
use Drupal\apigee_edge_teams\TeamMembershipManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Advanced list builder for team members.
 */
class BetterTeamMembersList extends TeamMembersList {

  /**
   * The route match service.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  private $routeMatch;

  /**
   * TeamMembersList constructor.
   *
   * @param \Drupal\apigee_edge_teams\TeamMembershipManagerInterface $team_membership_manager
   *   The team membership manager service.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The current route match.
   */
  public function __construct(TeamMembershipManagerInterface $team_membership_manager, EntityTypeManagerInterface $entity_type_manager, RouteMatchInterface $route_match) {
    parent::__construct($team_membership_manager, $entity_type_manager);
    $this->routeMatch = $route_match;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('apigee_edge_teams.team_membership_manager'),
      $container->get('entity_type.manager'),
      $container->get('current_route_match')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function overview(TeamInterface $team) {
    $build = parent::overview($team);
    $this->buildMemberListContent($team, $build);

    return $build;
  }

  /**
   * Alters the app info in the original list render array.
   *
   * @param \Drupal\apigee_edge_teams\Entity\TeamInterface $team
   *   The current team.
   * @param array $build
   *   The original render array.
   * @param string $rel
   *   The app name's link relationship type, defaults to 'canonical'.
   */
  private function buildMemberListContent(TeamInterface $team, array &$build, string $rel = 'canonical'): void {
    // Use custom template instead of table.
    unset($build['table']['#type']);
    $build['table']['#type'] = 'member';
    $build['table']['#theme'] = 'apigee_edge_ui_list';

    // Build the app rows from scratch.
    $build['table']['#items'] = [];
    foreach ($build['table']['#rows'] as $row) {
      $data = $row['data'];
      $member = $data['member'] instanceof Link ? $data['member']->toRenderable() : $data['member'];
      $item = [
        '#attributes' => new Attribute([
          'id' => $row['id'],
          'class' => 'row--info',
        ]),
        'name' => $member,
        'roles' => $data['roles']['data'],
        'operations' => $data['operations']['data'],
      ];

      $build['table']['#items'] += [$row['id'] => $item];
    }
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
      $args = ['@team' => $team->getName()];
      $title = $this->t('Members of @team', $args);
    }
    else {
      $title = $this->t('Members');
    }

    return $title;
  }

}
