<?php

namespace App\Controller;

use App\DTO\ContactFormDTO;
use App\Repository\ContactRequestRepository;
use App\Repository\UserRepository;
use App\Service\ProductService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Psr\Log\LoggerInterface;
use Symfony\Component\RateLimiter\RateLimiterFactory;

final class PageController extends AbstractController
{
    #[Route('/about', name: 'app_about')]
    public function about(): Response
    {
        return $this->render('page/about.html.twig');
    }

    #[Route('/faq', name: 'app_faq')]
    public function faq(): Response
    {
        return $this->render('page/faq.html.twig');
    }

    #[Route('/terms', name: 'app_terms')]
    public function terms(): Response
    {
        return $this->render('page/terms.html.twig');
    }

    #[Route('/privacy', name: 'app_privacy')]
    public function privacy(): Response
    {
        return $this->render('page/privacy.html.twig');
    }

    #[Route('/products', name: 'app_products')]
    public function products(Request $request, ProductService $productService): Response
    {
        $productService->setLocale($request->getLocale());
        $productsByCategory = $productService->getProductsByCategory();
        $categories = $productService->getCategories();

        return $this->render('page/products.html.twig', [
            'productsByCategory' => $productsByCategory,
            'categories' => $categories,
        ]);
    }

    #[Route('/contact', name: 'app_contact', methods: ['GET', 'POST'])]
    public function contact(
        Request $request,
        UserRepository $userRepository,
        ContactRequestRepository $contactRequestRepository,
        ValidatorInterface $validator,
        ProductService $productService,
        HttpClientInterface $httpClient,
        MailerInterface $mailer,
        LoggerInterface $logger,
        RateLimiterFactory $contactFormLimiter,
        #[Autowire('%env(TURNSTILE_SITE_KEY)%')] string $turnstileSiteKey,
        #[Autowire('%env(TURNSTILE_SECRET_KEY)%')] string $turnstileSecretKey,
        #[Autowire('%app.email%')] string $appEmail
    ): Response {
        $errors = [];
        $dto = new ContactFormDTO();

        // Rate limiting check (only on POST)
        $limiter = $contactFormLimiter->create($request->getClientIp());
        if ($request->isMethod('POST') && false === $limiter->consume(1)->isAccepted()) {
            $errors[] = 'Too many requests. Please try again in 15 minutes.';
        }

        // Set locale for products
        $productService->setLocale($request->getLocale());
        $products = $productService->getProductsForDropdown();

        // Get preselected product from query parameter
        $selectedProduct = $request->query->get('product');

        if ($request->isMethod('POST')) {
            // Verify Turnstile token
            $turnstileToken = $request->request->get('cf-turnstile-response');
            $turnstileValid = false;

            if ($turnstileToken) {
                $response = $httpClient->request('POST', 'https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                    'body' => [
                        'secret' => $turnstileSecretKey,
                        'response' => $turnstileToken,
                        'remoteip' => $request->getClientIp(),
                    ],
                ]);

                $result = $response->toArray();
                $turnstileValid = $result['success'] ?? false;
            }

            if (!$turnstileValid) {
                $errors[] = 'Security verification failed. Please try again.';
            }

            // Populate DTO
            $dto->name = trim($request->request->get('name'));
            $dto->email = trim($request->request->get('email'));
            $dto->phone = trim($request->request->get('phone'));
            $dto->message = trim($request->request->get('message'));
            $dto->product_id = $request->request->get('product_id') ?: null;

            // Validate
            $violations = $validator->validate($dto);

            if (count($violations) > 0) {
                foreach ($violations as $violation) {
                    $errors[] = $violation->getMessage();
                }
            }

            if (empty($errors)) {
                // Find or create User
                $user = $userRepository->findOrCreateClient($dto->email, $dto->name, $dto->phone);

                // Create ContactRequest
                $contactRequestRepository->createContactRequest($user, $dto->message, $dto->product_id);
                $contactRequestRepository->save();

                // Send email notification
                $productName = $dto->product_id ? ($products[$dto->product_id] ?? 'N/A') : 'N/A';
                $email = (new Email())
                    ->from($appEmail)
                    ->to($appEmail)
                    ->subject('New Contact Request from ' . $dto->name)
                    ->html(sprintf(
                        '<h2>New Contact Request</h2>
                        <p><strong>Name:</strong> %s</p>
                        <p><strong>Email:</strong> %s</p>
                        <p><strong>Phone:</strong> %s</p>
                        <p><strong>Product:</strong> %s</p>
                        <p><strong>Message:</strong></p>
                        <p>%s</p>',
                        htmlspecialchars($dto->name),
                        htmlspecialchars($dto->email),
                        htmlspecialchars($dto->phone ?? 'N/A'),
                        htmlspecialchars($productName),
                        nl2br(htmlspecialchars($dto->message))
                    ));

                try {
                    $mailer->send($email);

                    // Send confirmation email to customer
                    $customerEmail = (new Email())
                        ->from($appEmail)
                        ->to($dto->email)
                        ->subject('Получихме Вашето запитване / We received your inquiry')
                        ->html(sprintf(
                            '<h2>Благодарим Ви за запитването!</h2>
                            <p>Здравейте %s,</p>
                            <p>Получихме Вашето запитване и ще се свържем с Вас възможно най-скоро.</p>
                            <hr>
                            <h2>Thank you for your inquiry!</h2>
                            <p>Hello %s,</p>
                            <p>We have received your inquiry and will contact you as soon as possible.</p>
                            <hr>
                            <p><em>stoma.bg</em></p>',
                            htmlspecialchars($dto->name),
                            htmlspecialchars($dto->name)
                        ));
                    $mailer->send($customerEmail);
                } catch (\Exception $e) {
                    $logger->error('Failed to send email', [
                        'error' => $e->getMessage(),
                        'customer_email' => $dto->email,
                        'customer_name' => $dto->name,
                    ]);
                }

                $this->addFlash('success', 'Your message has been sent successfully!');

                return $this->redirectToRoute('app_contact');
            }
        }

        return $this->render('page/contact.html.twig', [
            'errors' => $errors,
            'form' => $dto,
            'products' => $products,
            'selectedProduct' => $selectedProduct,
            'turnstile_site_key' => $turnstileSiteKey,
        ]);
    }
}
