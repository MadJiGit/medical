<?php

namespace App\Controller\Admin;

use App\Entity\SpecialistQuestion;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class SpecialistQuestionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SpecialistQuestion::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Question')
            ->setEntityLabelInPlural('Specialist Questions')
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->setSearchFields(['name', 'email', 'question'])
            ->showEntityActionsInlined();
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(ChoiceFilter::new('status')->setChoices([
                'New' => 'new',
                'Answered' => 'answered',
                'Published' => 'published',
            ]))
            ->add(BooleanFilter::new('isPublic'))
            ->add(BooleanFilter::new('isAnonymous'));
    }

    public function configureFields(string $pageName): iterable
    {
        $statusChoices = [
            'New' => SpecialistQuestion::STATUS_NEW,
            'Answered' => SpecialistQuestion::STATUS_ANSWERED,
            'Published' => SpecialistQuestion::STATUS_PUBLISHED,
        ];

        $fields = [
            IdField::new('id')->hideOnForm(),
            TextField::new('name', 'From'),
            TextField::new('email', 'Email')->hideOnIndex(),
            AssociationField::new('specialist', 'Specialist'),
            ChoiceField::new('status')->setChoices($statusChoices),
            BooleanField::new('isPublic', 'Public'),
            BooleanField::new('isAnonymous', 'Anonymous'),
            DateTimeField::new('createdAt', 'Submitted')->hideOnForm(),
        ];

        if (in_array($pageName, [Crud::PAGE_DETAIL, Crud::PAGE_EDIT])) {
            $fields = [
                IdField::new('id')->hideOnForm(),
                TextField::new('name', 'From'),
                TextField::new('email', 'Email'),
                TextField::new('phone', 'Phone'),
                AssociationField::new('specialist', 'Specialist'),
                BooleanField::new('isAnonymous', 'Anonymous'),
                TextareaField::new('question', 'Question')->setNumOfRows(4)->setFormTypeOption('disabled', true),
                ChoiceField::new('status')->setChoices($statusChoices),
                TextareaField::new('answer', 'Answer')->setNumOfRows(8),
                AssociationField::new('answeredBySpecialist', 'Answered By')->setRequired(false),
                BooleanField::new('showSpecialistName', 'Show name')->setHelp('Default ON'),
                BooleanField::new('showSpecialistTitle', 'Show title/specialty')->setHelp('Default ON'),
                BooleanField::new('showSpecialistInstitution', 'Show institution')->setHelp('Default OFF'),
                BooleanField::new('showSpecialistAddress', 'Show address')->setHelp('Default OFF'),
                BooleanField::new('showSpecialistPhone', 'Show phone')->setHelp('Default OFF'),
                BooleanField::new('showSpecialistEmail', 'Show email')->setHelp('Default OFF'),
                DateTimeField::new('answeredAt', 'Answered At'),
                BooleanField::new('isPublic', 'Publish to /questions'),
                DateTimeField::new('createdAt', 'Submitted')->hideOnForm(),
            ];
        }

        return $fields;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_EDIT, Action::INDEX)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->remove(Crud::PAGE_INDEX, Action::NEW);
    }
}
