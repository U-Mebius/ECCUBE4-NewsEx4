<?php

namespace Plugin\NewsEx4;

use Eccube\Event\TemplateEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

class Event implements EventSubscriberInterface
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var Environment
     */
    protected $twig;

    public function __construct(
        RouterInterface $router,
        Environment $twig
    ) {
        $this->router = $router;
        $this->twig = $twig;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            '@admin/Content/news_edit.twig' => 'onAdminContentNewsEditTwig',
            KernelEvents::REQUEST => ['onKernelRequest', 6],
        ];
    }

    public function onAdminContentNewsEditTwig(TemplateEvent $event)
    {
        $event->addSnippet('@NewsEx4/admin/edit_js.twig');
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $this->twig->addGlobal('plg_news_url', $this->router->generate('plg_news'));
    }
}
