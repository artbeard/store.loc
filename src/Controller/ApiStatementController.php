<?php

namespace App\Controller;

use App\StatementTypes;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Statement;
use App\Service\Normalizer;

class ApiStatementController extends AbstractController
{
    #[Route('/api/statement', name: 'app_api_statement', methods: ['GET'])]
    public function index(Statement $statementService, Normalizer $normalizer): JsonResponse
    {
	    $postList = $statementService->getAllPosts();
	    return $this->json(
	    	$normalizer->normalizeArray($postList)
	    );
    }
	
	/**
	 * Добавляет входящие проводки
	 * @throws \App\Exception\ApiException
	 */
	#[Route('/api/statement/income', name: 'app_api_statement_add_income', methods: ['POST'])]
	public function add_income(Request $request, Statement $statementService): Response
	{
		$data = $request->toArray();
		$statement = $statementService->addIncomePost($data);
		return $this->json([
			'id' => $statement->getId()
		], Response::HTTP_CREATED);
	}
	
	
	
	/**
	 * Отображает баланс на текущую дату
	 * @param Statement $statementService
	 * @param Normalizer $normalizer
	 * @return JsonResponse
	 */
	#[Route('/api/statement/balance', name: 'app_api_statement_balance', methods: ['GET'])]
	public function get_balance(Statement $statementService, Normalizer $normalizer): JsonResponse
	{
		$balance = $statementService->getBalance();
		return $this->json(
			$normalizer->normalizeArray($balance)
		);
	}
}
