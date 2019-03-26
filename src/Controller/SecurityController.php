<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use App\Entity\User;
use App\Form\UserType;
use App\DataHandler\UserHandler;

class SecurityController extends AbstractController
{
    /**
     * @Route("/register", name="register", methods={"POST"})
     */
    public function register(Request $request, UserHandler $userHandler): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $form = $this->createForm(UserType::class, new User);
        $form->submit($data);

        if (!($form->isValid() && $form->isSubmitted())) {
            throw new BadRequestHttpException;
        }

        $user = $form->getData();
        $userHandler->register($user);

        return $this->json(
            $user,
            JsonResponse::HTTP_CREATED,
            [],
            ['groups' => 'default']
        );
    }
}
