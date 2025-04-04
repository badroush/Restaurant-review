<?php

namespace App\Controller\Admin;
use App\Entity\Restaurants;
use App\Entity\User;
use App\Entity\RestoCategories;
use App\Entity\Like;
use App\Entity\Evaluation;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
     
      
         $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
         return $this->redirect($adminUrlGenerator->setController(RestaurantsCrudController::class)->generateUrl());

        
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Nota Resto');
    }

    public function configureMenuItems(): iterable
    {
         yield MenuItem::linkToCrud('Restaurants', 'fas fa-list', Restaurants::class);
         yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-list', User::class);
         yield MenuItem::linkToCrud('Categories', 'fas fa-list', RestoCategories::class);
         yield MenuItem::linkToCrud('Gestion des jaimes', 'fas fa-list', Like::class);
         yield MenuItem::linkToCrud('Gestion des Commenatires', 'fas fa-list', Evaluation::class);
    }
}