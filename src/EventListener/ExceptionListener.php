<?php
namespace App\EventListener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use App\Exception\ApiException;


class ExceptionListener
{

	public function onKernelException(ExceptionEvent $event)
	{
		$exception = $event->getThrowable();
		
		if (is_a($exception, ApiException::class))
		{
			$response = new Response();
			$response->setContent(
				json_encode([
					'reason' => $exception->getMessage()
				])
			);
			$response->headers->add(['Content-Type' => 'application/json']);
			$response->setStatusCode(400);
			
			$event->setResponse($response);
		}
	}
}
