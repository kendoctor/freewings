<?php
/**
 * Created by PhpStorm.
 * User: kendoctor
 * Date: 4/30/18
 * Time: 1:42 AM
 */

namespace App\EventListener;

use Gedmo\Blameable\BlameableListener;
use Gedmo\Loggable\LoggableListener;
use Gedmo\Translatable\TranslatableListener;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Translation\DataCollectorTranslator;

class DoctrineExtensionListener
{
    private $tokenStorage;
    private $authorizationChecker;
    private $blameableListener;
    private $translator;


    public function __construct(BlameableListener $blameableListener, TranslatableListener $translatableListener, LoggableListener $loggableListener, DataCollectorTranslator $translator, TokenStorageInterface $tokenStorage = null, AuthorizationChecker $authorizationChecker = null)
    {
        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
        $this->blameableListener = $blameableListener;
        $this->translatableListener = $translatableListener;
        $this->loggableListener = $loggableListener;
        $this->translator = $translator;
    }


    public function onLateKernelRequest(GetResponseEvent $event)
    {
        $this->translatableListener->setTranslatableLocale($event->getRequest()->getLocale());
    }

    public function onConsoleCommand()
    {
        $this->translatableListener->setTranslatableLocale($this->translator->getLocale());

    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }
        if (null === $this->tokenStorage || null === $this->authorizationChecker) {
            return;
        }
        $token = $this->tokenStorage->getToken();
        if (null !== $token && $this->authorizationChecker->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $this->blameableListener->setUserValue($token->getUser());
            $this->loggableListener->setUsername($token);
        }
    }
}