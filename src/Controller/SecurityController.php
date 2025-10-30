<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    #[Route('/api/login_check', name: 'api_login_check', methods: ['POST'])]
    public function login(): JsonResponse
    {
        // This controller will never be executed because the json_login
        // authenticator intercepts the request before it reaches here
        return new JsonResponse(['message' => 'Login endpoint']);
    }
}
