<?php
/**
 * @file
 * Contains \Drupal\ajaxform_example\Form\AjaxFormSubmit.
 */

namespace Drupal\ajaxform_example\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\CssCommand;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Realize form with Ajax validation and Ajax submit.
 */
class AjaxFormSubmit extends FormBase {

  /**
   * {@inheritdoc}.
   */
  public function getFormId() {
    return 'ajax_form_submit';
  }

  /**
   * {@inheritdoc}.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Element for messages.
    $form['system_messages'] = [
      '#markup' => '<div id="form-system-messages"></div>',
      '#weight' => -100,
    ];
    // Email field use Ajax validation.
    $form['email'] = [
      '#title' => 'Email:',
      '#type' => 'email',
      '#required' => TRUE,
      '#ajax' => [
        'callback' => '::validateEmailAjax',
        'event' => 'change',
        'progress' => [
          'type' => 'throbber',
          'message' => t('Verifying email..'),
        ],
      ],
      '#suffix' => '<div class="email-validation-message"></div>',
    ];
    // Letter title field.
    $form['title'] = [
      '#title' => 'Letter title:',
      '#type' => 'textfield',
      '#required' => TRUE,
    ];
    // Letter body field.
    $form['body'] = [
      '#title' => 'Letter body:',
      '#type' => 'textarea',
    ];
    // Button use Ajax submit.
    $form['submit'] = [
      '#type' => 'submit',
      '#name' => 'Send mail',
      '#value' => 'Send mail',
      '#ajax' => [
        'callback' => '::ajaxSubmitCallback',
        'event' => 'click',
        'progress' => [
          'type' => 'throbber',
        ],
      ],
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $output = 'The letter to ' . $values['email'] . ' with title ' . $values['title'] . ' was sent';
    drupal_set_message($output);
  }

  /**
   * {@inheritdoc}
   */
  public function ajaxSubmitCallback(array &$form, FormStateInterface $form_state) {
    $ajax_response = new AjaxResponse();
    $message = [
      '#theme' => 'status_messages',
      '#message_list' => drupal_get_messages(),
      '#status_headings' => [
        'status' => t('Status message'),
        'error' => t('Error message'),
        'warning' => t('Warning message'),
      ],
    ];
    $messages = \Drupal::service('renderer')->render($message);
    $ajax_response->addCommand(new HtmlCommand('#form-system-messages', $messages));
    return $ajax_response;
  }

  /**
   * {@inheritdoc}
   */
  public function validateEmailAjax(array &$form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    // Check entered email and predefined template.
    if (preg_match('/(mail.ru)/', $form_state->getValue('email'))) {
      // If checking failed.
      $response->addCommand(new HtmlCommand('.email-validation-message', 'This provider was banned. Be care!'))
        ->addCommand(new CssCommand('.email-validation-message', ['color' => 'red']));
    }
    else {
      // If user fixed email.
      $response->addCommand(new HtmlCommand('.email-validation-message', ''));
    }
    return $response;
  }
}