<?php

namespace Drupal\tibco_general;

use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\Tags;
use Drupal\Core\Entity\EntityAutocompleteMatcher;

class TibcoGeneralAutocompleteMatcher extends EntityAutocompleteMatcher
{

  public function getMatches($target_type, $selection_handler, $selection_settings, $string = '')
  {
    if(empty($selection_settings['match_limit']))
      $selection_settings['match_limit'] = 20; //override default limit

    if($target_type == 'node')
    {
      //alter query sort so newer entities appear at the top by default
      if(empty($selection_settings['sort']) || $selection_settings['sort']['field'] == '_none')
      {
        $selection_settings['sort']['field'] = 'created';
        $selection_settings['sort']['direction'] = 'DESC';
      }
    }

    //Use parent class from core to get matches
    $matches = parent::getMatches($target_type, $selection_handler, $selection_settings, $string);

    // For speaker_profile nodes, alter label to indicate whether the referenced node has a headshot image.
    if($target_type == 'node'
      && !empty($selection_settings['target_bundles'])
      && array_key_exists('speaker_profile', $selection_settings['target_bundles']))
    {
      foreach ($matches as &$match)
      {
        $label = &$match['label'];
        $entity_id = null;
        if(!preg_match('/^.*\((\d*)\)$/', $match['value'], $entity_id) || empty($entity_id[1]))
          continue;

        $entity_id = $entity_id[1];
        /** @var \Drupal\node\Entity\Node $entity */
        $entity = \Drupal::entityTypeManager()->getStorage($target_type)->load($entity_id);
        $entity = \Drupal::service('entity.repository')->getTranslationFromContext($entity);

        if($entity->bundle() == 'speaker_profile' && $entity->hasField('field_headshot') && $entity->get('field_headshot')->getValue())
          $label = $label . ' (headshot)';
      }//end foreach
    }//end if

    return $matches;
  }
}
