<?php

namespace App\Service;

use App\Entity\Product;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Serializer;

/**
 * Нормализатор для вывода сущностей в виде массивов
 */
class Normalizer
{
	protected $serializer;
	
	public function __construct()
	{
		$dateNormalizeCallback = function ($innerObject)
		{
			return $innerObject instanceof \DateTimeInterface
				? $innerObject->format('Y-m-d')
				: '';
		};
		$productNormalizeCallback = function ($innerObject)
		{
			return $innerObject instanceof Product ? $innerObject->getName() : null;
		};
		$defaultContext = [
			AbstractNormalizer::CALLBACKS => [
				'balance_at'       => $dateNormalizeCallback,
				'balanceAt'       => $dateNormalizeCallback,
				'posted_at'       => $dateNormalizeCallback,
				'postedAt'       => $dateNormalizeCallback,
				'product'       => $productNormalizeCallback
			],
		];
		$normalizers = [
			new GetSetMethodNormalizer(null, new CamelCaseToSnakeCaseNameConverter(), null, null, null, $defaultContext),
		];
		
		$this->serializer = new Serializer($normalizers, []);
		
	}
	
	public function normalize($entity): array
	{
		return $this->serializer->normalize($entity,'array');
	}
	
	public function normalizeArray(array $entityArray): array
	{
		$result = [];
		foreach ($entityArray as $entity)
		{
			$result[] = $this->normalize($entity);
		}
		return $result;
	}
	
	
}