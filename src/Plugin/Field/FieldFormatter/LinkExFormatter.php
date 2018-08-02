<?php

namespace Drupal\link_ex\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\link\Plugin\Field\FieldFormatter\LinkFormatter;
use Drupal\Core\StreamWrapper\PublicStream;
use Drupal\Core\Url;

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

    $elements = parent::viewElements($items, $langcode);
    foreach ($elements as $delta => &$element) {

      $struri = $element['#url']->toUriString();

      $file = NULL;
      if ($element['#url']->isRouted()) {
        if ($element['#url']->getRouteName() === 'system.files') {
          $file = !empty($element['#url']->getOption('query')) ? 'private://' . $element['#url']->getOption('query')['file'] : '';
        }
        $options = $element['#url']->getOptions();
        unset($options['query']['file']);
        unset($element['#options']['query']['file']);
        // @todo Wrap in file_url_transform_relative()
        // Fix in https://www.drupal.org/node/2646744.
        $element['#url'] = Url::fromUri(file_create_url($file), $options);
      }
      else {
        $fileuri = $element['#url']->getUri();
        if (preg_match('/^base:/', $fileuri)) {
          $file = str_ireplace('base:' . PublicStream::basePath() . "/", "public://", $fileuri);
        }
      }

      $file_info = [];
      // Get the info as managed file.
      if (!empty($file) && $file_entity = $this->getFileEntity($file)) {
        $file_info['filename'] = $file_entity->getFilename();
        $file_info['size'] = $file_entity->getSize();
        $file_info['mime'] = $file_entity->getMimeType();
        $file_info['ext'] = pathinfo($file_entity->getFilename(), PATHINFO_EXTENSION);
      }
      else {
        // Try to get file info as unmanaged file.
        $file_info = $this->getUnmanagedFileInfo($file);
      }

      $strurl = urldecode($element['#url']->toString());
      $find = ['<filename>', '<url>', '<extension>', '<size>', '<mime>'];
      $replace = [$file_info['filename'], $strurl, $file_info['ext'], format_size($file_info['size'], $langcode), strtr($file_info['mime'], ['/' => '-', '.' => '-'])];

      if (isset($element['#options']['attributes']['title']) && !empty($file_info)) {

        $element['#options']['attributes']['title'] = str_ireplace($find, $replace, $element['#options']['attributes']['title']);
      }

      if (isset($element['#title']) && !empty($file_info)) {
        $element['#title'] = str_ireplace($find, $replace, $element['#title']);
      }

      if (isset($element['#options']['attributes']['class'])) {
        $element['#options']['attributes']['class'] = str_ireplace($find, $replace, $element['#options']['attributes']['class']);
      }

      if (isset($element['#options']['attributes']['download'])) {
        if (trim($element['#options']['attributes']['download']) === '<filename>' && isset($file_info['filename'])) {
          $element['#options']['attributes']['download'] = $file_info['filename'];
        }
        if (trim($element['#options']['attributes']['download']) === '<blank>') {
          $element['#options']['attributes']['download'] = "";
        }
      }

    }

    return $elements;
  }

  /**
   * Returns a unmanaged file information.
   */
  private function getUnmanagedFileInfo($fileuri) {
    $file_info = [];
    // Get the FileSystem service.
    $filepathabs = \Drupal::service('file_system')->realpath($fileuri);

    if (file_exists($filepathabs)) {
      $file_info['size'] = filesize($filepathabs);
      $file_info['filename'] = basename($filepathabs);
      $file_info['mime'] = mime_content_type($filepathabs);
      $file_info['ext'] = pathinfo($filepathabs, PATHINFO_EXTENSION);
    }

    return $file_info;
  }

  /**
   * Returns a managed file entity by uri.
   */
  private function getFileEntity($uri) {
    $file = FALSE;
    if ($files = \Drupal::entityTypeManager()->getStorage('file')->loadByProperties(['uri' => $uri])) {
      $file = reset($files);
    }
    return $file;
  }

}
