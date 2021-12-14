<?php

namespace Drupal\tibco_general\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

class TibcoGeneralAutocompleteRouteSubscriber extends RouteSubscriberBase {

  public function alterRoutes(RouteCollection $collection) {
    if ($route = $collection->get('system.entity_autocomplete')) {
      $route->setDefault('_controller', '\Drupal\tibco_general\Controller\TibcoGeneralAutocompleteController::handleAutocomplete');
    }
  }
}
