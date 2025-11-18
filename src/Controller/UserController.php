<?php

namespace App\Controller;

use App\Entity\Hamster;
use App\Entity\User;
use App\Service\HamsterService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class UserController extends AbstractController
{

    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    #[Route('/api/register', name: 'app_register', methods: ['POST'])]
    public function addUser(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, ValidatorInterface $validator, HamsterService $hs): JsonResponse
    {
        //body request
        $content = $request->getContent();
        // Déserialisation => transformer du texte en ojbet / sérialiser => transformer un objet en texte
        $user = $serializer->deserialize($content, User::class, 'json',[]);
        $password = $user->getPassword();
        if($user->getRoles()[0] === "ROLE_USER"){
            $user->setGold(500);
        }else{
            $user->setGold(0);
        }
        $user->setPassword($this->hasher->hashPassword($user, $password));
        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            return $this->json([
            'errors' => $errors,
        ], Response::HTTP_BAD_REQUEST);
        }

        // Create 4 hamster (2 males and 2 females)
         $genres = ['m', 'm', 'f', 'f'];
        foreach ($genres as $genre) {
            $hamster = new Hamster();
            $hamster->setGenre($genre);
            $hamster->setHunger(100);
            $hamster->setAge(0);
            $hamster->setActive(true);
            $hamster->setName($hs->getRandomName()); 
            $hamster->setOwner($user); 

            $em->persist($hamster);
        }

        $em->persist($user);
        $em->flush();

        return $this->json(
            [
                'user' => $user,
            ],
            Response::HTTP_CREATED,
            // Pour les headers
            [],
            // On appelle ce groupe => on veut que les champs concernés par ce groupe
            ['groups' => 'user_details']
        );
    }

    #[Route('/api/user', name: 'user_details', methods: ['GET'])]
    #[IsGranted('ROLE_USER',null, "Vous n'avez pas le droit d'accéder à cette ressource", 403)]
    public function getUserDetails(): JsonResponse
    {   
        $user = $this->getUser();
        return $this->json(
            [
                'user' => $user,
            ],
            200,
            // Pour les headers
            [],
            // On appelle ce groupe => on veut que les champs concernés par ce groupe
            ['groups' => 'user_details']
        );
    }

    #[Route('/api/delete/{id}', name: 'user_delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN')]
    public function deleteUserById(User $user, EntityManagerInterface $em): JsonResponse
    {   
        $em->remove($user);
        $em->flush();
        return $this->json([
            'message' => "L'utilisateur a été supprimé avec succès",
        ],200 );
    }
    
}
