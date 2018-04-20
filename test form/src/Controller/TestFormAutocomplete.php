<?php
/**
 * @file
 * Contains \Drupal\test_form\Controller\TestFormAutocomplete.
 */

namespace Drupal\test_form\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Database\Database;
use Drupal\Component\Utility\Html;

class TestFormAutocomplete {

  /**
   * Return data for autocomplete.
   *
   * {@inheritdoc}
   */
  public function autocomplete(Request $request) {

    $string = $request->query->get('q');

    $matches = [];

    if ($string) {
      // search node type content matches autocomplete.
      $query = Database::getConnection()->select('node_field_data', 'n')
        ->fields('n', ['nid', 'title'])
        ->condition('n.title', '%' . $string . '%', 'LIKE')
        ->range(0, 10);
      $result = $query->execute();

      // Add result to array.
      foreach ($result as $row) {
        $value = Html::escape($row->title . ' (' . $row->nid . ')');
        $label = Html::escape($row->title);
        $matches[] = ['value' => $value, 'label' => $label];
      }
    }
    return new JsonResponse($matches);
  }
}