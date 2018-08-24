<?php

namespace AppBundle\Service;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class RandomUserSelectorService extends Controller
{
    public function selectARandomUser(array $usersInTeaRound)
    {
       $selectedUser = array_rand($usersInTeaRound, 1);

       return $selectedUser;
    }
}