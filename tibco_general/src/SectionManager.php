<?php

namespace Drupal\tibco_general;

use Drupal\Core\Url;

/**
 * Section Manager
 */
class SectionManager {

  /**
   * Add subnav to render array
   *
   * @param $variables
   * @param $node
   */
  public function addSubnavToRenderArray(&$vars,$node){

    // Get submenu link definitions
    $submenuLinkDefinitions = $this->getSubnavDefinitions($node);

    // Get ordered sections
    $orderedSections = $this->getOrderedSections($node,$vars['view_mode']);

    // If we loaded submenu link definitions
    if(
      $submenuLinkDefinitions
      && $orderedSections
    ) {

      // Reorder links, we have to do a double for loop because there
      // can be multiple links per section
      $submenuLinks = [];
      // Loop over ordered sections
      foreach($orderedSections as $sectionName => $sectionTemplate) {
        // Loop over menu links
        foreach($submenuLinkDefinitions as $submenuLinkDefinition) {
          if($submenuLinkDefinition['section'] == $sectionName) {
            $submenuLinks[] = $submenuLinkDefinition;
          }
        }
      }

      // Loop over submenu link definitions
      foreach($submenuLinks as $submenuLink) {

        // If this link is visible
        if(
          count($submenuLink)
          && $submenuLink['visibility']
        ) {

          // Indicate we'll show the submenu
          $vars['show_subnav'] = TRUE;

          // Calculate link title
          if(
            $submenuLink['titleField']
            && $node->hasField($submenuLink['titleField'])
            && !$node->get($submenuLink['titleField'])->isEmpty()
          ) {
            $submenuLinkTitle = $node->get($submenuLink['titleField'])->value;
          }else {
            $submenuLinkTitle = $submenuLink['titleFallback'];
          }

          // Add submenu item
          $vars['sub_navigation']['#items'][] = [
            '#type' => 'link',
            '#title' => $submenuLinkTitle,
            '#url' => Url::fromUserInput($submenuLink['id'])
          ];

        }

      }

    }

  }

  /**
   * Return subnav definitions
   * @param $node
   */
  public function getSubnavDefinitions($node){

    $submenuLinks = [

      // Submenu link definitions for products
      'product' => [
        [
          'visibility'    => $node->hasField('field_benefits') && !$node->get('field_benefits')->isEmpty(),
          'titleField'    => 'field_benefits_sub_nav_link',
          'titleFallback' => t('Benefits'),
          'id'            => '#benefits',
          'section'       => 'benefits',
        ],
        [
          'visibility'    => $node->hasField('field_featured_capabilities') && !$node->get('field_featured_capabilities')->isEmpty(),
          'titleField'    => 'field_capabilities_sub_nav_link',
          'titleFallback' => t('Capabilities'),
          'id'            => '#capabilities',
          'section'       => 'capabilities',
        ],
        [
          'visibility'    => $node->hasField('field_products_solutions') && !$node->get('field_products_solutions')->isEmpty(),
          'titleField'    => 'field_capabilities_sub_nav_link',
          'titleFallback' => t('Products & Solutions'),
          'id'            => '#products-solutions',
          'section'       => 'video_carousel',
        ],
        [
          'visibility'    => $node->hasField('field_new_features') && !$node->get('field_new_features')->isEmpty(),
          'titleField'    => 'field_whats_new_sub_nav_link',
          'titleFallback' => t("What's New"),
          'id'            => '#newFeatures',
          'section'       => 'whats_new',
        ],
        [
          'visibility'    => $node->hasField('field_product_components') && !$node->get('field_product_components')->isEmpty(),
          'titleField'    => 'field_components_sub_nav_l',
          'titleFallback' => t("Components"),
          'id'            => '#components',
          'section'       => 'product_components',
        ],
        [
          'visibility'    => $node->hasField('field_featured_customers') && !$node->get('field_featured_customers')->isEmpty(),
          'titleField'    => 'field_customers_sub_nav_link',
          'titleFallback' => t('Customers'),
          'id'            => '#customers',
          'section'       => 'customers'
        ],
        [
          'visibility'    => $node->hasField('field_featured_resources') && !$node->get('field_featured_resources')->isEmpty(),
          'titleField'    => 'field_resources_sub_nav_link',
          'titleFallback' => t('Resources'),
          'id'            => '#resources',
          'section'       => 'resources'
        ],
      ],
      
      // Submenu link definitionfs for solutions
//      'solution' => [
//          [
//          'visibility'    => $node->hasField('field_featured_benefits') && !$node->get('field_featured_benefits')->isEmpty(),
//          'titleField'    => t('Benefits'),
//          'titleFallback' => t('Benefits'),
//          'id'            => '#benefits',
//          'section'       => 'benefits',
//        ],
//           [
//          'visibility'    => $node->hasField('field_featured_capabilities') && !$node->get('field_featured_capabilities')->isEmpty(),
//          'titleField'    => t('Capabilities'),
//          'titleFallback' => t('Capabilities'),
//          'id'            => '#capabilities',
//          'section'       => 'capabilities',
//        ],
//        
//        [
//          'visibility'    => $node->hasField('field_featured_customers') && !$node->get('field_featured_customers')->isEmpty(),
//          'titleField'    => t('Customers'),
//          'titleFallback' => t('Customers'),
//          'id'            => '#customers',
//          'section'       => 'customers'
//        ],
//        [
//          'visibility'    => $node->hasField('field_related_products') && !$node->get('field_related_products')->isEmpty(),
//          'titleField'    => t('Products'),
//          'titleFallback' => t('Products'),
//          'id'            => '#products',
//          'section'       => 'products'
//        ],
//        [
//          'visibility'    => $node->hasField('field_related_solutions') && !$node->get('field_related_solutions')->isEmpty(),
//          'titleField'    => t('Solutions'),
//          'titleFallback' => t('Solutions'),
//          'id'            => '#solutions',
//          'section'       => 'solutions'
//        ],
//        [
//          'visibility'    => $node->hasField('field_featured_resources') && !$node->get('field_featured_resources')->isEmpty(),
//          'titleField'    => t('Resources'),
//          'titleFallback' => t('Resources'),
//          'id'            => '#resources',
//          'section'       => 'resources'
//        ],
//      ],

      // Submenu link definitions for categories
      'category' => [
         [
          'visibility'    => $node->hasField('field_benefits') && !$node->get('field_benefits')->isEmpty(),
          'titleField'    => 'field_benefits_sub_nav_link',
          'titleFallback' => t('Benefits'),
          'id'            => '#benefits',
          'section'       => 'benefits',
        ],
        [
          'visibility'    => $node->hasField('field_featured_capabilities') && !$node->get('field_featured_capabilities')->isEmpty(),
          'titleField'    => 'field_capabilities_sub_nav_link',
          'titleFallback' => t('Capabilities'),
          'id'            => '#capabilities',
          'section'       => 'capabilities',
        ],
        [
          'visibility'    => $node->hasField('field_featured_customers') && !$node->get('field_featured_customers')->isEmpty(),
          'titleField'    => 'field_customers_sub_nav_link',
          'titleFallback' => t('Customers'),
          'id'            => '#customers',
          'section'       => 'customers'
        ],
        [
          'visibility'    => $node->hasField('field_featured_resources') && !$node->get('field_featured_resources')->isEmpty(),
          'titleField'    => 'field_resources_sub_nav_link',
          'titleFallback' => t('Resources'),
          'id'            => '#resources',
          'section'       => 'resources'
        ],
        [
          'visibility'    => $node->hasField('field_products_solutions') && !$node->get('field_products_solutions')->isEmpty(),
          'titleField'    => '',
          'titleFallback' => t("Demos"),
          'id'            => '#products-solutions',
          'section'       => 'solutions',
        ],
        [
          'visibility'    => ($node->hasField('field_related_products') && !$node->get('field_related_products')->isEmpty()) || ($node->hasField('field_custom_product') && !$node->get('field_custom_product')->isEmpty()),
          'titleField'    => 'field_related_product_heading',
          'titleFallback' => t("Products"),
          'id'            => '#relatedProduct',
          'section'       => 'related_products_categories'
        ],
        [
          'visibility'    => $node->hasField('field_related_categories') && !$node->get('field_related_categories')->isEmpty(),
          'titleField'    => 'field_related_category_heading',
          'titleFallback' => t("Categories"),
          'id'            => '#relatedCategory',
          'section'       => 'related_products_categories',
        ],
      ],

      // Product Components
      'product_component' => [
        [
          'visibility'    => $node->hasField('field_benefits') && !$node->get('field_benefits')->isEmpty(),
          'titleField'    => 'field_benefits_sub_nav_link',
          'titleFallback' => t('Benefits'),
          'id'            => '#benefits',
          'section'       => 'benefits',
        ],
        [
          'visibility'    => $node->hasField('field_featured_capabilities') && !$node->get('field_featured_capabilities')->isEmpty(),
          'titleField'    => 'field_capabilities_sub_nav_link',
          'titleFallback' => t('Capabilities'),
          'id'            => '#capabilities',
          'section'       => 'capabilities',
        ],
        [
          'visibility'    => $node->hasField('field_new_features') && !$node->get('field_new_features')->isEmpty(),
          'titleField'    => 'field_whats_new_sub_nav_link',
          'titleFallback' => t("What's New"),
          'id'            => '#newFeatures',
          'section'       => 'whats_new',
        ],
        [
          'visibility'    => $node->hasField('field_featured_resources') && !$node->get('field_featured_resources')->isEmpty(),
          'titleField'    => 'field_resources_sub_nav_link',
          'titleFallback' => t('Resources'),
          'id'            => '#resources',
          'section'       => 'resources',
        ],
        [
          'visibility'    => $node->hasField('field_featured_customers') && !$node->get('field_featured_customers')->isEmpty(),
          'titleField'    => 'field_customers_sub_nav_link',
          'titleFallback' => t('Customers'),
          'id'            => '#customers',
          'section'       => 'customers',
        ],
      ],

    ];

    // Return link definitions for this node type
    if(isset($submenuLinks[$node->getType()])) {
      return $submenuLinks[$node->getType()];
    }

  }

  /**
   * Return ordered sections
   */
  public function getOrderedSections($node,$viewMode) {

    if($node->hasField('field_section_order')) {

      // get section definitions
      $sections = $this->getSectionDefinitions();

      // Implement overridden section order
      $sectionOrderFieldValues = $node->get('field_section_order')->getValue();
      // If we have an overridden order field value
      if (count($sectionOrderFieldValues)) {
        $overriddenOrder = [];
        foreach ($sectionOrderFieldValues as $sectionOrderFieldValue) {
          if (
            isset($sections[$node->getType()][$viewMode][$sectionOrderFieldValue['value']])
            && $sections[$node->getType()][$viewMode][$sectionOrderFieldValue['value']] != ''
          ) {
            $overriddenOrder[$sectionOrderFieldValue['value']] = $sections[$node->getType()][$viewMode][$sectionOrderFieldValue['value']];
          }
        }
        $sections = $overriddenOrder;
      }
      // If we don't have an overridden order field value
      else {
        $sections = $sections[$node->getType()][$viewMode];
      }

      return $sections;

    }

  }

  /**
   * Returns section definitions.
   */
  public function getSectionDefinitions() {

    // Section Order and template file definitions
    $sectionDefinitions = [

      // Product Component bundle section definitions
      'category' => [

        // Product Component "full" view mode section definitions
        'full' => [
          'top_cta' => '@tibco/sections/product_categories/default/top_cta.html.twig',
          'media_bar' => '@tibco/sections/product_categories/default/media_bar.html.twig',
          'benefits' => '@tibco/sections/product_categories/default/benefits.html.twig',
          'capabilities' => '@tibco/sections/product_categories/default/capabilities.html.twig',
          'video_carousel' => '@tibco/sections/product_categories/default/video_carousel.html.twig',
          'related_products_categories' => '@tibco/sections/product_categories/default/related_products.html.twig',
          'customers' => '@tibco/sections/product_categories/default/customers.html.twig',
          'resources' => '@tibco/sections/product_categories/default/resources.html.twig',
        ],

      ],

      // Product Component bundle section definitions
      'product_component' => [

        // Product Component "full" view mode section definitions
        'full' => [
          'cta_bar' => '@tibco/sections/product_components/default/cta_bar.html.twig',
          'benefits' => '@tibco/sections/product_components/default/benefits.html.twig',
          'capabilities' => '@tibco/sections/product_components/default/capabilities.html.twig',
          'whats_new' => '@tibco/sections/product_components/default/whats_new.html.twig',
          'customers' => '@tibco/sections/product_components/default/customers.html.twig',
          'resources' => '@tibco/sections/product_components/default/resources.html.twig',
        ],

       ],

      // Product bundle section definitions
      'product' => [

        // Product "full" view mode section definitions
        'full' => [
          'media_bar' => '@tibco/sections/products/default/media_bar.html.twig',
          'benefits' => '@tibco/sections/products/default/benefits.html.twig',
          'capabilities' => '@tibco/sections/products/default/capabilities.html.twig',
          'cta_bar' => '@tibco/sections/products/default/cta_bar.html.twig',
          'video_carousel' => '@tibco/sections/products/default/video_carousel.html.twig',
          'whats_new' => '@tibco/sections/products/default/whats_new.html.twig',
          'product_components' => '@tibco/sections/products/default/product_components.html.twig',
          'customers' => '@tibco/sections/products/default/customers.html.twig',
          'resources' => '@tibco/sections/products/default/resources.html.twig',
        ],

      ],

      // Solution bundle section definitions
      'solution' => [

        // Section Pages use "full" view mode except on jaspersoft
        'full' => [
          'cta_bar' => '@tibco/sections/solutions/default/cta-bar.html.twig',
          'vertical_tabs' => '@tibco/solutions/sections/default/vertical-tabs.html.twig',
          'media_bar' => '@tibco/sections/solutions/default/media-bar.html.twig',
          'benefits' => '@tibco/sections/solutions/default/benafits.html.twig',
          'capabilities' => '@tibco/sections/solutions/default/capabilities.html.twig',
          'video_carousel' => '@tibco/sections/solutions/default/video-carousel.html.twig',
          'products' => '@tibco/sections/solutions/default/products.html.twig',
          'solutions' => '@tibco/sections/solutions/default/solutions.html.twig',
          'customers' => '@tibco/sections/solutions/default/customers.html.twig',
          'resources' => '@tibco/sections/solutions/default/resources.html.twig',
          'related_connectors' => '@tibco/sections/solutions/default/related-connectors.html.twig',
        ],

        // Section Pages use "sections" view mode on jaspersoft
        'sections' => [
          'cta_bar' => '@tibco/sections/solutions/jaspersoft/cta-bar.html.twig',
          'vertical_tabs' => '@tibco/solutions/sections/default/vertical-tabs.html.twig',
          'media_bar' => '@tibco/sections/solutions/jaspersoft/media-bar.html.twig',
          'benefits' => '@tibco/sections/solutions/jaspersoft/benafits.html.twig',
          'capabilities' => '@tibco/sections/solutions/jaspersoft/capabilities.html.twig',
          'video_carousel' => '@tibco/sections/solutions/jaspersoft/video-carousel.html.twig',
          'products' => '@tibco/sections/solutions/jaspersoft/products.html.twig',
          'solutions' => '@tibco/sections/solutions/jaspersoft/solutions.html.twig',
          'customers' => '@tibco/sections/solutions/jaspersoft/customers.html.twig',
          'resources' => '@tibco/sections/solutions/jaspersoft/resources.html.twig',
          'related_connectors' => '@tibco/sections/solutions/jaspersoft/related-connectors.html.twig',
        ],

      ],

    ];

    // Return definitions
    return $sectionDefinitions;

  }


}
