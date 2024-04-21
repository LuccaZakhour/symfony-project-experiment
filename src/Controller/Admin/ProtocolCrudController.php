<?php

namespace App\Controller\Admin;

use App\Entity\Protocol;
use Doctrine\Migrations\Configuration\Migration\FormattedFile;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;

class ProtocolCrudController extends AbstractCrudController
{
    public function __construct(private AdminUrlGenerator $adminUrlGenerator)
    {
    }

    public static function getEntityFqcn(): string
    {
        return Protocol::class;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('name')
            ->add('description')
            ->add('user')
        ;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // crud/detail.html.twig
            ->overrideTemplates([
                'crud/edit' => 'admin/custom_edit.html.twig',
                'crud/field/collection' => 'admin/field/protocol_field_collection.html.twig'
            ]);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            FormField::addTab('Protocol Detail'),
            FormField::addColumn(12),
            CollectionField::new('fields')
                ->useEntryCrudForm(ProtocolFieldCrudController::class)
                ->setFormTypeOption('by_reference', false)
                ->hideOnIndex(),

            FormField::addTab('Settings'),

            FormField::addColumn(12),
            TextField::new('name')->formatValue(function ($value, $entity) {
                # add linkto experiment
                $link = $this->adminUrlGenerator	
                    ->setController(self::class)
                    ->setAction(Action::DETAIL)
                    ->setEntityId($entity->getId())
                    ->generateUrl();
                return sprintf('<a href="%s">%s</a>', $link, $value);
            }),
            TextareaField::new('description')
                ->setFormTypeOption('attr', ['class' => 'my-ckeditor-textarea']),
            AssociationField::new('files')->setFormTypeOption('by_reference', false),
            AssociationField::new('experiments')->setFormTypeOption('by_reference', false)->onlyOnIndex(),
            AssociationField::new('user')->setFormTypeOption('by_reference', false)->onlyOnIndex(),

        ];
    }
}

