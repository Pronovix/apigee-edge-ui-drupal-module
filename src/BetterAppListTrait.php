<?php

declare(strict_types = 1);

namespace Drupal\apigee_edge_ui;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Template\Attribute;
use Drupal\apigee_edge\Entity\AppInterface;

/**
 * List builder additions for apps.
 */
trait BetterAppListTrait {

  /**
   * Alters the app info in the original list render array.
   *
   * @param array $build
   *   The original render array.
   * @param string $rel
   *   The app name's link relationship type, defaults to 'canonical'.
   */
  private function buildAppListContent(array &$build, string $rel = 'canonical'): void {
    // Use custom template instead of table.
    unset($build['table']['#type']);
    if (isset($this->entityTypeId)) {
      $build['table']['#type'] = $this->entityTypeId;
    }
    $build['table']['#theme'] = 'apigee_edge_ui_list';

    // Build the app rows from scratch.
    $build['table']['#items'] = [];
    foreach ($this->load() as $entity) {
      /** @var \Drupal\apigee_edge\Entity\AppInterface $entity */
      $app_row = [
        '#attributes' => new Attribute([
          'id' => $this->getCssIdForInfoRow($entity),
          'class' => 'row--info',
        ]),
        'name' => [
          '#type' => 'link',
          '#title' => $entity->label(),
          '#url' => $entity->toUrl($rel),
        ],
        'status' => $this->renderAppStatus($entity),
        'operations' => $this->buildOperations($entity),
        '#warning_attributes' => new Attribute([
          'id' => $this->getCssIdForWarningRow($entity),
          'class' => 'row--warning',
        ]),
      ];

      if (isset($build['table']['#header']['team'])) {
        /** @var \Drupal\apigee_edge_teams\Entity\TeamInterface[] $teams */
        $teams = $this->entityTypeManager->getStorage('team')->loadMultiple();
        $app_row['team'] = $teams[$entity->getCompanyName()]->access('view')
            ? $teams[$entity->getCompanyName()]->toLink()->toRenderable()
            : $teams[$entity->getCompanyName()]->label();
      }
      if ($warning_message = $this->getWarningTextByApp($entity)) {
        $app_row['warning_message'] = $warning_message;
      }

      $build['table']['#items'] += [$this->getCssIdForInfoRow($entity) => $app_row];
    }
  }

  /**
   * Returns the warning text for an app.
   *
   * @param \Drupal\apigee_edge\Entity\AppInterface $app
   *   The app entity.
   *
   * @return \Drupal\Core\StringTranslation\TranslatableMarkup|null
   *   The warning text.
   */
  private function getWarningTextByApp(AppInterface $app): ?TranslatableMarkup {
    // Display warning sign next to the status if the app's status is
    // approved, but:
    // - any credentials of the app is in revoked status
    // - any products of any credentials of the app is in revoked or
    //   pending status.
    $warnings = $this->checkAppCredentialWarnings($app);
    if ($app->getStatus() === AppInterface::STATUS_APPROVED && ($warnings['revokedCred'] || $warnings['revokedOrPendingCredProduct'])) {
      if ($warnings['revokedCred']) {
        return $warnings['revokedCred'];
      }
      elseif ($warnings['revokedOrPendingCredProduct']) {
        return $warnings['revokedOrPendingCredProduct'];
      }
    }

    return NULL;
  }

  /**
   * Add 'View' link to operations.
   *
   * @param \Drupal\Core\Entity\EntityInterface $app
   *   The App entity.
   * @param string $rel
   *   The view link relationship type, defaults to 'canonical'.
   *
   * @return array
   *   The new operations array including the 'View' operation.
   */
  private function getBetterOperations(EntityInterface $app, string $rel = 'canonical'): array {
    $operations = parent::getDefaultOperations($app);
    if ($app->access('view')) {
      $operations['view'] = [
        'title' => $this->t('View'),
        'weight' => -150,
        'url' => $app->toUrl($rel),
      ];
    }
    return $operations;
  }

}
