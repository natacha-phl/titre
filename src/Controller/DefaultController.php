<?php


namespace App\Controller;


use App\Entity\Category;
use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * page / action : Accueil
     */


    public function index()
    {
        # Transmettre à la vue
        return $this->render('default/index.html.twig');
    }

    /**
     * @Route("moncompte", name="default_monCompte", methods={"GET"})
     */
    public function monCompte()
    {
        # Transmettre à la vue
        return $this->render('default/mon_compte.html.twig');
    }

   /* /**
     * @Route("gestion_admin", name="default_gestionAdmin", methods={"GET"})
     */
    //public function gestionAdmin()
    #/{
        # Transmettre à la vue
        #return $this->render('default/gestion_admin.html.twig');
    #}*/
    /**
     * @Route("/{alias}", name="default_categorie", methods={"GET"})
     */
    public function category($alias)
    {
        $category = $this->getDoctrine()
        ->getRepository(Category::class)
        ->findOneBy(['alias' => $alias]);

        $products = $category->getProducts();
        # Transmettre à la vue
        return $this->render('default/categorie.html.twig',
            [
            'products' => $products
        ]);
    }
    /**
     * @Route("/{category}/{alias}_{id}.html", name="default_product", methods={"GET"})
     */
    public function product($id)
    {
        $product = $this->getDoctrine()
            ->getRepository(Product::class)
            ->find($id);

        # Transmettre à la vue
        return $this->render('default/fiche_produit.html.twig',  [
            'product' => $product
        ]);
    }

    /**
     * @Route("/index_test/test", name="test", methods={"GET"})
     */
    public function indextest()
    {
        # Transmettre à la vue
        return $this->render('default/test.html.twig');
    }

}
