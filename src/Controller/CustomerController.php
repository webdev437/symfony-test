<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
 * Class CustomerController
 * @package App\Controller
 */
class CustomerController extends AbstractController
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
     * @Route("/customer", name="customer")
     * @return Response
     */
    public function index() : Response
    {
        $successMsg =  $this->session->get('sucessMsg');
        $warningMsg =  $this->session->get('warningMsg');
        if(isset($successMsg)){
            $this->addFlash('success', $successMsg);
        }
        if(isset($warningMsg)){
            $this->addFlash('warning', $warningMsg);
        }
        $customers = $this->customerRepository->findUndeletedCustomers();
        
        return $this->render('customer/index.html.twig', [
            'controller_name' => 'CustomerController',
            'customers' => $customers
        ]);
        
    }

    /**
     * @Route("/customer/editCustomer", name="editCustomer", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function editCustomer(Request $request) : Response
    {
        $this->session->remove('sucessMsg');
        $id = $request->request->get('id');
        $customerRecord = $this->customerRepository->find($id);
        $userRecord = $customerRecord->getUser();
        if($request->request->get('allowedRunReport') == "checked"){
            $customerRecord->setIsAllowedRunReport("checked");
        }
        else{
            $customerRecord->setIsAllowedRunReport(null);
        }
        if($request->request->get('canGenerateReports') == "selected"){
            $customerRecord->setCanGenerateReports("selected");
        }
        else{
            $customerRecord->setCanGenerateReports(null);
        }
        $userRecord->setFirstName($request->request->get('firstName'));
        $userRecord->setLastName($request->request->get('lastName'));
        $userRecord->setEmail($request->request->get('email'));
        if($request->request->get('password')){
            $userRecord->setPassword($this->passwordEncoder->encodePassword(
                $userRecord,
                $request->request->get('password')
            ));
        }
        $this->entityManager->persist($customerRecord);
        $this->entityManager->persist($userRecord);
        $this->entityManager->flush();
        $this->session->set('sucessMsg', 'Current customer was updated succesfully!!');

        return $this->redirectToRoute('customer');   
    }

    /**
     * @Route("/customer/deleteCustomer", name="deleteCustomer", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function deleteCustomer(Request $request) : Response
    {
        $this->session->remove('sucessMsg');
        $id = $request->request->get('id');
        if(isset($id)){
            $id = $request->request->get('id');
            $customerRecord = $this->customerRepository->find($id);
            $userRecord = $customerRecord->getUser();
            $customerRecord->setIsDeleted("deleted");
            $userRecord->setEmail("");
            $this->entityManager->persist($customerRecord);
            $this->entityManager->persist($userRecord);
            $this->entityManager->flush();
               
        }
        $this->session->set('sucessMsg', 'Current customer was deleted succesfully!!');

        return $this->redirectToRoute('customer');
    }

    /**
     * @Route("/customer/deleteSelCustomer", name="deleteSelCustomer", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function deleteSelCustomer(Request $request) : Response
    {
        $this->session->remove('sucessMsg');
        $this->session->remove('warningMsg');
        $options = $request->request->get('options');
        if(isset($options)){
            foreach( $options as $option){
                $customerRecord = $this->customerRepository->find($option);
                $userRecord = $customerRecord->getUser();
                $customerRecord->setIsDeleted("deleted");
                $userRecord->setEmail("");
                $this->entityManager->persist($customerRecord);
                $this->entityManager->persist($userRecord);
                $this->entityManager->flush(); 
            }
            $this->session->set('sucessMsg', 'Current selected customers was deleted succesfully!!');
        }
        else{
            $this->session->set('warningMsg', 'You have to select any customer you want to remove!!');
        }

        return $this->redirectToRoute('customer');
    }
    
}
