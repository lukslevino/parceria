<?php
/**
 * Created by PhpStorm.
 * User: lazevedol
 * Date: 18/11/2017
 * Time: 16:00
 */

namespace Application\View;


use Zend\Form\Form;

class LoginView extends Form
{
    use ViewTrait;

    public function __construct($name = 'loginForm', $options = array())
    {
        parent::__construct($name, $options);
        $this->setAttribute('role', 'form');
        $this->setAttribute('id', $name);
    }

    private function getBtnEntrar($name, $value, $class)
    {
        $this->add($this->button($name,$value,$class));
    }

    private function getLogin( $name ="login")
    {
        $this->add($this->text($name));
    }
    private function getSenha($name="noSenha")
    {
        $this->add($this->addPassword($name));
    }

    private function addPassword($name)
    {
        return $this->add(
            [
                'name' => $name,
                'attributes' => [
                    'type' => 'password',
                    'class' => 'form-control',
                    'id' => 'senha',
                    'autocomplete' => "false",
                    'disabled' => true,
                    'value' => 30,
                ],
                'options' => [
                    'label' => 'Restantes:',
                ],
            ]
        );
    }

    public function loginUsuario()
    {
        $this->setAttribute('id','formLogin');
        $this->getLogin('login');
        $this->getSenha('noSenha');
        $this->getBtnEntrar('btnEntrar','Entrar','btn-default');
    }

}
