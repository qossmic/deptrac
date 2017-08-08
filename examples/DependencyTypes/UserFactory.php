<?php

namespace DependencyTypes\Factory;

use DependencyTypes\Entity\UserEntity;

class UserFactory
{
    public function createUser($name, $email) : UserEntity
    {
        $user = new UserEntity();
        $user->name = $name;
        $user->email = $email;
        return $user;
    }
}