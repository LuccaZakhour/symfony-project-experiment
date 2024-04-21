<?php

namespace App\Controller\Admin;

use App\Entity\Equipment;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;

class EquipmentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Equipment::class;
    }

    // In your CRUD controller, you can add custom filters
    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('name')
            ->add('status');
        // Add more filters as required
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name'),
            TextEditorField::new('description')->hideOnIndex(),
            TextField::new('serialNumber')->hideOnIndex(),
            TextField::new('location')->hideOnIndex(),
            BooleanField::new('isActive'),
            TextField::new('manufacturer')->hideOnIndex(),
            TextField::new('status')->hideOnIndex(),
        ];
    }
}
