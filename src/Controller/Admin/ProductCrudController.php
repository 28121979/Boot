<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Controller\VichImageField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;

class ProductCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Product::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Bootcamp')
            ->setEntityLabelInPlural('Bootcamps')
            ->setPageTitle(Crud::PAGE_INDEX, 'Les Bootcamps')
            ->setPageTitle(Crud::PAGE_NEW, 'Add Bootcamp')
            ->setPageTitle(Crud::PAGE_EDIT, 'Edit Bootcamp')
            ->setPageTitle(Crud::PAGE_DETAIL, 'Details of Bootcamp');
    }
    
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('forfait'),
            TextareaField::new('description'),
            ChoiceField::new('duration', 'Durée')
                ->setChoices([
                    '1/2 journée (4 heures)' => (string)Product::DURATION_HALF_DAY,
                    '1 journée (8 heures)' => (string)Product::DURATION_FULL_DAY,
                    '2 jours (16 heures)' => (string)Product::DURATION_TWO_DAYS,
                ]),
            NumberField::new('tarifBase', 'Tarif de base')->setNumDecimals(2),
            ImageField::new('imageName', 'Photo Bootcamps')
            ->setBasePath('/images/bootcamps')
            ->setUploadDir('public/images/bootcamps')  // définissez le répertoire d'upload ici
            ->onlyOnIndex(),
    
            VichImageField::new('imageFile', 'Photo Bootcamps')
            ->setTemplatePath('admin/field/vich_image_widget.html.twig') // chemin vers votre nouveau template personnalisé
            ->hideOnIndex(),
            ImageField::new('bgName', 'Background Bootcamps')
            ->setBasePath('/images/bootcamps')
            ->setUploadDir('public/images/bootcamps')  // définissez le répertoire d'upload ici
            ->onlyOnIndex(),
    
            VichImageField::new('bgFile', 'Background Bootcamps')
            ->setTemplatePath('admin/field/vich_image_widget.html.twig') // chemin vers votre nouveau template personnalisé
            ->hideOnIndex()
        ];
    }
    
}
