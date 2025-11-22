<?php

namespace App\Controller\Admin;

use App\Entity\ContactRequest;
use App\Service\ProductService;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use App\Entity\Sample;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManagerInterface;

class ContactRequestCrudController extends AbstractCrudController
{
    public function __construct(
        private RequestStack $requestStack,
        private EntityManagerInterface $entityManager,
        private ProductService $productService
    ) {}

    public static function getEntityFqcn(): string
    {
        return ContactRequest::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Contact Request')
            ->setEntityLabelInPlural('Contact Requests')
            ->setSearchFields(['user.email', 'user.name', 'user.phone', 'message'])
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->showEntityActionsInlined();
    }

    public function configureActions(Actions $actions): Actions
    {
        $createSample = Action::new('createSample', 'Create Sample', 'fa fa-box')
            ->linkToUrl(function (ContactRequest $contactRequest) {
                $currentUrl = $this->requestStack->getCurrentRequest()->getUri();

                return $this->container->get(AdminUrlGenerator::class)
                    ->setController(SampleCrudController::class)
                    ->setAction(Action::NEW)
                    ->set('contactRequestId', $contactRequest->getId())
                    ->set('referrer', $currentUrl)
                    ->generateUrl();
            })
            ->displayIf(fn (ContactRequest $contactRequest) => $contactRequest->getSample() === null);

        $editSample = Action::new('editSample', 'Edit Sample', 'fa fa-box')
            ->linkToUrl(function (ContactRequest $contactRequest) {
                return $this->container->get(AdminUrlGenerator::class)
                    ->setController(SampleCrudController::class)
                    ->setAction(Action::EDIT)
                    ->setEntityId($contactRequest->getSample()->getId())
                    ->generateUrl();
            })
            ->displayIf(fn (ContactRequest $contactRequest) => $contactRequest->getSample() !== null);

        $createSampleDetail = Action::new('createSampleDetail', 'Create Sample', 'fa fa-box')
            ->linkToUrl(function (ContactRequest $contactRequest) {
                $currentUrl = $this->requestStack->getCurrentRequest()->getUri();

                return $this->container->get(AdminUrlGenerator::class)
                    ->unsetAll()
                    ->setController(SampleCrudController::class)
                    ->setAction(Action::NEW)
                    ->set('contactRequestId', $contactRequest->getId())
                    ->set('referrer', $currentUrl)
                    ->generateUrl();
            })
            ->displayIf(fn (ContactRequest $contactRequest) => $contactRequest->getSample() === null);

        $editSampleDetail = Action::new('editSampleDetail', 'Edit Sample', 'fa fa-box')
            ->linkToUrl(function (ContactRequest $contactRequest) {
                return $this->container->get(AdminUrlGenerator::class)
                    ->setController(SampleCrudController::class)
                    ->setAction(Action::EDIT)
                    ->setEntityId($contactRequest->getSample()->getId())
                    ->generateUrl();
            })
            ->displayIf(fn (ContactRequest $contactRequest) => $contactRequest->getSample() !== null);

        return $actions
            ->disable(Action::NEW)
            ->disable(Action::EDIT)
            ->add(Crud::PAGE_INDEX, $createSample)
            ->add(Crud::PAGE_INDEX, $editSample)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
//            ->add(Crud::PAGE_DETAIL, $createSampleDetail)
//            ->add(Crud::PAGE_DETAIL, $editSampleDetail)
            ->add(Crud::PAGE_EDIT, Action::INDEX)
            ->update(Crud::PAGE_INDEX, Action::DELETE, function (Action $action) {
                return $action->displayIf(fn () => $this->isGranted('ROLE_ADMIN'));
            })
            ->update(Crud::PAGE_DETAIL, Action::DELETE, function (Action $action) {
                return $action->displayIf(fn () => $this->isGranted('ROLE_ADMIN'));
            });
    }

    public function detail(AdminContext $context)
    {
        $contactRequest = $context->getEntity()->getInstance();

        if ($contactRequest->getStatus() === 'new') {
            $contactRequest->setStatus('read');
            $this->entityManager->flush();
        }

        return parent::detail($context);
    }

    public function configureFields(string $pageName): iterable
    {
        // Get locale for product name display
        $locale = $this->requestStack->getCurrentRequest()?->getLocale() ?? 'bg';
        $this->productService->setLocale($locale);

        return [
            IdField::new('id')->hideOnForm(),
            AssociationField::new('user')
                ->setFormTypeOption('disabled', true),
            IntegerField::new('requestCount', 'Requests')
                ->hideOnForm(),
            IntegerField::new('productId', 'Product')
                ->formatValue(function ($value, ContactRequest $entity) {
                    if (!$value) {
                        return '-';
                    }
                    $product = $this->productService->getProduct($value);
                    return $product ? $product['name'] : (string)$value;
                })
                ->setFormTypeOption('disabled', true)
                ->hideOnForm(),
            TextareaField::new('message')
                ->setFormTypeOption('disabled', true)
                ->setMaxLength(50),
            ChoiceField::new('status')
                ->setChoices([
                    'New' => 'new',
                    'Read' => 'read',
                    'Replied' => 'replied',
                    'Cancelled' => 'cancelled',
                ]),
            AssociationField::new('sample'),
            DateTimeField::new('createdAt')
                ->setFormTypeOption('disabled', true),
        ];
    }
}
