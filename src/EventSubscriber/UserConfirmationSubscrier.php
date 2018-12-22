<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\UserConfirmation;
use App\Repository\UserRepository;
use App\Security\UserConfirmationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class UserConfirmationSubscrier implements EventSubscriberInterface
{
	/**
	 * @var UserRepository
	 */
	private $userRepository;
	/**
	 * @var EntityManagerInterface
	 */
	private $entityManager;
	/**
	 * @var UserConfirmationService
	 */
	private $userConfirmationService;

	public function __construct(UserRepository $userRepository,
															EntityManagerInterface $entityManager, UserConfirmationService $userConfirmationService)
	{
		$this->userRepository = $userRepository;
		$this->entityManager = $entityManager;
		$this->userConfirmationService = $userConfirmationService;
	}

	public static function getSubscribedEvents()
	{
		return [
			KernelEvents::VIEW => ['confirmUser', EventPriorities::PRE_VALIDATE]
		];
	}


	public function confirmUser(GetResponseForControllerResultEvent $event)
	{
		$request = $event->getRequest();

		if ('api_user_confirmations_post_collection' !== $request->get('_route')) {
			return;
		}

		/** @var UserConfirmation $confirmationToken */
		$confirmationToken = $event->getControllerResult();
		$this->userConfirmationService->confirmUser($confirmationToken->confirmationToken);


		$event->setResponse(new JsonResponse(null, Response::HTTP_OK));
	}
}