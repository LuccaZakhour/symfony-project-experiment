<?php

namespace App\Controller\Admin;

use App\Entity\SystemCapability;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class SystemCapabilityCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SystemCapability::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name', 'Name'),
            TextEditorField::new('description', 'Description'),
            BooleanField::new('isEnabled', 'Enabled'),
            // Add more fields here as needed
            // AssociationField::new('relatedEntity', 'Related Entity'),
        ];
    }
}
