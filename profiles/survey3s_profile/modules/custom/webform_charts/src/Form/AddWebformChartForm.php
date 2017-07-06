<?php

namespace Drupal\webform_charts\Form;

use Drupal\Core\Database\Database;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class AddWebformChartForm extends FormBase{
  public function getFormId() {
    return "add_webform_chart";
  }
  public function buildForm(array $form, FormStateInterface $form_state, $webform = NULL) {
    $form = [];
    $form['title'] = [
      '#type' => 'textfield',
      '#title' => 'Chart Title',
      '#size' => 10,
      '#required' => TRUE,
    ];
    $form['chart_type'] = [
      '#type' => 'select',
      '#title' => 'Chart type',
      '#options' => ['pie' => 'Pie', 'column' => 'Column'],
      '#required' => TRUE,
    ];

    $webform_elements = [];
    foreach ($webform->getElementsDecoded() as $key=>$element) {
      $webform_elements[$key] = $element['#title'];
    }
    $form['pie_chart_data_element'] = [
      '#type' => 'select',
      '#title' => 'Chart data based on',
      '#options' => $webform_elements,
      '#states' => array(
        // Show this only if pie chart is selected
        'visible' => array(
          ':input[name="chart_type"]' => array('value' => 'pie'),
        ),
      ),
    ];
    $form['#webform_id']  = $webform->id();
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => 'Create Chart',
    ];

    return $form;
  }
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state); // TODO: Change the autogenerated stub
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    // TODO: Implement submitForm() method.
    $webform_id = $form['#webform_id'];
    //TODO: build searialized data to build the chart
    $chart_options = serialize($form_state->getValue('pie_chart_data_element'));
    $connection = Database::getConnection();
    $connection->insert('webform_charts')->fields(
      array(
        'webform_id' => $webform_id,
        'chart_title' => $form_state->getValue('title'),
        'chart_type' => $form_state->getValue('chart_type'),
        'options' => $chart_options,
      )
    )->execute();
    //TODO: check if exception to be catched for DB insert
    drupal_set_message('Added chart');
    $form_state->setRedirect('entity.webform.results_charts', array('webform' => $webform_id));
  }
}