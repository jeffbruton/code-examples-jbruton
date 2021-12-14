<?php

namespace Drupal\tibco_nofollow\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\link\Plugin\Field\FieldWidget\LinkWidget;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'link' widget to remove rel="nofollow".
 *
 * @FieldWidget(
 *   id = "tibco_remove_nofollow_link",
 *   label = @Translation("Link (remove rel='nofollow')"),
 *   field_types = {
 *     "link"
 *   }
 * )
 */
class LinkRemoveRelNofollowWidget extends LinkWidget implements ContainerFactoryPluginInterface {

  /**
   * Constructs a LinkRemoveRelNofollowWidget object
   *
   * @param string $plugin_id
   *   The plugin_id for the widget.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The definition of the field to which the widget is associated.
   * @param array $settings
   *   The widget settings.
   * @param array $third_party_settings
   *   Any third party settings.
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, array $third_party_settings) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $third_party_settings);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return parent::create($container, $configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'nofollow_disabled' => 0,
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element = parent::formElement($items, $delta, $element, $form, $form_state);
    
    // Get the saved value if it exists otherwise use unchecked
    $value = $items[$delta]->getValue()['options']['nofollow'] ?? 0;
    
    // Remove the rel="nofollow" attribute
    $element['options']['nofollow_disabled'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Remove rel="nofollow"'),
      '#description' => $this->t('Removes the attribute rel="nofollow" on the link.'),
      '#default_value' => $value,
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $element = parent::settingsForm($form, $form_state);

    $element['options']['nofollow_disabled'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Remove rel="nofollow"'),
      '#description' => $this->t('Removes the attribute rel="nofollow" on the link.'),
      '#default_value' => $this->getSetting('nofollow_disabled'),

    ];
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = parent::settingsSummary();

    if ($enabled = $this->getSetting('nofollow_disabled')) {
      $summary[] = $this->t('Remove the rel="nofollow" attribute.');
    }

    return $summary;
  }
}
