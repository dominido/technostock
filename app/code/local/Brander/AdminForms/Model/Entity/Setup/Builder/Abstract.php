<?php
class Brander_AdminForms_Model_Entity_Setup_Builder_Abstract extends Varien_Object
{
    public function getNameFromCode($code)
    {
        return implode(' ', array_map('ucfirst', explode('_', $code)));
    }

    /**
     * Extends params by default value, rewrite default.
     *
     * @param array $params
     * @param array $default
     * @return array
     */
    protected function _appendDefault($params, $default)
    {
        return array_merge($default, $params);
    }
}
