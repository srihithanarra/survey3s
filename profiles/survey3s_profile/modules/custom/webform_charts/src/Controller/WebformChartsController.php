<?php

namespace Drupal\webform_charts\Controller;

use Drupal\Core\Config\Entity\Query\Query;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;
use Drupal\Core\Entity\Annotation\EntityType;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\webform\Entity\Webform;
use Drupal\webform\WebformSubmissionStorage;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\charts\Charts\ChartsRenderInterface;
use Drupal\Core\Url;


class WebformChartsController extends ControllerBase {
  public function index(Webform $webform) {
    $webform_id = $webform->id();
    $charts = [];
    //link for "add chart"
    $charts[] = [
      '#type' => 'link',
      '#title' => 'Add Chart',
      '#url' => Url::fromRoute('entity.webform.results_charts_add', array('webform' => $webform->id())),
    ];

    //get existing charts for the webform
    $query = \Drupal::database()->select('webform_charts', 'wc')
      ->fields('wc')
      ->condition('wc.webform_id', $webform_id);
    $result = $query->execute()->fetchAll();

    if (!empty($result)) {
      $chart_colors = ['#0d233a', '#8bbc21', '#910000']; //TODO: Increase color collection
      $library = 'c3';
      $options = [];
      foreach ($result as $chart_details) {
        //common chart options for every chart
        $options['title'] = $chart_details->chart_title;
        $options['yaxis_title'] = $this->t('lorem');
        $options['yaxis_min'] = '';
        $options['yaxis_max'] = '';
        $options['xaxis_title'] = $this->t('ipsum');
        $options['id'] = $chart_details->webform_id . "_" . $chart_details->chart_id;

        switch ($chart_details->chart_type) {
          //per type options for chart
          case 'pie' :
            $options['type'] = 'pie';
            $chart_data_element = unserialize($chart_details->options);
            $query = Database::getConnection()->select('webform_submission_data')
              ->fields('webform_submission_data', ['value'])
              ->condition('webform_id', $webform_id)
              ->condition('name', $chart_data_element)
              ->groupBy('value');
            $query->addExpression('count(sid)', 'submission_count');
            $values = $query->execute()
              ->fetchAllKeyed(0);
            $seriesData = [];
            if (!empty($values)) {
              $index = 0;
             foreach ($values as $key=>$value) {
               $seriesData[] = [
                 'name' => $key,
                 'color' => $chart_colors[$index],
                 'data' => [$value],
               ];
               $index++;
             }
            }
            //sample data format
            $categories = [
              "Category 1",
              "Category 2",
              "Category 3",
              "Category 4"
            ]; //TODO: Check why categories are needed

            break;
          case 'column':
            $options['type'] = 'column';
            //TODO: Get actual data to build the chart
            //sample data format
            $categories = [
              "Category 1",
              "Category 2",
              "Category 3",
              "Category 4"
            ];
            $seriesData = [
              [
                "name" => "Series 1",
                "color" => "#0d233a",
                "type" => NULL,
                "data" => [250, 350, 400, 200]
              ],
              [
                "name" => "Series 2",
                "color" => "#8bbc21",
                "type" => "column",
                "data" => [150, 450, 500, 300]
              ],
              [
                "name" => "Series 3",
                "color" => "#910000",
                "type" => "area",
                "data" => [0, 0, 60, 90]
              ]
            ];
            break;
        }
        //render the chart
        $charts[] = [
          '#theme' => 'charts_webform',
          '#library' => $this->t($library),
          '#categories' => $categories,
          '#seriesData' => $seriesData,
          '#options' => $options,
        ];
      }
    }

    return $charts;
  }
}