<?php

namespace App\DataHandler;

use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\User;


class UserHandler
{
	private $em;

	public function __construct(ObjectManager $em)
	{
        $this->em = $em;
	}

	public function register(User $user): User
	{
		$this->em->persist($user);
		$this->em->flush();
        return $user;
	}
}