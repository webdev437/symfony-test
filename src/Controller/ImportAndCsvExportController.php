<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Repository\CustomerRepository;
use App\Repository\OrdersRepository;
use function array_push;
use function format;

/**
 * Class ImportAndCsvExportController
 * @package App\Controller
 */
class ImportAndCsvExportController extends AbstractController
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
    )
    {
        $this->session = $session;
        $this->customerRepository = $customerRepository;
        $this->ordersRepository = $ordersRepository;
    }
    
    /**
     * @Route("/dashboard/csvorders", name="ordersFromShopify")
     * @return Response
     */
    public function csvExportOrdersFromShopify() : Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $customerId = $user->getCustomer()->getCustomerId();
        $url = 'https://ecd027e7165870006ff21f58e278c3fc:d11a55a65c712f779df5f28f551a4a89@thirty-six-purchase-it.myshopify.com/admin/api/2020-01/orders.json';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = json_decode(curl_exec($ch));
        curl_close($ch);
        $ordersByCustomer = [];
        foreach($result->orders as $order){
            if($order->customer->id == $customerId){
                array_push($ordersByCustomer, $order);
            }
        }
        $ordersList = [['No','Customer ID','Order Id','Email','Create Date','Update Date','Number','Gateway'
        ,'Total Price','Subtotal Price','Total Weight','Total Tax','Currency','Financial Status', 'Total Discount'
        ,'Total Lineitems Price']];
        $i = 1;
        foreach($ordersByCustomer as $order){
            $realcustomerId= $order->customer->id;
            $orderId = $order->id;
            $email = $order->email;
            $createdDate = date_format(new DateTimeImmutable($order->created_at),"Y/m/d H:i:s");
            $updatedDate = date_format(new DateTimeImmutable($order->updated_at),"Y/m/d H:i:s");
            $number= $order->number;
            $gateway = $order->gateway;
            $totalPrice = $order->total_price;
            $subTotalPrice = $order->subtotal_price;
            $totalWeight = $order->total_weight;
            $totalTax= $order->total_tax;   
            $currency = $order->currency;
            $financialStatus = $order->financial_status;
            $totalDiscount = $order->total_discounts;
            $totalLineItemsPrice = $order->total_line_items_price;
            array_push($ordersList, array($i,$realcustomerId, $orderId, $email, $createdDate, $updatedDate,
            $number,$gateway,$totalPrice, $subTotalPrice,$totalWeight, $totalTax, $currency, $financialStatus,$totalDiscount,
            $totalLineItemsPrice
            ));
            $i++;
        }
        
        $fp = fopen('php://output', 'w');

        foreach ($ordersList as $order) {
            fputcsv($fp, $order);
        }

        $response = new Response();
        $response->headers->set('Content-Type', 'text/csv');
        //it's gonna output in a testing.csv file
        $response->headers->set('Content-Disposition', 'attachment; filename="shopifyorders.csv"');

        return $response;
    }

    /**
     * @Route("/dashboard/csvfullfillmentorder", name="fullfillmentOrderFromShopify")
     * @return Response
     */
    public function csvExportFullfillmentOrderFromShopify() : Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $customerId = $user->getCustomer()->getCustomerId();
        $url = 'https://ecd027e7165870006ff21f58e278c3fc:d11a55a65c712f779df5f28f551a4a89@thirty-six-purchase-it.myshopify.com/admin/api/2020-01/orders.json';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = json_decode(curl_exec($ch));
        curl_close($ch);
        $ordersByCustomer = [];
        foreach($result->orders as $order){
            if($order->customer->id == $customerId){
                array_push($ordersByCustomer, $order);
            }
        }
        $fullfillmentOrdersList = [['No','Fullfillment ID','Shop Id','Order Id','Assigned Location Id']];
        $i = 1;
        foreach($ordersByCustomer as $order){
            $url1 = 'https://ecd027e7165870006ff21f58e278c3fc:d11a55a65c712f779df5f28f551a4a89@thirty-six-purchase-it.myshopify.com/admin/api/2020-01/orders/'.$order->id.'/fulfillment_orders.json';
            $ch1 = curl_init($url1);
            curl_setopt($ch1, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
            curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
            $result1 = json_decode(curl_exec($ch1));
            curl_close($ch1);
            $fullfillmentId= $result1->fulfillment_orders[0]->id;
            $shopId = $result1->fulfillment_orders[0]->shop_id;
            $orderId = $result1->fulfillment_orders[0]->order_id;
            $assignedLocationId = $result1->fulfillment_orders[0]->assigned_location_id;
            array_push($fullfillmentOrdersList, array($i,$fullfillmentId, $shopId, $orderId, $assignedLocationId));
            $i++;
        }
        
        $fp = fopen('php://output', 'w');

        foreach ($fullfillmentOrdersList as $fullfillmentorder) {
            fputcsv($fp, $fullfillmentorder);
        }

        $response = new Response();
        $response->headers->set('Content-Type', 'text/csv');
        //it's gonna output in a testing.csv file
        $response->headers->set('Content-Disposition', 'attachment; filename="fullfillmentorders.csv"');

        return $response;
    }
   
    /**
     * @Route("/dashboard/csvgiftcard", name="giftCardFromShopify")
     * @return Response
     */
    public function csvExportGiftCardFromShopify() : Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
        $customerId = $user->getCustomer()->getCustomerId();
        $url = 'https://ecd027e7165870006ff21f58e278c3fc:d11a55a65c712f779df5f28f551a4a89@thirty-six-purchase-it.myshopify.com/admin/api/2020-01/gift_cards.json';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = json_decode(strval(curl_exec($ch)));
        curl_close($ch);
        $giftcardByCustomer = [];
        foreach($result->gift_cards as $gift_card){
            if($gift_card->customer_id == $customerId){
                array_push($giftcardByCustomer, $gift_card);
            }
        }
        $giftCardList = [['No','Gift Card Id','Balance','Create Date','Update Date', 'Currency', 'Initial Value', 'Order Id']];
        $i = 1;
        foreach($giftcardByCustomer as $giftcardInfo){
            
            $giftCardId= $giftcardInfo->id;
            $balance = $giftcardInfo->balance;
            $createdDate = date_format(new DateTimeImmutable($giftcardInfo->created_at),"Y/m/d H:i:s");
            $updatedDate = date_format(new DateTimeImmutable($giftcardInfo->updated_at),"Y/m/d H:i:s");
            $currency = $giftcardInfo->currency;
            $initialValue = $giftcardInfo->initial_value;
            $orderId = $giftcardInfo->order_id;
            array_push($giftCardList, array($i,$giftCardId, $balance, $createdDate, $updatedDate, $currency, $initialValue, $orderId));
            $i++;
        }
        
        $fp = fopen('php://output', 'w');

        foreach ($giftCardList as $giftCard) {
            fputcsv($fp, $giftCard);
        }

        $response = new Response();
        $response->headers->set('Content-Type', 'text/csv');
        //it's gonna output in a testing.csv file
        $response->headers->set('Content-Disposition', 'attachment; filename="giftCard.csv"');

        return $response;
    }
    
}
