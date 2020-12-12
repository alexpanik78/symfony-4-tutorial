<?php


namespace App\Event;

use App\Entity\UserPreferences;
use App\Mailer\Mailer;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserSubscriber implements EventSubscriberInterface
{
    /**
     * @var Mailer
     */
    private $mailer;
    /**
     * @var EntityManager
     */
    private $entityManager;
    /**
     * @var string
     */
    private $defaultLocale;

    /**
     * UserSubscriber constructor.
     * @param Mailer $mailer
     * @param EntityManagerInterface $entityManager
     * @param string $defaultLocale
     */
    public function __construct(Mailer $mailer, EntityManagerInterface $entityManager, string $defaultLocale)
    {

        $this->mailer = $mailer;
        $this->entityManager = $entityManager;
        $this->defaultLocale = $defaultLocale;
    }

    public static function getSubscribedEvents()
    {
        return [
            UserRegisterEvent::NAME => 'onUserRegister'
        ];
    }

    public function onUserRegister(UserRegisterEvent $event)
    {
        $preferences = new UserPreferences();
        $preferences->setLocale($this->defaultLocale);

        $user = $event->getRegisteredUser();
        $user->setPreferences($preferences);

        $this->entityManager->flush();

        $this->mailer->sendConfirmationEmail($event->getRegisteredUser());
        /*$body = $this->twig->render(
            'email/registration.html.twig',
            [
                'user' => $event->getRegisteredUser()
            ]
        );
        $message = (new \Swift_Message())
            ->setSubject('Welcome to the micro-post app!')
            ->setFrom('micropost@micropost.com')
            ->setTo($event->getRegisteredUser()->getEmail())
            ->setBody($body, 'text/html');

        $this->mailer->send($message);*/
    }
}
