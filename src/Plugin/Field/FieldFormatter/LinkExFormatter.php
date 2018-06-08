<?php

namespace Drupal\link_ex\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
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


	 $elements = parent::viewElements($items, $langcode);
	 foreach ($elements as $delta => &$element) {
		 if( isset($element['#options']['attributes']['title'])) {
			 $strUrl = urldecode($element['#url']->toString()); 
			 $element['#options']['attributes']['title'] = str_ireplace( array('<filename>', '<url>'), array(basename($strUrl), $strUrl), $element['#options']['attributes']['title'] );   
		 }
	 
	 	 if( isset($element['#options']['attributes']['download'])) {
			  
	 	 	 if(trim($element['#options']['attributes']['download']) === '<filename>') {
	 	 	 	 $element['#options']['attributes']['download'] = basename( urldecode($element['#url']->toString()) ) ;
			 }
			 if(trim($element['#options']['attributes']['download']) === '<blank>') {
				 $element['#options']['attributes']['download'] = "";
			 }
	 	 }
	 }

    return $elements;
  }
  
}