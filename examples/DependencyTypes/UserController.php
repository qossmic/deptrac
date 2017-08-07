<?php

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
        $user->email = !empty($email) ? $email : UserEntity::EMAIL_MISSING; // static access should be considered as an architectural violation!

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