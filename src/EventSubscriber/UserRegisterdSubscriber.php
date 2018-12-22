<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 12/7/18
 * Time: 4:32 PM
 */

namespace App\EventSubscriber;


use ApiPlatform\Core\EventListener\EventPriorities;
use App\Email\Mailer;
use App\Entity\User;
use App\Security\TokenGenerator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserRegisterdSubscriber implements EventSubscriberInterface
{

	/**
	 * @var UserPasswordEncoderInterface
	 */
	private $passwordEncoder;
	/**
	 * @var TokenGenerator
	 */
	private $tokenGenerator;
	/**
	 * @var Mailer
	 */
	private $mailer;

	public function __construct(UserPasswordEncoderInterface $passwordEncoder,
															TokenGenerator $tokenGenerator, Mailer $mailer)
	{
		$this->passwordEncoder = $passwordEncoder;
		$this->tokenGenerator = $tokenGenerator;
		$this->mailer = $mailer;
	}

	public static function getSubscribedEvents()
	{
		return [
			KernelEvents::VIEW => ['userRegistered', EventPriorities::PRE_WRITE]
		];
	}

	public function userRegistered(GetResponseForControllerResultEvent $event)
	{
		/** @var User $user */
		$user = $event->getControllerResult();
		$method = $event->getRequest()->getMethod();

//		if (!$user instanceof User || in_array($method, [Request::METHOD_POST])) {
		if (!$user instanceof User || Request::METHOD_POST === $method) {
			return;
		}


		$user->setPassword(
			$this->passwordEncoder->encodePassword($user, $user->getPassword())
		);

		$user->setConfirmationToken($this->tokenGenerator->getRandomSecureToken(40));

		//Send Email

		$this->mailer->sendConfirmationEmail($user);
	}

}