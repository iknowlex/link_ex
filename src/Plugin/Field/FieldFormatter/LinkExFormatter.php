<?php

namespace Drupal\link_ex\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\link\Plugin\Field\FieldFormatter\LinkFormatter;

/**
 * Plugin implementation of the 'link_ex' formatter.
 *
 * @FieldFormatter(
 *   id = "link_ex",
 *   label = @Translation("Link (Ex)"),
 *   field_types = {
 *     "link"
 *   }
 * )
 */
class LinkExFormatter extends LinkFormatter {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {

    return parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    /** @var \Drupal\link\Plugin\Field\FieldType\LinkItem $item */

    /** @@TODO manage for private schema */
    $elements = parent::viewElements($items, $langcode);
    foreach ($elements as $delta => &$element) {
      $struri = $element['#url']->toUriString();

      if (isset($element['#options']['attributes']['title'])) {
        $strurl = urldecode($element['#url']->toString());
        $element['#options']['attributes']['title'] = str_ireplace(['<filename>', '<url>'], [basename($struri), $strurl], $element['#options']['attributes']['title']);
        // Lookup of size only if requested and local unmanaged/manged file.
        if (stripos($element['#options']['attributes']['title'], "<size>") !== FALSE && preg_match('/^base:/', $struri)) {
          if (preg_match('/^base:\\d/', $struri)) {
            $struri = str_replace('base:', 'base:/', $struri);
          }
          $uri_parts = parse_url($struri);
          if ($uri_parts !== FALSE) {
            // Set path with default schema to resolve real-path.
            $filepath = str_ireplace(PublicStream::basePath() . "/", "public://", $uri_parts['path']);

            // Get the FileSystem service.
            $filepathabs = \Drupal::service('file_system')->realpath($filepath);

            if (file_exists($filepathabs)) {
              $file_size = format_size(filesize($filepathabs), $langcode);
              $element['#options']['attributes']['title'] = str_ireplace('<size>', $file_size, $element['#options']['attributes']['title']);
            }
          }
        }
      }

      if (isset($element['#options']['attributes']['download'])) {

        if (trim($element['#options']['attributes']['download']) === '<filename>') {
          $element['#options']['attributes']['download'] = basename(urldecode($element['#url']->toString()));
        }
        if (trim($element['#options']['attributes']['download']) === '<blank>') {
          $element['#options']['attributes']['download'] = "";
        }
      }
    }

    return $elements;
  }

}
