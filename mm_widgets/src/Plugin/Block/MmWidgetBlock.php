<?php

/**
 * @file
 * Contains Drupal\mm_widgets\Plugin\Block\MmWidgetBlock.
 */

namespace Drupal\mm_widgets\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'MmWidgetBlock' block.
 *
 * @Block(
 *  id = "mm_widget_block",
 *  admin_label = @Translation("Media Magnet Block"),
 * )
 */
class MmWidgetBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form['mm_widgets_items_url'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('URL'),
      '#description' => $this->t('Media Magnet items API URL'),
      '#default_value' => isset($this->configuration['mm_widgets_items_url']) ? $this->configuration['mm_widgets_items_url'] : '',
    );
    $form['mm_widgets_items_template'] = array(
      '#type' => 'textarea',
      '#title' => $this->t('Item template'),
      '#description' => $this->t('handlebars template'),
      '#default_value' => isset($this->configuration['mm_widgets_items_template']) ? $this->configuration['mm_widgets_items_template'] : '',
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['mm_widgets_items_url'] = $form_state->getValue('mm_widgets_items_url');
    $this->configuration['mm_widgets_items_template'] = $form_state->getValue('mm_widgets_items_template');
  }


  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = array(
      '#theme'        => 'mm_widgets_block',
      '#url'          => $this->configuration['mm_widgets_items_url'],
      '#template'     => $this->configuration['mm_widgets_items_template'],
      '#machineName'  => $this->getMachineNameSuggestion(),
      '#attached' => array(
        'library' => array(
          'mm_widgets/mm-widgets',
        )
      ),
    );

    return $build;
  }

}
