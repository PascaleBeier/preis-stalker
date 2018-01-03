<?php

namespace App\Service\Email;

use App\Entity\Notification;
use Symfony\Bridge\Doctrine\RegistryInterface;

class NotifcationService
{
    /** @var \Swift_Mailer */
    private $mailer;
    /** @var \Twig_Environment */
    private $templating;
    /** @var Notification */
    private $notification;
    /** @var RegistryInterface */
    private $registry;

    /**
     * NotifcationService constructor.
     * @param \Swift_Mailer $mailer
     * @param \Twig_Environment $templating
     * @param RegistryInterface $registry
     */
    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $templating, RegistryInterface $registry)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
        $this->registry = $registry;
    }

    /**
     * @param Notification $notification
     */
    public function setNotification(Notification $notification): void
    {
        $this->notification = $notification;
    }

    /**
     * @param array $context
     *
     * @return int
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function run(array $context = [])
    {
        $success = $this
            ->mailer
            ->send((new \Swift_Message('PreisStalker Benachrichtigung'))
                ->setFrom('mail@pascalebeier.de')
                ->setTo($this->notification->getEmail())
                ->setBody(
                    $this->templating->render("emails/notification.html.twig", array_merge($context, ['notification' => $this->notification])),
                    'text/html'
                ));


        if ($success > 0) {
            $em = $this->registry->getManager();
            $em->remove($this->notification);
            $em->flush();
        }

        return $success;
    }

}