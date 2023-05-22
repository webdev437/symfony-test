<?php

namespace App\Controller;

use App\Repository\ProjectRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class UserController
 * @package App\Controller
 */
class UserController extends AbstractController
{

    private $session;

    /**@var ProjectRepository */
    private $projectRepository;

    /**@var UserRepository */
    private $userRepository;

    /**@var EntityManagerInterface */
    private $entityManager;

    private $passwordEncoder;
    /**
     * UserController constructor.
     * @param SessionInterface $session
     * @param ProjectRepository $projectRepository
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(
        SessionInterface $session,
        ProjectRepository $projectRepository,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        $this->session = $session;
        $this->projectRepository = $projectRepository;
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
    }


    /**
     * @Route("/user", name="user", methods={"GET"})
     * @return Response
     */
    public function index(): Response
    {
        $this->session->remove('sucessMsg');
        $users = $this->userRepository->findUndeletedUsers();
        $includeProjects = [];
        foreach($users as $key => $user) {
            $count = count($user->getProjects());
            if ($count < 1) {
                $includeProjects[$key] = true;
            } else {
                $includeProjects[$key] = false;
            }
        }
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
            'users' => $users,
            'includeProjects' => $includeProjects
        ]);

    }

    /**
     * @Route("/user/deleteUser", name="deleteUser", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function deleteUser(Request $request): Response
    {
        $this->session->remove('sucessMsg');
        $id = $request->request->get('id');
        if (isset($id)) {
            $user = $this->userRepository->find($id);
            $this->entityManager->remove($user);
            $this->entityManager->flush();
        }
        $this->session->set('sucessMsg', 'Current user was deleted succesfully!!');

        return $this->redirectToRoute('user');
    }
}