<?php

namespace App\Service;

use App\Entity\Notification;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\DBAL\Exception\NotNullConstraintViolationException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Goutte\Client;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Config\Definition\Exception\Exception;

class RemoteProductService
{
	/** @var Client */
	private $client;
	/** @var ProductRepository */
	private $productRepository;
	/** @var ManagerRegistry */
	private $manager;

    /**
     * RemoteProductService constructor.
     *
     * @param Client $client
     * @param ProductRepository $productRepository
     * @param ManagerRegistry $manager
     */
    public function __construct(Client $client, ProductRepository $productRepository, ManagerRegistry $manager)
    {
        $this->client = $client;
        $this->productRepository = $productRepository;
        $this->manager = $manager->getManager();
    }

    /**
     * @param string $link
     *
     * @return array
     */
    public function getData(string $link)
    {
        $title = null;
        $price = null;

        try {
            $source = $this->client->request('GET', $link);

            // Fill Title
            $title = trim($source->filter('#productTitle')->text());

            // Fill Current Price
            if (count($source->filter('#priceblock_ourprice')->getIterator()) > 0) {
                $price = $source->filter('#priceblock_ourprice')->text();
            } elseif (count($source->filter('#priceblock_dealprice')->getIterator()) > 0) {
                $price = $source->filter('#priceblock_dealprice')->text();
            }
            $price = str_replace(',', '.', str_replace('.', '', substr(trim($price), 4)));
        } catch (\InvalidArgumentException $e) {
            $title = null;
            $price = null;
        }

        return compact('title', 'price');
    }

    /**
     * @param Notification $notification
     * @param Product $product
     *
     * @return Notification|bool
     */
    public function findOrCreate(Notification $notification, Product $product)
    {
        try {
            $product = $this->productRepository->findByLink($product->getLink());
        } catch (NoResultException|NonUniqueResultException $e) {
            $data = $this->getData($product->getLink());
            if ($data['title'] === null || $data['price'] === null) {
                return false;
            }
            $product->setPrice($data['price']);
            $product->setTitle($data['title']);
            $this->manager->persist($product);
        }

        $notification->setProduct($product);
        $this->manager->persist($notification);
        $this->manager->flush();

        return $notification;
    }

    /*
     * @param Product $product
     *
     * @return void
     */
    public function update(Product $product)
    {
        $data = $this->getData($product->getLink());

        $this->manager->getRepository(Product::class)->updatePrice($product->getId(), $data['price']);
    }

}