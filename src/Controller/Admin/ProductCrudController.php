<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class ProductCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Product::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Product')
            ->setEntityLabelInPlural('Products')
            ->setDefaultSort(['id' => 'ASC'])
            ->showEntityActionsInlined();
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('category'))
            ->add(EntityFilter::new('manufacturerEntity', 'Manufacturer'))
            ->add(BooleanFilter::new('isActive'));
    }

    public function configureFields(string $pageName): iterable
    {
        $fields = [
            IdField::new('id')->hideOnForm(),
            AssociationField::new('category', 'Category'),
            TextField::new('slug')->hideOnIndex(),
            TextField::new('nameBg', 'Name (BG)'),
            TextField::new('nameEn', 'Name (EN)')->hideOnIndex(),
            AssociationField::new('manufacturerEntity', 'Manufacturer'),
            TextField::new('nhifCode', 'NHIF Code')->hideOnIndex(),
        ];

        if ($pageName === Crud::PAGE_INDEX) {
            $fields[] = IntegerField::new('quantity', 'Qty');
            $fields[] = BooleanField::new('isActive', 'Active');
        }

        if ($pageName === Crud::PAGE_DETAIL || $pageName === Crud::PAGE_EDIT || $pageName === Crud::PAGE_NEW) {
            $fields = array_merge($fields, [
                TextareaField::new('shortDescriptionBg', 'Short Desc (BG)')->hideOnIndex(),
                TextareaField::new('shortDescriptionEn', 'Short Desc (EN)')->hideOnIndex(),
                TextareaField::new('descriptionBg', 'Description (BG)')->hideOnIndex(),
                TextareaField::new('descriptionEn', 'Description (EN)')->hideOnIndex(),
                TextareaField::new('suitableForBg', 'Suitable For (BG)')->hideOnIndex(),
                TextareaField::new('suitableForEn', 'Suitable For (EN)')->hideOnIndex(),
                TextareaField::new('howToUseBg', 'How to Use (BG)')->hideOnIndex(),
                TextareaField::new('howToUseEn', 'How to Use (EN)')->hideOnIndex(),
                TextField::new('image', 'Image Path')->hideOnIndex(),
                IntegerField::new('quantity', 'Quantity'),
                BooleanField::new('isActive', 'Active'),
            ]);
        }

        $fields[] = DateTimeField::new('createdAt', 'Created')->hideOnForm();

        return $fields;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_EDIT, Action::INDEX)
            ->add(Crud::PAGE_NEW, Action::INDEX)
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }
}
