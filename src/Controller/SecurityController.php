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

        $user = new User();

        $email = $request->get('email');
        $user->setEmail($email);

        $password = $passwordEncoder->encodePassword($user, $request->get('password'));
        $user->setPassword($password);

        $user->setRoles(['ROLE_USER']);

        $errors = $validator->validate($user);

        if (count($errors) > 0) {
            // customizing errors array
            $cErrors = [];
            foreach ($errors as $error) {
                $cErrors[] = ['property'  => $error->getPropertyPath(), 'message'   => $error->getMessage()];
            }

            $playload = [
                'success' => 0,
                'message' => 'Please check email & password errors.',
                'errors' => $cErrors
            ];
            $code = 406;

        } else {
            $em = $this->getDoctrine()->getManager();

            $em->persist($user);
            $em->flush();

            $playload = [
                'success' => 1,
                'message' => 'User was created successfully.',
            ];
            $code = 201;

        }


        return new JsonResponse($playload, $code);
    }

}