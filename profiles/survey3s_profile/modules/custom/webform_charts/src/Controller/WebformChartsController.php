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
    $entity_type = 'webform_submission';
    $query = Database::getConnection()->select('webform_submission_data')
      ->fields('webform_submission_data', ['value'])
      ->condition('webform_id', $webform_id)
      ->condition('name', 'gender')
      ->groupBy('value');
    $query->addExpression('count(sid)', 'submission_count');
    $values = $query->execute()
      ->fetchAllKeyed(0);

    $charts = [];
    //add chart link
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
                "data" => [250]
              ],
              [
                "name" => "Series 2",
                "color" => "#8bbc21",
                "type" => "column",
                "data" => [150]
              ],
              [
                "name" => "Series 3",
                "color" => "#910000",
                "type" => "area",
                "data" => [60]
              ]
            ];

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