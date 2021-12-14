<?php

namespace Drupal\tibco_general\Controller;

use Drupal\Core\KeyValueStore\KeyValueStoreInterface;
use Drupal\tibco_general\TibcoGeneralAutocompleteMatcher;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\system\Controller\EntityAutocompleteController;

class TibcoGeneralAutocompleteController extends EntityAutocompleteController
{

  /**
   * The autocomplete matcher for entity references.
   */
  protected $matcher;

  /**
   * {@inheritdoc}
   */
  public function __construct(TibcoGeneralAutocompleteMatcher $matcher, KeyValueStoreInterface $key_value)
  {
    $this->matcher = $matcher;
    $this->keyValue = $key_value;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container)
  {
    return new static(
      $container->get('tibco_general.autocomplete_matcher'),
      $container->get('keyvalue')->get('entity_autocomplete')
    );
  }

}
