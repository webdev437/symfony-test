<?php

declare(strict_types=1);

namespace App\Commands;

use App\Repository\ProductsRepository;
use App\Repository\OrdersRepository;
use App\Repository\CustomerRepository;
use App\Repository\UserRepository;
use Swiftmailer\Swift_Mailer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class ImportDataCommand
 * @package App\Commands
 */
final class ImportDataCommand extends AbstractCommand
{
    /** @var string */
    protected static $defaultName = 'importFromShopify';

    /**
     * ImportDataCommand constructor.
     * @param ProductsRepository $productsRepository
     * @param OrdersRepository $ordersRepository
     * @param CustomerRepository $customerRepository
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $entityManager
     * @param \Swift_Mailer $mailer
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(
        ProductsRepository $productsRepository,
        OrdersRepository $ordersRepository,
        CustomerRepository $customerRepository,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        \Swift_Mailer $mailer,
        UserPasswordEncoderInterface $passwordEncoder
    ) 
    {
        parent::__construct();
        $this->commands = [
            // new ImportCollectionData($collectionRepository, $entityManager),
            // new ImportProductData($productsRepository, $entityManager),
            new ImportCustomerData($customerRepository, $userRepository, $entityManager, $passwordEncoder, $mailer),
            new ImportOrderData($ordersRepository, $customerRepository, $passwordEncoder, $entityManager),
            // new ImportPayoutData($payoutsRepository, $entityManager),
            // new ImportTransactionData($transactionsRepository, $entityManager),
            // new ImportFulfillmentOrderData($fullfillmentOrderRepository, $entityManager),
            // new ImportCustomerAddressData($customerAddressRepository, $entityManager),
            // new ImportLocationData($locationsRepository, $entityManager),
            // new ImportThemeData($themesRepository, $entityManager),
            // new ImportGiftCardData($giftCardRepository, $entityManager),
            // new ImportPolicyData($policiesRepository, $entityManager)
            
        ];
    }


    /**
     *
     */
    protected function configure(): void
    {
        $this->setDescription('Imports  data from shopify.');
    }
}
