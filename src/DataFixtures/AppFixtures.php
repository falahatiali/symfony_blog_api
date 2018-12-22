<?php

namespace App\DataFixtures;

use App\Entity\BlogPost;
use App\Entity\Comment;
use App\Entity\User;
use App\Security\TokenGenerator;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{

	/**
	 * @var UserPasswordEncoderInterface
	 */
	private $passwordEncoder;

	/**
	 * @var \Faker\Generator
	 */
	private $faker;


	private const USERS = [
		[
			'username' => 'admin',
			'email' => 'admin@admin.ir',
			'name' => 'admin',
			'password' => 'aA123123',
			'roles' => [User::ROLE_SUPERADMIN],
			'enabled' => true ,
		],
		[
			'username' => 'admin1',
			'email' => 'admin1@admin.ir',
			'name' => 'admin1',
			'password' => 'aA123123',
			'roles' => [User::ROLE_ADMIN],
			'enabled' => true ,
		],
		[
			'username' => 'admin2',
			'email' => 'admin2@admin.ir',
			'name' => 'admin2',
			'password' => 'aA123123',
			'roles' => [User::ROLE_WRITER],
			'enabled' => false ,
		],
		[
			'username' => 'admin3',
			'email' => 'admin3@admin.ir',
			'name' => 'admin3',
			'password' => 'aA123123',
			'roles' => [User::ROLE_EDITOR],
			'enabled' => true ,
		],
		[
			'username' => 'admin4',
			'email' => 'admin4@admin.ir',
			'name' => 'admin4',
			'password' => 'aA123123',
			'roles' => [User::ROLE_COMMENTATOR],
			'enabled' => true ,
		],
		[
			'username' => 'admin5',
			'email' => 'admin5@admin.ir',
			'name' => 'admin5',
			'password' => 'aA123123',
			'roles' => [User::ROLE_ADMIN],
			'enabled' => false ,
		],
		[
			'username' => 'admin6',
			'email' => 'admin6@admin.ir',
			'name' => 'admin6',
			'password' => 'aA123123',
			'roles' => [User::ROLE_WRITER],
			'enabled' => false ,
		],
		[
			'username' => 'admin7',
			'email' => 'admin7@admin.ir',
			'name' => 'admin7',
			'password' => 'aA123123',
			'roles' => [User::ROLE_WRITER],
			'enabled' => true ,
		],
		[
			'username' => 'admin8',
			'email' => 'admin8@admin.ir',
			'name' => 'admin8',
			'password' => 'aA123123',
			'roles' => [User::ROLE_EDITOR],
			'enabled' => false ,
		],
		[
			'username' => 'admin9',
			'email' => 'admin9@admin.ir',
			'name' => 'admin9',
			'password' => 'aA123123',
			'roles' => [User::ROLE_COMMENTATOR],
			'enabled' => true ,
		],
		[
			'username' => 'admin10',
			'email' => 'admin10@admin.ir',
			'name' => 'admin10',
			'password' => 'aA123123',
			'roles' => [User::ROLE_SUPERADMIN],
			'enabled' => false ,
		],
	];
	/**
	 * @var TokenGenerator
	 */
	private $tokenGenerator;

	public function __construct(UserPasswordEncoderInterface $passwordEncoder,TokenGenerator $tokenGenerator)
	{
		$this->passwordEncoder = $passwordEncoder;
		$this->faker = \Faker\Factory::create();
		$this->tokenGenerator = $tokenGenerator;
	}

	public function load(ObjectManager $manager)
	{
		$this->loadUsers($manager);
		$this->loadBlogPosts($manager);
		$this->loadComments($manager);
	}

	public function loadBlogPosts(ObjectManager $manager)
	{
		for ($i = 0; $i < 1000; $i++) {
			$blogPost = new BlogPost();
			$blogPost->setTitle($this->faker->realText(30));
			$blogPost->setPublished($this->faker->dateTimeThisYear);
			$blogPost->setContent($this->faker->realText());
			$blogPost->setSlug($this->faker->slug);
			$authorReference = $this->getRandomUserReference($blogPost);

			$blogPost->setAuthor($authorReference);

			$this->setReference("blog_post_$i", $blogPost);
			$manager->persist($blogPost);
		}

		$manager->flush();
	}

	public function loadComments(ObjectManager $manager)
	{
		for ($i = 0; $i < 1000; $i++) {
			for ($j = 0; $j < rand(1, 10); $j++) {
				$comment = new Comment();
				$comment->setContent($this->faker->realText());
				$comment->setPublished($this->faker->dateTimeThisYear);

				$authorReference = $this->getRandomUserReference($comment);
				$comment->setAuthor($authorReference);
				$comment->setBlogPost($this->getReference("blog_post_$i"));

				$manager->persist($comment);
			}
		}

		$manager->flush();
	}

	public function loadUsers(ObjectManager $manager)
	{
		foreach (self::USERS as $users) {
			$user = new User();
			$user->setusername($users['username']);
			$user->setEmail($users['email']);
			$user->setName($users['name']);
			$user->setRoles($users['roles']);

			$user->setPassword($this->passwordEncoder->encodePassword(
				$user,
				$users['password']
			));

			$user->setEnabled($users['enabled']);
			if (!$users['enabled']){
				$user->setConfirmationToken($this->tokenGenerator->getRandomSecureToken(40));
			}
			$this->addReference("user_admin_" . $users['username'], $user);


			$manager->persist($user);
		}

		$manager->flush();
	}

	public function getRandomUserReference($entituy): User
	{
		$randomUser = self::USERS[rand(0, 10)];

		if ($entituy instanceof BlogPost && count(array_intersect(
				$randomUser['roles'], [
				User::ROLE_SUPERADMIN, User::ROLE_ADMIN, User::ROLE_WRITER
			]))) {
			return $this->getRandomUserReference($entituy);
		}


		if ($entituy instanceof Comment && count(array_intersect(
				$randomUser['roles'], [
				User::ROLE_SUPERADMIN, User::ROLE_ADMIN, User::ROLE_WRITER,User::ROLE_COMMENTATOR
			]))) {
			return $this->getRandomUserReference($entituy);
		}

		return $this->getReference('user_admin_' . $randomUser ['username']);
	}
}
