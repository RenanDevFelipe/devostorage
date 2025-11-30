<?php namespace App\Services;

class AuthUser
{
    private $user;

    public function setUser($data)
    {
        $this->user = (array) $data;
    }

    public function user()
    {
        return $this->user;
    }

    public function id()
    {
        return $this->user['id'] ?? null;
    }

    public function tipo()
    {
        return $this->user['tipo'] ?? null;
    }
}
