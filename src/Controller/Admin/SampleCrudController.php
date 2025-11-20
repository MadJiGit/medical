<?php

namespace App\Controller\Admin;

use App\Entity\Sample;
use App\Repository\ContactRequestRepository;
use App\Service\ProductService;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\RequestStack;

class SampleCrudController extends AbstractCrudController
{
    public function __construct(
        private RequestStack             $requestStack,
        private ContactRequestRepository $contactRequestRepository,
        private ProductService           $productService
    )
    {
    }

    public static function getEntityFqcn(): string
    {
        return Sample::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined();
    }

    public function createEntity(string $entityFqcn)
    {
        $sample = new Sample();
        $sample->setSentDate(new \DateTime());

        $contactRequestId = $this->requestStack->getCurrentRequest()->query->get('contactRequestId');

        if ($contactRequestId) {
            $contactRequest = $this->contactRequestRepository->find($contactRequestId);

            if ($contactRequest) {
                $user = $contactRequest->getUser();
                $sample->setUser($user);
                $sample->setContactRequest($contactRequest);
                $sample->setRecipientName($user->getName());
                $sample->setRecipientPhone($user->getPhone());

                // Set product and manufacturer from contact request
                if ($contactRequest->getProductId()) {
                    $sample->setProductId($contactRequest->getProductId());

                    // Auto-populate manufacturer from product
                    $product = $this->productService->getProduct($contactRequest->getProductId());
                    if ($product && !empty($product['manufacturer'])) {
                        $sample->setSampleManufacture($product['manufacturer']);
                    }
                }
            }
        }

        return $sample;
    }

    public function configureActions(Actions $actions): Actions
    {
        $backToContactRequests = Action::new('backToContactRequests', 'Back to Contact Requests', 'fa fa-arrow-left')
            ->linkToUrl(function () {
                $referrer = $this->requestStack->getCurrentRequest()->query->get('referrer');
                if ($referrer) {
                    return $referrer;
                }
                return $this->container->get(AdminUrlGenerator::class)
                    ->setController(ContactRequestCrudController::class)
                    ->generateUrl();
            })
            ->setCssClass('btn btn-secondary');

        $deleteAction = Action::new(Action::DELETE, 'Delete', 'fa fa-trash')
            ->linkToCrudAction(Action::DELETE)
            ->setCssClass('btn btn-danger')
            ->displayIf(fn() => $this->isGranted('ROLE_ADMIN'));

        return $actions
            ->add(Crud::PAGE_EDIT, Action::INDEX)
            ->add(Crud::PAGE_NEW, $backToContactRequests)
            ->add(Crud::PAGE_NEW, Action::INDEX)
            ->add(Crud::PAGE_EDIT, $deleteAction)
            ->update(Crud::PAGE_INDEX, Action::DELETE, function (Action $action) {
                return $action->displayIf(fn() => false);
            })
            ->update(Crud::PAGE_DETAIL, Action::DELETE, function (Action $action) {
                return $action->displayIf(fn() => false);
            })
            ->reorder(Crud::PAGE_EDIT, [Action::INDEX, 'delete', Action::SAVE_AND_CONTINUE, Action::SAVE_AND_RETURN]);
    }

    public function configureFields(string $pageName): iterable
    {
        // Get locale and products
        $locale = $this->requestStack->getCurrentRequest()?->getLocale() ?? 'bg';
        $this->productService->setLocale($locale);
        $products = $this->productService->getProductsForDropdown();

        return [
            IdField::new('id')->hideOnForm(),
            AssociationField::new('user')
                ->setFormTypeOption('disabled', true),
            AssociationField::new('contactRequest')
                ->setFormTypeOption('disabled', true),
            FormField::addPanel('Sample Info'),
            ChoiceField::new('productId', 'Product')
                ->setChoices(array_flip($products))
                ->setFormTypeOption('placeholder', 'Choose product')
                ->setRequired(true),
            TextField::new('sampleManufacture', 'Manufacturer')
                ->setHelp('Auto-filled from selected product')
                ->setFormTypeOption('attr', ['readonly' => true]),

            FormField::addPanel('Recipient & Shipping'),
            TextField::new('recipientName')->setRequired(true),
            TextField::new('recipientPhone')->setRequired(true),
            TextField::new('shippingAddress')->setRequired(true),
            TextField::new('shippingCity')->setRequired(true),
            TextField::new('shippingPostalCode')->setRequired(true),

            FormField::addPanel('Status'),
            BooleanField::new('sentSample'),
            DateField::new('sentDate'),
            BooleanField::new('confirmReceivedSample'),
            DateField::new('confirmReceivedDate'),

            FormField::addPanel('Return Info'),
            BooleanField::new('returnedSample'),
            DateField::new('returnedDate'),
            TextareaField::new('returnedReason'),

            FormField::addPanel('Feedback'),
            TextareaField::new('userFeedback'),
        ];
    }
}
