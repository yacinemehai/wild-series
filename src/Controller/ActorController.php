<?php

namespace App\Controller;

use App\Entity\Actor;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class ActorController extends AbstractController
{
    /**
     * @Route("/actor/{id}", name="actor_show")
     */
    public function show(Actor $actor) :Response
    {
        return $this->render('actor/show.html.twig', ['actor' => $actor]);
    }
}
