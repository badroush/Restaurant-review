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
        yield MenuItem::linkToDashboard('Tableau de bord', 'fa fa-home');

        if ($this->isGranted('ROLE_ADMIN')) {
            //  revenir a la page home
            yield MenuItem::linkToUrl('Retour à la page d accueil', 'fa fa-home', '/');
            yield MenuItem::linkToCrud('Restaurants', 'fas fa-list', Restaurants::class);
            yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-list', User::class);
            yield MenuItem::linkToCrud('Categories', 'fas fa-list', RestoCategories::class);
            yield MenuItem::linkToCrud('Gestion des jaimes', 'fas fa-list', Like::class);
            yield MenuItem::linkToCrud('Gestion des Commentaires', 'fas fa-list', Evaluation::class);
            yield MenuItem::linkToUrl('Statistiques', 'fas fa-chart-bar', $this->generateUrl('admin_statistics'));

        }
    
        if ($this->isGranted('ROLE_RESTAURATEUR')) {
            //  revenir a la page home
            yield MenuItem::linkToUrl('Retour à la page d accueil', 'fa fa-home', '/');
            yield MenuItem::linkToCrud('Mes Restaurants', 'fas fa-list', Restaurants::class);
            yield MenuItem::linkToCrud('Mes Likes', 'fas fa-list', Like::class);
            yield MenuItem::linkToCrud('Mes Commentaires', 'fas fa-list', Evaluation::class);
        }
    }
}