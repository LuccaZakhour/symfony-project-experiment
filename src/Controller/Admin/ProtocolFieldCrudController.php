<?php

namespace App\Controller\Admin;

use App\Entity\ProtocolField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;

class ProtocolFieldCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ProtocolField::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // crud/detail.html.twig
            ->overrideTemplates([
                'crud/edit' => 'admin/custom_edit.html.twig',
                'crud/field/textarea' => 'admin/field/raw.html.twig'
            ]);
    }

    public function configureFields(string $pageName): iterable
    {
        // if env dev, show meta field
        if ($_ENV['APP_ENV'] == 'dev') {
            $meta = ArrayField::new('meta')
            ->formatValue(function ($value) {
                // array ({"draft": "", "author": "A eLABJournal", "protID": 152, "rating": 0, "userID": 2, "created": "2010-04-19T16:54:53Z", "deleted": "", "groupID": 0, "version": 1, "authorID": 2, "category": "Experimental Procedures", "isPublic": 1, "numSteps": 6, "storageID": 0, "viewCount": 7137, "appViewURL": "https://www.elabjournal.com/members/protocol/appView/?protID=152&protVersionID=136", "categoryID": 40, "subgroupID": 24, "latestVersion": 0, "protVersionID": 136, "groupShareCount": 0, "latestVersionId": 0}) to string
                return json_encode($value);
            })->hideOnForm();

            $idField = IdField::new('id');
        }

        $fields = [
            FormField::addColumn(12),
            TextField::new('name'),
            TextareaField::new('value')->setCssClass('col-md-12 col-xxl-12')
                ->setFormTypeOption('attr', ['class' => 'hide-label-on-form'])
                ->setFormTypeOption('attr', ['class' => 'my-ckeditor-textarea']),
            NumberField::new('stepId')->setCssClass('col-md-12 col-xxl-12')->onlyOnIndex(),
            NumberField::new('sortBy')->setCssClass('col-md-12 col-xxl-12'),
            AssociationField::new('protocol')->onlyOnIndex(),
        ];

        if (isset($meta)) {
            $fields[] = $meta;
        }

        if (isset($idField)) {
            $fields[] = $idField;
        }

        return $fields;
    }

}
