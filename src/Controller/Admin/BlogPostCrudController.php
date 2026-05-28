<?php

namespace App\Controller\Admin;

use App\Entity\BlogPost;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class BlogPostCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return BlogPost::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Blog Post')
            ->setEntityLabelInPlural('Blog Posts')
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setSearchFields(['slug'])
            ->showEntityActionsInlined();
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('category'))
            ->add(BooleanFilter::new('isActive'));
    }

    public function configureFields(string $pageName): iterable
    {
        $fields = [
            IdField::new('id')->hideOnForm(),
            AssociationField::new('category', 'Category'),
            TextField::new('slug')->hideOnIndex(),
            TextField::new('titleBg', 'Title (BG)'),
            TextField::new('titleEn', 'Title (EN)')->hideOnIndex(),
            BooleanField::new('isActive', 'Active'),
            DateTimeField::new('createdAt', 'Created')->hideOnForm(),
        ];

        if (in_array($pageName, [Crud::PAGE_DETAIL, Crud::PAGE_EDIT, Crud::PAGE_NEW])) {
            $fields = [
                IdField::new('id')->hideOnForm(),
                AssociationField::new('category', 'Category'),
                TextField::new('slug'),
                TextField::new('titleBg', 'Title (BG)'),
                TextField::new('titleEn', 'Title (EN)'),
                TextareaField::new('excerptBg', 'Excerpt (BG)'),
                TextareaField::new('excerptEn', 'Excerpt (EN)'),
                TextareaField::new('contentBg', 'Content (BG)')->setNumOfRows(15),
                TextareaField::new('contentEn', 'Content (EN)')->setNumOfRows(15),
                TextField::new('image', 'Image Path'),
                BooleanField::new('isActive', 'Active'),
                DateTimeField::new('createdAt', 'Created')->hideOnForm(),
            ];
        }

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
