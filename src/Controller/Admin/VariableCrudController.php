<?php

namespace App\Controller\Admin;

use App\Entity\Variable;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class VariableCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Variable::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name'),
            TextEditorField::new('description'),
            TextField::new('type'),
            TextEditorField::new('contents'),
        ];
    }
}
