<?php

namespace App\Controller;

use App\Repository\TimeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserMeTimeController extends AbstractController
{
    private $timeRepo;
    private $em;

    public function __construct(
        private Security $security,
        TimeRepository $timeRepo,
        EntityManagerInterface $em
    ) {
        $this->timeRepo = $timeRepo;
        $this->em = $em;
    }
    public function __invoke($id)
    {
        $userAuth = $this->security->getUser();
        if (!$userAuth) {
            $errorMessage = (object) array('message' => 'Invalid credentials.');
            return new JsonResponse(json_encode($errorMessage), Response::HTTP_BAD_REQUEST, [], true);
        }
        $verb = $this->timeRepo->findOneBy(['id' => $id]);
        $verb->addUser($userAuth);
        $this->em->persist($verb);
        $this->em->flush();
        return 'ok';
    }
}
