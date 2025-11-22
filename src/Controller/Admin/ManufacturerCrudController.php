<?php

namespace App\Controller\Admin;

use App\Entity\Manufacturer;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class ManufacturerCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Manufacturer::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Manufacturer')
            ->setEntityLabelInPlural('Manufacturers')
            ->setDefaultSort(['name' => 'ASC'])
            ->showEntityActionsInlined();
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name', 'Name'),
            TextField::new('slug', 'Slug'),
            UrlField::new('website', 'Website')->hideOnIndex(),
            TextField::new('logo', 'Logo Path')->hideOnIndex(),
            AssociationField::new('products', 'Products')
                ->hideOnForm()
                ->formatValue(function ($value, $entity) {
                    return $entity->getProducts()->count();
                }),
            BooleanField::new('isActive', 'Active'),
            DateTimeField::new('createdAt', 'Created')->hideOnForm(),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_EDIT, Action::INDEX)
            ->add(Crud::PAGE_NEW, Action::INDEX);
    }
}
