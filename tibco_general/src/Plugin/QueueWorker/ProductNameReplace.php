<?php
/**
 * @file
 * Contains Drupal\tibco_general\Plugin\QueueWorker\ActiveMatrixQueueWorker
 */

namespace Drupal\tibco_general\Plugin\QueueWorker;

use Drupal\Core\Queue\QueueWorkerBase;

/**
 * Processes tasks for example module.
 *
 * @QueueWorker(
 *   id = "product_name_replace",
 *   title = @Translation("Updating ActiveMatrix Strings"),
 *   cron = {"time" = 90}
 * )
 */
class ProductNamereplace extends QueueWorkerBase {

  /**
   * {@inheritdoc}
   */
  public function processItem($item) {
    $node = \Drupal\node\Entity\Node::load($item);
    foreach($node->getFields() as $field_id => $field_obj){
      $field_type = $field_obj->getFieldDefinition()->getType();
      if(strpos($field_type, 'string') !== FALSE || strpos($field_type, 'text') !== FALSE) {
        $value_arr = $field_obj->getValue();
        if(array_key_exists(0, $value_arr)) {
          $field_value = $value_arr[0]['value'];
          $text_format = '';
          if(array_key_exists('format', $value_arr[0])){
            $text_format = $value_arr[0]['format'];
          }
          $has_text = stripos($field_value, 'TIBCO ActiveMatrix BPM') !== FALSE;
          $has_text_with_tm = stripos($field_value,'TIBCO ActiveMatrix® BPM') !== FALSE;
          if ($has_text || $has_text_with_tm) {
            if($has_text && $has_text_with_tm){
              $new_string = str_replace('TIBCO ActiveMatrix BPM', 'TIBCO BPM Enterprise', $field_value);
              $newest_string = str_replace('TIBCO ActiveMatrix® BPM', 'TIBCO BPM Enterprise', $new_string);
            } elseif ($has_text && !$has_text_with_tm) {
              $newest_string = str_replace('TIBCO ActiveMatrix BPM', 'TIBCO BPM Enterprise', $field_value);
            } else {
              $newest_string = str_replace('TIBCO ActiveMatrix® BPM', 'TIBCO BPM Enterprise', $field_value);
            }

            if(!empty($text_format)) {
              $node->get($field_id)->setValue(['value' => $newest_string, 'format' => $text_format]);
            } else {
              $node->set($field_id, $newest_string);
            }
            $node->save();
          }
        }
      }
    }

  }

}
