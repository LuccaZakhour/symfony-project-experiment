<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class OrderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('orderNumber')->setLabel('Order Number'),
            DateTimeField::new('orderDate')->setLabel('Order Date'),
            TextField::new('supplier'),
            AssociationField::new('orderedBy', 'Ordered By')->autocomplete(),
            ChoiceField::new('status')->setChoices([
                'Pending' => 'Pending',
                'Completed' => 'Completed',
            ]),
            MoneyField::new('totalAmount')->setCurrency('EUR'),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('Orders')
            ->setEntityLabelInSingular('Order')
            ->setDefaultSort(['orderDate' => 'DESC']);
    }
}