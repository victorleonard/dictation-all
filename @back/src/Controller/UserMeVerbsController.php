<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserMeVerbsController extends AbstractController
{
    private $userRepo;

    public function __construct(
        private Security $security,
        UserRepository $userRepo
    ) {
        $this->userRepo = $userRepo;
    }
    public function __invoke()
    {
        $userAuth = $this->security->getUser();
        if (!$userAuth) {
            $errorMessage = (object) array('message' => 'Invalid credentials.');
            return new JsonResponse(json_encode($errorMessage), Response::HTTP_BAD_REQUEST, [], true);
        }
        $user = $this->userRepo->findOneBy(['id' => $userAuth->getId()]);
        $verbs = $user->getVerbs();
        $verbsData = [];
        foreach($verbs as $verb) {
            $wordsData[] = [
                'id' => $verb->getId()
            ];
        }
        /* usort($wordsData, function ($a, $b) {
            return strcmp($a['value'], $b['value']);
        }); */

        return $verbs;
    }
}
