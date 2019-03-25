<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use App\Form\StudentType;
use App\Entity\Student;
use App\Requests\IntranetClient;
use App\Repository\StudentRepository;

/**
 * @Route("/grades", name="grades_")
 */
class GradesController extends AbstractController
{
    /**
     * @Route("/all", name="grade", methods={"POST"})
     */
    public function getStudentGrades(
        Request $request,
        IntranetClient $intranetClient,
        StudentRepository $studentRepository
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $form = $this->createForm(StudentType::class, new Student);
        $form->submit($data);
        
        if (!$form->isValid()) {
            throw new BadRequestHttpException;
        }
        
        $student = $form->getData();
        
        if ($student->getPassword() == StudentType::PASSWORD_PLACEHOLDER) {
            $student = $studentRepository->findOneBy(['username' => $student->getUsername()]);
        }
        
        if (null === $student) {
            throw new NotFoundHttpException;
        }

        $grades = $intranetClient->getGrades($student);
        
        return $this->json(
            ['grades' => $grades],
            JsonResponse::HTTP_OK,
            [],
            ['groups' => 'default']
        );
    }
}
