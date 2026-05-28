<?php

namespace App\Controller;

use App\Repository\BlogPostRepository;
use App\Repository\VideoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(BlogPostRepository $postRepo, VideoRepository $videoRepo): Response
    {
        return $this->render('home/index.html.twig', [
            'latest_posts'  => $postRepo->findLatest(3),
            'latest_videos' => $videoRepo->findLatest(3),
        ]);
    }
}
