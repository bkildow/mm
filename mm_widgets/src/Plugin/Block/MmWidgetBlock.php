<?php

/**
 * @file
 * Contains Drupal\mm_widgets\Plugin\Block\MmWidgetBlock.
 */

namespace Drupal\mm_widgets\Plugin\Block;

use Drupal\Core\Annotation\Action;
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
      '#required' => TRUE,
      '#description' => $this->t('Media Magnet items API URL'),
      '#default_value' => isset($this->configuration['mm_widgets_items_url']) ? $this->configuration['mm_widgets_items_url'] : 'https://mediamagnet.osu.edu/api/v1/items.json',
    );
    $form['mm_widgets_items_template'] = array(
      '#type' => 'textarea',
      '#title' => $this->t('Item template'),
      '#required' => TRUE,
      '#description' => $this->t('handlebars template'),
      '#default_value' => isset($this->configuration['mm_widgets_items_template']) ? $this->configuration['mm_widgets_items_template'] : $this->handlebarsTemplate(),
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

  /**
   * This is the handlebars template that gets store in the configuration.
   *
   * @return string
   *   handlebars template
   */
  protected function handlebarsTemplate() {
    $default = <<<EOD
<article class="item {{channel.machine_type_name}}">
  <div class="content">
    <span class="excerpt"><a href="{{link}}">{{{excerpt}}}</a></span>
    <br />
  </div>
  <div class="network">
    <div class="attribution">
      {{formatted_published_at}} via <a href="{{channel.url}}">{{channel.name}}</a>.
    </div>
  </div>
</article>
EOD;

    return $default;
  }

}
