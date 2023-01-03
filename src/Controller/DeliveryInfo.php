<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


class DeliveryInfo extends AbstractController
{
    /**
     * @Route("delivery/info", name="deliveryInfo_shippinfInfo", methods={"GET"})
     */
    public function shippingInfo()
    {
        return $this->render('payment/deliveryinfo.html.twig');
    }
}