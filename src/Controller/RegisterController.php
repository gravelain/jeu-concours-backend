<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherAwareInterface ;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


class RegisterController extends AbstractController
{ 
    
    // Constructeur pour initialiser les services nécessaires
    public function __construct(private EntityManagerInterface $manager, private UserRepository $userRepository ,private TokenStorageInterface $tokenStorage , private  EntityManagerInterface $entityManager)
    {
    
    }
    
    /*Cette méthode gère l'enregistrement des nouveaux utilisateurs.
     * Elle vérifie si l'email existe déjà, valide les données et crée un nouvel utilisateur.*/
    #[Route('/api/register', name: 'register', methods: ['POST'])]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        $email = $data['email'];
        $password = $data['password'];
        $firstName=$data['firstName'];
        $name=$data['name'];
        $user = new User();

        $email_exist= $this->userRepository->findOneBy(['Email' => $email]);

        if($email_exist)
        {   
            return new JsonResponse(
                ['message' => 'Cet  utilisaeur  existe deja, Voulez vous vous connecter ? SVP'],
                Response::HTTP_BAD_REQUEST
            );
            
            }
        if (!$email || !$password || !$firstName) {
            return new JsonResponse(['message' => 'Email firstname and  password are required'], Response::HTTP_BAD_REQUEST);
        }

        $user = new user();
        $user->setEmail($email);
        $user->setFirstName($firstName);
        $hashedPassword = $passwordHasher->hashPassword($user , $password);
        $user->setPassword($hashedPassword);

        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Utilisateur  enregistrer avec  succes'], Response::HTTP_CREATED);
    }

#[Route('/api/me', name: 'api_me', methods: ['GET'])]
public function getMe(): Response
{
    $token = $this->tokenStorage->getToken();

    if (!$token || !$user = $token->getUser()) {
        throw new AccessDeniedException('Unauthorized');
    }

    if (!$user instanceof User) {
        throw new AccessDeniedException('Unauthorized');
    }

    $userData = [
        'email' => $user->getUserIdentifier(),
        'firstName' => $user->getFirstName(),
        'name' => $user->getName(),
        'roles' => $user->getRoles(),
        'phone'=>$user->getPhone(),
        'id'=>$user->getId(),
        'Rle'=>$user->getRle()

    ];

    return new JsonResponse($userData);
}
    
   #[Route('/api/user/update', name: 'api_user_update', methods: ['PUT'])]
    public function update(Request $request): Response
    {
        $token = $this->tokenStorage->getToken();

        if (!$token || !$user = $token->getUser()) {
            throw new AccessDeniedException('Unauthorized');
        }

        if (!$user instanceof User) {
            throw new AccessDeniedException('Unauthorized');
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['firstName'])) {
            $user->setFirstName($data['firstName']);
        }

        if (isset($data['name'])) {
            $user->setName($data['name']);
        }

        if (isset($data['email'])) {
            $user->setEmail($data['email']);
        }
        
        if (isset($data['phone'])) {
            $user->setPhone($data['phone']);
        }
        // Ajoutez ici d'autre champs que vous souhaitez mettre à jour

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'User updatede successfully'], Response::HTTP_OK);
    }

}
