<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Entity\Product;
use App\Service\RemoteProductService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class NotificationController extends Controller
{
    /** @var RemoteProductService */
    private $remoteProductService;

    /**
     * NotificationController constructor.
     *
     * @param RemoteProductService $remoteProductService
     */
    public function __construct(RemoteProductService $remoteProductService)
    {
        $this->remoteProductService = $remoteProductService;
    }

    /**
     * @Route("/", name="home")
     * @param Request $request
     *
     * @return Response
     */
    public function new(Request $request)
    {
        $notification = new Notification();
        $product = new Product();

        $form = $this->createFormBuilder([$notification->getEmail(), $product->getLink(), $notification->getPrice()])
            ->add('email', EmailType::class, ['label' => 'Deine E-Mail', 'attr' => ['placeholder' => 'Deine E-Mail']])
            ->add('link', UrlType::class, ['label' => 'Amazon-Link', 'attr' => ['placeholder' => 'Amazon-Link']])
            ->add('price', MoneyType::class, ['label' => 'Preis geringer als ', 'attr' => ['placeholder' => 'Nur ab diesem Preis']])
            ->add('save', SubmitType::class, ['label' => 'Benachrichtigt werden!'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $notification->setEmail($form->get('email')->getData());
            $notification->setPrice($form->get('price')->getData());
            $product->setLink(strtok($form->get('link')->getData(), '?'));

            if (!$this->remoteProductService->findOrCreate($notification, $product)) {
                $form->get('link')->addError(new FormError('Das ist kein gültiger Amazon-Produktlink'));
                return $this->render('notification/create.html.twig', ['form' => $form->createView()]);
            };

            return $this->render("notification/success.html.twig", compact('notification'));
        }

        return $this->render('notification/create.html.twig', ['form' => $form->createView()]);
    }
}
