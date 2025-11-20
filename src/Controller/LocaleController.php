<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LocaleController extends AbstractController
{
    #[Route('/locale/{locale}', name: 'app_locale')]
    public function changeLocale(string $locale, Request $request): Response
    {
        $request->getSession()->set('_locale', $locale);

        // Redirect back to previous page or home
        $referer = $request->headers->get('referer');

        return $this->redirect($referer ?: $this->generateUrl('app_home'));
    }
}
