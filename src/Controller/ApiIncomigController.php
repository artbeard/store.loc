<?php

namespace App\Controller;

use App\StatementTypes;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Statement;

class ApiIncomigController extends AbstractController
{
    #[Route('/api/incomig', name: 'app_api_incomig', methods: ['GET'])]
    public function index(): Response
    {
	    return $this->json([
		    'message' => 'Welcome to your new controller!',
		    'path' => 'src/Controller/ApiPreorderController.php',
	    ]);
    }
    
	#[Route('/api/incomig', name: 'app_api_add_incomig', methods: ['POST'])]
	public function add_incomig(Request $request, Statement $statementService): Response
	{

		$statementService->addPost($request->toArray());
		
		return $this->json($request->toArray());
	}

	#[Route('/api/expense', name: 'app_api_add_expense', methods: ['POST'])]
	public function add_expense(Request $request, Statement $statementService): Response
	{

		$statementService->addPost($request->toArray(), StatementTypes::POST_EX);

		return $this->json($request->toArray());
	}
}
