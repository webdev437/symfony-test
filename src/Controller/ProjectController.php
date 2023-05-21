<?php

namespace App\Controller;

use App\Entity\Project;
use App\Repository\ProjectRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ProjectController
 * @package App\Controller
 */
class ProjectController extends AbstractController
{

    private $session;

    /**@var ProjectRepository */
    private $projectRepository;

    /**@var UserRepository */
    private $userRepository;

    /**@var EntityManagerInterface */
    private $entityManager;

    /**
     * UserController constructor.
     * @param SessionInterface $session
     * @param ProjectRepository $projectRepository
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        SessionInterface $session,
        ProjectRepository $projectRepository,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->session = $session;
        $this->projectRepository = $projectRepository;
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/user/{id}", name="viewProject")
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function index(Request $request, int $id): Response
    {
        $this->session->remove('sucessMsg');
        $projects = $this->projectRepository->findByUser($id);
        $user = $this->userRepository->find($id);

        return $this->render('project/index.html.twig', [
            'controller_name' => 'ProjectController',
            'projects' => $projects,
            'user' => $user,
            'count' => count($projects)
        ]);
    }

    /**
     * @Route("/user/{id}/{projectId}/deleteProject", name="deleteProject", methods={"POST"})
     * @param Request $request
     * @param int $id
     * @param int $projectId
     * @return Response
     */
    public function deleteProject(Request $request, int $id, int $projectId): Response
    {
        $this->session->remove('sucessMsg');
        if (isset($projectId)) {
            $project = $this->projectRepository->find($projectId);
            $this->entityManager->remove($project);
            $this->entityManager->flush();
        }
        $this->session->set('sucessMsg', 'Current project was deleted succesfully!!');

        return $this->redirectToRoute('viewProject', [
            'id' => $id
        ]);
    }

    /**
     * @Route("/user/{id}/addProject", name="addProject", methods={"POST"})
     * @param Request $request
     *  @param int $id
     * @return Response
     */
    public function addProject(Request $request, int $id): Response
    {
        $this->session->remove('sucessMsg');
        $user = $this->userRepository->find($id);
        $project = new Project();

        $project->setUser($user);
        $project->setTitle($request->request->get('title'));
        $project->setDescription($request->request->get('description'));
        $this->entityManager->persist($project);
        $this->entityManager->flush();
        $this->session->set('sucessMsg', 'An project was added succesfully!!');

        return $this->redirectToRoute('viewProject', [
            'id' => $id
        ]);
    }
}