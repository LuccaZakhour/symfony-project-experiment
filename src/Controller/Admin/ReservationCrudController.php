<?php

namespace App\Controller\Admin;

use App\Entity\Reservation;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;

class ReservationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Reservation::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),  // Auto-generated, so hide on form
            DateTimeField::new('startTime', 'Start Time'),
            DateTimeField::new('endTime', 'End Time'),
            TextField::new('reservationCode', 'Reservation Code'),
            AssociationField::new('equipment', 'Equipment'),
            AssociationField::new('user', 'User'),
            TextEditorField::new('notes', 'Notes')->hideOnIndex()  // Optional: Hide on index view
        ];
    }
}
