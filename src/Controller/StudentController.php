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
use App\Requests\IntranetClient;
use App\Repository\StudentRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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

    /**
     * @Route("/grades", name="grade", methods={"GET"})
     */
    public function getStudentGrades(
        Request $request,
        IntranetClient $intranetClient,
        StudentRepository $studentRepository
    ): JsonResponse {
        $username = $request->query->get('username');
        
        if (null !== $request->query->get('password')) {
            $student = (new Student)
                ->setUsername($username)
                ->setPassword($request->query->get('password'));
        } else {
            $student = $studentRepository->findOneBy(['username' => $username]);
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
