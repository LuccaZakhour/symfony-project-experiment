<?php

namespace App\Controller\Admin;

use App\Entity\CatalogItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CatalogItemCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return CatalogItem::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name'),
            TextField::new('sku'),
            MoneyField::new('price')->setCurrency('EUR'),
            TextEditorField::new('description')->hideOnIndex(),
        ];
    }
}
