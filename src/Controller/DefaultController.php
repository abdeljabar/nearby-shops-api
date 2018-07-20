<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="home")
     */
    public function index() {
        $playload = [
            'success' => 1,
            'message' => 'API URLs.',
            'result' => [
                'shops_uri' => $this->generateUrl('shops_index'),
                'shops_with_location_uri' => $this->generateUrl('shops_index', ['location'=>'0,0']),
                'preferred_shops_uri' => $this->generateUrl('shops_index', ['liked'=>'true']),
            ]
        ];

        return new JsonResponse($playload, 200);
    }
}