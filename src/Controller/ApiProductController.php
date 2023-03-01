<?php

namespace App\Controller;

use App\Service\Normalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Statement;

/**
 * Контроллер, отвечающий за создание и вывод продуктов
 */
class ApiProductController extends AbstractController
{
    #[Route('/api/product', name: 'app_api_product', methods: ['GET'])]
    public function index(Statement $statementService, Normalizer $normalizer): JsonResponse
    {
        $result = $statementService->getAllProducts();
	
    	return $this->json(
		    $normalizer->normalizeArray($result)
        );
    }
	
	#[Route('/api/product', name: 'app_api_product_add', methods: ['POST'])]
	public function add_product(Request $request, Statement $statementService): JsonResponse
	{
		$data = $request->toArray();
		$id = $statementService->createProduct($data['name']);
		return $this->json(['id' => $id], Response::HTTP_CREATED);
	}
}
