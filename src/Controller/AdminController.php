<?php

namespace App\Controller;

use App\Entity\Membre;
use DateTime;
use App\Entity\Semaine;
use App\Repository\MembreRepository;
use App\Repository\SemaineRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;




class AdminController extends AbstractController
{
    #[Route('/api/admin', name: 'app_admin', methods: ['POST'])]
    public function nextProposeurs(Request $request, EntityManagerInterface $em, Semaine $semaine, SerializerInterface $serializer,): JsonResponse
    {
        $array_request = json_decode($request->getContent(), true);

        $semaine = new Semaine();
        $semaine->setProposeur($array_request['prochain_proposeur']);
        $semaine->setJour($array_request['date']);
        $semaine->setPropositionTermine(0);
        $semaine->setTheme("");

        $em->persist($semaine);
        $em->flush();

        $jsonProposeurs = $serializer->serialize($semaine, 'json', ['groups' => 'getProposeurs']); 
        return new JsonResponse($jsonProposeurs, Response::HTTP_CREATED, [], true);
    }

    #[Route('/api/newmembre', name:"createMembre", methods: ['POST'])]
    public function createMmebre(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, UrlGeneratorInterface $urlGenerator): JsonResponse 
    {

        $membre = $serializer->deserialize($request->getContent(), Membre::class, 'json');
        $em->persist($membre);
        $em->flush();

        $jsonMembre = $serializer->serialize($membre, 'json', ['groups' => 'getMembre']);
        
        $location = $urlGenerator->generate('detailMembre', ['id' => $membre->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse($jsonMembre, Response::HTTP_CREATED, ["Location" => $location], true);
    }
}
