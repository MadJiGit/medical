<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\ContactRequest;
use App\Entity\Manufacturer;
use App\Entity\Product;
use App\Entity\Sample;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);

        if ($this->isGranted('ROLE_ADMIN')) {
            $url = $adminUrlGenerator
                ->setController(UserCrudController::class)
                ->generateUrl();
        } else {
            $url = $adminUrlGenerator
                ->setController(ContactRequestCrudController::class)
                ->generateUrl();
        }

        return $this->redirect($url);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Medical');
    }

    public function configureAssets(): Assets
    {
        return Assets::new()
            ->addCssFile('css/admin.css')
            ->addJsFile('js/admin-columns.js');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

        if ($this->isGranted('ROLE_ADMIN')) {
            yield MenuItem::linkToCrud('Users', 'fa fa-users', User::class);
        }

        yield MenuItem::linkToCrud('Contact Requests', 'fa fa-envelope', ContactRequest::class);
        yield MenuItem::linkToCrud('Samples', 'fa fa-box', Sample::class);

        yield MenuItem::section('Catalog');
        yield MenuItem::linkToCrud('Categories', 'fa fa-folder', Category::class);
        yield MenuItem::linkToCrud('Products', 'fa fa-medkit', Product::class);
        yield MenuItem::linkToCrud('Manufacturers', 'fa fa-industry', Manufacturer::class);

        yield MenuItem::section();
        yield MenuItem::linkToLogout('Logout', 'fa fa-sign-out');
    }
}
