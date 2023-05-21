<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\User;
use App\Entity\Customer;
use App\Repository\CustomerRepository;
use App\Repository\UserRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class SubUsersController
 * @package App\Controller
 */
class SubUsersController extends AbstractController
{
    private $session;

    /**@var UserPasswordEncoderInterface */
    private $passwordEncoder;

    /**@var CustomerRepository */
    private $customerRepository;

    /**@var UserRepository */
    private $userRepository;

    /**@var EntityManagerInterface */
    private $entityManager;

    /**
     * CustomerController constructor.
     * @param CustomerRepository $customerRepository
     * @param UserRepository $userRepository
     * @param SessionInterface $session
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        CustomerRepository $customerRepository,
        UserRepository $userRepository,
        SessionInterface $session,
        UserPasswordEncoderInterface $passwordEncoder,
        EntityManagerInterface $entityManager
    )
    {
        $this->customerRepository = $customerRepository;
        $this->userRepository = $userRepository;
        $this->session = $session;
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
    }


    /**
     * @Route("/subuser", name="subuser")
     * @return Response
     */
    public function index() : Response
    {
        $successMsg =  $this->session->get('sucessMsg');
        if(isset($successMsg)){
            $this->addFlash('success', $successMsg);
        }
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $userId = $user->getId();
        $subUsers = $this->userRepository->findSubUsersByParentId($userId);
        
        return $this->render('subUser/index.html.twig', [
            'controller_name' => 'SubUsersController',
            'subUsers' => $subUsers
        ]);
        
    }

    /**
     * @Route("/subuser/editSubUser", name="editSubUser", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function editSubUser(Request $request) : Response
    {
        $this->session->remove('sucessMsg');
        $id = $request->request->get('id');
        $userRecord = $this->userRepository->find($id);
        $userRecord->setFirstName($request->request->get('firstName'));
        $userRecord->setLastName($request->request->get('lastName'));
        $userRecord->setEmail($request->request->get('email'));
        if($request->request->get('password')){
            $userRecord->setPassword($this->passwordEncoder->encodePassword(
                $userRecord,
                $request->request->get('password')
            ));
        }
        $this->entityManager->persist($userRecord);
        $this->entityManager->flush();
        $this->session->set('sucessMsg', 'Current user was updated succesfully!!');

        return $this->redirectToRoute('subuser');   
    }

    
    /**
     * @Route("/subuser/addSubUser", name="addSubUser", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function addSubUser(Request $request) : Response
    {
        $this->session->remove('sucessMsg');
        $id = $request->request->get('id');
        $userRecord =new User();
        $userRecord->setFirstName($request->request->get('firstName'));
        $userRecord->setLastName($request->request->get('lastName'));
        $userRecord->setEmail($request->request->get('email'));
        $userRecord->setRoles(['ROLE_CUSTOMER']);
        $userRecord->setPassword($this->passwordEncoder->encodePassword(
            $userRecord,
            $request->request->get('password')
        ));
        $userRecord->setParentId($id);
        $this->entityManager->persist($userRecord);
        $this->entityManager->flush();
        $this->session->set('sucessMsg', 'Current SubUser was added succesfully!!');

        return $this->redirectToRoute('subuser');   
    }

    /**
     * @Route("/subuser/deleteSubuser", name="deleteSubuser", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function deleteSubuser(Request $request) : Response
    {
        $this->session->remove('sucessMsg');
        $id = $request->request->get('id');
        if(isset($id)){
            $id = $request->request->get('id');
            $userRecord = $this->userRepository->find($id);
            $userRecord->setIsDeleted("deleted");
            $userRecord->setEmail("");
            $this->entityManager->persist($userRecord);
            $this->entityManager->flush();
               
        }
        $this->session->set('sucessMsg', 'Current user was deleted succesfully!!');

        return $this->redirectToRoute('subuser');
    }

    /**
     * @Route("/subuser/deleteSelSubuser", name="deleteSelSubuser", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function deleteSelSubuser(Request $request) : Response
    {
        $this->session->remove('sucessMsg');
        $this->session->remove('warningMsg');
        $options = $request->request->get('options');
        if(isset($options)){
            foreach( $options as $option){
                $userRecord = $this->userRepository->find($option);
                $userRecord->setIsDeleted("deleted");
                $userRecord->setEmail("");
                $this->entityManager->persist($userRecord);
                $this->entityManager->flush(); 
            }
            $this->session->set('sucessMsg', 'Current selected users was deleted succesfully!!');
        }
        else{
            $this->session->set('warningMsg', 'You have to select any user you want to remove!!');
        }

        return $this->redirectToRoute('subuser');
    }
}



