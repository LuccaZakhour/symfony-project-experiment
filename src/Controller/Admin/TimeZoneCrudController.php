<?php

namespace App\Controller\Admin;

use App\Entity\TimeZone;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;

class TimeZoneCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return TimeZone::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name'),
            TextField::new('offset'),
            BooleanField::new('isDefault'),
        ];
    }
}
