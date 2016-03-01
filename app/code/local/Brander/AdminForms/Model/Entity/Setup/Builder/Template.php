<?php
class Brander_AdminForms_Model_Entity_Setup_Builder_Template extends Brander_AdminForms_Model_Entity_Setup_Builder_Abstract
{
    public function getTemplate($template)
    {
        $method = $template;
        if (method_exists($this, $method)) {
            return $this->$method();
        }

        return array();
    }

    public function text()
    {
        return $this->textfield();
    }

    public function textfield()
    {
        return array(
            'type'  => 'varchar',
            'input' => 'text'
        );
    }

    public function textarea()
    {
        return array(
            'type'  => 'text',
            'input' => 'textarea',
            'is_wysiwyg_enabled' => 0
        );
    }

    public function wtextarea()
    {
        return array_merge($this->textarea(), array(
            'is_wysiwyg_enabled' => 1
        ));
    }

    public function dropdown()
    {
        return array(
            'type'   => 'int',
            'input'  => 'select',
            'source' => 'adminforms/config_source_options'
        );
    }

    public function image()
    {
        return array(
            'backend' => 'adminforms/attribute_backend_image',
            'input'   => 'image'
        );
    }

    public function datetime()
    {
        return array(
            'type'    => 'datetime',
            'backend' => 'eav/entity_attribute_backend_datetime',
            'input'   => 'date'
        );
    }

    public function file()
    {
        return array(
            'type'    => 'varchar',
            'input'   => 'file',
            'backend' => 'adminforms/attribute_backend_file'
        );
    }

    public function status()
    {
        return array_merge($this->dropdown(), array(
            'source' => 'adminforms/config_source_status'
        ));
    }

    public function yesno()
    {
        return array_merge($this->dropdown(), array(
            'source' => 'adminforms/config_source_yesno'
        ));
    }
}
