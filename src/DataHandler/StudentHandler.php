<?php

namespace App\DataHandler;

use Doctrine\Common\Persistence\ObjectManager;
use App\Entity\Student;

class StudentHandler
{
    private $em;

    public function __construct(ObjectManager $em)
    {
        $this->em = $em;
    }
    
    public function saveStudent(Student $student): Student
    {
        $this->em->persist($student);
        $this->em->flush();
        return $student;
    }
}
