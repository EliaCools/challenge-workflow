<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Status;
use App\Entity\Ticket;
use App\Entity\User;
use App\Form\CommentType;
use App\Form\TicketReopenType;
use App\Form\TicketType;
use App\Repository\StatusRepository;
use App\Repository\TicketRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/ticket')]
class TicketController extends AbstractController
{
//    private $session;
//    private $userRepository;
//    private $name;
//    private $verified;
    /**
     * @var UrlGeneratorInterface
     * */
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    #[Route('/', name: 'ticket_index', methods: ['GET'])]
    public function index(TicketRepository $ticketRepository): Response
    {
        $user = $this->getUser();
        $roles = $user->getRoles();

        if (in_array('ROLE_CUSTOMER', $roles)) {
            return $this->render('ticket/index.html.twig', [
                'tickets' => $user->getTicketsCreated(),
            ]);
        } elseif (in_array('ROLE_FIRST_LINE_AGENT', $roles) || in_array('ROLE_SECOND_LINE_AGENT',
                $roles)) {
            return $this->render('ticket/index.html.twig', [
                'tickets' => $user->getTickets(),
            ]);
        } elseif (in_array('ROLE_MANAGER', $roles)) {
            return $this->render('ticket/index.html.twig', [
                'tickets' => $ticketRepository->findAll(),
            ]);
        }
    }

    #[Route('/opentickets', name: 'open_tickets', methods: ['GET'])]
    public function openTickets(TicketRepository $ticketRepository, StatusRepository $statusRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_EMPLOYEE');

        $statusId = $statusRepository->findBy(['name' => 'open']);
        $tickets = $ticketRepository->findBy(['status' => $statusId]);

        return $this->render('ticket/index.html.twig', [
            'tickets' => $tickets,
        ]);
    }

    #[Route('/new', name: 'ticket_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $ticket = new Ticket();
        $form = $this->createForm(TicketType::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ticket->setDateCreated(new \DateTime());
            $ticket->setCreatedBy($this->getUser());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($ticket);
            $entityManager->flush();

            return $this->redirectToRoute('ticket_index');
        }

        return $this->render('ticket/new.html.twig', [
            'ticket' => $ticket,
            'form' => $form->createView(),
        ]);
    }

    #[Route('ticket/{id}', name: 'ticket')]
    public function show(Ticket $ticket, StatusRepository $statusRepository): Response
    {
        $form = $this->createForm(CommentType::class, null, [
            'action' => $this->urlGenerator->generate('ticket_add_comment', [
                'id' => $ticket->getId()])
        ]);

        $formReopen = $this->createForm(TicketReopenType::class, null, [
            'action' => $this->urlGenerator->generate('ticket_reopen', [
                'id' => $ticket->getId()])
        ]);

        return $this->render('ticket/show.html.twig', [
            'ticket' => $ticket,
            'comment_form' => $form->createView(),
            'ticket_reopen_form' => $formReopen->createView()
        ]);
    }

    #[Route('ticket/{id}/addcomment', name: 'ticket_add_comment')]
    public function addComment(Ticket $ticket, Request $request): Response
    {
        //Add logic to not allow agent to send message if ticket not assigned to him
        //Add logic to differentiate who posted the comment (customer: , Agent: )
//        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->getUser() instanceof User) {
            throw new \DomainException('You are not allowed to send a message.');
        }

        $comment = new Comment($this->getUser(), $ticket);

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->persist($comment);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Your message was sent.');
        }

        return $this->redirectToRoute('ticket', ['id' => $ticket->getId()]);

    }

    #[Route('ticket/{id}/reopenticket', name: 'ticket_reopen')]
    public function reopenTicket(Ticket $ticket, Request $request): Response
    {

        $status = new Status();
        $formStatus = $this->createForm(TicketReopenType::class);
        $formStatus->handleRequest($request);

//        if (in_array('ROLE_CUSTOMER', $roles)) {
//            throw new \DomainException('You are not allowed to reopen a closed ticket.');

        if ($formStatus->isSubmitted() && $formStatus->isValid()) {
            $status->setName('closed');
            $this->getDoctrine()->getManager()->persist($status);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Your ticket was reopened.');

        }
        return $this->redirectToRoute('ticket', ['id' => $ticket->getId()]);
//        } else {
//            if ($status->getName() === 'open') {
//                if ($formStatus->isSubmitted() && $formStatus->isValid()) {
//                    $status->setName('closed');
//                    $this->getDoctrine()->getManager()->persist($status);
//                    $this->getDoctrine()->getManager()->flush();
//
//                    $this->addFlash('success', 'The customer ticket was closed.');
//                }
//            }
//        }
//        return $this->redirectToRoute('ticket', ['id' => $ticket->getId()]);
    }

    #[Route('/{id}/edit', name: 'ticket_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Ticket $ticket): Response
    {
        $form = $this->createForm(TicketType::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('ticket_index');
        }

        return $this->render('ticket/edit.html.twig', [
            'ticket' => $ticket,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'ticket_delete', methods: ['POST'])]
    public function delete(Request $request, Ticket $ticket): Response
    {
        if ($this->isCsrfTokenValid('delete' . $ticket->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($ticket);
            $entityManager->flush();
        }

        return $this->redirectToRoute('ticket_index');
    }
}

// 3 templates. Index customer.twig, ...
// if statements based on user role to render right twig
