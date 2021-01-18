<?php

namespace Plugin\NewsEx4;

use Eccube\Event\TemplateEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class Event implements EventSubscriberInterface
{
    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            '@admin/Content/news_edit.twig' => 'onAdminContentNewsEditTwig',
        ];
    }

    public function onAdminContentNewsEditTwig(TemplateEvent $event)
    {
        $event->addSnippet('@NewsEx4/admin/edit_js.twig');
    }
}
