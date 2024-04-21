<?php

namespace App\Controller\Admin;

use App\Entity\Task;
use App\Entity\TaskManagement;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;

class TaskManagementCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return TaskManagement::class;
    }

    public function configureFields(string $pageName): iterable
    {
        // You can define the status options in TaskManagement entity as constants

        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('title'),
            TextEditorField::new('description')->setFormTypeOption('required', false),
            DateTimeField::new('dueDate')->setFormTypeOption('required', false),
            AssociationField::new('assignedTo'),
            AssociationField::new('samples'),
            AssociationField::new('experiments'),
            ChoiceField::new('status')->setChoices([
                Task::TASK_STATUS_TODO => Task::TASK_STATUS_TODO,
                Task::TASK_STATUS_IN_PROGRESS => Task::TASK_STATUS_IN_PROGRESS,
                Task::TASK_STATUS_DONE => Task::TASK_STATUS_DONE,
                Task::TASK_STATUS_ON_HOLD => Task::TASK_STATUS_ON_HOLD,
                Task::TASK_STATUS_CANCELLED => Task::TASK_STATUS_CANCELLED,
            ])
        ];
    }
}


    