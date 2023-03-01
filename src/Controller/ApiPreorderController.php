<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiPreorderController extends AbstractController
{
    #[Route('/api/preorder', name: 'app_api_preorder',  methods: 'GET')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/ApiPreorderController.php',
        ]);
    }
	
	#[Route('/api/preorder', name: 'app_api_preorder_add', methods: 'POST')]
	public function add_preorder(Request $request): JsonResponse
	{
		return $this->json([
			'id' => 152,
		], Response::HTTP_CREATED);
	}
}
