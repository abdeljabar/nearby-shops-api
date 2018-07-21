<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class SecurityController extends Controller
{
    /**
     * @Route("/register", name="register")
     * @METHOD("POST")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param ValidatorInterface $validator
     * @return JsonResponse
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, ValidatorInterface $validator) {

        if ( !empty($request->get('email')) && !empty($request->get('password'))) {
            $user = new User();

            $email = $request->get('email');
            $user->setEmail($email);

            $password = $passwordEncoder->encodePassword($user, $request->get('password'));
            $user->setPassword($password);

            $user->setRoles(['ROLE_USER']);

            $errors = $validator->validate($user);

            if (count($errors) > 0) {
                //dump($errors);

                // customizing errors array
                $cErrors = [];
                foreach ($errors as $error) {
                    $cErrors[] = ['property'  => $error->getPropertyPath(), 'message'   => $error->getMessage()];
                }

                $playload = [
                    'success' => 0,
                    'message' => 'Please verify the following errors.',
                    'errors' => $cErrors
                ];
                $code = 400;

            } else {
                $em = $this->getDoctrine()->getManager();

                $em->persist($user);
                $em->flush();

                $playload = [
                    'success' => 1,
                    'message' => 'User was created successfully.',
                    'home'  => $this->generateUrl('home'),
                ];
                $code = 201;

            }

        } else {

            $playload = [
                'success' => 0,
                'message' => 'Please submit email & password of the user.'
            ];
            $code = 400;

        }

        return new JsonResponse($playload, $code);
    }

}