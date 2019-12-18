<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Repository\StatusRepository;
use App\Repository\ProductRepository;
use App\Repository\CustomerOrderRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminDashboardController extends AbstractController
{
    /**
     * @Route("/admin", name="admindashboard")
     */
    public function index(ProductRepository $products, CustomerOrderRepository $orders, UserRepository $users, StatusRepository $status) {
        
        $waitingStatus = $status->find(1);
        $waitingOrders = $waitingStatus->getCustomerOrders();
        
        $sentStatus = $status->find(2);
        $sentOrders = $sentStatus->getCustomerOrders();
        
        $finishedStatus = $status->find(3);
        $finishedOrders = $finishedStatus->getCustomerOrders();

        return $this->render('admin/dashboard/index.html.twig', [
            'waiting' => $waitingOrders,
            'sent' => $sentOrders,
            'finished' => $finishedOrders,
            'users' => $users->findAll(),
            'orders' => $orders->findAll(),
            'products' => $products->findAll()
        ]);
    }
}
