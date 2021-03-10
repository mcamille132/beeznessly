<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Entity\Contact;
use App\Repository\DownloadRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\ContactRepository;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @Route("/entrepreneur", name="entrepreneur_")
 * @IsGranted("ROLE_ENTREPRENEUR")
 */
class EntrepreneurController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        $user = $this->getUser();
        if ($user->getIsValidated() == false) {
            return $this->render('entrepreneur/validation.html.twig', [
                'user' => $user,
            ]);
        }
        return $this->render('entrepreneur/index.html.twig', [
            'user' => $user,
        ]);
    }

      /**
     * @Route("/profil", name="profil")
     */
    public function profil(): Response
    {
        $user = $this->getUser();
        if ($user->getIsValidated() == false) {
            return $this->render('entrepreneur/validation.html.twig', [
                'user' => $user,
            ]);
        }
        return $this->render('entrepreneur/profil.html.twig', [
            'user' => $user,
        ]);
    }

     /**
     * @Route("/messagerie", methods={"GET"}, name="messagerie")
     */
    public function message(
        ContactRepository $contactRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $user = $this->getUser();
        if ($user->getIsValidated() == false) {
            return $this->render('entrepreneur/validation.html.twig', [
                'user' => $user,
            ]);
        }
        $email = $user->getEmail();
        $contacts = $contactRepository->findByEntrepreuneurEmail($email);

        // Paginate the results of the query
        $messages = $paginator->paginate(
            // Doctrine Query, not results
            $contacts,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            5
        );

        return $this->render('entrepreneur/messagerie.html.twig', [
            'contacts' => $messages,
            'user' => $user = $this->getUser()
        ]);
    }

    /**
     * @Route("/messagerie/{id}", name="messagerie_show", methods={"GET"})
     */
    public function show(Contact $contact): Response
    {
        $user = $this->getUser();
        if ($user->getIsValidated() == false) {
            return $this->render('entrepreneur/validation.html.twig', [
                'user' => $user,
            ]);
        }

        return $this->render('entrepreneur/show_message.html.twig', [
            'contact' => $contact,
            'user' => $user = $this->getUser()
        ]);
    }

      /**
     * @Route("/ebook", methods={"GET"}, name="ebook")
     */
    public function ebooks(
        DownloadRepository $donwloadRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $user = $this->getUser();
        if ($user->getIsValidated() == false) {
            return $this->render('entrepreneur/validation.html.twig', [
                'user' => $user,
            ]);
        }

        $downloads = $donwloadRepository->findBy(['user' => $user]);

        // Paginate the results of the query
        $ebooks = $paginator->paginate(
            // Doctrine Query, not results
            $downloads,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            5
        );

        return $this->render('entrepreneur/ebook.html.twig', [
            'downloads' => $ebooks,
            'user' => $user = $this->getUser()
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user): Response
    {
        $user = $this->getUser();
        if ($user->getIsValidated() == false) {
            return $this->render('entrepreneur/validation.html.twig', [
                'user' => $user,
            ]);
        }

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('entrepreneur_profil');
        }

        return $this->render('entrepreneur/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
}
