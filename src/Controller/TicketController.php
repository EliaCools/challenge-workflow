<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Status;
use App\Entity\Ticket;
use App\Entity\User;
use App\Form\CommentType;
use App\Form\DeassignFormType;
use App\Form\TicketAssignType;
use App\Form\TicketCloseType;
use App\Form\TicketEscalateType;
use App\Form\TicketReopenType;
use App\Form\TicketType;
use App\Form\TicketWontFixType;
use App\Repository\StatusRepository;
use App\Repository\TicketRepository;
use App\Repository\UserRepository;
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
    private UserRepository $userRepository;
//    private $name;
//    private $verified;
    /**
     * @var UrlGeneratorInterface
     * */
    private UrlGeneratorInterface $urlGenerator;
    private StatusRepository $statusRepository;
    private TicketRepository $ticketRepository;

    public function __construct(UserRepository $userRepository, UrlGeneratorInterface $urlGenerator, StatusRepository $statusRepository, TicketRepository $ticketRepository)
    {
        $this->userRepository = $userRepository;
        $this->urlGenerator = $urlGenerator;
        $this->statusRepository = $statusRepository;
        $this->ticketRepository = $ticketRepository;
    }

    #[Route('/', name: 'ticket_index', methods: ['GET'])]
    public function index(TicketRepository $ticketRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();
        $roles = $user->getRoles();

        if (in_array('ROLE_CUSTOMER', $roles)) {
            return $this->render('ticket/index.html.twig', [
                'tickets' => $user->getTicketsCreated(),
                'title' => 'My Tickets'
            ]);
        } elseif (in_array('ROLE_FIRST_LINE_AGENT', $roles) || in_array('ROLE_SECOND_LINE_AGENT',
                $roles)) {
            return $this->render('ticket/index.html.twig', [
                'tickets' => $user->getTickets(),
                'title' => 'My Tickets'
            ]);
        } elseif (in_array('ROLE_MANAGER', $roles)) {
            $ticketDeassign = $this->createForm(DeassignFormType::class, null, [
                'action' => $this->urlGenerator->generate('ticket_deassign')
            ]);
            return $this->render('ticket/index.html.twig', [
                'tickets' => $ticketRepository->findAll(),
                'title' => 'All Tickets',
                'ticketDeassign' => $ticketDeassign->createView()
            ]);
        }
    }

    #[Route('/opentickets', name: 'open_tickets', methods: ['GET'])]
    public function openTickets(TicketRepository $ticketRepository, StatusRepository $statusRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_EMPLOYEE');

        $statusId = $statusRepository->findBy(['name' => 'open']);
        $tickets = $ticketRepository->findBy(['status' => $statusId,
            'isEscalated' => false]);

        if ($this->isGranted('ROLE_SECOND_LINE_AGENT')) {
            $tickets = $ticketRepository->findBy(['status' => $statusId,
                'isEscalated' => true]);

            return $this->render('ticket/index.html.twig', [
                'tickets' => $tickets,
                'title' => 'Escalated Tickets'
            ]);

        }

        return $this->render('ticket/index.html.twig', [
            'tickets' => $tickets,
            'title' => 'Open Tickets'
        ]);
    }

    #[Route('/new', name: 'ticket_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CUSTOMER');

        $statusName = $this->statusRepository->findBy(['name' => 'open']);


        $ticket = new Ticket();
        $form = $this->createForm(TicketType::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ticket->setDateCreated(new \DateTime());
            $ticket->setCreatedBy($this->getUser());
            $ticket->setStatus($statusName[0]);
            $ticket->setPriority('undefined');
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
    public function show(Ticket $ticket, StatusRepository $statusRepository, TicketRepository $ticketRepository): Response
    {
        $wontFixStatus = $statusRepository->findBy(['name' => 'wont_fix']);
        $wontFixId = $wontFixStatus[0]->getId();

        $user = $this->getUser();
        $userId = $this->getUser()->getId();
        $roles = $user->getRoles();

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $statusId = $statusRepository->findBy(['name' => 'closed']);
        $tickets = $ticketRepository->findBy(['status' => $statusId]);

        $form = $this->createForm(CommentType::class, null, [
            'action' => $this->urlGenerator->generate('ticket_add_comment', [
                'roles' => $this->getUser()->getRoles(),
                'id' => $ticket->getId()])
        ]);

        $ticketEscalate = $this->createForm(TicketEscalateType::class, null, [
            'action' => $this->urlGenerator->generate('ticket_escalate', [
                'id' => $ticket->getId()])
        ]);

        $ticketReopen = $this->createForm(TicketReopenType::class, null, [
            'action' => $this->urlGenerator->generate('ticket_reopen', [
                'id' => $ticket->getId()])
        ]);

        $statusTicketForm = $this->createForm(TicketCloseType::class, null, [
            'action' => $this->urlGenerator->generate('ticket_close', [
                'id' => $ticket->getId()])
        ]);
        $ticketAssignForm = $this->createForm(TicketAssignType::class, null, [
            'action' => $this->urlGenerator->generate('ticket_assign', [
                'id' => $ticket->getId()])
        ]);


        return $this->render('ticket/show.html.twig', [
            'ticket' => $ticket,
            'comment_form' => $form->createView(),
            'ticket_close' => $statusTicketForm->createView(),
            'ticket_reopen' => $ticketReopen->createView(),
            'ticket_escalate' => $ticketEscalate->createView(),
            'ticket_assign' => $ticketAssignForm->createView(),
            'wontFixId' => $wontFixId,
            'user' => $user
        ]);
    }

    #[Route('ticket/{id}/addcomment', name: 'ticket_add_comment')]
    public function addComment(Ticket $ticket, Request $request): Response
    {
        //Add logic to not allow agent to send message if ticket not assigned to him
        //Add logic to differentiate who posted the comment (customer: , Agent: )
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $roles = $user->getRoles();

        if (!$this->getUser() instanceof User) {
            throw new \DomainException('You are not allowed to send a message.');
        }

        $comment = new Comment($this->getUser(), $ticket);

        $form = $this->createForm(CommentType::class, $comment, ['roles' => $this->getUser()->getRoles()]);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $comment->setIsPublic(true);
            if ($comment->getTicket()->getAssignedTo() !== null) {
                $comment->getTicket()->setStatus($this->statusRepository->findOneBy(['name' => 'in_progress']));
            }

            if (in_array('ROLE_EMPLOYEE', $roles)) {

                $form->get('isPublic')->getData();
                $comment->setIsPublic($form->get('isPublic')->getData());

                if ($comment->getIsPublic() === true) {
                    $comment->getTicket()->setStatus($this->statusRepository->findOneBy(['name' => 'waiting_for_customer']));
                }

            }
            $this->getDoctrine()->getManager()->persist($comment);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Your message was sent.');
        }
        return $this->redirectToRoute('ticket', ['id' => $ticket->getId()]);

    }

    #[Route('ticket/{id}/reopenticket', name: 'ticket_reopen')]
    public function reopenTicket(Ticket $ticket, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_CUSTOMER');

        $status = $this->statusRepository->findOneBy(['name' => 'open']);

        $formStatus = $this->createForm(TicketReopenType::class);
        $formStatus->handleRequest($request);

        if ($formStatus->isSubmitted() && $formStatus->isValid()) {
            $increased = $ticket->getAssignedTo()->increaseNumberReopenedTickets();
            $ticket->getAssignedTo();
            $ticket->getAssignedTo()->setNumberReopenedTickets($increased);


            $ticket->setStatus($status);
            $ticket->setDateClosed(null);
            $this->getDoctrine()->getManager()->persist($status);
            $this->getDoctrine()->getManager()->flush();


            $this->addFlash('success', 'Your ticket was reopened.');
        }
        return $this->redirectToRoute('ticket', ['id' => $ticket->getId()]);

    }

    #[Route('ticket/{id}/closeticket', name: 'ticket_close')]
    public function closeTicket(Ticket $ticket, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_EMPLOYEE');

        $status = $this->statusRepository->findOneBy(['name' => 'closed']);

        $formStatus = $this->createForm(TicketCloseType::class);
        $formStatus->handleRequest($request);


        if ($formStatus->isSubmitted() && $formStatus->isValid()) {
            $ticket->setStatus($status);
            $ticket->setDateClosed(new \DateTime());
            $this->getDoctrine()->getManager()->persist($status);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'The ticket was successfully closed.');
        }
        return $this->redirectToRoute('ticket', ['id' => $ticket->getId()]);

    }

    #[Route('ticket/{id}/escalateticket', name: 'ticket_escalate')]
    public function EscalateTicket(Ticket $ticket, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_EMPLOYEE');

        $formTicketEscalate = $this->createForm(TicketEscalateType::class);
        $formTicketEscalate->handleRequest($request);

        $openStatus = $this->statusRepository->findOneBy(['name' => 'open']);
        if ($formTicketEscalate->isSubmitted() && $formTicketEscalate->isValid()) {
            $ticket->setIsEscalated(true);
            $ticket->setAssignedTo(null);
            $ticket->setStatus($openStatus);
            $this->getDoctrine()->getManager()->persist($ticket);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'The ticket was sucessfully escalated.');
        }
        return $this->redirectToRoute('ticket_index');

    }

    #[Route('/{id}/edit', name: 'ticket_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Ticket $ticket): Response
    {
        $form = $this->createForm(TicketType::class, $ticket, [
            'is_edit' => true,
        ]);
        $form->handleRequest($request);

        $statusName = $this->statusRepository->findBy(['name' => 'wont_fix']);


        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('wontFix')->getData() === true) {
                $ticket->setStatus($statusName[0]);
            }

            $this->getDoctrine()->getManager()->flush();

            if ($form->get('wontFix')->getData() === true) {
                return $this->redirectToRoute('ticket_wont_fix', ['id' => $ticket->getId()]);
            }


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


    #[Route('ticket/{id}/wontfix', name: 'ticket_wont_fix')]
    public function wontFix(Ticket $ticket, Request $request): Response
    {

        $form = $this->createForm(CommentType::class);

        return $this->render('comment/wontfixreason.html.twig', [
            'form' => $form->createView()
        ]);

    }


    #[Route('ticket/{id}/assignTicket', name: 'ticket_assign')]
    public function assignTicket(Ticket $ticket, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_EMPLOYEE');

        $formTicketEscalate = $this->createForm(TicketAssignType::class);
        $formTicketEscalate->handleRequest($request);

        $statusId = $this->statusRepository->findOneBy(['name' => 'in_progress']);

        if ($formTicketEscalate->isSubmitted() && $formTicketEscalate->isValid()) {
            $ticket->setAssignedTo($this->getUser());
            $ticket->setStatus($statusId);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'The ticket was successfully assigned');
        }
        return $this->redirectToRoute('ticket', ['id' => $ticket->getId()]);

    }

    #[Route('ticket/deassignTicket', name: 'ticket_deassign')]
    public function deassignTickets(Ticket $ticket, Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_MANAGER');

        $deassignForm = $this->createForm(DeassignFormType::class);
        $deassignForm->handleRequest($request);

        $statusId = $this->statusRepository->findOneBy(['name' => 'open']);
        $allTickets = $this->ticketRepository->findAll();

        if ($deassignForm->isSubmitted() && $deassignForm->isValid()) {
            foreach ($allTickets as $ticket) {
                $ticket->setAssignedTo(null);
                $ticket->setStatus($statusId);
            }
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'All tickets were succesfully deassigned.');
        }
        return $this->redirectToRoute('ticket_index');

    }

}




// 3 templates. Index customer.twig, ...
// if statements based on user role to render right twig
