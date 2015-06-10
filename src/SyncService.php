<?php

/**
 * @file
 * Contains Drupal\osu_mm\SyncService.
 */

namespace Drupal\osu_mm;

use Drupal\Core\Http\Client;
use Drupal\Core\Config\Entity\Query;
use Drupal\taxonomy\Entity\Vocabulary;

/**
 * Class SyncService.
 *
 * @package Drupal\osu_mm
 */
class SyncService {

  /**
   * Drupal\Core\Http\Client definition.
   *
   * @var Drupal\Core\Http\Client
   */
  protected $http_client;

  /**
   * Drupal\Core\Config\Entity\Query definition.
   *
   * @var Drupal\Core\Config\Entity\Query
   */
  protected $entity_query;

  /**
   * Constructor.
   */
  public function __construct(Client $http_client, Query $entity_query) {
    $this->http_client = $http_client;
    $this->entity_query = $entity_query;
  }

  /**
   * Fetch media magnet data.
   *
   * @return array
   *   categories and keywords
   */
  public function fetch() {
    $categories = array();
    $page = 0;
    $last = 1;
    while ($page < $last) {
      $page++;
      $path = "https://mediamagnet.osu.edu/api/v1/keywords.json?page=$page";

      // TODO: How do we get SSL to properly work?
      $response = $this->http_client->get($path, ['verify' => FALSE]);
      $result = json_decode($response->getBody());
      $last = $result->meta->total_pages;
      foreach ($result->keywords as $keyword) {
        $categories[$keyword->category][] = $keyword;
      }
    }
    return $categories;
  }

  /**
   * Creates vocabularies and terms if they don't exist.
   */
  public function sync() {
    $categories = $this->fetch();
    foreach ($categories as $category => $keywords) {

      $vid = $this->getVidFromCategory($category);
      $vocab = Vocabulary::load($vid);

      // Create the vocabulary if it doesn't exist.
      if (!$vocab) {
        $this->createVocabulary($category);
      }

      $this->createTerms($vid, $keywords);
    }
  }

  /**
   * Removes all Media Magnet vocabularies.
   */
  public function uninstall() {
    $vocabs = Vocabulary::loadMultiple();
    foreach ($vocabs as $vocab) {
      if (substr($vocab->id(), 0, 6) == 'osu_mm') {
        $vocab->delete();
      }
    }
  }

  /**
   * Create a new vocabulary.
   *
   * @param string $category
   *   name of category
   */
  protected function createVocabulary($category) {
    $vid = $this->getVidFromCategory($category);
    $display_name = ucfirst($category) . 's (Media Magnet)';

    entity_create('taxonomy_vocabulary', array(
      'name' => $display_name,
      'vid' => $vid,
    ))->save();
  }

  /**
   * Create terms.
   *
   * @param string $vid
   *   vocab id
   * @param array $terms
   *   array of mm objects
   */
  protected function createTerms($vid, $terms) {
    foreach ($terms as $t) {
      $tids = $this->taxonomyGetTermByName($t->display_name);

      if (!$tids) {
        $term = entity_create('taxonomy_term', array(
          'name' => $t->display_name,
          'vid' => $vid,
        ));
        $term->description->value = $t->description;
        $term->save();
      }
    }
  }

  /**
   * Gets taxonomy terms by name.
   *
   * @param string $name
   *   term name
   *
   * @return array|null
   *   term ids
   */
  protected function taxonomyGetTermByName($name) {
    $term_query = $this->entity_query->get('taxonomy_term');
    return $term_query->condition('name', $name)->execute();
  }

  /**
   * Returns a vocabulary id given a media magnet category.
   *
   * @param string $category
   *   media magnet category
   *
   * @return string
   *   The vocabulary id
   */
  protected function getVidFromCategory($category) {
    return 'osu_mm_' . $category;
  }


}
