<?php
/**
 * @file
 * Contains Drupal\test_form\Form\TestForm.
 */

namespace Drupal\test_form\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Test form with autocomplete field.
 */
class TestForm extends ConfigFormBase {

  /**
   * {@inheritdoc}.
   */
  public function getFormId() {
    return 'test_form_autocomplete';
  }

  protected function getEditableConfigNames() {
    // Define config file.
    return [
      'test_form.settings',
    ];
  }

  /**
   * {@inheritdoc}.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Call config service.
    $config = \Drupal::config('test_form.settings');
    // Custom form with autocomplete.
    $form['test_autocomplete_field'] = [
      '#type' => 'textfield',
      '#default_value' => $config->get('test_autocomplete_field'),
      '#title' => $this->t('Field autocomplete'),
      '#autocomplete_route_name' => 'test_form.custom_autocomplete',
    ];
    $controller = \Drupal::entityManager()->getStorage('node');
    $controller->loadMultiple([1, 2, 3]);

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    // Put node title to the config file and save.
    $this->config('test_form.settings')
      ->set('test_autocomplete_field', $values['test_autocomplete_field'])
      ->save();
  }
}