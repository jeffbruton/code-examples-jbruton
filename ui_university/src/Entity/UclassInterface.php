<?php

namespace Drupal\ui_university\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\user\EntityOwnerInterface;
use Drupal\Core\Entity\EntityChangedInterface;

/**
 * Provides an interface defining core functions of the Uclass entity.
 *
 * @ingroup ui_university
 */
interface UclassInterface extends ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface {

  
}
