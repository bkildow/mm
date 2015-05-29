<?php

/**
 * @file
 * Contains Drupal\osu_mm\SyncService.
 */

namespace Drupal\osu_mm;

use Drupal\Core\Http\Client;

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
   * Constructor.
   */
  public function __construct(Client $http_client) {
    $this->http_client = $http_client;
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

  public function sync() {
  }


}
