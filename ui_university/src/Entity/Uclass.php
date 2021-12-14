<?php

namespace Drupal\ui_university\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\ui_university\Entity\UclassInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Uclass entity.
 *
 * @ingroup ui_university
 *
 * @ContentEntityType(
 *   id = "uiu_class",
 *   label = @Translation("Class"),
 *   base_table = "uiu_class",
 *   entity_keys = {
 *     "id" = "id",
 *     "uid" = "uid",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "published" = "status",
 *     "created" = "created",
 *     "changed" = "changed",
 *   },
 *   fieldable = TRUE,
 *   admin_permission = "administer uiu_class entity",
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\ui_university\UclassListBuilder",
 *     "access" = "Drupal\ui_university\UclassAccessControlHandler",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "form" = {
 *       "default" = "Drupal\ui_university\Form\UclassForm",
 *       "add" = "Drupal\ui_university\Form\UclassForm",
 *       "edit" = "Drupal\ui_university\Form\UclassForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     },
 *   },
 *   links = {
 *     "canonical" = "/classes/class/{uiu_class}",
 *     "add-page" = "/admin/content/classes/class/add",
 *     "add-form" = "/admin/content/classes/class/add",
 *     "edit-form" = "/admin/content/classes/class/{uiu_class}/edit",
 *     "delete-form" = "/admin/content/classes/class/{uiu_class}/delete",
 *     "collection" = "/admin/content/classes",
 *   },
 *   field_ui_base_route = "ui_university.uclass_settings",
 * )
 */

class Uclass extends ContentEntityBase implements UclassInterface, EntityPublishedInterface {

  use EntityChangedTrait;
  use EntityPublishedTrait;

  /**
   * {@inheritdoc}
   *
   * Set the uid entity reference to the current user as the creator.
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += [
      'uid' => \Drupal::currentUser()->id(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('uid')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('uid')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('uid', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('uid', $account->id());
    return $this;
  }
  
  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   *
   * Define BaseFields
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    
    // Standard field, used as unique if primary index.
    $fields['id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Class ID'))
      ->setDescription(t('The ID of the Class entity.'))
      ->setReadOnly(TRUE);

    // Name field for the Class.
    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the Class.'))
      ->setSettings([
        'max_length' => 255,
        'text_processing' => 0,
      ])
      // Set no default value.
      ->setDefaultValue(NULL)
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -10,
      ])
      ->setDisplayConfigurable('form', TRUE);
    
    $fields['details'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Details'))
      ->setSettings(array(
        'default_value' => '',
        'text_processing' => 0,
      ))
      ->setDisplayOptions('view', [
        'weight' => 1,
        'label' => 'hidden',
      ])
      ->setDisplayOptions('form', array(
        'type' => 'text_textarea',
        'settings' => array(
          'rows' => 4,
        ),
        'weight' => 1,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);
    
    $fields['timeslot'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('Timeslot'))
      ->setDescription(t('Start time for this class.'))
      ->setSettings([
        'datetime_type' => 'datetime',
      ])
      ->setDisplayOptions('view', [
        'label' => 'inline',
        'type' => 'datetime_default',
        'settings' => [
          'format_type' => 'time',
        ],
        'weight' => -4,
      ])
      ->setDisplayOptions('form', [
        'type' => 'datetime_datelist',
        'settings' => [
          'time_type' => 12,
          'increment' => 15,
        ],
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);
    
    // Author field
    $fields['uid'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the Class.'))
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 10,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE);

    // Standard field, unique for this entity.
    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The UUID of the Class entity.'))
      ->setReadOnly(TRUE);
    
    // Status field
    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Published'))
      ->setDescription(t('The published state of this Class.'))
      ->setDefaultValue(TRUE)
      ->setDisplayOptions('form', [
        'weight' => 12,
      ])
      ->setDisplayConfigurable('form', TRUE);
    
    $fields['langcode'] = BaseFieldDefinition::create('language')
      ->setLabel(t('Language'))
      ->setDescription(t('The language code of this entity.'));
      
    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'))
      ->setDisplayOptions('form', [
        'weight' => 11,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  }
}