<?php
/**
* @package     jelix
* @subpackage  forms
* @author      Bruno Perles
* @copyright   2011 Bruno Perles
* @link        http://www.jelix.org
* @licence     http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

include_once(JELIX_LIB_PATH.'forms/jFormsBuilderHtml.class.php');

class htmlbootstrapJformsBuilder extends jFormsBuilderHtml {

    public function outputHeader($params){
        $this->options = array_merge(array('errorDecorator'=>$this->jFormsJsVarName.'ErrorDecoratorHtml',
            'method'=>'post'), $params);
        if (isset($params['attributes']))
            $attrs = $params['attributes'];
        else
            $attrs = array();

        echo '<form';
        if (preg_match('#^https?://#',$this->_action)) {
            $urlParams = $this->_actionParams;
            $attrs['action'] = $this->_action;
        } else {
            $url = jUrl::get($this->_action, $this->_actionParams, 2); // retourne le jurl correspondant
            $urlParams = $url->params;
            $attrs['action'] = $url->getPath();
        }
        $attrs['method'] = $this->options['method'];
        $attrs['id'] = $this->_name;

        if($this->_form->hasUpload())
            $attrs['enctype'] = "multipart/form-data";

        $this->_outputAttr($attrs);
        echo '>';

        $this->outputHeaderScript();

        $hiddens = '';
        foreach ($urlParams as $p_name => $p_value) {
            $hiddens .= '<input type="hidden" name="'. $p_name .'" value="'. htmlspecialchars($p_value). '"'.$this->_endt. "\n";
        }

        foreach ($this->_form->getHiddens() as $ctrl) {
            if(!$this->_form->isActivated($ctrl->ref)) continue;
            $hiddens .= '<input type="hidden" name="'. $ctrl->ref.'" id="'.$this->_name.'_'.$ctrl->ref.'" value="'. htmlspecialchars($this->_form->getData($ctrl->ref)). '"'.$this->_endt. "\n";
        }

        if($this->_form->securityLevel){
            $tok = $this->_form->createNewToken();
            $hiddens .= '<input type="hidden" name="__JFORMS_TOKEN__" value="'.$tok.'"'.$this->_endt. "\n";
        }

        if($hiddens){
            echo '<div class="jforms-hiddens">',$hiddens,'</div>';
        }

        $errors = $this->_form->getContainer()->errors;
        if(count($errors)){
            $ctrls = $this->_form->getControls();
            $outputError = array();
            $errRequired='';
            foreach($errors as $cname => $err){
                if(!$this->_form->isActivated($ctrls[$cname]->ref)) continue;
                if ($err === jForms::ERRDATA_REQUIRED) {
                    if ($ctrls[$cname]->alertRequired){
                        $outputError[] = $ctrls[$cname]->alertRequired;
                    }
                    else {
                        $outputError[] = jLocale::get('jelix~formserr.js.err.required', $ctrls[$cname]->label);
                    }
                }else if ($err === jForms::ERRDATA_INVALID) {
                    if($ctrls[$cname]->alertInvalid){
                        $outputError[] = $ctrls[$cname]->alertInvalid;
                    }else{
                        $outputError[] = jLocale::get('jelix~formserr.js.err.invalid', $ctrls[$cname]->label);
                    }
                }
                elseif ($err === jForms::ERRDATA_INVALID_FILE_SIZE) {
                    $outputError[] = jLocale::get('jelix~formserr.js.err.invalid.file.size', $ctrls[$cname]->label);
                }
                elseif ($err === jForms::ERRDATA_INVALID_FILE_TYPE) {
                    $outputError[] = jLocale::get('jelix~formserr.js.err.invalid.file.type', $ctrls[$cname]->label);
                }
                elseif ($err === jForms::ERRDATA_FILE_UPLOAD_ERROR) {
                    $outputError[] = jLocale::get('jelix~formserr.js.err.file.upload', $ctrls[$cname]->label);
                }
                elseif ($err != '') {
                    $outputError[] = $err;
                }
            }
            if(count($outputError))
            {
                echo '<div id="'.$this->_name.'_errors" class="jforms-error-list alert-message error">';
                foreach($outputError as $value)
                    echo '<p>'.$value.'</p>';
                echo '</div>';
            }
        }
    }

    public function outputAllControls() {
        echo '<div class="clearfix">';
        foreach( $this->_form->getRootControls() as $ctrlref=>$ctrl){
            if($ctrl->type == 'submit' || $ctrl->type == 'reset' || $ctrl->type == 'hidden') continue;
            if(!$this->_form->isActivated($ctrlref)) continue;
            if($ctrl->type == 'group') {
                $this->outputControl($ctrl);
            }else{
                echo '<div class="clearfix">';
                $this->outputControlLabel($ctrl);
                echo '<div class="input'.($ctrl->type == 'radiobuttons' || $ctrl->type == 'checkbox'?' inputs-list':'').'">';
                $this->outputControl($ctrl);
                echo "</div></div>\n";
            }
        }
        echo '</div> <div class="jforms-submit-buttons actions">';
        foreach( $this->_form->getSubmits() as $ctrlref=>$ctrl){
            if(!$this->_form->isActivated($ctrlref)) continue;
            $this->outputControl($ctrl);
            echo ' ';
        }
        if ( $ctrl = $this->_form->getReset() ) {
            if(!$this->_form->isActivated($ctrl->ref)) continue;
            $this->outputControl($ctrl);
            echo ' ';
        }
        echo "</div>\n";
    }

    protected function outputGroup($ctrl, &$attr) {
        echo '<fieldset id="',$attr['id'],'"><legend>',htmlspecialchars($ctrl->label),"</legend>\n";
        foreach( $ctrl->getChildControls() as $ctrlref=>$c){
            if($c->type == 'submit' || $c->type == 'reset' || $c->type == 'hidden') continue;
            if(!$this->_form->isActivated($ctrlref)) continue;
            echo '<div class="clearfix'.(isset($this->_form->getContainer()->errors[$c->ref]) ?' error':'').'">',"\n";
            $this->outputControlLabel($c);
            echo "\n<div class=\"input".($c->type == 'radiobuttons' || $c->type == 'checkbox'?' inputs-list':'')."\">";
            $this->outputControl($c);
            echo "</div></div>\n";
        }
        echo "</fieldset>";
    }

    public function outputControl($ctrl, $attributes=array()){
        if($ctrl->type == 'hidden') return;
        $ro = $ctrl->isReadOnly();
        $attributes['name'] = $ctrl->ref;
        $attributes['id'] = $this->_name.'_'.$ctrl->ref;

        if ($ro)
            $attributes['readonly'] = 'readonly';
        else
            unset($attributes['readonly']);
        if (!isset($attributes['title']) && $ctrl->hint) {
            $attributes['title'] = $ctrl->hint;
        }

        $class = 'jforms-ctrl-'.$ctrl->type;
        $class .= ($ctrl->required == false || $ro?'':' jforms-required');
        //$class .= (isset($this->_form->getContainer()->errors[$ctrl->ref]) ?' jforms-error':'');
        $class .= ($ro && $ctrl->type != 'captcha'?' jforms-readonly disabled':'');
        if (isset($attributes['class']))
            $attributes['class'].= ' '.$class;
        else
            $attributes['class'] = $class;
        $this->{'output'.$ctrl->type}($ctrl, $attributes);
        echo "\n";
        $this->{'js'.$ctrl->type}($ctrl);
        $this->outputHelp($ctrl);
    }

    public function outputControlLabel($ctrl){
        if($ctrl->type == 'hidden' || $ctrl->type == 'group') return;
        $required = ($ctrl->required == false || $ctrl->isReadOnly()?'':' jforms-required');
        $reqhtml = ($required?'<span class="jforms-required-star">*</span>':'');
        $inError = (isset($this->_form->getContainer()->errors[$ctrl->ref]) ?' jforms-error':'');
        $hint = ($ctrl->hint == ''?'':' title="'.htmlspecialchars($ctrl->hint).'"');
        $id = $this->_name.'_'.$ctrl->ref;
        $idLabel = ' id="'.$id.'_label"';
        if($ctrl->type != 'submit' && $ctrl->type != 'reset')
            echo '<label><span class="jforms-label',$required,$inError,'"',$idLabel,$hint,'>',htmlspecialchars($ctrl->label),$reqhtml,"</span></label>\n";
    }

    protected function outputSubmit($ctrl, $attr) {
        unset($attr['readonly']);
        $attr['class'] = 'jforms-submit btn primary';
        $attr['type'] = 'submit';

        if($ctrl->standalone){
            $attr['value'] = $ctrl->label;
            echo '<input';
            $this->_outputAttr($attr);
            echo $this->_endt;
        }else{
            $id = $this->_name.'_'.$ctrl->ref.'_';
            $attr['name'] = $ctrl->ref;
            foreach($ctrl->datasource->getData($this->_form) as $v=>$label){
                // because IE6 sucks with <button type=submit> (see ticket #431), we must use input :-(
                $attr['value'] = $label;
                $attr['id'] = $id.$v;
                echo ' <input';
                $this->_outputAttr($attr);
                echo $this->_endt;
            }
        }
    }

    protected function outputReset($ctrl, &$attr) {
        unset($attr['readonly']);
        $attr['class'] = 'jforms-reset btn';
        $attr['type'] = 'reset';
        echo '<button';
        $this->_outputAttr($attr);
        echo '>',htmlspecialchars($ctrl->label),'</button>';
    }

}
