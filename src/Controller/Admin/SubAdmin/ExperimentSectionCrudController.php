<?php

namespace App\Controller\Admin\SubAdmin;

use App\Entity\File;
use App\Entity\Section;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;

class ExperimentSectionCrudController extends AbstractCrudController
{

    public static function getEntityFqcn(): string
    {
        return Section::class;
    }

    public function configureFields(string $pageName): iterable
    {

        $fields = [
            FormField::addColumn(12),
            TextField::new('name'),
            # add type
            TextField::new('type'),
            TextareaField::new('description')
                ->setFormTypeOption('attr', ['class' => 'my-ckeditor-textarea'])
                ->setTemplatePath('admin/field/raw.html.twig'),
            //AssociationField::new('files'),
            //AssociationField::new('samples'),
            //AssociationField::new('experiment'),
        ];

        return $fields;
    }
}
