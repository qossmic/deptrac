<?php

class UserFactory
{
    public function createUser($name, $email) : UserEntity
    {
        $user = new UserEntity();
        $user->name = $name;
        $user->email = !empty($email) ? $email : UserEntity::EMAIL_MISSING;
        return $user;
    }
}