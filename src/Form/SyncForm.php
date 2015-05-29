<?php

/**
 * @file
 * Contains Drupal\osu_mm\Form\SyncForm.
 */

namespace Drupal\osu_mm\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\osu_mm\SyncService;

/**
 * Class SyncForm.
 *
 * @package Drupal\osu_mm\Form
 */
class SyncForm extends ConfigFormBase {

  /**
   * Drupal\osu_mm\SyncService definition.
   *
   * @var Drupal\osu_mm\SyncService
   */
  protected $syncService;

  public function __construct(
    ConfigFactoryInterface $config_factory,
    SyncService $osu_mm_sync
  ) {
    parent::__construct($config_factory);
    $this->syncService = $osu_mm_sync;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('osu_mm.sync')
    );
  }


  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'osu_mm.sync_config'
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'sync_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('osu_mm.sync_config');


    $categories = $this->syncService->fetch();
    $output = var_export($categories, TRUE);

    $form['sync_info'] = array(
      '#markup' => "<pre>$output</pre>",
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config('osu_mm.sync_config')
      ->save();
  }

}
