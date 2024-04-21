<?php

namespace App\Controller\Admin;

use App\Entity\File;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

class FileCrudController extends AbstractCrudController
{
    public function __construct(private AdminUrlGenerator $adminUrlGenerator)
    {
    }

    public static function getEntityFqcn(): string
    {
        return File::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('filename')
                ->formatValue(function ($value, $entity) {
                    # add linkto experiment
                    $link = $this->adminUrlGenerator	
                        ->setController(self::class)
                        ->setAction(Action::DETAIL)
                        ->setEntityId($entity->getId())
                        ->generateUrl();
                    return sprintf('<a href="%s">%s</a>', $link, $value);
                }),
            IntegerField::new('filesize'),
            TextEditorField::new('description')->hideOnIndex(),
            TextField::new('filetype'),
            TextField::new('filepath', 'File path')->hideOnIndex(),
            TextField::new('fullFilePath', 'Full file path')->hideOnIndex(),
            AssociationField::new('experiment'),
            AssociationField::new('experimentSection'),
            AssociationField::new('protocol'),
            AssociationField::new('sample'),
        ];
    }
}
