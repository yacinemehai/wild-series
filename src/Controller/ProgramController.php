<?php
// src/Controller/DefaultController.php
namespace App\Controller;

use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/program", name="program_")
 */
class ProgramController extends AbstractController
    {
        /**
         * @Route ("/{id<^[0-9]+$>}", name="show")
         * @return Response
         */
        public function show($id): Response
        {
            $program = $this->getDoctrine()
                ->getRepository(Program::class)
                ->findOneBy(['id' => $id]);

            $seasons = $this->getDoctrine()
                ->getRepository(Season::class)
                ->findBy(['program' => $program]);


            if (!$program) {
                throw $this->createNotFoundException(
                    'No program with id : ' . $id . ' found in program\'s table.'
                );
            }
            return $this->render('program/show.html.twig', [
                'program' => $program,
                'seasons' => $seasons,
            ]);
        }

         /**
        * @Route ("/{programId}/seasons/{seasonId}", name="season_show")
        * @return Response
        */
        public function showSeason(int $programId, int $seasonId) : Response
        {
            $programId = $this->getDoctrine()
                ->getRepository(Program::class)
                ->findOneBy(['id' => $programId]);

            $seasonId = $this->getDoctrine()
                ->getRepository(Season::class)
                ->findOneBy(['id' => $seasonId]);

            $episodes = $this->getDoctrine()
                ->getRepository(Episode::class)
                ->findBy(['season' => $seasonId]);

            return $this->render('/program/season_show.html.twig',
                ['program' => $programId, 'season' => $seasonId, 'episodes' => $episodes]);
        }

        /**
        * Show all rows from Program’s entity
        *
        * @Route("/", name="index")
        * @return Response A response instance
        */
    public function index() : Response
    {
        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findAll();
        return $this->render('program/index.html.twig',
            ['programs'  => $programs]);
    }
    }