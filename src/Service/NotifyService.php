<?php

// todo:
// FormularValidierung komplett
// Design
// Logs aufbessern

namespace App\Service;

use App\Repository\NotificationRepository;
use App\Repository\ProductRepository;
use Psr\Log\LoggerInterface;
use App\Service\Email\NotifcationService;

class NotifyService
{
    /** @var NotificationRepository */
    private $notificationRepository;
    /** @var ProductRepository */
    private $productRepository;
    /** @var RemoteProductService */
    private $remoteProductService;
    /** @var NotifcationService */
    private $notificationService;
    /** @var LoggerInterface */
    private $logger;

    /**
     * NotifyService constructor.
     *
     * @param NotificationRepository $notificationRepository
     * @param ProductRepository $productRepository
     * @param RemoteProductService $remoteProductService
     * @param NotifcationService $notifcationService
     * @param LoggerInterface $logger
     */
    public function __construct(NotificationRepository $notificationRepository, ProductRepository $productRepository,
        RemoteProductService $remoteProductService, NotifcationService $notifcationService, LoggerInterface $logger)
    {
        $this->notificationRepository = $notificationRepository;
        $this->productRepository = $productRepository;
        $this->remoteProductService = $remoteProductService;
        $this->notificationService = $notifcationService;
        $this->logger = $logger;
    }

    public function run()
    {
        $mailFeedback = [];

        foreach ($this->notificationRepository->findAll() as $notification) {
            /** @var $notification \App\Entity\Notification */
            $this->remoteProductService->update($notification->getProduct());

            if ($notification->shouldNotify()) {
                $this->notificationService->setNotification($notification);
                $mailFeedback[] = $this->notificationService->run();
            }
        }

        $mailsSent = 0;
        foreach ($mailFeedback as $item) {
            if ($item > 0) {
                $mailsSent++;
            }
        }
        $this->logger->info('NotifyService hat '. $mailsSent. ' E-Mails versendet.');
    }


}