<?php

namespace App\Controller;

use App\Entity\DislikedShop;
use App\Entity\Shop;
use App\Entity\User;
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
        } else {
            $playload = [
                'success' => 0,
                'message' => 'Error: Please provide the location in the query.'
            ];
            $code = 400;
            return new JsonResponse($playload, $code);
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

    /**
     * @Route("/{shop}")
     * @param Shop $shop
     * @param Request $request
     * @return JsonResponse
     * @Method("POST")
     */
    public function shopAction(Shop $shop, Request $request) {
        $playload = [];

        $em = $this->getDoctrine()->getManager();

        $user = new User();
        $user->setEmail('taoufikallah@gmail.com');
        $user->setPlainPassword('123456');
        $user->setRoles(['ROLE_USER']);

        $em->persist($user);
        $em->flush();

        $userRepo = $em->getRepository('App:User');

        /** @var User $user */
        $user = $userRepo->find(1);

        if (!empty($request->query->get('action'))) {

            $action = $request->query->get('action');

            switch ($action) {
                case 'like':
                   if ($this->isLiked($user, $shop)) {
                       $playload = [
                           'success'=>0,
                           'message'=>'Shop already liked.'
                       ];
                   } elseif ($this->like($user, $shop)) {
                       $playload = [
                           'success'=>1,
                           'message'=>'Shop liked.'
                       ];
                   } else {
                       $playload = [
                           'success'=>0,
                           'message'=>'Could not like shop.'
                       ];
                   }

                    break;
                case 'unlike':
                    if ($this->isLiked($user, $shop) && $this->unlike($user, $shop) ) {
                        $playload = [
                            'success'=>1,
                            'message'=>'Shop unliked'
                        ];
                    } else {
                        $playload = [
                            'success'=>0,
                            'message'=>'Could not unlike shop.'
                        ];
                    }

                    break;
                case 'dislike':
                    $state = false;

                    if ($this->isDisliked($user, $shop)) {
                        if ($this->dislike($user, $shop, true))
                            $state=true;
                    } else {
                        if ($this->dislike($user, $shop, false))
                            $state=true;
                    }

                    if ($state)
                        $playload = [
                            'success'=>1,
                            'message'=>'Shop disliked'
                        ];
                    else
                        $playload = [
                            'success'=>0,
                            'message'=>'Could not dislike shop.'
                        ];

                    break;
            }

        } else {
            $playload = [
                'success'=>0,
                'message'=>'Please, specify an action.'
            ];
        }

        return new JsonResponse($playload, 200);
    }

    private function like(User $user, Shop $shop) {
        $user->addShop($shop);
        $shop->addLiker($user);

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->persist($shop);

        $em->flush();

        return true;
    }

    private function unlike(User $user, Shop $shop) {
        $user->removeShop($shop);
        $shop->removeLiker($user);

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->persist($shop);

        $em->flush();

        return true;
    }

    private function dislike(User $user, Shop $shop, $isOld) {
        $em = $this->getDoctrine()->getManager();

        if ($isOld) {
            $dislikedShop = $em->getRepository('App:DislikedShop')->findOneBy(['user'=>$user, 'shop'=>$shop]);
            $dislikedShop->setUpdatedAt(new \DateTime());
        } else {
            $dislikedShop = new DislikedShop();
            $dislikedShop->setUser($user);
            $dislikedShop->setShop($shop);
        }

        $em->persist($dislikedShop);
        $em->flush();

        return true;
    }

    private function isLiked(User $user, Shop $shop) {

        $em = $this->getDoctrine()->getManager();

        $qb = $em->createQueryBuilder();

        $qb->select('count(s.id) as is_liked');

        $qb->from('App:Shop','s');
        $qb->join('s.users', 'u');

        $qb->where('s.id=:shopId');
        $qb->andWhere('u.id=:userId');

        $qb->setParameter('userId', $user->getId());
        $qb->setParameter('shopId', $shop->getId());

        $result = $qb->getQuery()->getOneOrNullResult();
        //dump($result);exit;

        if ($result['is_liked'] > 0) {
            return true;
        } else {
            return false;
        }

    }

    private function isDisliked(User $user, Shop $shop) {
        $em = $this->getDoctrine()->getManager();

        $qb = $em->createQueryBuilder();

        $qb->select('count(ds.id) as is_disliked');
        $qb->from('App:DislikedShop','ds');

        $qb->join('ds.user', 'u');
        $qb->join('ds.shop', 's');

        $qb->where('u.id=:userId');
        $qb->andWhere('s.id=:shopId');


        $qb->setParameter('userId', $user->getId());
        $qb->setParameter('shopId', $shop->getId());

        $result = $qb->getQuery()->getOneOrNullResult();
        //dump($result);exit;

        if ($result['is_disliked'] > 0) {
            return true;
        } else {
            return false;
        }

    }
}