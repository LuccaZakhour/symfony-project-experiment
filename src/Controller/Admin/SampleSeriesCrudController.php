<?php

namespace App\Controller\Admin;

use App\Entity\SampleSeries;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;

class SampleSeriesCrudController extends AbstractCrudController
{
    public function __construct(private AdminUrlGenerator $adminUrlGenerator)
    {
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // crud/detail.html.twig
            ->overrideTemplates([
                'crud/detail' => 'admin/sampleSeries/detail.html.twig',
                'crud/field/collection' => 'admin/field/sample_series_collection.html.twig',
                'crud/field/text' => 'admin/field/raw.html.twig', # this is important for "Sample Type" raw display with css
            ])
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions;
    }

    public function createEntity(string $entityFqcn) {
        $entity = new SampleSeries();
        $entity->setBarcode('BRCD-' . uniqid());
        return $entity;
    }

    public static function getEntityFqcn(): string
    {
        return SampleSeries::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            FormField::addColumn(6),
            IdField::new('id')->hideOnForm(),
            TextField::new('name')->formatValue(function ($value, $entity) {
                # add linkto experiment

                // if $entity return N/A
                if ($entity === null) {
                    return 'N/A';
                }

                $link = $this->adminUrlGenerator	
                    ->setController(self::class)
                    ->setAction(Action::DETAIL)
                    ->setEntityId($entity->getId())
                    ->generateUrl();

                $urlString = sprintf('<a href="%s">%s</a>', $link, $value);

                return $urlString;
            }),      
            TextField::new('barcode'),
            FormField::addColumn(6),
            AssociationField::new('samples')
                ->hideOnDetail(),
            AssociationField::new('user', 'Owner'),
            TextField::new('sampleTypeTitle', 'Sample Type'),
            FormField::addColumn(12),
            CollectionField::new('samples', 'Samples in the series')
                ->hideOnIndex()
                ->hideOnForm(),
        ];
    }

}
