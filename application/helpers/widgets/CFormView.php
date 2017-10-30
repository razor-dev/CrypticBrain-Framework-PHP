<?php
/**
 * CFormView widget helper class file
 *
 * PUBLIC:					PROTECTED:					PRIVATE:
 * ----------               ----------                  ----------
 * init                                                 formField
 *
 */

class CFormView
{
    const NL = "\n";

    /**
     * Draws HTML form
     * @param array $params
     * @return mixed
     */
    public static function init($params = array())
    {
        $output = '';
        $action = isset($params['action']) ? $params['action'] : '';
        $method = isset($params['method']) ? $params['method'] : 'post';
        $htmlOptions = (isset($params['htmlOptions']) and is_array($params['htmlOptions'])) ? $params['htmlOptions'] : array();
        $formName = isset($htmlOptions['name']) ? $htmlOptions['name'] : '';
        $fields = isset($params['fields']) ? $params['fields'] : array();
        $checkboxes = isset($params['checkboxes']) ? $params['checkboxes'] : array();
        $buttons = isset($params['buttons']) ? $params['buttons'] : array();
        $events = isset($params['events']) ? $params['events'] : array();
        $captcha = isset($params['captcha']) ? $params['captcha'] : false;
        $output .= CHtml::openForm($action, $method, $htmlOptions, $captcha).self::NL;

        foreach($fields as $field => $fieldInfo){
            if(isset($fieldInfo['disabled']) and (bool)$fieldInfo['disabled'] === true) unset($fields[$field]);
        }

        foreach($fields as $field => $fieldInfo){
            if(preg_match('/separator/i', $field) and is_array($fieldInfo)){
                $legend = isset($fieldInfo['separatorInfo']['legend']) ? $fieldInfo['separatorInfo']['legend'] : '';
                unset($fieldInfo['separatorInfo']);

                if($legend !== false and $legend != ''){
                    $output .= CHtml::openTag('fieldset').self::NL;
                    $output .= CHtml::tag('legend', array(), $legend, true).self::NL;
                    foreach($fieldInfo as $iField => $iFieldInfo){
                        $output .= self::formField($iField, $iFieldInfo, $events, $formName);
                    }
                    $output .= CHtml::closeTag('fieldset').self::NL;
                }
            }else{
                $output .= self::formField($field, $fieldInfo, $events, $formName);
            }
        }
        if($captcha){
            $output .= self::formField(CrypticBrain::app()->getRequest()->getCaptchaKey(), array('type'=>'textbox', 'htmlOptions'=>array('class'=>'field medium transition', 'placeholder'=>CrypticBrain::t('app', 'Code from picture'), 'maxlength'=>CConfig::get('validation.captcha.length'), 'autocomplete'=>'off'), 'prependCode'=>'<i class="input-icon" data-icon="&#xe030;"></i>', 'appendCode'=>'<img src="' . CCaptcha::getImage() . '" class="captcha-image" />'), array(), $formName);
        }

        foreach($buttons as $key => $val){
            if(isset($val['disabled']) and (bool)$val['disabled'] === true) unset($buttons[$key]);
        }

        if(count($checkboxes) > 0){
            $output .= CHtml::openTag('div', array('class'=>'checkboxes-group'));
            foreach($checkboxes as $checkbox => $checkboxInfo){
                $title = isset($checkboxInfo['title']) ? $checkboxInfo['title'] : false;
                $checked = isset($checkboxInfo['checked']) ? $checkboxInfo['checked'] : false;
                $htmlOptions = (isset($checkboxInfo['htmlOptions']) and is_array($checkboxInfo['htmlOptions'])) ? $checkboxInfo['htmlOptions'] : array();
                $htmlLabelOptions =  isset($checkboxInfo['htmlLabelOptions']) ? $checkboxInfo['htmlLabelOptions'] : array();
                $appendCode = isset($checkboxInfo['appendCode']) ? $checkboxInfo['appendCode'] : '';
                $output .= CHtml::checkBox($checkbox, $checked, $htmlOptions).self::NL;
                if($title){
                    $output .= CHtml::label($title, $checkbox, $htmlLabelOptions);
                }
                $output .= $appendCode;
            }
            $output .= CHtml::closeTag('div').self::NL;
        }

        if(count($buttons) > 0){
            $output .= CHtml::openTag('div', array('class'=>'buttons-group')).self::NL;
            foreach($buttons as $button => $buttonInfo){
                $type = isset($buttonInfo['type']) ? $buttonInfo['type'] : '';
                $value = isset($buttonInfo['value']) ? $buttonInfo['value'] : '';
                $htmlOptions = (isset($buttonInfo['htmlOptions']) and is_array($buttonInfo['htmlOptions'])) ? $buttonInfo['htmlOptions'] : array();
                $appendCode = isset($buttonInfo['appendCode']) ? $buttonInfo['appendCode'] : '';
                if(!isset($htmlOptions['value'])) $htmlOptions['value'] = $value;
                switch($type){
                    case 'button':
                        $htmlOptions['type'] = 'button';
                        $output .= CHtml::button('button', $htmlOptions).self::NL;
                        break;
                    case 'reset':
                        $output .= CHtml::resetButton('reset', $htmlOptions).self::NL;
                        break;
                    case 'submit':
                    default:
                        $output .= CHtml::submitButton('submit', $htmlOptions).self::NL;
                        break;
                }
                $output .= $appendCode;
            }
            $output .= CHtml::closeTag('div').self::NL;
        }

        $output .= CHtml::closeForm().self::NL;

        foreach($events as $event => $eventInfo){
            $field = isset($eventInfo['field']) ? $eventInfo['field'] : '';
            if($event == 'focus'){
                if(!empty($field)){
                    CrypticBrain::app()->getClientScript()->registerScript($formName.$event, 'var f = document.querySelector("form[name='.$formName.']").querySelector("input[name='.$field.']"); if (f) f.focus();', 2);
                }else{
                    CrypticBrain::app()->getClientScript()->registerScript($formName.$event, 'var f = document.querySelector("form[name='.$formName.']").querySelector("input[event=focus]"); if (f) f.focus();', 2);
                }
            }else{
                CrypticBrain::app()->getClientScript()->registerScript($formName.$event, $eventInfo, 2);
            }
        }

        return $output;
    }

    /**
     * Draws HTML form field
     * @param string $field
     * @param array $fieldInfo
     * @param array $events
     * @return string
     */
    private static function formField($field, $fieldInfo, $events)
    {
        $output = '';
        $type = isset($fieldInfo['type']) ? strtolower($fieldInfo['type']) : 'textbox';
        $value = isset($fieldInfo['value']) ? $fieldInfo['value'] : '';
        $title = isset($fieldInfo['title']) ? $fieldInfo['title'] : false;
        $definedValues = isset($fieldInfo['definedValues']) ? $fieldInfo['definedValues'] : '';
        $htmlOptions = (isset($fieldInfo['htmlOptions']) and is_array($fieldInfo['htmlOptions'])) ? $fieldInfo['htmlOptions'] : array();
        $prependCode = isset($fieldInfo['prependCode']) ? $fieldInfo['prependCode'] : '';
        $appendCode = isset($fieldInfo['appendCode']) ? $fieldInfo['appendCode'] : '';

        if($type != 'textarea'){
            $value = CHtml::encode($value);
        }
        if(!isset($htmlOptions['id'])) $htmlOptions['id'] = false;
        if(isset($events['focus']['field']) and $events['focus']['field'] == $field){
            if(isset($htmlOptions['class'])) $htmlOptions['class'] .= ' field-error';
            else $htmlOptions['class'] = 'field-error';
        }

        switch($type){
            case 'checkbox':
                $viewType = isset($fieldInfo['viewType']) ? $fieldInfo['viewType'] : '';
                $checked = isset($fieldInfo['checked']) ? (bool)$fieldInfo['checked'] : false;
                if(!empty($value)) $htmlOptions['value'] = $value;
                if($viewType == 'custom'){
                    $fieldHtml  = CHtml::openTag('div', array('class'=>'slideBox'));
                    $fieldHtml .= CHtml::checkBox($field, $checked, $htmlOptions);
                    $fieldHtml .= CHtml::label('', $htmlOptions['id']);
                    $fieldHtml .= CHtml::closeTag('div');
                }else{
                    $fieldHtml = CHtml::checkBox($field, $checked, $htmlOptions);
                }
                break;
            case 'label':
                $format = isset($fieldInfo['format']) ? $fieldInfo['format'] : '';
                $stripTags = isset($fieldInfo['stripTags']) ? (bool)$fieldInfo['stripTags'] : false;
                if($stripTags) $value = strip_tags(CHtml::decode($value));

                if(is_array($definedValues) and isset($definedValues[$value])){
                    $value = $definedValues[$value];
                }else if($format != '' and $format != 'american' and $format != 'european'){
                    $value = date($format, strtotime($value));
                }

                $for = isset($htmlOptions['for']) ? (bool)$htmlOptions['for'] : false;
                $fieldHtml = CHtml::label($value, $for, $htmlOptions);
                break;
            case 'link':
                $linkUrl = isset($fieldInfo['linkUrl']) ? $fieldInfo['linkUrl'] : '#';
                $linkText = isset($fieldInfo['linkText']) ? $fieldInfo['linkText'] : '';
                $fieldHtml = CHtml::link($linkText, $linkUrl, $htmlOptions);
                break;
            case 'date':
            case 'datetime':
                if(is_array($definedValues) and isset($definedValues[$value])){
                    $value = $definedValues[$value];
                }
                if(!isset($htmlOptions['autocomplete'])) $htmlOptions['autocomplete'] = 'off';
                $fieldHtml = CHtml::textField($field, $value, $htmlOptions);
                break;
            case 'hidden':
                $fieldHtml = CHtml::hiddenField($field, $value, $htmlOptions);
                break;
            case 'password':
                $fieldHtml = CHtml::passwordField($field, $value, $htmlOptions);
                break;
            case 'select':
            case 'dropdown':
            case 'dropdownlist':
                $data = isset($fieldInfo['data']) ? $fieldInfo['data'] : array();
                $fieldHtml = CHtml::dropDownList($field, $value, $data, $htmlOptions);
                break;
            case 'file':
                if(APP_MODE == 'demo') $htmlOptions['disabled'] = 'disabled';
                $fieldHtml = CHtml::fileField($field, $value, $htmlOptions);
                break;
            case 'textarea':
                $fieldHtml = CHtml::textArea($field, $value, $htmlOptions);
                break;
            case 'radio':
            case 'radiobutton':
                $checked = isset($fieldInfo['checked']) ? (bool)$fieldInfo['checked'] : false;
                if(!empty($value)) $htmlOptions['value'] = $value;
                $fieldHtml = CHtml::radioButton($field, $checked, $htmlOptions);
                break;
            case 'radiobuttons':
            case 'radiobuttonlist':
                $data = isset($fieldInfo['data']) ? $fieldInfo['data'] : array();
                $checked = isset($fieldInfo['checked']) ? $fieldInfo['checked'] : false;
                $htmlOptions['separator'] = "\n";
                $fieldHtml = CHtml::radioButtonList($field, $checked, $data, $htmlOptions);
                break;
            case 'color':
            case 'colorpicker':
                $fieldHtml = CHtml::colorField($field, $value, $htmlOptions);
                break;
            case 'textbox':
            default:
                $fieldHtml = CHtml::textField($field, $value, $htmlOptions);
                break;
        }
        if($type == 'hidden'){
            $output .= $fieldHtml.self::NL;
        }else{
            $output .= CHtml::openTag('div', array('class'=>'field-group'));
            $output .= $prependCode;
            if($title){
                $htmlLabelOptions = isset($fieldInfo['htmlLabelOptions']) ? $fieldInfo['htmlLabelOptions'] : array();
                $for = (isset($htmlOptions['id']) and $htmlOptions['id']) ? $htmlOptions['id'] : false;
                $output .= CHtml::label($title, $for, $htmlLabelOptions);
            }
            $output .= $fieldHtml;
            $output .= $appendCode;
            $output .= CHtml::closeTag('div').self::NL;
        }
        return $output;
    }
}