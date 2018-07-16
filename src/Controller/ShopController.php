<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * Class ShopController
 * @package App\Controller
 * @Route("/shops")
 */
class ShopController extends Controller
{
    /**
     * @Route("/", name="shops_index")
     * @param Request $request
     * @return JsonResponse
     * @internal param Request $request
     * @Method("GET")
     */
    public function index(Request $request) {
        $em = $this->getDoctrine()->getManager();
        /** @var \App\Repository\ShopRepository $shopRepo */
        $shopRepo = $em->getRepository('App:Shop');

        if (!empty($request->query->get('liked')) && $request->query->get('liked') == true) {
            // find liked shops only
            $shops = $shopRepo->findPreferred(1);
        } elseif (!empty($request->query->get('location'))) {
            $location = explode(',', $request->query->get('location'));
            $shops = $shopRepo->findAllWithDistanceOrder($location[0], $location[1]);
        }

        //dump($shops);exit;

        if (null === $shops) {
            $playload = [
                'success' => 0,
                'message' => 'Error: database failure.'
            ];
            $code = 500;
        } else {
           if (empty($shops)) {
               $playload = [
                   'success' => 0,
                   'message' => 'Did not find any shops.'
               ];
               $code = 200;
           } else {

               $result = [];

               /** @var \App\Entity\Shop $shop */
               foreach ($shops as $shop) {
                   $result[] = [
                       'name' => $shop->getName(),
                       'email' => $shop->getEmail(),
                       'city' => $shop->getCity(),
                       'picture' => $shop->getPicture(),
                       'location' => [
                           'type' => 'point',
                           'coordinates' => [
                               $shop->getLatitude(),
                               $shop->getLongitude()
                           ]
                       ]
                   ];
               }

               $playload = [
                   'success' => 1,
                   'message' => 'Result found.',
                   'result' => $result
               ];
               $code = 200;
           }
        }

        return new JsonResponse($playload, $code);
    }
}