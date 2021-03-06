<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Form\AdminBookingType;
use App\Service\PaginatorService;
use App\Repository\BookingRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminBookingController extends AbstractController
{
    /**
     * @Route("/admin/bookings/{page<\d+>?1}", name="admin_booking_index")
     */
    public function index(BookingRepository $repo, $page, PaginatorService $paginator)
    {
        $paginator->setEntityClass(Booking::class)
            ->setLimit(10)
            ->setcurrentPage($page);

        return $this->render('admin/booking/index.html.twig', [
            'paginator' => $paginator,
        ]);
    }

    /**
     * Edit booking
     *
     * @Route("/admin/bookings/{id}/edit", name="admin_booking_edit")
     *
     * @param Booking $booking
     * @param Request $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function edit(Booking $booking, Request $request, ObjectManager $manager)
    {
        $form = $this->createForm(AdminBookingType::class, $booking);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $booking->setAmount(0);

            $manager->persist($booking);
            $manager->flush();

            $this->addFlash(
                "success",
                "La réservation n° {$booking->getId()} a bien été modifiée !"
            );

            return $this->redirectToRoute("admin_booking_index");
        }

        return $this->render("admin/booking/edit.html.twig", [
            'form' => $form->createView(),
            'booking' => $booking,
        ]);
    }

    /**
     * Delete booking
     *
     * @Route("/admin/bookings/{id}/delete", name="admin_booking_delete")
     *
     * @param Booking $booking
     * @param ObjectManager $manager
     * @return Response
     */
    public function delete(Booking $booking, ObjectManager $manager)
    {
        $manager->remove($booking);
        $manager->flush();

        $this->addFlash(
            'success',
            "La réservations n°{$booking->getId()} a bien été supprimer !"
        );

        return $this->redirectToRoute("admin_booking_index");
    }
}
