<?php

namespace App\Controller\Admin;

use App\Entity\ClientAppSetting;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;

class ClientAppSettingCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ClientAppSetting::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name', 'Name'),
            TextareaField::new('value', 'Value'),
            TextEditorField::new('description', 'Description')->setFormTypeOption('required', false),
        ];
    }
}
