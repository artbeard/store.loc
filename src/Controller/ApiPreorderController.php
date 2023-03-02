<?php

namespace App\Controller;


use App\Service\Normalizer;
use App\Service\Preorder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiPreorderController extends AbstractController
{
 
	#[Route('/api/preorder', name: 'app_api_preorder',  methods: 'GET')]
    public function index(Preorder $preorderService, Normalizer $normalizer): JsonResponse
    {
        $preorders = $preorderService->getPreorders();
    	return $this->json(
		    $normalizer->normalizeArray($preorders)
	    );
    }
	
	#[Route('/api/preorder', name: 'app_api_preorder_add', methods: 'POST')]
	public function add_preorder(Request $request, Preorder $preorderService): JsonResponse
	{
		$preorderData = $request->toArray();
		$preorder = $preorderService->addPreorder($preorderData);
		return $this->json([
			'id' => $preorder->getId(),
		], Response::HTTP_CREATED);
	}
	
	/**
	 * @throws \App\Exception\ApiException
	 */
	#[Route('/api/preorder/expense', name: 'app_api_preorder_expense', methods: 'POST')]
	public function preorder_expense(Request $request, Preorder $preorderService): JsonResponse
	{
		$preorderParams = $request->toArray();
		$preorder = $preorderService->postPreorder(
			$preorderParams['preorder_id'] ?? 0,
			$preorderParams['price'] ?? 0
		);
		return $this->json([
			$preorder->getId()
		]);
	}
	
	
}
