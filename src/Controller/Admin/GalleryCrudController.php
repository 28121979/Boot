<?php

namespace App\Controller\Admin;

use App\Entity\Gallery;
use App\Controller\VichImageField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class GalleryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Gallery::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Photo')
            ->setEntityLabelInPlural('Photos')
            ->setPageTitle(Crud::PAGE_INDEX, 'La Galerie des Bootcamps')
            ->setPageTitle(Crud::PAGE_NEW, 'Add Photo')
            ->setPageTitle(Crud::PAGE_EDIT, 'Edit Photo')
            ->setPageTitle(Crud::PAGE_DETAIL, 'Details Photo');
    }
    
    public function configureFields(string $pageName): iterable
    {
        return [
            ImageField::new('imageName', 'Photo Bootcamps')
            ->setBasePath('/images/gallery')
            ->setUploadDir('public/images/gallery')  // définissez le répertoire d'upload ici
            ->onlyOnIndex(),
    
            VichImageField::new('imageFile', 'Image File')
            ->setTemplatePath('admin/field/vich_image_widget.html.twig') // chemin vers votre nouveau template personnalisé
            ->hideOnIndex(),
            TextField::new('altText','Alt Text'),
            AssociationField::new('idBootcamps')
        ];
    }
    
}
