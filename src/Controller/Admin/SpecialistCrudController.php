<?php

namespace App\Controller\Admin;

use App\Entity\Specialist;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class SpecialistCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Specialist::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Specialist')
            ->setEntityLabelInPlural('Specialists')
            ->setDefaultSort(['sortOrder' => 'ASC', 'name' => 'ASC'])
            ->setSearchFields(['name', 'email'])
            ->showEntityActionsInlined();
    }

    public function configureFields(string $pageName): iterable
    {
        $fields = [
            IdField::new('id')->hideOnForm(),
            TextField::new('name', 'Name'),
            TextField::new('specialtyBg', 'Specialty (BG)'),
            TextField::new('specialtyEn', 'Specialty (EN)')->hideOnIndex(),
            TextField::new('email', 'Email')->hideOnIndex(),
            BooleanField::new('isActive', 'Active'),
            IntegerField::new('sortOrder', 'Order')->hideOnIndex(),
        ];

        if (in_array($pageName, [Crud::PAGE_DETAIL, Crud::PAGE_EDIT, Crud::PAGE_NEW])) {
            $fields = [
                IdField::new('id')->hideOnForm(),
                TextField::new('slug'),
                TextField::new('name', 'Name'),
                TextField::new('specialtyBg', 'Specialty (BG)'),
                TextField::new('specialtyEn', 'Specialty (EN)'),
                TextareaField::new('bioBg', 'Bio (BG)')->setNumOfRows(5),
                TextareaField::new('bioEn', 'Bio (EN)')->setNumOfRows(5),
                TextField::new('photo', 'Photo Path'),
                TextField::new('email', 'Email'),
                BooleanField::new('isActive', 'Active'),
                IntegerField::new('sortOrder', 'Sort Order'),
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
