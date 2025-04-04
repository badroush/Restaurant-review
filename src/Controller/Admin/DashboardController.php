<?php

namespace App\Controller\Admin;
use App\Entity\Restaurants;
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
    }
}
