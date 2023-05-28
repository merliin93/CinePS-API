<?php

namespace App\Controller;

use App\Entity\AVote;
use App\Service\CurrentSemaine;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AVoteController extends AbstractController
{
    //Afficher les membres ayant voté pour la semaine
    #[Route('/membreVotant/{id_semaine}', name:'membreVotant', methods: ['GET'])]
    public function membreVotant(int $id_semaine, EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        //Récupérer les membre ayant voté
        $queryBuilder_get_membre_votant = $entityManager->createQueryBuilder();
        $queryBuilder_get_membre_votant->select('a')
        ->from(AVote::class, 'a')
        ->where('a.semaine = :semaine')
        ->setParameter('semaine', $id_semaine);

        $membre_votant = $queryBuilder_get_membre_votant->getQuery()->getResult();
        $jsonMembreVotant = $serializer->serialize($membre_votant, 'json', ['groups' => 'getPropositions']);

        return new JsonResponse ($jsonMembreVotant, Response::HTTP_OK, [], true);
    }
    
    // Indique si l'utilisateur a voté pour alse amine en cours
    #[Route('/aVoteCurrentSemaine/{id_membre}', name:'AVoteCurrentSemaine', methods: ['GET'])]
    public function aVoteCurrentSemaine(CurrentSemaine $currentSemaine, int $id_membre, EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        $id_current_semaine = $currentSemaine->getIdCurrentSemaine($entityManager);

        $queryBuilder_get_aVote = $entityManager->createQueryBuilder();
        $queryBuilder_get_aVote->select('av.id')
        ->from(AVote::class, 'av')
        ->where('av.semaine = :semaine')
        ->andWhere('av.votant = :votant')
        ->setParameters(array('semaine' => $id_current_semaine, 'votant' => $id_membre));

        $resultats_aVote = $queryBuilder_get_aVote->getQuery()->getResult();


        if (empty($resultats_aVote)) {
            $result = $serializer->serialize(false, 'json');
        } else {
            $result = $serializer->serialize(true, 'json');
        }
        return new JsonResponse ($result, Response::HTTP_OK, [], true);

    }

}
