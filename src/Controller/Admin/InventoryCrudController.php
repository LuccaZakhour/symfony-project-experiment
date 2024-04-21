<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use App\Entity\InventoryDummy;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;

class InventoryCrudController extends AbstractCrudController
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }
    public static function getEntityFqcn(): string
    {
        return InventoryDummy::class;
    }

    public function configureActions(Actions $actions): Actions
{
    return $actions
        // Remove the "new" (Create) action globally for this CRUD controller
        ->disable(Action::NEW)
        ->disable(Action::DELETE)
        ->disable(Action::EDIT);
}


    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, 'Inventory List')
            ->overrideTemplates([
                'crud/index' => 'admin/crud/inventory_dummy_index.html.twig',
            ]);
    }

    // 

    public function configureFields(string $pageName): iterable
    {
        // get storage and transform to json
        $apiEndpoint = $_ENV['API_ENDPOINT'];
        $endpointVarScript = "alert('loaded'); <script>var API_ENDPOINT = '$apiEndpoint';</script>";

        return [
            IdField::new('id')->hideOnForm()
                ->setFormTypeOptions(['mapped' => false])
                ->formatValue(function ($value, $entity) use ($endpointVarScript) {
                    
                    return $value . $endpointVarScript;
                }),
            TextField::new('name', 'Item Name')->addCssClass('dummy-class')
                ->setFormTypeOptions(['mapped' => false]), // Example of adding a custom CSS class
            NumberField::new('quantity', 'Quantity')
                ->setFormTypeOptions(['mapped' => false]),
            BooleanField::new('isActive', 'Active?')
                ->setFormTypeOptions(['mapped' => false]),
            DateTimeField::new('createdAt', 'Created At')
                ->setFormTypeOptions(['mapped' => false]),
            // Add more fields as necessary...
        ];
    }

}
