<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Ebook;
use App\Form\UserType;
use App\Entity\Contact;
use App\Form\EbookType;
use App\Form\ExpertType;
use App\Service\SlugifyService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Vich\UploaderBundle\Handler\DownloadHandler;
use App\Repository\DownloadRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\ContactRepository;

/**
 * @Route("/prestataire", name="expert_")
 * @IsGranted("ROLE_EXPERT")
 */
class ExpertController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        $user = $this->getUser();
        if ($user->getIsValidated() == false) {
            return $this->render('expert/validation.html.twig', [
                'user' => $user,
            ]);
        }

        $nbEbooksUser = count($user->getEbooks());

        return $this->render('expert/index.html.twig', [
            'user' => $user,
            'nbEbooks' => $nbEbooksUser
        ]);
    }

    /**
     * @Route("/profil", name="profil")
     */
    public function profil(): Response
    {
        $user = $this->getUser();
        if ($user->getIsValidated() == false) {
            return $this->render('expert/validation.html.twig', [
                'user' => $user,
            ]);
        }
        return $this->render('expert/profile/profil.html.twig', [
            'user' => $user,
        ]);
    }

     /**
     * @Route("/messagerie", methods={"GET"}, name="messagerie")
     */
    public function message(
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $user = $this->getUser();
        if ($user->getIsValidated() == false) {
            return $this->render('expert/validation.html.twig', [
                'user' => $user,
            ]);
        }

        $email = $user->getContacts();

          // Paginate the results of the query
          $messages = $paginator->paginate(
              // Doctrine Query, not results
              $email,
              // Define the page parameter
              $request->query->getInt('page', 1),
              // Items per page
              5
          );

        return $this->render('expert/message/messagerie.html.twig', [
            'contacts' => $messages,
            'user' => $user = $this->getUser()
        ]);
    }

    /**
     * @Route("/messagerie/{id}", name="messagerie_show", methods={"GET"})
     */
    public function showMessage(Contact $contact): Response
    {
        $user = $this->getUser();
        if ($user->getIsValidated() == false) {
            return $this->render('expert/validation.html.twig', [
                'user' => $user,
            ]);
        }

        return $this->render('expert/message/show_message.html.twig', [
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
            return $this->render('expert/validation.html.twig', [
                'user' => $user,
            ]);
        }

        $ebooks = $user->getEbooks();
        $nbDownloadByEbook = [];
        foreach ($ebooks as $ebook) {
            $downloads = $donwloadRepository->findBy(['ebook' => $ebook]);
            $nbDownloads = count($downloads);
            $nbDownloadByEbook[$ebook->getId()] = $nbDownloads;
        }

        // Paginate the results of the query
        $allEbooks = $paginator->paginate(
            // Doctrine Query, not results
            $ebooks,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            5
        );

        return $this->render('expert/ebook/ebook.html.twig', [
            'ebooks' => $allEbooks,
            'nbDownloadByEbook' => $nbDownloadByEbook,
            'user' => $user
        ]);
    }

    /**
     * @Route("/ebook/{id}", name="ebook_show", methods={"GET"})
     */
    public function showEbook(Ebook $ebook, DownloadRepository $donwloadRepository): Response
    {
        $downloads = $donwloadRepository->findBy(['ebook' => $ebook]);
        $nbDownloads = count($downloads);

        return $this->render('expert/ebook/ebook_show.html.twig', [
            'ebook' => $ebook,
            'nbDownloads' => $nbDownloads,
            'user' => $this->getUser()
        ]);
    }

    /**
     * @Route("/ebook/{id}/telechargements", name="ebook_download_show", methods={"GET"})
     */
    public function showEbookDownloads(Ebook $ebook, DownloadRepository $donwloadRepository): Response
    {
        $downloads = $donwloadRepository->findBy(['ebook' => $ebook]);
        $entrepreneurs = [];
        $downloadedAt = [];
        foreach ($downloads as $download) {
            $entrepreneurs[] = $download->getUser();
            $downloadedAt[$download->getUser()->getId()] = $download->getDownloadedAt();
        }

        return $this->render('expert/ebook/ebook_download_show.html.twig', [
            'ebook' => $ebook,
            'entrepreneurs' => $entrepreneurs,
            'downloadedAt' => $downloadedAt,
            'user' => $this->getUser()
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user): Response
    {
        $user = $this->getUser();
        if ($user->getIsValidated() == false) {
            return $this->render('expert/validation.html.twig', [
                'user' => $user,
            ]);
        }

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('expert_profil');
        }

        return $this->render('expert/profile/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/ebook/new/add", name="ebook_new", methods={"GET","POST"})
     */
    public function newEbook(Request $request, SlugifyService $slugifyService): Response
    {
        $ebook = new Ebook();
        $form = $this->createForm(EbookType::class, $ebook);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ebook->setUser($this->getUser());
            $ebook->setIsValidated(false);
            $slug = $slugifyService->generate($ebook->getTitle());
            $ebook->setSlug($slug);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($ebook);
            $entityManager->flush();

            return $this->redirectToRoute('expert_ebook');
        }

        return $this->render('expert/ebook/ebook_new.html.twig', [
            'ebook' => $ebook,
            'ebookForm' => $form->createView(),
        ]);
    }


    /**
     * @Route("/ebook/{id}/edit", name="ebook_edit", methods={"GET","POST"})
     */
    public function editEbook(Request $request, Ebook $ebook): Response
    {
        $form = $this->createForm(EbookType::class, $ebook);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ebook->setIsValidated(false);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('expert_ebook');
        }

        return $this->render('expert/ebook/ebook_edit.html.twig', [
            'ebook' => $ebook,
            'ebookForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/ebook/{id}", name="ebook_delete", methods={"DELETE"})
     */
    public function deleteEbook(Request $request, Ebook $ebook): Response
    {
        if ($this->isCsrfTokenValid('delete' . $ebook->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($ebook);
            $entityManager->flush();
        }

        return $this->redirectToRoute('expert_ebook');
    }

    /**
     * @Route("/page-expert", name="expertPage")
     */
    public function expertPage(): Response
    {
        $user = $this->getUser();
        if ($user->getIsValidated() == false) {
            return $this->render('expert/validation.html.twig', [
                'user' => $user,
            ]);
        }
        return $this->render('expert/expert_page/expertPage.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/page-expert/edit/{id}", name="expertPage_edit", methods={"GET","POST"})
     */
    public function editExpertPage(Request $request, User $user): Response
    {
        $user = $this->getUser();
        if ($user->getIsValidated() == false) {
            return $this->render('expert/validation.html.twig', [
                'user' => $user,
            ]);
        }

        $form = $this->createForm(ExpertType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('expert_expertPage');
        }

        return $this->render('expert/expert_page/edit_expertPage.html.twig', [
            'user' => $user,
            'expertForm' => $form->createView(),
        ]);
    }
}
