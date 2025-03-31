<?php


// src/Controller/PasswordResetController.php
namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use OpenApi\Annotations as OA;




#[Route('/api')]
class PasswordResetController extends AbstractController
{
 

    public function __construct(private EntityManagerInterface $entityManager , private UserRepository $userRepository)
    {
       
    }

     /**
     * @Route("/request-reset-password", name="request_reset_password", methods={"POST"})
     * @OA\Post(
     *     path="/request-reset-password",
     *     summary="Demande de réinitialisation de mot de passe",
     *     description="Permet à l'utilisateur de demander un lien de réinitialisation de mot de passe en utilisant son adresse email.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="Email", type="string", description="L'adresse email de l'utilisateur")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Le token de réinitialisation du mot de passe a été généré et envoyé par email."
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Utilisateur non trouvé."
     *     )
     * )
     */
    //#[Route('/request-reset-password', name: 'request_reset_password', methods: ['POST'])]
    public function requestResetPassword(Request $request, MailerInterface $mailer): JsonResponse
    {
        // Rechercher l'utilisateur par son email
        //$email = $request->get('Email');
       /* $user = $this->entityManager->getRepository(User::class)->findOneBy(['Email' => $email]);

        $user = $this->userRepository->findOneByEmail($email);*/
       // $user= $this->userRepository->findOneBy(['Email' => $email]);

        $email = $request->request->get('Email');
        $user = $this->userRepository->findOneBy(['Email' => $email]);
        
        if (!$user) {
            return new JsonResponse(['message' => 'User not found'], 404);
        }

        // Générer un token de réinitialisation
        $token = bin2hex(random_bytes(32));

        // Définir une expiration pour le token (par exemple 1 heure)
        $expiresAt = new \DateTime('+10 hour');

        // Stocker le token et la date d'expiration dans l'entité User
        $user->setPasswordResetToken($token);
        $user->setPasswordResetExpiresAt($expiresAt);

        // Sauvegarder les modifications
        $this->entityManager->flush();

        // Envoyer un email avec le token
        $email = (new Email())
            ->from('isaactpro@gmail.com')
            ->to($user->getEmail())
            ->subject('Password Reset Request')
            ->text("To reset your password, please click the following link: " .
                   "https://your-app.com/reset-password?token=" . $token);

        $mailer->send($email);

        return new JsonResponse(['message' => 'Password reset token generated and email sent'], 200);
    }

     /*
     * @Route("/reset-password", name="reset_password", methods={"POST"})
     * @OA\Post(
     *     path="/reset-password",
     *     summary="Réinitialisation du mot de passe",
     *     description="Permet à l'utilisateur de réinitialiser son mot de passe en fournissant le token reçu par email.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="token", type="string", description="Le token de réinitialisation du mot de passe"),
     *             @OA\Property(property="new_password", type="string", description="Le nouveau mot de passe")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Le mot de passe a été réinitialisé avec succès."
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Token invalide ou expiré."
     *     )
     * )
     */

   /* #[Route('/reset-password', name: 'reset_password', methods: ['POST'])]*/
public function resetPassword(Request $request): JsonResponse
{
    $token = $request->get('token');
    $newPassword = $request->get('new_password');

    // Rechercher l'utilisateur avec le token
    $user = $this->entityManager->getRepository(User::class)->findOneBy(['passwordResetToken' => $token]);

    if (!$user || $user->getPasswordResetExpiresAt() < new \DateTime()) {
        return new JsonResponse(['message' => 'Invalid or expired token'], 400);
    }

    // Changer le mot de passe (assurez-vous de le hasher correctement)
    $user->setPassword(password_hash($newPassword, PASSWORD_BCRYPT));

    // Supprimer le token et la date d'expiration
    $user->setPasswordResetToken(null);
    $user->setPasswordResetExpiresAt(null);

    // Sauvegarder les modifications
    $this->entityManager->flush();

    return new JsonResponse(['message' => 'Password successfully reset'], 200);
}
}


