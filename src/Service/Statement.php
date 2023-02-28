<?php

namespace App\Service;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Product;


class Statement
{
	protected ManagerRegistry $doctrine;
	protected ObjectManager $entityManager;

	public function __constructor(ManagerRegistry $doctrine){
		$this->doctrine = $doctrine;
		$this->entityManager = $this->doctrine->getManager();
	}
	
	public function getAllProducts()
	{
		return $this->entityManager->getRepository(Product::class)->findAll();
	}
	
}