<?php


namespace App\Controller;
use App\Entity\Product;
use App\Repository\ProductRepository;
use http\Client\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CartController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{
    /**
     * @Route("/panier", name="panier")
     */
    public function panier(SessionInterface $session, ProductRepository $productRepository)
    {
        $panier = $session->get('panier', []);
        # Transmettre à la vue

        $panierWithData =[];

        foreach ($panier as $id => $quantity) {
            $panierWithData[] = [
                'product' => $productRepository->find($id),
                'quantity' => $quantity
            ];

        }

        $total = 0;

        foreach ($panierWithData as $item){
            $totalItem = $item['product']->getPricedisplay() * $item['quantity'];
            $total += $totalItem;

        }

        return $this->render('cart/panier.html.twig', [
            'items'=>$panierWithData,
            'total'=> $total

        ]);

    }

    /**
     * @Route("/panier/add/{id}", name="cart_add")
     */
    public function add($id, SessionInterface $session) {

        $panier = $session->get('panier', []);

        if(!empty($panier[$id])) {
            $panier[$id]++;
        }else {
            $panier[$id] = 1;
        }



        $session->set('panier', $panier);

//        dd($session->get('panier'));

        return $this->redirectToRoute('panier');

    }
    /**
     * @Route("/panier/remove/{id}", name="cart_remove")
     */
    public function remove($id, SessionInterface $session){
        $panier = $session->get('panier', []);
        if(!empty($panier[$id])) {
            unset($panier[$id]);
        }

        $session->set('panier', $panier);
        return $this->redirectToRoute('panier');

    }

    /**
     * @Route("/success", name="success")
     */
    public function success(){
        $this->addFlash('notice', 'Votre achat a bien été effectué !' );
        return $this->render('cart/success.html.twig', [
        ]);

    }

    /**
     * @Route("/error", name="error")
     */
    public function error(){
        $this->addFlash('notice', 'Une erreur est survenue lors de votre paiement ! Veuillez vérifier vos données.' );
        return $this->render('cart/error.html.twig', [
        ]);

    }

    /**
     * @Route("/create-checkout-session", name="cart_checkout")
     */
    public function checkout(SessionInterface $session, ProductRepository $productRepository){
        \Stripe\Stripe::setApiKey('sk_test_51HhyDnBt4PlsSGAO8Qh7nipsylKFbtDBgfD8kIgPsRwhIczvRaPYqEh0tfNtwSGZzTrdU4NtKuqhtdgasW9ewuO000QREQS7Ov');
        $panier = $session->get('panier', []);
        $products = [];

        foreach ($panier as $id => $quantity) {
            $products[] = [
                'product' => $productRepository->find($id),
                'quantity' => $quantity
            ];

        }

        foreach ($products as $item) {
            $itemname = $item['product']->getName();
            $itemprice = $item['product']->getPrice();

        }

        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $itemname,
                    ],
                    'unit_amount_decimal' => $itemprice,
                ],
                'quantity' => $quantity,
            ]],
            'mode' => 'payment',
            'success_url' => $this->generateUrl('success', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->generateUrl('error', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);
        return new JsonResponse([ 'id' => $session->id ]);
    }
}