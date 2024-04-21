<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('salutation')->hideOnIndex(),
            TextField::new('firstname'),
            TextField::new('lastname'),
            EmailField::new('email'),
            ArrayField::new('roles'),
            ChoiceField::new('roles')
                ->setChoices([
                    'Admin' => User::ROLE_ADMIN,
                    'Researcher' => User::ROLE_RESEARCHER,
                    'Technician' => User::ROLE_TECHNICIAN,
                    'Lab Technician' => User::ROLE_LAB_TECHNICIAN,
                    'User' => User::ROLE_USER,
                ])
                ->allowMultipleChoices(),
                    BooleanField::new('isVerified'),
                    BooleanField::new('enabled'),
                    DateTimeField::new('removedAt')->hideOnForm(),
                    AssociationField::new('tasks'),
                    AssociationField::new('groups'),
                    AssociationField::new('experiments')
        ];
    }
}
