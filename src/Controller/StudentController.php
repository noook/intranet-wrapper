<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Form\NewStudentType;
use App\Entity\Student;
use App\DataHandler\StudentHandler;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @Route("/student", name="student_")
 */
class StudentController extends AbstractController
{
    /**
     * @Route("", name="new", methods={"POST"})
     */
    public function newStudent(Request $request, StudentHandler $studentHandler): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $form = $this->createForm(NewStudentType::class, new Student);
        $form->submit($data);
        
        if (!$form->isValid()) {
            throw new BadRequestHttpException;
        }
        
        $student = $form->getData();
        $studentHandler->saveStudent($student);
        
        return $this->json(
            ['student' => $student],
            JsonResponse::HTTP_CREATED,
            [],
            ['groups' => 'default']
        );
    }
}
