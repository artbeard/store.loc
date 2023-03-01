<?php

namespace App\Service;

use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Serializer;

class Normalizer
{
	protected $serializer;
	
	public function __construct()
	{
		$dateNormalizeCallback = function ($innerObject, $outerObject, string $attributeName, string $format = null, array $context = [])
		{
			return $innerObject instanceof \DateTime
				? $innerObject->format('Y-m-d')
				: '';
		};
		$defaultContext = [
			AbstractNormalizer::CALLBACKS => [
				'balance_at'       => $dateNormalizeCallback,
				'posted_at'       => $dateNormalizeCallback,
			],
		];
		$normalizers = [
			new GetSetMethodNormalizer(null, null, null, null, null, $defaultContext),
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
			$result = $this->normalize($entity);
		}
		return $result;
	}
	
	
}