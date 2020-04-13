<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Repository\StatusRepository;
use App\Repository\ProductRepository;
use App\Repository\PreferenceRepository;
use App\Repository\CustomerOrderRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminDashboardController extends AbstractController
{
    /**
     * @Route("/admin", name="admindashboard")
     */
    public function index(ProductRepository $products, CustomerOrderRepository $orders, UserRepository $users, StatusRepository $status,  PreferenceRepository $pref) {
        
        $waitingStatus = $status->find(1);
        $waitingOrders = $waitingStatus->getCustomerOrders();
        
        $preparingStatus = $status->find(2);
        $preparingOrders = $preparingStatus->getCustomerOrders();
        
        $expedingStatus = $status->find(3);
        $expedingOrders = $expedingStatus->getCustomerOrders();

        $min = $pref->findOneBy(array('id' => 1));
        $lowStock = $products->findLowStock($min->getMinStock());

        return $this->render('admin/dashboard/index.html.twig', [
            'waiting' => $waitingOrders,
            'preparing' => $preparingOrders,
            'expeding' => $expedingOrders,
            'users' => $users->findAll(),
            'orders' => $orders->findAll(),
            'products' => $products->findAll(),
            'lowStock' => $lowStock
        ]);
    }
}
