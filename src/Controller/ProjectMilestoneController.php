<?php

namespace App\Controller;

use App\Entity\ProjectMilestone;
use App\Repository\ProjectMilestoneRepository;
use App\Repository\UserRepository;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

use DateTime;

/**
 * Class ProjectMilestoneController
 * @package App\Controller
 */
class ProjectMilestoneController extends AbstractController
{

    private $session;

    /**@var ProjectMilestoneRepository */
    private $projectMilestoneRepository;

    /**@var UserRepository */
    private $userRepository;

    /**@var ProjectRepository */
    private $projectRepository;

    /**@var EntityManagerInterface */
    private $entityManager;

    /**
     * UserController constructor.
     * @param SessionInterface $session
     * @param ProjectMilestoneRepository $projectMilestoneRepository
     * @param ProjectRepository $projectRepository
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        SessionInterface $session,
        ProjectMilestoneRepository $projectMilestoneRepository,
        ProjectRepository $projectRepository,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->session = $session;
        $this->projectMilestoneRepository = $projectMilestoneRepository;
        $this->projectRepository = $projectRepository;
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/user/{id}/{projectId}", name="viewMilestone")
     * @param Request $request
     * @param int $id
     * @param int $projectId
     * @return Response
     */
    public function index(Request $request, int $id, int $projectId): Response
    {
        $this->session->remove('sucessMsg');
        $milestones = $this->projectMilestoneRepository->findByProject($projectId);
        $user = $this->userRepository->find($id);
        $project = $this->projectRepository->find($projectId);

        return $this->render('milestone/index.html.twig', [
            'controller_name' => 'ProjectMilestoneController',
            'milestones' => $milestones,
            'user' => $user,
            'project' => $project,
            'count' => count($milestones)
        ]);
    }

    /**
     * @Route("/user/{id}/{projectId}/{milestoneId}/deleteMilestone", name="deleteMilestone", methods={"POST"})
     * @param Request $request
     * @param int $id
     * @param int $projectId
     * @param int $milestoneId
     * @return Response
     */
    public function deleteMilestone(
        Request $request,
        int $id,
        int $projectId,
        int $milestoneId
    ): Response {
        $this->session->remove('sucessMsg');
        if (isset($milestoneId)) {
            $milestone = $this->projectMilestoneRepository->find($milestoneId);
            $this->entityManager->remove($milestone);
            $this->entityManager->flush();
        }
        $this->session->set('sucessMsg', 'Current project milestone was deleted succesfully!!');

        return $this->redirectToRoute('viewMilestone', [
            'id' => $id,
            'projectId' => $projectId
        ]);
    }

    /**
     * @Route("/user/{id}/{projectId}/addMilestone", name="addMilestone", methods={"POST"})
     * @param Request $request
     * @param int $id
     * @param int $projectId
     * @return Response
     */
    public function addMilestone(Request $request, int $id, int $projectId): Response
    {
        $this->session->remove('sucessMsg');
        $project = $this->projectRepository->find($projectId);
        $milestone = new ProjectMilestone();

        $milestone->setProject($project);
        $milestone->setTitle($request->request->get('title'));
        $milestone->setDescription($request->request->get('description'));
        $milestone->setMilestoneDeadline(new DateTime($request->request->get('milestoneDeadline')));
        $this->entityManager->persist($milestone);
        $this->entityManager->flush();
        $this->session->set('sucessMsg', 'An project milestone was added succesfully!!');

        return $this->redirectToRoute('viewMilestone', [
            'id' => $id,
            'projectId' => $projectId
        ]);
    }

}