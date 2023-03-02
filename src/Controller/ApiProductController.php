<?php

namespace App\Controller;

use App\Service\Normalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Statement;
use App\Service\Product as ProductService;

/**
 * Контроллер, отвечающий за создание и вывод продуктов
 */
class ApiProductController extends AbstractController
{
	
	/**
	 * Отдаем все карторчки товаров
	 * @param ProductService $productService
	 * @param Normalizer $normalizer
	 * @return JsonResponse
	 */
	#[Route('/api/product', name: 'app_api_product', methods: ['GET'])]
    public function index(ProductService $productService, Normalizer $normalizer): JsonResponse
    {
        $result = $productService->getAllProducts();
    	return $this->json(
		    $normalizer->normalizeArray($result)
        );
    }
	
	/**
	 * Добавляем карточку товара
	 * @param Request $request
	 * @param ProductService $productService
	 * @return JsonResponse
	 */
	#[Route('/api/product', name: 'app_api_product_add', methods: ['POST'])]
	public function add_product(Request $request, ProductService $productService): JsonResponse
	{
		$data = $request->toArray();
		$id = $productService->createProduct($data['name']);
		return $this->json(['id' => $id], Response::HTTP_CREATED);
	}
}
