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

        $user = $em->getRepository('App:User')->find(1);

        if (!empty($request->query->get('liked')) && $request->query->get('liked') == true) {

            // find liked shops only
            $shops = $shopRepo->findPreferred(1);

        } elseif (!empty($request->query->get('location'))) {

            $location = explode(',', $request->query->get('location'));
            $shops = $shopRepo->findNonDislikedWithDistanceOrder($location[0], $location[1], $user->getId());

        } else {

            $shops = $shopRepo->findNonDisliked($user->getId());

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
                       ],
                       'like_action_uri' => $this->generateUrl('shop_action', ['shop'=>$shop->getId(), 'action'=>'like']),
                       'unlike_action_uri' => $this->generateUrl('shop_action', ['shop'=>$shop->getId(), 'action'=>'unlike']),
                       'dislike_action_uri' => $this->generateUrl('shop_action', ['shop'=>$shop->getId(), 'action'=>'dislike'])
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
     * @Route("/{shop}", name="shop_action")
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
                    if ($this->dislike($user, $shop))
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

    private function dislike(User $user, Shop $shop) {
        $em = $this->getDoctrine()->getManager();

        $dislikedShop = $em->getRepository('App:DislikedShop')->findOneBy(['user'=>$user, 'shop'=>$shop]);
        if (null !== $dislikedShop)
            $dislikedShop->setUpdatedAt(new \DateTime());
        else {
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

}