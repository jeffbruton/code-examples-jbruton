<?php

namespace Drupal\tibco_nofollow\Plugin\Filter;

use Drupal\Component\Utility\Html;
use Drupal\filter\Plugin\FilterBase;
use Drupal\filter\FilterProcessResult;

/**
 * Provides a filter to add rel=nofollow to links.
 *
 * @Filter(
 *   id = "tibco_nofollow",
 *   title = @Translation("TIBCO Nofollow"),
 *   description = @Translation("Provides a nofollow filter."),
 *   type = \Drupal\filter\Plugin\FilterInterface::TYPE_MARKUP_LANGUAGE,
 * )
 */
class TibcoNofollowFilter extends FilterBase {

  /**
   * {@inheritdoc}
   */
  public function process($text, $langcode) {
    if ($text != null) {
      $text = $this->addNoFollowAttribute($text);
    }

    return new FilterProcessResult($text);
  }

  /**
   * Adds rel="nofollow" attribute to anchor tags if host is not whitelisted.
   *
   * @param String $text
   *   The HTML text string to be filtered.
   *
   * @return String
   *   Filtered HTML string with the applied rel attribute.
   */
  public function addNoFollowAttribute($text) {
    $config = \Drupal::config('tibco_nofollow.settings');
    if ($hosts = $config->get('hosts')) {
      $hosts = preg_split('/\s+/', $hosts);
      $match = null;

      // Apply attribute restrictions to tags
      $html_dom = Html::load($text);
      $links = $html_dom->getElementsByTagName('a');
      foreach ($links as $link) {
        $url = parse_url($link->getAttribute('href'));

        // Check if the host is in the list of whitelisted hosts set at admin/config/tibco/tibco_nofollow
        if (isset($url['host'])) {
          $match = array_filter($hosts, function($host) use ($url) {
            $hosturl = $url['host'];
            return preg_match("#^$hosturl$#", $host);
          });
        }

        // If the host url is set, and it is not in the whitelist, set the rel
        if (isset($url['host']) && empty($match)) {
          $link->setAttribute('rel', 'nofollow');
        }
      }

      $text = Html::serialize($html_dom);
    }

    return trim($text);
  }
}
