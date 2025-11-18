<?php

namespace App\Controller;

use App\Entity\Hamster;
use App\Repository\HamsterRepository;
use App\Service\HamsterService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class HamsterController extends AbstractController
{
    // Return the list of hamsters of current user
    #[Route('/api/hamsters', name: '_hamsters_user', methods: ['GET'])]
    // Admins already have ROLE_USER
    #[IsGranted('ROLE_USER',null, "Vous n'avez pas le droit d'accéder à cette ressource", 403)]
    public function getHamstersFromCurrentUser(): JsonResponse
    {   
        $user = $this->getUser();
        $hamsters = $user->getHamsters();
        return $this->json(
            [
                'hamsters' => $hamsters,
            ],
            200,
            // Pour les headers
            [],
            // On appelle ce groupe => on veut que les champs concernés par ce groupe
        );
    }

    #[Route('/api/hamsters/{id}', name: 'hamster_by_id', methods: ['GET'])]
    #[IsGranted('ROLE_USER',null, "Vous n'avez pas le droit d'accéder à cette ressource", 403)]
    public function getHamsterById(Hamster $hamster): JsonResponse
    {   
        return $this->json([
            'hamster' => $hamster,
        ],
        200,
        // Pour les headers
        [],
        // On appelle ce groupe => on veut que les champs concernés par ce groupe
        // ['groups' => 'author_details']
    );
    }

    #[Route('/api/hamsters/reproduce', name: 'hamster_add', methods: ['POST'])]
    #[IsGranted('ROLE_USER',null, "Vous n'avez pas le droit d'accéder à cette ressource", 403)]
    public function createHamster(Request $request, EntityManagerInterface $em, HamsterRepository $hr, HamsterService $hs): JsonResponse
    {   
        //get the current user and ver
        //body request
        $content = $request->getContent();
        $user = $this->getUser();
        $data = json_decode($content, true);
        $idHamster1 = $data['idHamster1'] ?? null;
        $idHamster2 = $data['idHamster2'] ?? null;

        // Find hamster objects
        $hamster1 = $hr->findOneById($idHamster1);
        $hamster2 = $hr->findOneById($idHamster2);

        if($hamster1->getOwner() === $user && $hamster1->getOwner() === $user){
            // Verify if hasmters are male and female
            if(($hamster1->getGenre() === "f" && $hamster2->getGenre() === "m") || ($hamster1->getGenre() === "m" && $hamster2->getGenre() === "f")){
                // Verify if the both are active
                if($hamster1->isActive() === true && $hamster2->isActive() === true)
                {
                    $genres = [
                        'f',
                        'm'
                    ];
                    $randGenre = $genres[array_rand($genres)];

                    $newHamster = new Hamster();
                    $newHamster->setGenre($randGenre);
                    $newHamster->setHunger(100);
                    $newHamster->setAge(0);
                    $newHamster->setActive(true);
                    $newHamster->setName($hs->getRandomName()); 
                    $newHamster->setOwner($user); 
                    $em->persist($newHamster);
                    $em->flush();
                    return $this->json(
                        [
                            'newHamster' => $newHamster,
                        ], 
                        Response::HTTP_CREATED,
                        // Pour les headers
                        [],
                        // On appelle ce groupe => on veut que les champs concernés par ce groupe
                        ['groups' => 'new_rabbit']
                    );
                }
                return $this->json(
                    [
                        'message' => "Les hamsters doivent être actifs",
                    ], 
                    Response::HTTP_BAD_REQUEST
                );
            }else{
                return $this->json(
                    [
                        'message' => "Les hamsters doivent être de genre opposés",
                    ], Response::HTTP_BAD_REQUEST
                );
            }            
        }

        return $this->json(
            [
                'message' => "Vous devez être le propriétaire des hamsters",
            ], Response::HTTP_BAD_REQUEST
        );
    }

    #[Route('/api/hamsters/{id}/sell', name: 'hamster_sell', methods: ['POST'])]
    #[IsGranted('ROLE_USER',null, "Vous n'avez pas le droit d'accéder à cette ressource", 403)]
    public function sellHamster(Hamster $hamster,EntityManagerInterface $em): JsonResponse
    {  
        $user = $this->getUser();
        $gold = $user->getGold();
        $newGoldValue = $gold + 300;
        $user->removeHamster($hamster);
        $user->setGold($newGoldValue);    
        $hamsters = $user->getHamsters();
        foreach ($hamsters as $hamsterFromUser) {
            $age = $hamsterFromUser->getAge();
            $hunger = $hamsterFromUser->getHunger();
            $hamsterFromUser->setAge($age+5);
            $hamsterFromUser->setHunger($hunger-5);
            $em->persist($hamsterFromUser);
            $em->flush();
        }
        $em->persist($user);
        $em->flush();
        return $this->json(
            [
                'success' => "Hamster vendu avec succès",
                'user' => $user,
            ],
            Response::HTTP_CREATED,
            // Pour les headers
            [],
            // On appelle ce groupe => on veut que les champs concernés par ce groupe
            ['groups' => 'user_details']
        );
    }

    #[Route('/api/hamsters/{id}/feed', name: 'hamster_feed', methods: ['POST'])]
    #[IsGranted('ROLE_USER',null, "Vous n'avez pas le droit d'accéder à cette ressource", 403)]
    public function feedHamster(Hamster $hamster,EntityManagerInterface $em): JsonResponse
    {   
  
        $user = $this->getUser();
        $goldToRemove = 100 - ($hamster->getHunger());
        $hamster->setHunger(100);
        $gold = $user->getGold();
        $newGoldValue = $gold - $goldToRemove;
        $user->setGold($newGoldValue);
        $hamsters = $user->getHamsters();
        foreach ($hamsters as $hamsterFromUser) {
            $age = $hamsterFromUser->getAge();
            $hunger = $hamsterFromUser->getHunger();
            $hamsterFromUser->setAge($age+5);
            $hamsterFromUser->setHunger($hunger-5);
            $em->persist($hamsterFromUser);
            $em->flush();
        }

        $em->persist($user);
        $em->persist($hamster);
        $em->flush();
        return $this->json([
            'gold' => $user->getGold(),
        ], Response::HTTP_CREATED);
    }

    #[Route('/api/hamsters/sleep/{nbDays}', name: 'hamster_feed', methods: ['POST'])]
    #[IsGranted('ROLE_USER',null, "Vous n'avez pas le droit d'accéder à cette ressource", 403)]
    public function sleepHamsters(int $nbDays,EntityManagerInterface $em): JsonResponse
    {   
        $user = $this->getUser();
        $hamsters = $user->getHamsters();
        foreach ($hamsters as $hamsterFromUser) {
            $age = $hamsterFromUser->getAge();
            $hunger = $hamsterFromUser->getHunger();
            $hamsterFromUser->setAge($age+$nbDays);
            $hamsterFromUser->setHunger($hunger - $nbDays);
            $em->persist($hamsterFromUser);
            $em->flush();
        }

        return $this->json([
            'message' => "Les hamsters ont été mis au lit !",
        ], Response::HTTP_CREATED);
    }

    #[Route('/api/hamsters/{id}/rename', name: 'hamster_rename', methods: ['PUT'])]
    #[IsGranted('ROLE_USER',null, "Vous n'avez pas le droit d'accéder à cette ressource", 403)]  
    public function renameHamster(Hamster $hamster,Request $request,EntityManagerInterface $em): JsonResponse
    {   
  
        $user = $this->getUser();
        $content = $request->getContent();
        $data = json_decode($content, true);
        $givenName = $data['newName'] ?? null;
        $hamster->setName($givenName);
        $em->persist($hamster);
        $em->flush();
        return $this->json([
            'hamster' => $hamster->getName(),
        ], Response::HTTP_CREATED);
    }

}
