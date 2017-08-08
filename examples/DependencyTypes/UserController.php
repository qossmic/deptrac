<?php

namespace DependencyTypes\Controller;

use DependencyTypes\Entity\UserEntity;
use DependencyTypes\Factory\UserFactory;
use DependencyTypes\Repository\UserRepository;

class UserController
{
    private $factory;
    private $repository;

    public function __construct()
    {
        $this->factory = new UserFactory();
        $this->repository = new UserRepository();
    }

    public function createUserAction($name, $email)
    {
        $user = new UserEntity(); // direct instantiation should be considered as an architectural violation!
        $user->name = $name;
        $user->email = $email;

        $this->addToRepository($user);
    }

    public function createUserActionWithFactory($name, $email)
    {
        $user = $this->factory->createUser($name, $email);

        $this->addToRepository($user);
    }

    private function addToRepository(UserEntity $user)
    {
        $this->repository->add($user);
    }
}