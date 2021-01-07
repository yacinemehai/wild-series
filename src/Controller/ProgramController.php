<?php
// src/Controller/DefaultController.php
namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
use App\Form\CommentType;
use App\Form\ProgramType;
use App\Form\SearchProgramType;
use App\Repository\ProgramRepository;
use App\Service\Slugify;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;


/**
 * @Route("/program", name="program_")
 */
class ProgramController extends AbstractController
    {
        /**
         * Show all rows from Program’s entity
         *
         * @Route("/", name="index")
         * @return Response A response instance
         */
        public function index(Request $request, ProgramRepository $programRepository): Response
        {
            $form = $this->createForm(SearchProgramType::class);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $search = $form->getData()['search'];
                $programs = $programRepository->findLikeName($search);
            } else {
                $programs = $programRepository->findAll();
            }

            return $this->render('program/index.html.twig', [
                'programs' => $programs,
                'form' => $form->createView(),
            ]);
        }
        /**
         *
         * @Route("/new", name="new")
         */
        public function new (Request $request, Slugify $slugify, MailerInterface $mailer) :Response
        {
            $program = new Program();
            $form = $this->createForm(ProgramType::class, $program);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()){
                $entityManager = $this->getDoctrine()->getManager();
                $slug = $slugify->generate($program->getTitle());
                $program->setSlug($slug);
                $entityManager->persist($program);
                $entityManager->flush();

                $email = (new Email())
                    ->from ($this->getParameter('mailer_from'))
                    ->to('to@example.com')
                    ->subject('Une nouvelle série vient d\'être publiée !')
                    ->html($this->renderView('program/new_program_email.html.twig', ['program' => $program]));

                $mailer->send($email);

                return $this->redirectToRoute('program_index');
            }

            return $this->render('program/new.html.twig', [
                "form" => $form->createView(),
            ]);

        }
        /**
         * @Route("/{program}", methods={"GET"},  name="show")
         * @ParamConverter("program", class="App\Entity\Program", options={"mapping": {"program": "slug"}})
         * @return Response
         */
        public function show(Program $program): Response
        {

            $seasons = $this->getDoctrine()->getRepository(Season::class)->findBy(['program' => $program]);

            return $this->render('program/show.html.twig', [
                'program' => $program,
                'seasons' => $seasons,
            ]);
        }

         /**
        * @Route ("/{programId}/seasons/{seasonId}", name="season_show")
        * @ParamConverter ("program", class="App\Entity\Program", options={"mapping": {"programId": "slug"}})
        * @ParamConverter("season", class="App\Entity\Season", options={"mapping": {"seasonId": "id"}})
        * @return Response
        */
        public function showSeason(Program $program, Season $season) : Response
        {
            $episodes = $this->getDoctrine()
                ->getRepository(Episode::class)
                ->findBy(['season' => $season]);

            return $this->render('/program/season_show.html.twig',
                ['program' => $program, 'season' => $season, 'episodes' => $episodes]);
        }

        /**
        * @Route ("/{programId}/seasons/{seasonId}/episodes/{episodeId}", name="episode_show")
        * @ParamConverter ("program", class="App\Entity\Program", options={"mapping": {"programId": "slug"}})
        * @ParamConverter("season", class="App\Entity\Season", options={"mapping": {"seasonId": "id"}})
        * @ParamConverter ("episode", class="App\Entity\Episode", options={"mapping": {"episodeId": "slug"}})
        * @return Response
        */
        public function showEpisode(Program $program, Season $season, Episode $episode, Request $request) :Response
        {
            $comment = new Comment();
            $user = $this->getUser();
            $formComment= $this->createForm(CommentType::class, $comment);
            $formComment->handleRequest($request);
            $comment->setEpisode($episode);
            $comment->setAuthor($user);

            if ($formComment->isSubmitted() && $formComment->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();

                $entityManager->persist($comment);
                $entityManager->flush();

            }

            return $this->render('program/episode_show.html.twig',[
                'program' => $program,
                'season'  => $season,
                'episode' => $episode,
                'form'    => $formComment->createView()

            ]);
        }
    }
