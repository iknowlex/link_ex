<?php

namespace Drupal\link_ex\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\link\Plugin\Field\FieldWidget\LinkWidget;

/**
 * Plugin implementation of the 'link' widget.
 *
 * @FieldWidget(
 *   id = "link_ex",
 *   label = @Translation("Link (Ex)"),
 *   field_types = {
 *     "link"
 *   }
 * )
 */
class LinkExFieldWidget extends LinkWidget {

  /**
   * Constructs a LinkExFieldWidget object.
   *
   * @param string $plugin_id
   *   The plugin_id for the widget.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The definition of the field to which the widget is associated.
   * @param array $settings
   *   The widget settings.
   * @param array $third_party_settings
   *   Any third party settings.
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, array $third_party_settings) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $third_party_settings);

  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {

    // @todo default for attributes ?

    return [
      'placeholder_url' => '',
      'placeholder_title' => '',
      'enabled_attributes' => [],

    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $element = parent::formElement($items, $delta, $element, $form, $form_state);
    // Add each of the enabled attributes.
    // @todo move this to plugins that nominate form and label.

    $item = $items[$delta];

    $options = $item->get('options')->getValue();
    $attributes = isset($options['attributes']) ? $options['attributes'] : [];
    $element['options']['attributes'] = [
      '#type' => 'details',
      '#title' => $this->t('Attributes'),
      '#tree' => TRUE,
    // count($attributes),
      '#open' => FALSE,
    ];

    $attOptions = $this->attributeOptions();
    // Remove hidden options as.
    $plugin_definitions = array_diff_key($attOptions, array_filter($attOptions, function ($var) {
        return ($var['#type'] === 'hidden');
    }));

    $enabled_attributes = array_keys(array_filter($this->getSetting('enabled_attributes')));

    foreach ($enabled_attributes as $attribute) {
      if (isset($plugin_definitions[$attribute])) {
        $element['options']['attributes'][$attribute] = $plugin_definitions[$attribute];
        $element['options']['attributes'][$attribute]['#default_value'] = isset($attributes[$attribute]) ? $attributes[$attribute] : '';
      }
    }

    // @todo setup based on the attribute configuration
    if (in_array('imce', $enabled_attributes)) {
      $element['uri']['#attributes']['data-link_ex-file_browser'] = 'imce';
    }

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $element = parent::settingsForm($form, $form_state);
    $selected = array_keys(array_filter($this->getSetting('enabled_attributes')));
    $attOptions = $this->attributeOptions();
    $options = array_combine(array_keys($attOptions), array_column($attOptions, '#title'));

    $element['enabled_attributes'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Enabled attributes'),
      '#options' => $options,
      '#default_value' => array_combine($selected, $selected),
      '#description' => $this->t('Select the attributes to allow the user to edit.'),
    ];
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {
    return array_map(function (array $value) {
      $value['options']['attributes'] = array_filter($value['options']['attributes'], function ($attribute) {
        return $attribute !== "";
      });
      return $value;
    }, $values);
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = parent::settingsSummary();
    $enabled_attributes = array_filter($this->getSetting('enabled_attributes'));
    if ($enabled_attributes) {
      $summary[] = $this->t('With attributes: @attributes', ['@attributes' => implode(', ', array_keys($enabled_attributes))]);
    }
    return $summary;
  }

  /**
   * Setting form attribute options for link.
   */
  public function attributeOptions() {

    $option['id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Link ID'),
      '#placeholder' => $this->t('ID attribute'),
      '#default_value' => NULL,
      '#maxlength' => 255,
    ];

    $option['rel'] = [
      '#type' => 'select',
      '#title' => $this->t('Rel'),
      '#default_value' => NULL,
      '#options' => [
        'alternate' => 'alternate',
        'author' => 'author',
        'bookmark' => 'bookmark',
        'external' => 'external',
        'help' => 'help',
        'license' => 'license',
        'next' => 'next',
        'nofollow' => 'nofollow',
        'noreferrer' => 'noreferrer',
        'noopener' => 'noopener',
        'prev' => 'prev',
        'search' => 'search',
        'tag' => 'tag',
      ],
      '#required' => FALSE,
      '#empty_value' => '',
    ];

    $option['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#placeholder' => $this->t('Name attribute'),
      '#default_value' => NULL,
      '#maxlength' => 255,
      '#description' => '',
    ];

    $option['target'] = [
      '#type' => 'select',
      '#title' => $this->t('Link target'),
      '#options' => [
        '_self'  => $this->t('Same window (_self)'),
        '_blank' => $this->t('New window (_blank)'),
      ],
      '#required' => FALSE,
      '#empty_value' => '',
    ];

    $option['class'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Class'),
      '#placeholder' => $this->t('CSS classs'),
      '#default_value' => NULL,
      '#maxlength' => 255,
    ];

    $option['accesskey'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Accesskey'),
      '#placeholder' => $this->t('Link accesskey'),
      '#default_value' => NULL,
      '#maxlength' => 255,
      '#description' => '',
    ];

    $option['imce'] = [
      '#type' => 'hidden',
      '#title' => $this->t('Imce File Manager'),
      '#placeholder' => $this->t('Link accesskey'),
    ];

    return $option;
  }

}
