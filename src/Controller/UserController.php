<?php


namespace App\Controller;


use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    /**
     * formulaire permettant l'inscription d'un nouvel utilisateur
     * @Route("/security/login_register", name="user_create", methods={"GET|POST"})
     */
    public function createUser(Request $request, UserPasswordEncoderInterface $encoder)
    {
        #1a. creation d'un nouveau user
        $user = new User();
        $user->setRoles(['ROLE_USER']);

        #2.création d'un formulaire avec post
        $form = $this->createFormBuilder($user)
            ->add('firstname', TextType::class,[
                'label' => 'Prénom',
            ])

            ->add('lastname', TextType::class,[
                'label' => 'Nom',
            ])

            ->add('email', EmailType::class)


            ->add('password', PasswordType::class,[
                'label' => 'Mot de passe',
            ])

            ->add('adress', TextType::class,[
                'label' => 'Adresse',
            ])

            ->add('postal_code', TextType::class,[
                'label' => 'Code Postal',
            ])

            ->add('city', TextType::class,[
                'label'=> 'Ville',
            ])

            ->add('telephone', TextType::class,[
                'label'=> 'Numéro de téléphone',
            ])

            #2e. Bouton du formulaire connexion
            ->add('submit', SubmitType::class, [
                'label' => 'Je m\'inscris !',
                'attr' => [
                    'class' => 'btn btn-lg',
                    'style' => 'background-color:#C2A476'
                ]

            ])

            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            #3. encodage du mdp
            $user->setPassword(
                $encoder->encodePassword($user, $user->getPassword())
            );

            #4. sauvegarde en bdd
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            #5. notification flash
            $this->addFlash('notice', 'Félicitation pour votre inscription !');

            #6. redirection
            return $this->redirectToRoute('user_create');

        }

        return $this->render('security/login_register.html.twig', [
            'form' => $form->createView()

        ]);


    }
}
