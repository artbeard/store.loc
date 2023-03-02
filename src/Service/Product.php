<?php

namespace App\Service;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\RequestStack;
use App\Entity\Product as ProductEntity;

class Product
{
	protected ObjectManager $entityManager;
	protected \DateTimeImmutable $currentDate;
	
	public function __construct(ManagerRegistry $doctrine, RequestStack $requestStack){
		$this->entityManager = $doctrine->getManager();
		$this->currentDate = new \DateTimeImmutable(
			$requestStack->getCurrentRequest()->headers->get('x-current-date') ?? date('Y-m-d')
		);
	}
	
	/**
	 * @return array
	 */
	public function getAllProducts(): array
	{
		return $this->entityManager->getRepository(Product::class)->findAll();
	}
	
	/**
	 * Согдаем карточку продукта
	 * @param $name
	 * @return int|null
	 */
	public function createProduct($name): ?int
	{
		$product = new ProductEntity();
		$product->setName($name);
		$this->entityManager->persist($product);
		$this->entityManager->flush();
		return $product->getId();
	}
}