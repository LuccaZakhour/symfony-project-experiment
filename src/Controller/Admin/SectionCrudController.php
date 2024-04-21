<?php

namespace App\Controller\Admin;

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

class SectionCrudController extends AbstractCrudController
{
    public function __construct(private AdminUrlGenerator $adminUrlGenerator)
    {
    }

    public static function getEntityFqcn(): string
    {
        return Section::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // crud/detail.html.twig
            ->overrideTemplates([
                'crud/new' => 'admin/custom_new.html.twig',
                'crud/edit' => 'admin/custom_edit.html.twig'
            ]);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('name')
            ->add('type')
            ->add('description')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        $metaField = ArrayField::new('meta')
            ->formatValue(function ($value, $entity) {
                if (!isset($entity)) return $value;
                return '<strong>meta:</strong>' . json_encode($value)
                . '<strong>metaOrig:</strong>' . json_encode($entity->getOrigMeta());
            })->hideOnForm();

        $idField = IdField::new('id', 'Id generatedValue')->hideOnForm();

        $fields = [
            FormField::addColumn(12),
            TextField::new('name')->formatValue(function ($value, $entity) {

                if (!isset($entity)) return $value;

                $link = $this->adminUrlGenerator	
                    ->setController(self::class)
                    ->setAction(Action::DETAIL)
                    ->setEntityId($entity->getId())
                    ->generateUrl();
                return sprintf('<a href="%s">%s</a>', $link, $value);
            }),
            # add type
            TextField::new('type')->hideOnForm(),
            TextareaField::new('description')
                ->setFormTypeOption('attr', ['class' => 'my-ckeditor-textarea'])
                ->setTemplatePath('admin/field/raw.html.twig'),
            AssociationField::new('files'),
            AssociationField::new('samples'),
            AssociationField::new('experiment'),
        ];

        if ($_ENV['APP_ENV'] === 'dev' && isset($metaField)) {
            $fields[] = $metaField;
            $fields[] = $idField;
        }

        return $fields;
    }
}
