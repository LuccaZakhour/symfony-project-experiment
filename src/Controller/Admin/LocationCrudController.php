<?php

namespace App\Controller\Admin;

use App\Entity\Location;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class LocationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Location::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('department', 'Department')->setRequired(false),
            TextField::new('building', 'Building')->setRequired(false),
            TextField::new('floor', 'Floor')->setRequired(false),
            TextField::new('room', 'Room')->setRequired(false),
            AssociationField::new('storages', 'Storages')->setRequired(false)
                ->setFormTypeOptions([
                    'by_reference' => false,
                ]),
            // Add other fields here as needed
        ];
    }
    
    // If you need to configure actions like delete, edit, create, etc., override the configureActions method.
    // If you need to configure any filters or searches, override the configureFilters method.
}
