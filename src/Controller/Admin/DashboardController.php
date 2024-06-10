<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Product;
use Symfony\Component\HttpFoundation\Response;
use App\Controller\Admin\ProductCrudController;
use App\Entity\Booking;
use App\Entity\Gallery;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
       

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
         $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
         return $this->redirect($adminUrlGenerator->setController(ProductCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Bootcamps Centurion 🪖');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa-solid fa-screwdriver-wrench');
        yield MenuItem::section('Gestion des BootCamps');
        yield MenuItem::linkToCrud('Les BootCamps', 'fa-solid fa-campground', Product::class);
        yield MenuItem::linkToCrud('Les Réservations', 'fa-solid fa-calendar-check', Booking::class);
        yield MenuItem::linkToCrud('Galerie Photos', 'fa-solid fa-image', Gallery::class);
        yield MenuItem::section('Gestion des Utilisateurs');
        yield MenuItem::linkToCrud('Utilisateur', 'fa fa-user', User::class);
        yield MenuItem::section('Vue Coté Utilisateur');
        yield MenuItem::linkToUrl('Retour au site', 'fa fa-home', '/');
        yield MenuItem::linkToUrl('Vue profil', 'fa-regular fa-address-card', '/profile');

        
    }
}
