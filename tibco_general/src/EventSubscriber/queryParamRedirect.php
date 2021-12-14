<?php

namespace Drupal\tibco_general\EventSubscriber;

use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class queryParamRedirect implements EventSubscriberInterface
{
  /**
   * @param GetResponseEvent $event
   *
   * For the certifications View at route view.certifications.tcp, we want to prevent PII from ever
   * being included in the URL query string. The View itself has been configured to use AJAX for its
   * exposed filters so that it doesn't add PII to the query string. However, we also need to prevent
   * a user from manually issuing a request that contains PII, so the purpose of this event subscriber
   * is to remove any PII-related query parameters (while retaining any others) when a request is issued
   * for this route.
   */
  public function redirectCerts(GetResponseEvent $event)
  {
    $request = $event->getRequest();
    $attributes = $request->attributes;

    if (empty($attributes))
      return;

    if ((!empty($attributes->get('_route')))
      && ($attributes->get('_route') == 'view.certifications.tcp')
      && (is_array($request->query->all()))
      && (count($request->query->all()) > 0)) {

      // Establish array of query parameters to disallow because they correspond to actual exposed filters
      $disallowed_query_params = [
        'field_cert_first_name_value',
        'field_cert_last_name_value',
        'field_cert_email_address_value',
      ];

      $query_params = [];
      $query_string = '';
      // Filter out disallowed query parameters, and build an array of any that remain
      foreach ($request->query->all() as $key => $val) {
        if (!in_array($key, $disallowed_query_params)) {
          $query_params[$key] = $val;
        }
      }
      if (count($query_params) > 0) {
        if ((count($query_params)) == (count($request->query->all()))) {
          // None of the requested query parameters are disallowed, so do not redirect
          return;
        }
        // Build the final, encoded query string from the filtered array
        $query_string = http_build_query($query_params);
      }

      $redirect_path = Url::fromRoute('<current>')->toString();
      if (!empty($query_string)) {
        $redirect_path = $redirect_path . '?' . $query_string;
      }

      $response = new RedirectResponse($redirect_path, 301);
      $event->setResponse($response);
    }
  }


  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents()
  {
    $events[KernelEvents::REQUEST][] = array('redirectCerts');
    return $events;
  }
}
