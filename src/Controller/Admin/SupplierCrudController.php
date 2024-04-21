<?php

namespace App\Controller\Admin;

use App\Entity\Supplier;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class SupplierCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Supplier::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),  // Auto-generated, so hide on form
            TextField::new('name', 'Supplier Name'),
            EmailField::new('email', 'Email Address'),
            NumberField::new('phoneNumber', 'Contact Number'),
            TextField::new('address', 'Address')->hideOnIndex(),  // Optional: Hide on index view
            AssociationField::new('products', 'Products Supplied')->onlyOnDetail()  // Optional: Show only on detail view
        ];
    }
}
