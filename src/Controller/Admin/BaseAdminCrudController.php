<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

abstract class BaseAdminCrudController extends AbstractCrudController
{
    protected function checkPermission(string $permission, $subject): void
    {
        if (!$this->isGranted($permission, $subject)) {
            throw new AccessDeniedException('You do not have permission to perform this action.');
        }
    }

    /* eg. usage:
    
class YourEntityCrudController extends BaseCrudController
{
    public static function getEntityFqcn(): string
    {
        return YourEntity::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions = parent::configureActions($actions);

        $actions->update(Action::EDIT, function (Action $action) {
            return $action->displayIf(function ($entity) {
                // Check permissions using the BaseCrudController method
                $this->checkPermission('EDIT', $entity);
                return true; // If no exception is thrown, display the action
            });
        });
    */
}