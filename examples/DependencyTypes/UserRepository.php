<?php

class UserRepository
{
    private $items = [];

    public function add(UserEntity $user)
    {
        $this->items[$user->name] = $user;
    }

    public function remove($user)
    {
        // instanceof usage should be considered as an architectural violation
        if (!$user instanceof UserEntity) {
            throw new Exception('Expected user entity type');
        }

        unset($this->items[$user->name]);
    }

    public function all()
    {
        return $this->items;
    }
}