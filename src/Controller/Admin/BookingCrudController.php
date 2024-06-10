<?php

namespace App\Controller\Admin;

use App\Entity\Booking;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;

class BookingCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Booking::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            DateTimeField::new('bookAt')->setLabel('Date de réservation'),
            DateTimeField::new('createdAt')->setLabel('Date de création')->hideOnForm(),
            TextField::new('period')->setLabel('Période'),
            BooleanField::new('isGroup')->setLabel('Est un groupe'),
            AssociationField::new('product')->setLabel('Produit'),
            AssociationField::new('profile')->setLabel('Profil'),
            BooleanField::new('isPaid')->setLabel('Est payé'),
            CollectionField::new('participants')->setLabel('Participants')->onlyOnDetail(),
            AssociationField::new('invoice')->setLabel('Facture')->onlyOnDetail(),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Booking')
            ->setEntityLabelInPlural('Bookings')
            ->setPageTitle(Crud::PAGE_INDEX, 'Liste des réservations')
            ->setPageTitle(Crud::PAGE_DETAIL, 'Détails de la réservation')
            ->setPageTitle(Crud::PAGE_NEW, 'Créer une réservation')
            ->setPageTitle(Crud::PAGE_EDIT, 'Modifier la réservation')
            ->showEntityActionsInlined();
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->update(Crud::PAGE_DETAIL, Action::EDIT, function (Action $action) {
                return $action->setLabel('Modifier');
            })
            ->update(Crud::PAGE_DETAIL, Action::DELETE, function (Action $action) {
                return $action->setLabel('Supprimer');
            });
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('profile')
            ->add('bookAt')
            ->add('createdAt')
            ->add('product')
            ->add('period')
            ->add('isGroup')
            ->add('isPaid');
    }
}



