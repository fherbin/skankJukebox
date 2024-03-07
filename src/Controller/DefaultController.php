<?php
declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\WebLink\Link;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function getRecordingsByRelease(): Response
    {
        return $this->render('base.html.twig');
    }
}