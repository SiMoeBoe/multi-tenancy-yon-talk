<?php

namespace App\Controller;

use App\Entity\Landlord\User;
use App\Entity\Tenant\BlogPost;
use App\FormType\BlogPostType;
use App\Repository\BlogPostRepository;
use App\Tenant\TenantManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class HomeController extends AbstractController
{
    public function __construct(private readonly TenantManager $tenantManager)
    {
    }

    #[Route('/', name: 'app_home')]
    public function index(#[CurrentUser] User $user, Request $request, BlogPostRepository $blogPostRepository): Response
    {
        $blogPost = new BlogPost();
        $form = $this->createForm(BlogPostType::class, $blogPost);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $blogPost = $form->getData();

            $blogPostRepository->save($blogPost, true);

            return $this->redirect($request->getUri());
        }

        return $this->render('home/index.html.twig', [
            'user' => $user,
            'tenant' => $this->tenantManager->getCurrentTenant(),
            'form' => $form,
            'blogPosts' => $blogPostRepository->findAll()
        ]);
    }
}
