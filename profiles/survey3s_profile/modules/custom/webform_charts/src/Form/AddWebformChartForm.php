<?php

namespace Drupal\webform_charts\Form;

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
    $form['x_axis_title'] = [
      '#type' => 'textfield',
      '#title' => 'Horizontal Axis Title',
      '#size' => 10,
      '#required' => TRUE,
    ];
    $form['y_axis_title'] = [
      '#type' => 'textfield',
      '#title' => 'Vertical Axis Title',
      '#size' => 10,
      '#required' => TRUE,
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
  }
}