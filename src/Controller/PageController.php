<?php
declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PageController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function home(): Response
    {
        return $this->render('pages/home.html.twig');
    }

    #[Route('/cd-loader', name: 'cd-loader')]
    public function cdLoader(): Response
    {
        return $this->render('pages/cd-loader.html.twig');
    }

    #[Route('/remote', name: 'remote')]
    public function remote(): Response
    {
        return $this->render('pages/remote.html.twig');
    }
}