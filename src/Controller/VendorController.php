<?php

namespace App\Controller;

use App\Entity\Vendor;
use App\Repository\VendorRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class VendorController extends Controller
{
    /**
     * @Route("/anbieter", name="vendor")
     */
    public function index()
    {
        $vendors = $this->getDoctrine()
                        ->getRepository(Vendor::class)
                        ->findAll();


        return $this->render(':vendor:index.html.twig', compact('vendors'));
    }
}
