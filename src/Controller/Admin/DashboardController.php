<?php

namespace App\Controller\Admin;

use App\Entity\Location;
use App\Entity\Order;
use App\Entity\SupplyOrderItem;
use App\Entity\SystemCapability;
use App\Entity\TimeZone;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Experiment;
use App\Entity\Sample;
use App\Entity\Protocol;
use App\Entity\Task;
use App\Entity\User;
use App\Entity\SampleType;
use App\Entity\Section;
use App\Entity\Storage;
use App\Entity\StorageType;
use App\Entity\Study;
use App\Entity\SupplyOrder;
use App\Entity\Template;
use App\Entity\Group;
use App\Entity\File;
use App\Entity\TaskManagement;
use App\Entity\SystemSetting;
use App\Entity\Reservation;
use App\Entity\SampleSeries;
use App\Entity\Equipment;
use App\Entity\CatalogItem;
use App\Entity\ClientAppSetting;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use App\Entity\Project;
use App\Entity\ProtocolField;
use App\Entity\Variable;
use App\Entity\InventoryDummy;

class DashboardController extends AbstractDashboardController
{
    #[Route('/', name: 'admin')]
    public function index(): Response
    {
        //return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(ExperimentCrudController::class)->generateUrl());

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

    public function configureCrud(): Crud
    {
        return Crud::new()
            //->overrideTemplate('layout', 'LabOwlEasyAdminLayout.html.twig')
        ;
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Lab Owl')
            ->setLocales(['de', 'en']);
    }
    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToCrud('Inventory browser', 'fas fa-boxes', InventoryDummy::class);

        // Studies Section
        yield MenuItem::section('Studies');
        
        yield MenuItem::linkToCrud('Projects', 'fas fa-project-diagram', Project::class);
        yield MenuItem::linkToCrud('Studies', 'fas fa-book-reader', Study::class);
        yield MenuItem::linkToCrud('Protocols', 'fas fa-book', Protocol::class);
        yield MenuItem::linkToCrud('Experiments', 'fas fa-flask', Experiment::class);
        # if $_ENV is debug
        if ($_ENV['APP_ENV'] === 'dev') {
            yield MenuItem::section('DEBUG');
            yield MenuItem::linkToCrud('Experiment Sections', 'fas fa-list', Section::class);
            # add ProtocolField
            yield MenuItem::linkToCrud('Protocol Fields', 'fas fa-list', ProtocolField::class);
            # add Variable
            yield MenuItem::linkToCrud('Variables', 'fas fa-list', Variable::class);
        }

        // Samples and Protocols Section
        yield MenuItem::section('Samples and Storages');
        
        yield MenuItem::linkToCrud('Samples', 'fas fa-vial', Sample::class);
        # add SampleSeries
        yield MenuItem::linkToCrud('Sample Series', 'fas fa-vials', SampleSeries::class);
        yield MenuItem::linkToCrud('Sample Types', 'fas fa-tags', SampleType::class);
        yield MenuItem::linkToCrud('Storages', 'fas fa-box', Storage::class);
        yield MenuItem::linkToCrud('Storage Types', 'fas fa-boxes', StorageType::class);

        // Tasks and Users Section
        yield MenuItem::section('Tasks');

        yield MenuItem::linkToCrud('Tasks', 'fas fa-tasks', Task::class);
        yield MenuItem::linkToCrud('Task Management', 'fas fa-project-diagram', TaskManagement::class);

        // Supply and Orders
        yield MenuItem::section('Supply and Orders');
        yield MenuItem::submenu('Order Management')->setSubItems([
            MenuItem::linkToCrud('Supply Orders', 'fas fa-shopping-cart', SupplyOrder::class),
            MenuItem::linkToCrud('Supply Order Items', 'fas fa-shopping-cart', SupplyOrderItem::class),
            MenuItem::linkToCrud('Orders', 'fas fa-shopping-cart', Order::class),
            MenuItem::linkToCrud('Catalog Items', 'fas fa-shopping-cart', CatalogItem::class),
            MenuItem::linkToCrud('Reservations', 'fas fa-calendar-alt', Reservation::class),
            MenuItem::linkToCrud('Equipment', 'fas fa-tools', Equipment::class),
        ]);

        // System and Miscellaneous
        yield MenuItem::section('System and Miscellaneous');
        yield MenuItem::submenu('System Settings')->setSubItems([
            MenuItem::linkToCrud('Time Zones', 'fas fa-clock', TimeZone::class),
            MenuItem::linkToCrud('Settings', 'fas fa-cogs', ClientAppSetting::class)
        ]);
        yield MenuItem::linkToCrud('Files', 'fas fa-file-alt', File::class);
        yield MenuItem::linkToCrud('Users', 'fas fa-user', User::class);
        yield MenuItem::linkToCrud('Groups roles and permissions', 'fas fa-lock', Group::class);

    }

    
    
}
