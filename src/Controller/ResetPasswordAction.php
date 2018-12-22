<?php

namespace App\Controller;


use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ResetPasswordAction
{
	/**
	 * @var ValidatorInterface
	 */
	private $validator;
	/**
	 * @var UserPasswordEncoderInterface
	 */
	private $passwordEncoder;
	/**
	 * @var EntityManagerInterface
	 */
	private $entityManager;
	/**
	 * @var JWTTokenManagerInterface
	 */
	private $JWTTokenManager;

	public function __construct(ValidatorInterface $validator,
															UserPasswordEncoderInterface $passwordEncoder,
															EntityManagerInterface $entityManager,
															JWTTokenManagerInterface $JWTTokenManager)
	{
		$this->validator = $validator;
		$this->passwordEncoder = $passwordEncoder;
		$this->entityManager = $entityManager;
		$this->JWTTokenManager = $JWTTokenManager;
	}

	/**
	 * @param User $data
	 * @return User
	 */
	public function __invoke(User $data)
	{
		//$reset = new ResetPasswordAction();
		// $reset()

//		var_dump($data->getOldPassword());exit;
//
//			$data->getNewPassword(),
//			$data->getNewRetypedPassword(),
//			$data->getRetypedPassword()
//		);
//		die;


		$this->validator->validate($data);

		$data->setPassword(
			$this->passwordEncoder->encodePassword($data, $data->getNewPassword())
		);

		$data->setPasswordChangeDate(time());

		$this->entityManager->flush();
		$token = $this->JWTTokenManager->create($data);

		return new JsonResponse(['token' => $token]);
	}
}