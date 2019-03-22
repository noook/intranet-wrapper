<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Requests\IntranetClient;
use App\Repository\StudentRepository;

class GetStudentGrades extends Command
{
    protected static $defaultName = 'intranet:grades';
    private $client;
    private $studentRepository;
    
    public function __construct(IntranetClient $client, StudentRepository $studentRepository)
    {
        $this->client = $client;
        $this->studentRepository = $studentRepository;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Gets a student grades')
            ->addArgument('username', InputArgument::REQUIRED, 'Argument description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $username = $input->getArgument('username');

        $student = $this->studentRepository->findOneBy(['username' => $username]);
        
        if (null === $student) {
            return $io->error('Student not found in database');
        }

        dump($this->client->getGrades($student));
        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');
    }
}
