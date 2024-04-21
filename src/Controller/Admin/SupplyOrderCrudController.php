<?php

namespace App\Controller\Admin;

use App\Entity\SupplyOrder;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;

class SupplyOrderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SupplyOrder::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('orderNumber', 'Order Number'),
            DateTimeField::new('orderDate', 'Order Date'),
            ChoiceField::new('status', 'Order Status')
                ->setChoices([
                    SupplyOrder::STATUS_REQUESTED => SupplyOrder::STATUS_REQUESTED,
                    SupplyOrder::STATUS_PENDING_APPROVAL => SupplyOrder::STATUS_PENDING_APPROVAL,
                    SupplyOrder::STATUS_APPROVED => SupplyOrder::STATUS_APPROVED,
                    SupplyOrder::STATUS_ORDERED => SupplyOrder::STATUS_ORDERED,
                    SupplyOrder::STATUS_SHIPPED => SupplyOrder::STATUS_SHIPPED,
                    SupplyOrder::STATUS_PARTIALLY_RECEIVED => SupplyOrder::STATUS_PARTIALLY_RECEIVED,
                    SupplyOrder::STATUS_RECEIVED => SupplyOrder::STATUS_RECEIVED,
                    SupplyOrder::STATUS_CHECKED => SupplyOrder::STATUS_CHECKED,
                    SupplyOrder::STATUS_STORED => SupplyOrder::STATUS_STORED,
                    SupplyOrder::STATUS_INVOICED => SupplyOrder::STATUS_INVOICED,
                    SupplyOrder::STATUS_PAID => SupplyOrder::STATUS_PAID,
                    SupplyOrder::STATUS_CLOSED => SupplyOrder::STATUS_CLOSED,
                    SupplyOrder::STATUS_CANCELLED => SupplyOrder::STATUS_CANCELLED,
                    SupplyOrder::STATUS_RETURNED => SupplyOrder::STATUS_RETURNED,
                    SupplyOrder::STATUS_ERROR => SupplyOrder::STATUS_ERROR,
                    SupplyOrder::STATUS_ARCHIVED => SupplyOrder::STATUS_ARCHIVED,
                                
                ]),
            AssociationField::new('orderedBy', 'Ordered By'),
            AssociationField::new('items', 'Order Items')->onlyOnDetail(),
        ];
    }
}
