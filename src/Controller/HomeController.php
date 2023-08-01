<?php

namespace App\Controller;

use App\Entity\User;
use App\Tenant\TenantManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class HomeController extends AbstractController
{
    public function __construct(private readonly TenantManager $tenantManager)
    {
    }

    #[Route('/', name: 'app_home')]
    public function index(#[CurrentUser] User $user): Response
    {
        return $this->render('home/index.html.twig', [
            'user_identifier' => $user->getUserIdentifier(),
            'tenant' => $this->tenantManager->getCurrentTenant()
        ]);
    }
}
