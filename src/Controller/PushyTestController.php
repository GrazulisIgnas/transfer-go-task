<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/pushy-test', name: 'pushy_test_')]
class PushyTestController extends AbstractController
{
    #[Route('', name: 'page', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('pushy-test.html.twig', [
            'appId' => $_ENV['PUSHY_APP_ID']
        ]);
    }
}
