<?php

namespace App\Controller\Admin;

use App\Entity\SampleType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ColorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\HiddenField;

class SampleTypeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SampleType::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // crud/detail.html.twig
            ->overrideTemplates([
                'crud/edit' => 'admin/custom_edit.html.twig',
                'crud/new' => 'admin/custom_new.html.twig',
            ])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        $fields = [
            IdField::new('id')->hideOnForm(), // Auto-generated, so hide on form
            TextField::new('name', 'Sample Type Name'),
            TextEditorField::new('description', 'Description'), // Optional: Hide on index view
            ColorField::new('bgColor', 'Background Color')->setFormTypeOptions(['attr' => ['class' => 'colorpicker']]),
            ColorField::new('fgColor', 'Foreground Color')->setFormTypeOptions(['attr' => ['class' => 'colorpicker']]),
            //DateTimeField::new('createdAt', 'Created At')->setFormTypeOptions(['disabled' => true]), // Disable on form
            //DateTimeField::new('updatedAt', 'Updated At')->setFormTypeOptions(['disabled' => true]), // Disable on form
            AssociationField::new('samples', 'Samples')->onlyOnDetail(), // Optional: Show only on detail view

             HiddenField::new('customFields', 'Custom Fields (JSON)')
                ->setRequired(false)
                ->hideOnIndex()
                ->setFormTypeOptions([
                    'attr' => [
                        'class' => 'custom-fields-json',
                        //'style' => 'display:none;', // Hide the textarea
                    ],
                ])
                ->formatValue(function ($value, $entity) {
                    return json_encode($value);
                }),
        ];

        return $fields;
    }
}
