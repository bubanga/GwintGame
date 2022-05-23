<?php

namespace App\Controller\Admin;

use App\Entity\Card;
use App\Entity\Deck;
use App\Entity\Game;
use App\Entity\Player;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        return parent::index();
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('GwintGame');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Cards', 'fas fa-list', Card::class);
        yield MenuItem::linkToCrud('Games', 'fas fa-list', Game::class);

        yield MenuItem::linkToCrud('Decks', 'fas fa-list', Deck::class);

        yield MenuItem::linkToCrud('Players', 'fas fa-users', Player::class);

        yield MenuItem::linkToCrud('Users', 'fas fa-users', User::class);

    }
}
