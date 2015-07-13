<?php

/**
 * @file
 * Contains Drupal\mm_vocab\Form\SyncForm.
 */

namespace Drupal\mm_vocab\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\mm_vocab\SyncService;

/**
 * Class SyncForm.
 *
 * @package Drupal\mm_vocab\Form
 */
class SyncForm extends ConfigFormBase {

  /**
   * Drupal\mm_vocab\SyncService definition.
   *
   * @var Drupal\mm_vocab\SyncService
   */
  protected $syncService;

  public function __construct(
    ConfigFactoryInterface $config_factory,
    SyncService $mm_vocab_sync
  ) {
    parent::__construct($config_factory);
    $this->syncService = $mm_vocab_sync;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('mm_vocab.sync')
    );
  }


  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'mm_vocab.sync_config'
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
    $config = $this->config('mm_vocab.sync_config');


    $categories = $this->syncService->fetch();

//    dpm($categories);

//    $output = var_export($categories, TRUE);
//
//    $form['sync_info'] = array(
//      '#markup' => "<pre>$output</pre>",
//    );

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
    $categories = $this->syncService->sync();

    $this->config('mm_vocab.sync_config')
      ->save();
  }



}
