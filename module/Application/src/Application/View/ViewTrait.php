<?php

namespace Application\View;

trait ViewTrait
{

    protected function text($name, $class = null, $attr = array())
    {
        $attributes = array(
            'class' => 'form-control ' . $class,
            'id' => $name
        );

        return array(
            'name' => $name,
            'type' => 'Zend\Form\Element\Text',
            'filters' => array(
                'StringTrim'
            ),
            'attributes' => array_merge($attributes, $attr),
        );
    }

    public function file($name, $class = '')
    {
        return array(
            'name' => $name,
            'type' => 'Zend\Form\Element\File',
            'attributes' => array(
                'id' => $name,
                'class' => $class
            )
        );
    }

    public function hidden($name, $value = '')
    {
        return array(
            'name' => $name,
            'type' => 'Zend\Form\Element\Hidden',
            'attributes' => array(
                'class' => 'form-control',
                'id' => $name,
                'value' => $value
            ),
        );
    }

    private function select($options, $name, $empty_option = 'Selecione')
    {
        return array(
            'name' => $name,
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'value_options' => $options,
                'empty_option' => $empty_option,
                'disable_inarray_validator' => true,
            ),
            'attributes' => array(
                'type' => 'select',
                'class' => 'form-control',
                'id' => $name
            )
        );
    }

    private function selectWithoutEmpty($options, $name)
    {
        return array(
            'name' => $name,
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'value_options' => $options,
                'disable_inarray_validator' => true,
            ),
            'attributes' => array(
                'type' => 'select',
                'class' => 'form-control',
                'id' => $name
            )
        );
    }

    private function selectMultipleCkb($options, $name)
    {
        return array(
            'name' => $name,
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'value_options' => $options,
                'disable_inarray_validator' => true,
            ),
            'attributes' => array(
                'type' => 'select',
                'class' => 'form-control SlectBox',
                'id' => $name,
                'multiple' => 'true'
            )
        );
    }

    private function checkbox($name, $checkValue = 'S', $uncheckedValue = 'N')
    {
        return array(
            'name' => $name,
            'type' => 'Zend\Form\Element\Checkbox',
            'options' => array(
                'class' => 'form-control',
                'use_hidden_element' => true,
                'checked_value' => $checkValue,
                'unchecked_value' => $uncheckedValue,
                'disable_inarray_validator' => true,
            ),
            'attributes' => array(
                'id' => $name
            )
        );
    }

    private function multiCheckbox($options, $name, $itemClass = '')
    {
        return array(
            'name' => $name,
            'type' => 'Zend\Form\Element\MultiCheckbox',
            'options' => array(
                'value_options' => $options,
                'disable_inarray_validator' => true,
                'label_attributes' => array(
                    'class' => $itemClass
                ),
            ),
            'attributes' => array(
                'value' => '0',
                'id' => $name
            )
        );
    }

    private function radio($options, $name, $class = 'radio-inline')
    {
        return array(
            'name' => $name,
            'type' => 'Zend\Form\Element\Radio',
            'options' => array(
                'disable_inarray_validator' => true,
                'value_options' => $options,
                'label_attributes' => array(
                    'class' => $class
                ),
            ),
            'attributes' => array(
                'id' => $name,
            ),
        );
    }

    private function textarea($name, $maxlength, $class = 'form-control', $rows = 6, $attributes = null)
    {
        $defaultAttributes = [
            'id' => $name,
            'class' => $class,
            'maxlength' => $maxlength,
            'rows' => $rows
        ];

        if (!empty($attributes) && count($attributes)) {
            $defaultAttributes = array_merge($defaultAttributes, $attributes);
        }

        return array(
            'name' => $name,
            'type' => 'Textarea',
            'required' => true,
            'filters' => array(
                'StringTrim'
            ),
            'attributes' => $defaultAttributes
        );
    }

    private function button($name, $value, $class, $type = 'button', $attributes = null)
    {

        $defaultAttributes = [
            'type' => $type,
            'class' => 'btn ' . $class,
            'id' => $name,
            'value' => $value
        ];

        if (!empty($attributes) && count($attributes)) {
            $defaultAttributes = array_merge($defaultAttributes, $attributes);
        }

        return array(
            'name' => $name,
            'attributes' => $defaultAttributes
        );
    }

    public function buttonIcon($name, $value, $class, $icon = '', $type = 'button')
    {
        return array(
            'name' => $name,
            'options' => array(
                'label' => $icon . $value,
                'label_options' => array(
                    'disable_html_escape' => true,
                ),
            ),
            'attributes' => array(
                'type' => $type,
                'class' => 'btn ' . $class,
                'id' => $name
            )
        );
    }

    private function submit($id, $label, $value = '', $class = 'btn')
    {
        return array(
            'name' => $id,
            'type' => 'Button',
            'options' => array(
                'label' => $label,
            ),
            'attributes' => array(
                'id' => $id,
                'value' => $value,
                'type' => 'submit',
                'class' => $class
            )
        );
    }

    private function getBtnAdicionar($value = 'Adicionar', $name = 'btnAdicionar', $class = 'btn-warning', $icon = '')
    {
        $this->add($this->buttonIcon($name, $value, $class, $icon));
    }

    private function getBtnAdicionarSemIcon(
        $value = 'Adicionar',
        $name = 'btnAdicionar',
        $class = 'btn-warning',
        $type = 'button',
        $attributes = null
    )
    {
        $this->add($this->button($name, $value, $class, $type, $attributes));
    }

    private function getBtnPesquisar(
        $value = 'Pesquisar',
        $name = 'btnPesquisar',
        $class = 'btn-primary',
        $type = 'button',
        $attributes = null
    )
    {
        $this->add($this->button($name, $value, $class, $type, $attributes));
    }

    private function getBtnSalvar(
        $value = 'Salvar',
        $name = 'btnSalvar',
        $class = 'btn-success',
        $type = 'button',
        $attributes = null
    )
    {
        $this->add($this->button($name, $value, $class, $type, $attributes));
    }

    private function getBtnCancelar(
        $value = 'Cancelar',
        $name = 'btnCancelar',
        $class = 'btn-danger',
        $type = 'button',
        $attributes = null
    )
    {
        $this->add($this->button($name, $value, $class, $type, $attributes));
    }

    private function getBtnSelecionar(
        $value = 'Selecionar',
        $name = 'btnSelecionar',
        $class = 'btn-primary',
        $type = 'button',
        $attributes = null
    )
    {
        $this->add($this->button($name, $value, $class, $type, $attributes));
    }

    private function getBtnLimpar(
        $value = 'Limpar',
        $name = 'btnLimpar',
        $class = 'btn-default',
        $type = 'reset',
        $attributes = null
    )
    {
        $this->add($this->button($name, $value, $class, $type, $attributes));
    }

    private function getBtnNovo(
        $value = 'Novo',
        $name = 'btnNovo',
        $class = 'btn-success',
        $type = 'button',
        $attributes = null
    )
    {
        $this->add($this->button($name, $value, $class, $type, $attributes));
    }

    private function getBtnVisualizar(
        $value = 'Visualizar',
        $name = 'btnVisualizar',
        $class = 'btn-primary',
        $type = 'button',
        $attributes = null
    )
    {
        $this->add($this->button($name, $value, $class, $type, $attributes));
    }

    private function getBtnVoltar(
        $value = 'Voltar',
        $name = 'btnVoltar',
        $class = 'btn-primary',
        $type = 'button',
        $attributes = null
    )
    {
        $this->add($this->button($name, $value, $class, $type, $attributes));
    }

    private function getBtnAlterar(
        $value = 'Alterar',
        $name = 'btnAlterar',
        $class = 'btn-primary',
        $type = 'button',
        $attributes = null
    )
    {
        $this->add($this->button($name, $value, $class, $type, $attributes));
    }

    private function getBtnExportar(
        $value = 'Exportar',
        $name = 'btnExportar',
        $class = 'btn-default',
        $type = 'button',
        $attributes = null
    )
    {
        $this->add($this->button($name, $value, $class, $type, $attributes));
    }

    private function getAno($name = 'ano')
    {
        $current_year = date('Y');
        $range = range($current_year, $current_year - 19);
        $options = array_combine($range, $range);
        $this->add($this->select($options, $name));
    }

}
