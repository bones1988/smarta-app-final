<?php

namespace Drupal\twitter_block\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Defines a twitter block block type.
 *
 * @Block(
 *   id = "twitter_block",
 *   admin_label = @Translation("Twitter block"),
 *   category = @Translation("Twitter"),
 * )
 */
class TwitterBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {

    $config = $this->getConfiguration();

    $form['username'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Username'),
      '#default_value' => $config['username'],
      '#required' => TRUE,
      '#field_prefix' => '@',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->setConfigurationValue('username', $form_state->getValue('username'));
    foreach (['appearance', 'functionality', 'size', 'accessibility'] as $fieldset) {
      $fieldset_values = $form_state->getValue($fieldset);
      foreach ($fieldset_values as $key => $value) {
        $this->setConfigurationValue($key, $value);
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function build() {

    $config = $this->getConfiguration();

    $render['block'] = [
      '#type' => 'link',
      '#title' => $this->t('Tweets by @@username', ['@username' => $config['username']]),
      '#url' => Url::fromUri('https://twitter.com/' . $config['username']),
      '#attributes' => [
        'class' => ['twitter-timeline'],
      ],
      '#attached' => [
        'library' => ['twitter_block/widgets'],
      ],
    ];

    return $render;
  }

  /**
   * {@inheritdoc}
   */
  

}
