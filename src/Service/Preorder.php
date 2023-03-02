<?php

namespace App\Service;

use App\Entity\Product;
use App\Exception\ApiException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Entity\Preorder as PreorderEntity;

class Preorder
{
	protected ManagerRegistry $doctrine;
	protected ObjectManager $entityManager;
	protected \DateTimeImmutable $currentDate;
	protected Statement $statementService;
	
	public function __construct(ManagerRegistry $doctrine, RequestStack $requestStack, Statement $statementService){
		$this->doctrine = $doctrine;
		$this->entityManager = $this->doctrine->getManager();
		$this->currentDate = new \DateTimeImmutable(
			$requestStack->getCurrentRequest()->headers->get('x-current-date') ?? date('Y-m-d')
		);
		$this->statementService = $statementService;
	}
	
	/**
	 * Возвращает предзаказы с указанной датой
	 * Возвращаются предзаказы, сделанные предыдущей датой
	 * либо выполненные текущей
	 * @param \DateTimeImmutable|null $currentDate
	 * @return array
	 */
	public function getPreorders(?\DateTimeImmutable $currentDate = null): array
	{
		if (is_null($currentDate))
			$currentDate = $this->currentDate;
		$preorderRepository = $this->entityManager->getRepository(PreorderEntity::class);
		return $preorderRepository->getPreordersByDate($currentDate);
	}
	
	/**
	 * Добавляет предзаказ на текущую дату
	 * @param array $preorderData
	 * @return PreorderEntity
	 */
	public function addPreorder(array $preorderData): PreorderEntity
	{
		$product = $this->entityManager->getRepository(Product::class)
			->find($preorderData['product_id']);
		$preorder = new PreorderEntity();
		$preorder->setProduct($product)
			->setAmount($preorderData['amount'])
			->setOrderedAt($this->currentDate)
			->setDocumentProp('O-'.time());
		$this->entityManager->getRepository(PreorderEntity::class)
			->save($preorder, true);
		return $preorder;
	}
	
	/**
	 * Выполняет проводку предзаказа
	 * @param int $preorder_id
	 * @param int $price
	 * @return PreorderEntity|mixed|object|null
	 * @throws ApiException
	 */
	public function postPreorder(int $preorder_id, float $price): PreorderEntity
	{
		$preorder = $this->entityManager->getRepository(PreorderEntity::class)
			->find($preorder_id);
		
		if ( is_null($preorder) )
		{
			throw new ApiException('Не удалось найти предзаказ с id = '.$preorder_id);
		}
		$preorder
			->setPrice($price)
			->setSentAt($this->currentDate);
		
		$this->statementService->addexpensePost([
			'amount' => $preorder->getAmount(),
			'document_prop' => $preorder->getDocumentProp(),
			'product' => $preorder->getProduct(),
		]);
		
		
		$this->entityManager->getRepository(PreorderEntity::class)
			->save($preorder, true);
		return $preorder;
	}
	
}