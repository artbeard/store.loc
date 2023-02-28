<?php

namespace App\Service;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Product;


class Statement
{
	protected ManagerRegistry $doctrine;
	protected ObjectManager $entityManager;

	public function __construct(ManagerRegistry $doctrine){
		$this->doctrine = $doctrine;
		$this->entityManager = $this->doctrine->getManager();
	}
	
	public function getAllProducts()
	{
		return $this->entityManager->getRepository(Product::class)->findAll();
	}
	
	public function createProduct($name)
	{
		$product = new Product();
		$product->setName($name);
		//todo добавить валидатор
		//if ($this->validate($product))
		//{
			$this->entityManager->persist($product);
			$this->entityManager->flush();
			return $product->getId();
		//}
		//throw new \Exception('Не удвлось создать Товар');
	}
	
}