<?php
// src/Controller/DefaultController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class ProgramController extends AbstractController
    {
        /**
        @Route("/programs/show/{id}", methods={"GET"},requirements={"id"="\d+"}, name="program_show")
         */
        public function show($id): Response
        {
            return $this->render('programs/show.html.twig', ['id' => $id ]);
        }
    }