<?php

namespace App\Controller\Admin;


use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[IsGranted('ROLE_ADMIN')]
class UserCrudController extends AbstractCrudController
{
    private Security $security;
    private UserPasswordHasherInterface $passwordHasher;


    public function __construct(Security $security, UserPasswordHasherInterface $passwordHasher)
    {
        $this->security = $security;
        $this->passwordHasher = $passwordHasher;
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined();
    }

    public function configureFields(string $pageName): iterable
    {
        $rolesChoices = [
            'User' => 'ROLE_USER',
            'Assistant' => 'ROLE_ASSISTANT',
        ];

        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            $rolesChoices['Admin'] = 'ROLE_ADMIN';
            $rolesChoices['Super Admin'] = 'ROLE_SUPER_ADMIN';
        }

        $fields = [
            IdField::new('id')->hideOnForm(),
            TextField::new('email'),
            TextField::new('password')
                ->setFormType(PasswordType::class)
                ->onlyOnForms()
                ->setRequired($pageName === Crud::PAGE_NEW),
            TextField::new('name'),
            TextField::new('phone'),
        ];

        $currentUserId = $this->getUser()?->getId();
        $editingUserId = $this->getContext()?->getEntity()?->getInstance()?->getId();

        if ($currentUserId !== $editingUserId) {
            $fields[] = ChoiceField::new('roles')
                ->setChoices($rolesChoices)
                ->allowMultipleChoices()
                ->renderExpanded();
        }

        $fields[] = BooleanField::new('isActive');
        $fields[] = BooleanField::new('isBanned');
        $fields[] = DateTimeField::new('createdAt')->hideOnForm();

        return $fields;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_EDIT, Action::INDEX)
            ->add(Crud::PAGE_NEW, Action::INDEX)
            ->update(Crud::PAGE_INDEX, Action::EDIT, function (Action $action) {
                return $action->displayIf(function ($entity) {
                    // Prevent editing yourself
                    if ($entity->getId() === $this->getUser()->getId()) {
                        return false;
                    }
                    // ADMIN cannot edit SUPER_ADMIN
                    if (!$this->isGranted('ROLE_SUPER_ADMIN') && in_array('ROLE_SUPER_ADMIN', $entity->getRoles())) {
                        return false;
                    }
                    return true;
                });
            })
            ->update(Crud::PAGE_INDEX, Action::DELETE, function (Action $action) {
                return $action->displayIf(function ($entity) {
                    // Prevent deleting yourself
                    if ($entity->getId() === $this->getUser()->getId()) {
                        return false;
                    }
                    // ADMIN cannot delete SUPER_ADMIN
                    if (!$this->isGranted('ROLE_SUPER_ADMIN') && in_array('ROLE_SUPER_ADMIN', $entity->getRoles())) {
                        return false;
                    }
                    return true;
                });
            });
    }

    public function persistEntity($entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof User && $entityInstance->getPassword()) {
            $hashedPassword = $this->passwordHasher->hashPassword($entityInstance, $entityInstance->getPassword());
            $entityInstance->setPassword($hashedPassword);
        }
        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity($entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof User) {
            $password = $entityInstance->getPassword();
            if ($password && strlen($password) < 60) { // not hashed yet
                $hashedPassword = $this->passwordHasher->hashPassword($entityInstance, $password);
                $entityInstance->setPassword($hashedPassword);
            } else {
                // Restore original password
                $originalData = $entityManager->getUnitOfWork()->getOriginalEntityData($entityInstance);
                $entityInstance->setPassword($originalData['password']);
            }
        }
        parent::updateEntity($entityManager, $entityInstance);
    }
}
