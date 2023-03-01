<?php

namespace App\Service;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Entity\Preorder as PreorderEntity;

class Preorder
{
	protected ManagerRegistry $doctrine;
	protected ObjectManager $entityManager;
	protected ValidatorInterface $validator;
	protected \DateTimeImmutable $currentDate;

	public function __construct(ManagerRegistry $doctrine, RequestStack $requestStack, ValidatorInterface $validator){
		$this->doctrine = $doctrine;
		$this->entityManager = $this->doctrine->getManager();
		$this->validator = $validator;
		$this->currentDate = new \DateTimeImmutable(
			$requestStack->getCurrentRequest()->headers->get('x-current-date') ?? date('Y-m-d')
		);
	}

	public function getPreorders()
	{
		$preorderRepository = $this->entityManager->getRepository(PreorderEntity::class);
		return $preorderRepository->getPreordersByDate($this->currentDate);
	}
}