<?php

namespace App\Controller\Admin;

use App\Entity\Storage;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\HiddenField;
use App\Field\GridField;

class StorageCrudController extends AbstractCrudController
{
    public function __construct(private AdminUrlGenerator $adminUrlGenerator)
    {
    }

    public function createEntity(string $entityFqcn) {
        $entity = new Storage();
        $entity->setBarcode('BRCD-' . uniqid());
        return $entity;
    }


    public static function getEntityFqcn(): string
    {
        return Storage::class;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('name')
            ->add('description')
            ->add('positionTaken')
        ;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return Crud::new()
            ->overrideTemplates([
                'crud/index' => 'admin/storage/index.html.twig',
                'crud/new' => 'admin/custom_new.html.twig',
                'crud/edit' => 'admin/custom_edit.html.twig',
            ])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        if ($_ENV['APP_ENV'] === 'dev') {
            $allPositionsGeneratedField = ArrayField::new('generateAllPositions', 'Generate All Positions')
            //->setRequired(false)
            ->setFormTypeOptions(['mapped' => false])
            ->formatValue(function ($value, $entity) {
                return $entity instanceof Storage && $entity->getGenerateAllPositions() ? count($entity->getGenerateAllPositions()) : 'N/A';
            });
        }
        $fields = [
            IdField::new('id')->hideOnForm(),  // Auto-generated, so hide on form
            TextField::new('name', 'Storage Name')->formatValue(function ($value, $entity) {
                # add linkto experiment
                $link = $this->adminUrlGenerator	
                    ->setController(self::class)
                    ->setAction(Action::DETAIL)
                    ->setEntityId($entity->getId())
                    ->generateUrl();
                return sprintf('<a href="%s">%s</a>', $link, $value);
            }),
            GridField::new('colsAndRows', 'Grid show')
                ->setFormTypeOption('attr', ['class' => 'grid-field'])
                ->hideOnForm(),
            HiddenField::new('dimensions')
                ->setFormTypeOption('attr', ['class' => 'dimensions-field'])
                ->hideOnIndex(),
                // add rows and cols input but not mapped
            NumberField::new('rows')
                ->setFormTypeOption('attr', ['class' => 'dimensions-rows', 'required' => true])
                ->setFormTypeOption('mapped', false)
                ->formatValue(function ($value, $entity) {
                    $dimensions = $entity->getDimensions();
                    if ($dimensions) {
                        // get from format {"rows":{"numbering":"NUMERIC","count":2},"columns":{"numbering":"NUMERIC","count":3}}
                        $dimensions = json_decode($dimensions, true);
                    }
                    if (isset($dimensions['rows']['count'])) {
                        $cols = $dimensions['rows']['count'];
                        return $cols;
                    } else{
                        return 0;
                    }
                }),
            NumberField::new('cols')
                ->setFormTypeOption('attr', ['class' => 'dimensions-cols', 'required' => true])
                ->setFormTypeOption('mapped', false)
                ->formatValue(function ($value, $entity) {
                    $dimensions = $entity->getDimensions();
                    if ($dimensions) {
                        // get from format {"rows":{"numbering":"NUMERIC","count":2},"columns":{"numbering":"NUMERIC","count":3}}
                        $dimensions = json_decode($dimensions, true);
                    }
                    if (isset($dimensions['columns']['count'])) {
                        $rows = $dimensions['columns']['count'];
                        return $rows;
                    } else{
                        return 0;
                    }
                }),
            TextEditorField::new('description', 'Description')->hideOnIndex(),  // Optional: Hide on index view
            AssociationField::new('storageType', 'Storage Type'),
            AssociationField::new('samples', 'Samples')->onlyOnDetail(),
            TextField::new('barcode', 'Barcode')
                ->setFormTypeOption('attr', ['class' => 'barcode']),
            TextField::new('getHierarchicalStorageString', 'Hierarchical Storage')
                ->formatValue(function ($value, $entity) {
                    
                    return $this->getHierarchicalStorageString($entity);
                })
                ->onlyOnIndex(),
                
            ArrayField::new('positionTaken', 'Position Taken')
                ->formatValue(function ($value, $entity) {
                    // Directly return the count of positions taken or 'N/A' if not applicable
                    return $entity instanceof Storage && $entity->getPositionTaken() ? count($entity->getPositionTaken()) : 'N/A';
                }),
            
            ArrayField::new('availablePositions', 'Available Positions')
                ->setFormTypeOptions(['mapped' => false, 'disabled' => true]) // Assuming you don't need to input this in forms, just display
                ->formatValue(function ($value, $entity) {
                    // Ensure you use the getAvailablePositions method to calculate the value
                    $availablePositions = $entity instanceof Storage ? $entity->getAvailablePositions() : 'N/A';
                    // Return the value wrapped in a badge for visual emphasis
                    return '<span class="badge badge-secondary">' . $availablePositions . '</span>';
                }),
                  
            AssociationField::new('samples', 'Samples'),

            AssociationField::new('parent', 'Parent Storage')
                ->setRequired(false)
                ->autocomplete(),
            IntegerField::new('level', 'Level')
                ->setFormTypeOption('disabled','disabled')
                ->onlyOnIndex()
                ->formatValue(function ($value, $entity) {
                    return $entity instanceof Storage && $entity->getLevel() ? $entity->getLevel() : 'N/A';
                }),
        ];

        if ($_ENV['APP_ENV'] === 'dev') {
            array_push($fields, $allPositionsGeneratedField);
        }

        return $fields;
    }

    private function getHierarchicalStorageString(Storage $storage): string
    {
        $path = [];

        while ($storage !== null) {
            $storageName = $storage->getName();
            $link = $this->adminUrlGenerator	
                    ->setController(self::class)
                    ->setAction(Action::DETAIL)
                    ->setEntityId($storage->getId())
                    ->generateUrl();
            $storageNameWithLink = sprintf('<a href="%s">%s</a>', $link, $storageName);

            array_unshift($path, $storageNameWithLink); // Prepend the name of the current storage to the path
            $storage = $storage->getParent(); // Move up to the parent storage
        }

        return implode(' > ', $path); // Join the path parts with a delimiter
    }

}