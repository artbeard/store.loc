<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Statement;

class ApiProductController extends AbstractController
{
    #[Route('/api/product', name: 'app_api_product', methods: ['GET'])]
    public function index(Statement $statementService): JsonResponse
    {
        $result = [];
        foreach ($statementService->getAllProducts() as $prd)
        {
	        $result[] = $prd->getName();
        }
    	return $this->json(
		    $result
        );
    }
	
	#[Route('/api/product', name: 'app_api_product_add', methods: ['POST'])]
	public function add_product(Request $request, Statement $statementService): JsonResponse
	{
		$data = $request->toArray();
		$id = $statementService->createProduct($data['name']);
		return $this->json(['id' => $id]);
	}
}
