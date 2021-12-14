<?php

namespace Drupal\tibco_general\EventSubscriber;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class httpHeaderSubscriber implements EventSubscriberInterface
{
  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents()
  {
    $events[KernelEvents::RESPONSE][] = array('addCSP');
    return $events;
  }

  public function addCSP(FilterResponseEvent $event)
  {
    if(stripos(\Drupal::request()->getHost(), 'www.jaspersoft.com') !== 0)
      return;

    $response = $event->getResponse();
    $response->headers->remove('X-Frame-Options');

    //For www.jaspersoft.com/aws-signup page when it's in an iframe
    if(stripos(\Drupal::request()->getHost(), 'www.jaspersoft.com') === 0
    && \Drupal::service('path_alias.manager')->getAliasByPath(\Drupal::service('path.current')->getPath()) == '/aws-signup'
    && !empty(\Drupal::request()->query->get('instance')))
      return; //can't set CSP for iframes in localhost

    $response->headers->set('Content-Security-Policy', "frame-ancestors 'self' https://www.tibco.com http://tibco.lookbookhq.com https://tibco.lookbookhq.com https://sso-awsqa.tibco.com https://sso-ext.tibco.com http://library.tibco.com https://library.tibco.com https://www-dev.tibco.com https://tibco.seismic.com");
  }

}
