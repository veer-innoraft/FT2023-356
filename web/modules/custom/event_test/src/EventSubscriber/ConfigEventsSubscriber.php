<?php

namespace Drupal\event_test\EventSubscriber;

// Use Drupal\devel\Plugin\Devel\Dumper\Kint;.
use Drupal\Core\Config\ConfigFactory;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class to subscrive VIEW event and invoce custom onMyCustomEvent method.
 */
class ConfigEventsSubscriber implements EventSubscriberInterface {

  /**
   * Stores the ConfigFactory Object.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $config;

  /**
   * Calling constructor to initialize required services.
   */
  public function __construct(ConfigFactory $config) {
    $this->config = $config;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      KernelEvents::VIEW => ['onMyCustomEvent', 1],
    ];
  }

  /**
   * Add custom message text to movie nodes based on there price comparison.
   *
   * @param \Symfony\Component\HttpKernel\Event\ViewEvent $event
   *   Contains the object to retrive and set controllerResult and node details.
   */
  public function onMyCustomEvent(ViewEvent $event) {

    if ($event->getRequest()->attributes->get('node') && $event->getRequest()->attributes->get('node')->getType() == 'movie') {
      $node       = $event->getRequest()->attributes->get('node');
      $moviePrice = $node->field_movie_price->value;
      $budget     = $this->config->get('movie_budget');

      // Setting the message value depending upon
      // the movie budget and actual cost.
      $message = $moviePrice > $budget ? "The movie is over budget." : ($moviePrice < $budget ? "The movie is under budget." : "The movie is within budget.");
      $build = $event->getControllerResult();
      $build['myContent'] = [
        '#type'   => 'markup',
        '#prefix' => '<h1>',
        '#markup' => $message,
        '#suffix' => '</h1>',
      ];
      $event->setControllerResult($build);
    }
  }

}
