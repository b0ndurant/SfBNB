<?php

namespace App\Controller;

use App\Entity\Role;
use App\Entity\User;
use App\Form\AdminUserType;
use App\Service\PaginatorService;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminUserController extends AbstractController
{
    /**
     * Show all users
     *
     * @Route("/admin/users/{page<\d+>?1}", name="admin_user_index")
     */
    public function index(UserRepository $repo, $page, PaginatorService $paginator)
    {
        $paginator->setEntityClass(User::class)
            ->setLimit(10)
            ->setcurrentPage($page);

        return $this->render('admin/user/index.html.twig', [
            'paginator' => $paginator,
        ]);
    }

    /**
     * Edit User
     *
     * @Route("/admin/users/{id}/edit", name="admin_user_edit")
     *
     * @param User $user
     * @param Request $request
     * @param ObjectManager $manager
     * @return void
     */
    public function edit(User $user, Request $request, ObjectManager $manager)
    {
        $form = $this->createForm(AdminUserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->flush();

            $this->addFlash(
                "success",
                "L'utilisateur <strong>{$user->getFullName()}</strong> a bien été mofidié"
            );

            return $this->redirectToRoute("admin_user_index");
        }

        return $this->render("admin/user/edit.html.twig", [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }
}
