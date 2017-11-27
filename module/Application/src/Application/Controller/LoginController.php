<?php
/**
 * Created by PhpStorm.
 * User: lazevedol
 * Date: 18/11/2017
 * Time: 15:52
 */

namespace Application\Controller;


use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class LoginController extends AbstractActionController
{

    public function indexAction()
    {

        $viewModel = new ViewModel();
        return $viewModel;
    }


}