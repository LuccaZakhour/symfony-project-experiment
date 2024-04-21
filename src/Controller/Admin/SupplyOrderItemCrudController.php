<?php

namespace App\Controller\Admin;

use App\Entity\SupplyOrderItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;

class SupplyOrderItemCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SupplyOrderItem::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('itemName', 'Item Name'),
            IntegerField::new('quantity', 'Quantity'),
            MoneyField::new('price', 'Price')->setCurrency('EUR'),  // Change the currency as needed
            AssociationField::new('supplyOrder', 'Supply Order'),
        ];
    }
}
