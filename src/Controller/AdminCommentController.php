<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\AdminCommentType;
use App\Service\PaginatorService;
use App\Repository\CommentRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminCommentController extends AbstractController
{
    /**
     * @Route("/admin/comments/{page<\d+>?1}", name="admin_comment_index")
     */
    public function index(CommentRepository $repo, $page, PaginatorService $paginator)
    {
        $paginator->setEntityClass(Comment::class)
            ->setLimit(10)
            ->setcurrentPage($page);

        return $this->render('admin/comment/index.html.twig', [
            'paginator' => $paginator,
        ]);
    }

    /**
     * Edit comment
     * 
     * @Route("/admin/comments/{id}/edit", name="admin_comment_edit")
     *
     * @param Comment $comment
     * @param ObjectManager $manager
     * @param Request $request
     * @return Response
     */
    public function edit(Comment $comment, Request $request, ObjectManager $manager) {
        $form = $this->createForm(AdminCommentType::class, $comment);

        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $manager->persist($comment);
            $manager->flush();

            $this->addFlash(
                "success",
                "Votre commentaire pour l'annonce <strong>{$comment->getAd()->getTitle()}</strong> a bien été modifié !"
            );

            return $this->redirectToRoute("admin_comment_index");
        }

        return $this->render("admin/comment/edit.html.twig", [
            'comment' => $comment,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Delete comment
     * 
     * @Route("/admin/comments/{id}/delete", name="admin_comment_delete")
     *
     * @param Comment $comment
     * @param ObjectManager $manager
     * @return Response
     */
    public function delete(Comment $comment, ObjectManager $manager) {
        $manager->remove($comment);
        $manager->flush();

        $this->addFlash(
            "success",
            "Votre commentaire pour l'annonce <strong>{$comment->getAd()->getTitle()}</strong> a bien été supprimer"
        );

        return $this->redirectToRoute("admin_comments_index");
    }
}
