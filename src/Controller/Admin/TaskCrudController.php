<?php

namespace App\Controller\Admin;

use App\Entity\Task;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;

class TaskCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Task::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('title', 'Title'),
            TextEditorField::new('description', 'Description'),
            ChoiceField::new('status', 'Status')
                ->setChoices([
                    Task::TASK_STATUS_TODO => Task::TASK_STATUS_TODO,
                    Task::TASK_STATUS_IN_PROGRESS => Task::TASK_STATUS_IN_PROGRESS,
                    Task::TASK_STATUS_DONE => Task::TASK_STATUS_DONE,
                    Task::TASK_STATUS_ON_HOLD => Task::TASK_STATUS_ON_HOLD,
                    Task::TASK_STATUS_CANCELLED => Task::TASK_STATUS_CANCELLED,
                ]),
            DateTimeField::new('dueDate', 'Due Date')->setFormTypeOption('required', false),
            AssociationField::new('assignedTo', 'Assigned To User'),
            AssociationField::new('experiments', 'Experiments'),
            AssociationField::new('samples', 'Samples'),
            AssociationField::new('protocols', 'Protocols'),
        ];
    }
}
