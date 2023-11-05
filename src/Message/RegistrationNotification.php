<?php

// src/Message/RegistrationNotification.php

namespace App\Message;

class RegistrationNotification
{
    private $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }
}
