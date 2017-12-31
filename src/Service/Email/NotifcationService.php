<?php

namespace App\Service\Email;

use App\Entity\Notification;

class NotifcationService
{
    /** @var \Swift_Mailer */
    private $mailer;
    /** @var \Twig_Environment */
    private $templating;
    /** @var Notification */
    private $notification;

    /**
     * NotifcationService constructor.
     *
     * @param \Swift_Mailer $mailer
     * @param \Twig_Environment $templating
     */
    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $templating)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
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
        return $this
            ->mailer
            ->send((new \Swift_Message('PreisStalker Benachrichtigung'))
                ->setFrom('mail@pascalebeier.de')
                ->setTo($this->notification->getEmail())
                ->setBody(
                     $this->templating->render("emails/notification.html.twig", array_merge($context, ['notification' => $this->notification])),
                     'text/html'
                ));
    }

}