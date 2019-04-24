<?php

namespace App\Requests;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\CssSelector\CssSelectorConverter;
use App\Entity\Student;
use App\Entity\Grade;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class IntranetClient
{
    private $client;
    private $jar;
    private $cssSelectorConverter;

    public function __construct()
    {
        $this->client = new Client();
        $this->jar = new CookieJar();
        $this->cssSelectorConverter = new CssSelectorConverter();
    }
    
    public function login(Student $student): void
    {
        $response = $this->client->request('POST', 'http://intranet.supinternet.fr', [
            'query' => [
                'action' => 'login',
            ],
            'form_params' => [
                'login' => $student->getUsername(),
                'pwd' => $student->getPassword(),
                'do' => 'Connexion',
            ],
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded'
            ],
            'cookies' => $this->jar
        ]);
        
        $crawler = new Crawler((string) $response->getBody());
        $isLoggedFilter = $this->cssSelectorConverter->toXPath('header #v_card #v_card_photo img');

        try {
            $crawler->filterXPath($isLoggedFilter)->html();
        } catch (\InvalidArgumentException $e) {
            throw new BadRequestHttpException('Bad credentials');
        }
    }
    
    public function getGrades(Student $student): array
    {
        $this->login($student);

        $response = $this->client->request('GET', 'http://intranet.supinternet.fr', [
            'query' => [
                'action' => 'grades',
            ],
            'cookies' => $this->jar
        ]);

        $filters = [
            'date' => $this->cssSelectorConverter->toXPath('tr .name:nth-child(1)'),
            'ECUE' => $this->cssSelectorConverter->toXPath('tr .name:nth-child(2)'),
            'project' => $this->cssSelectorConverter->toXPath('tr .name:nth-child(3)'),
            'grade' => $this->cssSelectorConverter->toXPath('tr .grade b'),
            'comment' => $this->cssSelectorConverter->toXPath('tr .comment'),
        ];
        
        $crawler = new Crawler((string) $response->getBody());
        
        $results = [];
        
        foreach ($filters as $index => $filter) {
            $results[$index] = $crawler->filterXPath($filter)->each(function (Crawler $node, $i) {
                return $node->text();
            });
        }
        
        $items = [];

        for ($i = 0; $i < count(array_values($results)[0]); $i++) {
            $date = explode('/', $results['date'][$i]);
            $items[] = [
                'date' => implode('/', [$date[2], $date[1], $date[0]]),
                'ECUE' => $results['ECUE'][$i],
                'project' => $results['project'][$i],
                'grade' => $results['grade'][$i],
                'comment' => $results['comment'][$i],
            ];
        }

        foreach ($items as $index => $grade) {
            $items[$index] = (new Grade)
                ->setDate(new \DateTime($grade['date']))
                ->setECUE($grade['ECUE'])
                ->setProject($grade['project'])
                ->setValue($grade['grade'])
                ->setComment($grade['comment']);
        }
        
        return $items;
    }
}
