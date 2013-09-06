<?php
/**
 * Submit button for formfull bootstrap
 * @version Jelix 1.5
 * @author Guillaume Peres rab_gui@yahoo.fr "khena"
 */

class submit_htmlFormWidget extends \jelix\forms\HtmlWidget\WidgetBase {

    function outputControl() {
        $attr = $this->getControlAttributes();

        unset($attr['readonly']);
        $attr['class'] = 'btn btn-primary jforms-submit';
        $attr['type'] = 'submit';

        if ($this->ctrl->standalone) {
            $attr['value'] = $this->ctrl->label;
            echo '<input';
            $this->_outputAttr($attr);
            echo "/>\n";
        } else {
            $id = $this->builder->getName() . '_' . $this->ctrl->ref . '_';
            $attr['name'] = $this->ctrl->ref;
            foreach ($this->ctrl->datasource->getData($this->builder->getForm()) as $v => $label) {
                // because IE6 sucks with <button type=submit> (see ticket #431), we must use input :-(
                $attr['value'] = $label;
                $attr['id'] = $id . $v;
                echo ' <input';
                $this->_outputAttr($attr);
                echo "/>";
            }
            echo "\n";
        }
    }

}
