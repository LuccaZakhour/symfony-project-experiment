<?php

namespace App\Controller\Admin;

use App\Entity\Study;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;

class StudyCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Study::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),  // Auto-generated, so hide on form
            TextField::new('name', 'Study Name'),
            TextAreaField::new('description', 'Description')->hideOnIndex(),  // Optional: Hide on index view
            # add status
            TextField::new('status'),
            TextareaField::new('description', 'Description')->hideOnIndex(),  // Optional: Hide on index view
            AssociationField::new('leadResearcher', 'Lead Researcher'),
            AssociationField::new('experiments', 'Experiments'),  // Optional: Show only on detail view
            AssociationField::new('samples', 'Samples'), // Optional: Show only on detail view
            AssociationField::new('project', 'Project'),
        ];
    }
}
