<?php

namespace Drupal\ui_university\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\user\Entity\User;
use Drupal\ui_university\Entity\Uclass;
use Drupal\ui_university\Entity\UclassInterface;

/**
 * Form controller for the Uclass Registration form.
 *
 * @ingroup ui_university
 */
class UclassRegistrationForm extends FormBase {
  
  /**
   * Returns a unique string identifying the form.
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    return 'uclass_registration_form';
  }

  /**
   * Form constructor
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $userCurrent = \Drupal::currentUser();
    $user = \Drupal\user\Entity\User::load($userCurrent->id());
    
    $username = $user->name[0]->value;
    $usermail = $user->mail[0]->value;
    
    $form['#prefix'] = '<div id="uclass-registration-form-wrapper">';
    $form['#suffix'] = '</div>';
    $form['#attached']['library'][] = 'ui_university/registration_form';
    
    $student_info = '<h2>My Schedule</h2><p>Username: '. $username .'<br>Email: '. $usermail .'<br>Student ID: '. $userCurrent->id() .'</p>';
    
    $view = views_embed_view('class_schedule', 'block_1');
    // render view
    $student_info .= render($view);
    
    $form['student_information'] = array(
      '#type' => 'markup', 
      '#markup' => t($student_info),
    );
    
    // Build out all available select options for Subject, Topic, and Uclass fields to avoid invalid selection
    // Subject form options based on top level terms in the Courses vocabulary
    $con = \Drupal\Core\Database\Database::getConnection();
    
    $subjectoptions = [];
    $topicoptions = [];
    $classoptions = [];
    
    $subjectterms = \Drupal::service('entity_type.manager')->getStorage("taxonomy_term")->loadTree('courses', $parent = 0, $max_depth = 1, $load_entities = FALSE);
    foreach($subjectterms as $subjectoption){
      $subjectoptions[$subjectoption->tid] = t($subjectoption->name);
      
      // Topic form options based on 2nd level terms in the Courses vocabulary
      $topicterms = \Drupal::service('entity_type.manager')->getStorage("taxonomy_term")->loadTree('courses', $parent = $subjectoption->tid, $max_depth = 1, $load_entities = FALSE);
      foreach($topicterms as $topicoption){
        $topicoptions[$topicoption->tid] = t($topicoption->name);
        
        $query = $con->select('uiu_class', 'c');
        $query->leftJoin('uiu_class__field_course', 's', 's.entity_id = c.id');
        $query->fields('c', array('id', 'name', 'timeslot', 'status'));
        $query->fields('s', array('entity_id', 'field_course_target_id'));
        $query->condition('c.status', 1, '=');
        $query->condition('s.field_course_target_id', $topicoption->tid, '=');
        $result = $query->distinct()->execute();
        
        foreach($result as $row) {
          $classoptions[$row->id] = t($row->name);
        }
      }
    }
    
    $form['username'] = array(
      '#type' => 'textfield', 
      '#title' => t('Username'), 
      '#default_value' => $username, 
      '#size' => 60, 
      '#maxlength' => 128, 
      '#required' => TRUE,
    );
    
    $form['usermail'] = array(
      '#type' => 'textfield', 
      '#title' => t('E-mail'), 
      '#default_value' => $usermail, 
      '#size' => 60, 
      '#maxlength' => 128, 
      '#required' => TRUE,
    );
    
    $form['userid'] = array(
      '#type' => 'textfield', 
      '#title' => t('Student ID'), 
      '#default_value' => $userCurrent->id(), 
      '#size' => 60, 
      '#maxlength' => 128, 
      '#required' => TRUE,
    );
    
    $form['subject'] = array(
      '#type' => 'select', 
      '#title' => t('Course Subject'), 
      '#default_value' => '',
      '#options' => $subjectoptions,
      '#required' => TRUE,
      '#empty_option' => "- Select -",
    );
    
    $form['topic'] = array(
      '#type' => 'select', 
      '#title' => t('Course Topic'), 
      '#default_value' => '',
      '#options' => $topicoptions,
      '#required' => TRUE,
      '#empty_option' => "- Select -",
      '#prefix' => '<div id="edit-topic-wrapper">',
      '#suffix' => '</div>',
    );
    
    $form['uclass'] = array(
      '#type' => 'select', 
      '#title' => t('Course Timeslot'), 
      '#default_value' => '',
      '#options' => $classoptions,
      '#required' => TRUE,
      '#empty_option' => "- Select -",
      '#prefix' => '<div id="edit-uclass-wrapper">',
      '#suffix' => '</div>',
    );
    
    $form['actions'] = [
      '#type' => 'actions',
    ];
    
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add Course'),
    ];
    
    return $form;
  }
  
  /**
   * Validate the Registration form
   * 
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   * 
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $userCurrent = \Drupal::currentUser();
    $user = \Drupal\user\Entity\User::load($userCurrent->id());
    
    parent::validateForm($form, $form_state);
    
    $username = $form_state->getValue('username');
    if (!ctype_alnum($username)) {
      $form_state->setErrorByName('username', t('Username should be alphanumeric values.'));
    }
    
    $email = $form_state->getValue('usermail');
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $form_state->setErrorByName('usermail', t('Invalid email address'));
    }
    
    $studentid = $form_state->getValue('userid');
    if (!is_numeric($studentid)) {
      $form_state->setErrorByName('userid', t('Student ID should be a number.'));
    }
    
    $newclassid = $form_state->getValue('uclass');
    $newclass = \Drupal\ui_university\Entity\Uclass::load($newclassid);
    $newclassdtime = $newclass->timeslot[0]->value;
    $newclassutime = strtotime($newclassdtime);
    $newclassuendtime = $newclassutime + 3540;
    
    $userclasses = $user->field_class_schedule;
    
    // check if already registered & time slot available
    foreach($userclasses as $userclass) {
      if ($userclass->target_id == $newclassid) {
        $form_state->setErrorByName('uclass', t('You are already registered for this class.'));
      } else {
        $existingclass = \Drupal\ui_university\Entity\Uclass::load($userclass->target_id);
        $classdtime = $existingclass->timeslot[0]->value;
        $classutime = strtotime($classdtime);
        $classuendtime = $classutime + 3540;  // 59 minutes unix
        
        if ($newclassutime == $classutime) {
          $form_state->setErrorByName('uclass', t('You are already scheduled for another class at this timeslot.'));
        } 
        if (($newclassutime > $classutime && $newclassutime < $classuendtime) || ($newclassuendtime > $classutime && $newclassuendtime < $classuendtime)) {
          $form_state->setErrorByName('uclass', t('This class overlaps another class on your schedule.'));
        }
        
      }
    }
  }

  /**
   * Form submission handler.
   *
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    
    $newclass = $form_state->getValue('uclass');
    $userCurrent = \Drupal::currentUser();
    $user = \Drupal\user\Entity\User::load($userCurrent->id());
    $user->field_class_schedule[] = ["target_id" => $newclass];
    
    $user->save();
    
    \Drupal::messenger()->addStatus(t('Your Course schedule has been successfully updated'));
  } 

}