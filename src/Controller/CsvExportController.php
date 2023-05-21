<?php

namespace App\Controller;

use App\Repository\CustomerRepository;
use App\Repository\OrdersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CsvExportController
 * @package App\Controller
 */
class CsvExportController extends AbstractController
{

    private $session;

    /**@var CustomerRepository */
    private $customerRepository;

    /**@var OrdersRepository */
    private $ordersRepository;

    /**
     * OrderController constructor.
     * @param SessionInterface $session
     * @param CustomerRepository $customerRepository
     * @param OrdersRepository $ordersRepository
     */
    public function __construct(
        SessionInterface $session,
        CustomerRepository $customerRepository,
        OrdersRepository $ordersRepository
    ) {
        $this->session = $session;
        $this->customerRepository = $customerRepository;
        $this->ordersRepository = $ordersRepository;
    }

    /**
     * @Route("/orders/csvorders", name="csvAllorders")
     * @return Response
     */
    public function csvExportAllorders(): Response
    {
        $allOrders = $this->ordersRepository->findAll();
        $ordersList = [['No', 'Customer ID', 'FirstName', 'LastName', 'CustomerEmail', 'ProcessDate']];
        $i = 1;
        foreach ($allOrders as $order) {
            $customerId = $order->getCustomer()->getUser()->getId();
            $firstName = $order->getCustomer()->getUser()->getFirstName();
            $lastName = $order->getCustomer()->getUser()->getLastName();
            $customerEmail = $order->getCustomer()->getUser()->getEmail();
            $processDate = $order->getProcessedAt()->format('Y-m-d H:i:s');
            array_push($ordersList, array($i, $customerId, $firstName, $lastName, $customerEmail, $processDate));
            $i++;
        }

        $fp = fopen('php://output', 'w');

        foreach ($ordersList as $order) {
            fputcsv($fp, $order);
        }

        $response = new Response();
        $response->headers->set('Content-Type', 'text/csv');
        //it's gonna output in a testing.csv file
        $response->headers->set('Content-Disposition', 'attachment; filename="allorders.csv"');

        return $response;
    }

    /**
     * @Route("/order/csvselorders", name="csvSelorders")
     * @return Response
     */
    public function csvExportSelorders(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $ifMainUser = $user->getParentId();
        $customerId = "";
        if (!$ifMainUser) {
            $customerId = $user->getCustomer()->getCustomerId();
        }
        $fromDate = $this->session->get('fromDate');
        $toDate = $this->session->get('toDate');
        if (!$fromDate) {
            $fromDate = "1970-01-01";
        }
        if (!$toDate) {
            $toDate = date('Y-m-d');
        }
        $selOrders = $this->ordersRepository
            ->findOrdersByFilters($customerId, date("Y-m-d", strtotime($fromDate)), date("Y-m-d", strtotime($toDate)));
        $ordersList = [['No', 'OrderNumber', 'GateWay', 'TotalPrice', 'TotalWeight', 'TotalTax', 'ProcessDate']];
        $i = 1;
        foreach ($selOrders as $order) {
            $orderNumber = $order->getNumber();
            $gateway = $order->getGateway();
            $totalPrice = $order->getTotalPrice();
            $totalWeight = $order->getTotalWeight();
            $totalTax = $order->getTotalTax();
            $processDate = $order->getProcessedAt()->format('Y-m-d H:i:s');
            array_push($ordersList, array($i, $orderNumber, $gateway, $totalPrice, $totalWeight, $totalTax, $processDate));
            $i++;
        }

        $fp = fopen('php://output', 'w');

        foreach ($ordersList as $selorder) {
            fputcsv($fp, $selorder);
        }

        $response = new Response();
        $response->headers->set('Content-Type', 'text/csv');
        //it's gonna output in a testing.csv file
        $response->headers->set('Content-Disposition', 'attachment; filename="suborders.csv"');

        return $response;

    }

}
