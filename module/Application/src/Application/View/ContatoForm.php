<?php

namespace Application\View;

use Zend\Form\Form;

class ContatoForm extends Form
{

    public function __construct($name = null, $options = array())
    {

        parent::__construct($name, $options);
        $this->setAttribute('class_name', 'form-inline');
        $this->setAttribute('role', 'form');
    }

    public function gerenciarContato($funcoes)
    {

        $this->add(array(
            'name' => 'funcao',
            'type' => 'form_select',
            'option' => array(
                'value_option' => $funcoes
            ),
            'attributes' => array(
                'class_name' => 'form_control',
                'id' => 'comboFuncao',
            ),
        ));
    }


}