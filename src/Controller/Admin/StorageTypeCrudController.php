<?php

namespace App\Controller\Admin;

use App\Entity\StorageType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;


class StorageTypeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return StorageType::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            //IdField::new('id')->hideOnForm(),  // Auto-generated, so hide on form
            TextField::new('name', 'Storage Type Name'),
            # add allow add
            ChoiceField::new('shape', 'Shape')->setChoices([
                        'Storage' => 'STORAGE',
                        'Equipment' => 'EQUIPMENT',
                    ])
                    ->autocomplete(),
            TextEditorField::new('description', 'Description')->hideOnIndex(),  // Optional: Hide on index view
            AssociationField::new('storages', 'Storages')->onlyOnDetail(),  // Optional: Show only on detail view
        ];
    }
}
