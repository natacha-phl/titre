<?php


namespace App\Controller;

use App\Entity\Category;

use App\Entity\Product;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;


class ProductController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{

    /**
     * Formulaire permettant d'ajouter un Produit
     * @Route("/gestion/creer-un-produit", name="gestion_admin", methods={"GET|POST"})
     ** @IsGranted("ROLE_ADMIN")
     */
    public function createProduct(Request $request, SluggerInterface $slugger){
        #1a. Création d'un nouvel article
        $product = new Product();

        #2. Création d'un formulaire avec $product
        $form = $this->createFormBuilder($product)

            #2a. Référence du produit
            ->add('reference', TextType::class,[
                'label' => 'Référence',
            ])

            # nom du produit
            ->add('name', TextType::class,[
                'label' => 'Nom',
            ])

            # description du produit
            ->add('description', TextType::class)

            # prix du produit
            ->add('price', MoneyType::class, [
                'label' => 'Prix',
                'divisor' => 100,
            ])
            # Affichage du prix
            ->add('pricedisplay', TextType::class, [
            ])

            #2b. Categorie du produit (/androide ou /Accessoire (Liste déroulante)
            ->add('categorie', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
            ])


            #2d. Upload Image du produit
            ->add('featuredImage', FileType::class)

            #2e. Bouton Submit de l'article
            ->add('submit', SubmitType::class, [
                'label' => 'Ajouter le produit',
                'attr' => [
                    'class' => 'button create-account'
                ]
            ])

            #2f. Permet de récupérer le formulaire généré
            ->getForm();

        #3.  Demande à Symfony de récupérer les infos dans la request.
        $form->handleRequest($request);

        #4. Vérifie si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {

            # 4a. Gestion Upload de l'image
            /** @var UploadedFile $featuredImage */
            $featuredImage = $form->get('featuredImage')->getData();

            if ($featuredImage) {
                $originalFilename = pathinfo($featuredImage->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$featuredImage->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $featuredImage->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'featureImagename' property to store the PDF file name
                // instead of its contents

                # on stock dans la BDD
                $product->setFeaturedImage($newFilename);
            }


            # 4b. Génération de l'alias
            $product->setAlias(
                $slugger->slug(
                    $product->getName()
                )
            );
            # 4c. Sauvegarde dans la BDD
            /*
             * Qu'est ce que le Entity Manager (em) ?
             * C'est une classe qui sait comment sauvegarder d'autres classes.
             */
            $em = $this->getDoctrine()->getManager(); # Récupération du EM
            $em->persist($product); # Demande pour sauvegarder en BDD $post
            $em->flush(); # On execute la demande

            # 4d. Notification / Confirmation
            $this->addFlash('notice', 'votre produit est enregistré ! ');

            # 4e. Redirection
            return $this->redirectToRoute('gestion_admin');
            #, [
                #'alias'=> $product->getAlias()
            #]);
        }

        #5. Transmission du formulaire à la vue
        return $this->render('default/gestion_admin.html.twig', [
            'form' => $form->createView()
        ]);
    }
}

