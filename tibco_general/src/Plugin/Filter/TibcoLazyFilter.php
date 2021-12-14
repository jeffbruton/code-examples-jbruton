<?php

namespace Drupal\tibco_general\Plugin\Filter;

use Drupal\Component\Utility\Html;
use Drupal\filter\Plugin\FilterBase;
use Drupal\filter\FilterProcessResult;

/**
 * Provides a filter to add loading=lazy to img tags.
 *
 * @Filter(
 *   id = "tibco_lazy",
 *   title = @Translation("TIBCO Lazy Loading Images"),
 *   description = @Translation("Provides a lazyload filter."),
 *   type = \Drupal\filter\Plugin\FilterInterface::TYPE_MARKUP_LANGUAGE,
 * )
 */
class TibcoLazyFilter extends FilterBase {

  /**
   * {@inheritdoc}
   */
  public function process($text, $langcode) {
    if ($text != null) {
      $text = $this->addLazyAttribute($text);
    }

    return new FilterProcessResult($text);
  }

  /**
   * Adds loading = "lazy" attribute to inline img tags.
   *
   * @param String $text
   *   The HTML text string to be filtered.
   *
   * @return String
   *   Filtered HTML string with the applied loading attribute.
   */
  public function addLazyAttribute($text) {
    // Apply attribute restrictions to tags.
    $html_dom = Html::load($text);

    $images = $html_dom->getElementsByTagName('img');

    foreach ($images as $image) {
      $image->setAttribute('loading', 'lazy');
    }

    $text = Html::serialize($html_dom);

    return trim($text);
  }
}
