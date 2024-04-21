<?php

namespace App\Controller\Admin;

use App\Entity\Project;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;

class ProjectCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Project::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('shortName'),
            TextField::new('name'),
            AssociationField::new('group'),
            TextEditorField::new('description'),
            TextEditorField::new('notes'),
            AssociationField::new('collaborators'),
            ChoiceField::new('status')->setChoices(['Active' => 'active', 'Closed' => 'closed']),
            AssociationField::new('studies'),
            // Add other fields as necessary
        ];
    }
}
