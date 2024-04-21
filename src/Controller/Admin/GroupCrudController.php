<?php

namespace App\Controller\Admin;

use App\Entity\Group;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class GroupCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Group::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name'),
            TextEditorField::new('description')->hideOnIndex(),
            AssociationField::new('users'),
            ChoiceField::new('permissions')
                ->setChoices([
                    'Add' => 'add',
                    'Delete' => 'delete',
                    'Update' => 'update',
                    'Move' => 'move',
                    'Reserve' => 'reserve',
                ])
                ->allowMultipleChoices()  // enables multiple selections
                ->renderAsNativeWidget(false),
        ];
    }

    // change title Group to "Groups roles and permissions"
    public function configureCrud(Crud $crud): Crud
    {

        $title = 'Groups roles and permissions';
        // add to title font awesome lock icon
        $title = '<i class="fas fa-lock"></i> ' . $title;

        return $crud
            ->setPageTitle(Crud::PAGE_INDEX,  $title)
            ->setPageTitle(Crud::PAGE_NEW,  $title)
            ->setPageTitle(Crud::PAGE_EDIT,  $title)
            ->setPageTitle(Crud::PAGE_DETAIL,  $title);
    }
}
