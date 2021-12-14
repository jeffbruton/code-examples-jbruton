<?php

namespace Drupal\tibco_nofollow\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class TibcoNofollowForm.
 *
 * @package Drupal\tibco_nofollow\Form
 */
class TibcoNofollowForm extends ConfigFormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'tibco_nofollow_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'tibco_nofollow.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('tibco_nofollow.settings');

    $form['hosts'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Nofollow URLs'),
      '#description' => $this->t('Whitelist domains that should not have the rel="nofollow" attribute applied. Add one host per line. Ex, www.google.com'),
      '#default_value' => $config->get('hosts'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Retrieve the configuration.
    $this->config('tibco_nofollow.settings')
      // Set the submitted form data.
      ->set('hosts', $form_state->getValue('hosts'))
      // Save the new config values.
      ->save();

    parent::submitForm($form, $form_state);
  }

}
