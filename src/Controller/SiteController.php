<?php

namespace App\Controller;

use App\Repository\GalleryRepository;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SiteController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();
        return $this->render('site/index.html.twig', [
            'products' => $products,
        ]);
    }
    #[Route('/gallery', name: 'gallery_index')]
    public function pageGallery(GalleryRepository $galleryRepository): Response
    {
        $photos = $galleryRepository->findAll();

        return $this->render('site/gallery.html.twig', [
            'photos' => $photos,
        ]);
    }
}
