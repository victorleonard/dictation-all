<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Repository\VerbRepository;
use App\Repository\TimeRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HoursRandomController extends AbstractController
{
    private $timeRepo;

    public function __construct(
        private Security $security,
        TimeRepository $timeRepo
    ) {
        $this->timeRepo = $timeRepo;
    }
    public function __invoke()
    {
        $userAuth = $this->security->getUser();
        if (!$userAuth) {
            $errorMessage = (object) array('message' => 'Invalid credentials.');
            return new JsonResponse(json_encode($errorMessage), Response::HTTP_BAD_REQUEST, [], true);
        }
        $word = $this->timeRepo->findRandomTime();
        return $word;
    }
}
