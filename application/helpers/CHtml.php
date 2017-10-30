<?php
/**
 * CHtml is a helper class that provides a collection of helper methods for creating HTML elements
 *
 * PUBLIC:					PROTECTED:					PRIVATE:
 * ----------			   ----------				  ----------
 * tag					  _inputField				 _renderAttributes
 * openTag				                			 _escapeHex
 * closeTag											 _escapeHexEntity
 * link
 * label
 * encode
 * decode
 * css
 * cssFile
 * script
 * scriptFile
 * form
 * openForm
 * closeForm
 * hiddenField
 * textField
 * passwordField
 * fileField
 * getIdByName
 * dropDownList
 * listBox
 * listOptions
 * textArea
 * checkBox
 * radioButton
 * radioButtonList
 * submitButton
 * resetButton
 * image
 * button
 * convertFileSize
 * convertImageDimensions
 */

class CHtml
{
    /** @const string */
    const ID_PREFIX = 'uid_';
    /** @var string */
    private static $_count = 0;

    /**
     * Generates an HTML tag
     * @param string $tag
     * @param array $htmlOptions
     * @param mixed $content
     * @param boolean $closeTag
     * @return string - HTML tag
     */
    public static function tag($tag, $htmlOptions = array(), $content = false, $closeTag = true)
    {
        $html = '<'.$tag.self::_renderAttributes($htmlOptions);
        if($content === false){
            return $closeTag ? $html.' />' : $html.'>';
        }else{
            return $closeTag ? $html.'>'.$content.'</'.$tag.'>' : $html.'>'.$content;
        }
    }

    /**
     * Generates an open HTML tag
     * @param string $tag
     * @param array $htmlOptions
     * @return string - HTML tag
     */
    public static function openTag($tag, $htmlOptions = array())
    {
        return '<'.$tag.self::_renderAttributes($htmlOptions).'>';
    }

    /**
     * Generates a close HTML tag
     * @param string $tag
     * @return string - HTML tag
     */
    public static function closeTag($tag)
    {
        return '</'.$tag.'>';
    }

    /**
     * Generates a hyperlink tag
     * @param string $text
     * @param string $url
     * @param array $htmlOptions
     * @return string - HTML tag
     */
    public static function link($text, $url = '#', $htmlOptions = array())
    {
        if($url !== '') $htmlOptions['href'] = $url;
        if(isset($htmlOptions['escape']) && $htmlOptions['escape'] === true){
            $text = self::_escapeHexEntity($text);
            $htmlOptions['href'] = self::_escapeHex($htmlOptions['href']);
            unset($htmlOptions['escape']);
        }
        return self::tag('a', $htmlOptions, $text);
    }

    /**
     * Generates a label tag
     * @param string $label
     * @param bool|string $for
     * @param array $htmlOptions
     * @return string - HTML tag
     */
    public static function label($label, $for = false, $htmlOptions = array())
    {
        if($for === false) unset($htmlOptions['for']);
        else $htmlOptions['for'] = $for;
        return self::tag('label', $htmlOptions, $label);
    }

    /**
     * Encodes special characters into HTML entities
     * @param string $text
     * @return string
     */
    public static function encode($text)
    {
        return htmlspecialchars($text, ENT_QUOTES, CrypticBrain::app()->charset);
    }

    /**
     * Decodes special HTML entities back to the corresponding characters
     * @param string $text
     * @return string
     */
    public static function decode($text)
    {
        return htmlspecialchars_decode($text, ENT_QUOTES);
    }

    /**
     * Encloses the passed CSS content with a CSS tag
     * @param string $text
     * @param string $media
     * @return string the CSS tag
     */
    public static function css($text, $media = '')
    {
        if($media !== '') $media = ' media="'.$media.'"';
        return "<style type=\"text/css\"{$media}>\n/*<![CDATA[*/\n{$text}\n/*]]>*/\n</style>";
    }

    /**
     * Links to required CSS file
     * @param string $url
     * @param string $media
     * @return string - HTML tag
     */
    public static function cssFile($url, $media = '')
    {
        if($media !== '') $media=' media="'.$media.'"';
        return '<link rel="stylesheet" type="text/css" href="'.self::encode($url).'"'.$media.' />'."\n";
    }

    /**
     * Encloses the passed JavaScript within a Script tag
     * @param string $text
     * @return string the Script tag
     */
    public static function script($text)
    {
        return "<script type=\"text/javascript\">" . $text . "</script>";
    }

    /**
     * Includes a JavaScript file
     * @param string $url
     * @return string
     * @return string - HTML tag
     */
    public static function scriptFile($url)
    {
        return '<script type="text/javascript" src="'.self::encode($url).'"></script>'."\n";
    }

    /**
     * Generates an open form tag
     * This is a shortcut to {@link openForm}
     * @param mixed $action
     * @param string $method
     * @param array $htmlOptions
     * @param boolean $captcha
     * @return string
     */
    public static function form($action = '', $method = 'post', $htmlOptions = array(), $captcha = false)
    {
        return self::openForm($action, $method, $htmlOptions);
    }

    /**
     * Generates an opening form tag
     * @param mixed $action
     * @param string $method
     * @param array $htmlOptions
     * @param boolean $captcha
     * @return string
     * @see endForm
     */
    public static function openForm($action = '', $method = 'post', $htmlOptions = array(), $captcha = false)
    {
        $htmlOptions['action'] = $url = $action;
        $htmlOptions['method'] = $method;
        $form = self::tag('form', $htmlOptions, false, false);
        $hiddens = array();
        if(!strcasecmp($method, 'get') && ($pos = strpos($url, '?')) !== false){
            foreach(explode('&', substr($url, $pos+1)) as $pair){
                if(($pos = strpos($pair, '=')) !== false){
                    $hiddens[] = self::hiddenField(urldecode(substr($pair, 0, $pos)), urldecode(substr($pair, $pos+1)), array('id'=>false));
                }
            }
        }

        /** @var CHttpRequest $request */
        $request = CrypticBrain::app()->getRequest();
        if($request->getCsrfValidation() && !strcasecmp($method, 'post')){
            $hiddens[] = self::hiddenField($request->getCsrfTokenKey(), $request->getCsrfTokenValue(), array('id'=>false));
        }
        if($captcha){
            $request->getCaptchaValue();
        }
        if($hiddens !== array()){
            $form .= "\n".implode("\n", $hiddens)."\n";
        }

        return $form;
    }

    /**
     * Generates a closing form tag
     * @return string
     * @see openForm
     */
    public static function closeForm()
    {
        return '</form>';
    }

    /**
     * Generates a hidden input
     * @param string $name
     * @param string $value
     * @param array $htmlOptions
     * @return string
     * @see inputField
     */
    public static function hiddenField($name, $value = '', $htmlOptions = array())
    {
        return self::_inputField('hidden', $name, $value, $htmlOptions);
    }

    /**
     * Generates a textbox input
     * @param string $name
     * @param string $value
     * @param array $htmlOptions
     * @return string
     * @see inputField
     */
    public static function textField($name, $value = '', $htmlOptions = array())
    {
        return self::_inputField('text', $name, $value, $htmlOptions);
    }

    /**
     * Generates a password field
     * @param string $name
     * @param string $value
     * @param array $htmlOptions
     * @return string
     * @see inputField
     */
    public static function passwordField($name, $value = '', $htmlOptions = array())
    {
        return self::_inputField('password', $name, $value, $htmlOptions);
    }

    /**
     * Generates a file field
     * @param string $name
     * @param string $value
     * @param array $htmlOptions
     * @return string
     * @see inputField
     */
    public static function fileField($name, $value = '', $htmlOptions = array())
    {
        return self::_inputField('file', $name, $value, $htmlOptions);
    }

    /**
     * Generates a valid HTML ID based on name
     * @param string $name
     * @return string
     */
    public static function getIdByName($name)
    {
        return str_replace(array('#', '[]', '][', '[', ']'), array('-', '', '_', '_', ''), $name);
    }

    /**
     * Generates a color input
     * @param string $name
     * @param string $value
     * @param array $htmlOptions
     * @return string
     * @see inputField
     */
    public static function colorField($name, $value = '', $htmlOptions = array())
    {
        return self::_inputField('color', $name, $value, $htmlOptions);
    }

    /**
     * Generates an input HTML tag
     * @param string $type
     * @param string $name
     * @param string $value
     * @param array $htmlOptions
     * @return string
     */
    protected static function _inputField($type, $name, $value, $htmlOptions)
    {
        $htmlOptions['type'] = $type;
        $htmlOptions['value'] = $value;
        $htmlOptions['name'] = $name;
        if(!isset($htmlOptions['id'])) $htmlOptions['id'] = self::getIdByName($name);
        else if($htmlOptions['id'] === false) unset($htmlOptions['id']);
        return self::tag('input', $htmlOptions, false);
    }

    /**
     * Draws textarea
     * @param string $name
     * @param string $value
     * @param array $htmlOptions
     * @return string
     */
    public static function textArea($name, $value='', $htmlOptions = array())
    {
        $htmlOptions['name'] = $name;
        if(!isset($htmlOptions['id'])) $htmlOptions['id'] = self::getIdByName($name);
        else if($htmlOptions['id'] === false) unset($htmlOptions['id']);
        return self::tag('textarea', $htmlOptions, isset($htmlOptions['encode']) && !$htmlOptions['encode'] ? $value : self::encode($value));
    }

    /**
     * Generates a check box
     * @param string $name
     * @param boolean $checked
     * @param array $htmlOptions
     * @return string
     * @see inputField
     */
    public static function checkBox($name, $checked = false, $htmlOptions = array())
    {
        if($checked){
            $htmlOptions['checked'] = 'checked';
        }else{
            unset($htmlOptions['checked']);
        }

        $value = (isset($htmlOptions['value']) && $htmlOptions['value'] !== '') ? $htmlOptions['value'] : 1;
        if(array_key_exists('uncheckValue', $htmlOptions)){
            $uncheck = $htmlOptions['uncheckValue'];
            unset($htmlOptions['uncheckValue']);
        }else{
            $uncheck = null;
        }

        if($uncheck !== null){
            $hidden = self::hiddenField($name, $uncheck);
        }else{
            $hidden = '';
        }

        return $hidden.self::_inputField('checkbox', $name, $value, $htmlOptions);
    }

    /**
     * Generates a radio button
     * @param string $name
     * @param boolean $checked
     * @param array $htmlOptions
     * @return string
     * @see inputField
     */
    public static function radioButton($name, $checked = false, $htmlOptions = array())
    {
        if($checked) $htmlOptions['checked'] = 'checked';
        else unset($htmlOptions['checked']);

        $value = isset($htmlOptions['value']) ? $htmlOptions['value'] : 1;

        if(array_key_exists('uncheckValue', $htmlOptions)){
            $uncheck = $htmlOptions['uncheckValue'];
            unset($htmlOptions['uncheckValue']);
        }else{
            $uncheck = null;
        }

        if($uncheck !== null){
            $hidden = self::hiddenField($name,$uncheck);
        }else{
            $hidden = '';
        }
        return $hidden.self::_inputField('radio', $name, $value, $htmlOptions);
    }

    /**
     * Generates radio buttons list
     * @param string $name
     * @param string $checked
     * @param array $data
     * @param array $htmlOptions
     * @return string
     * @see tag
     */
    public static function radioButtonList($name, $checked, $data, $htmlOptions = array())
    {
        $template = isset($htmlOptions['template']) ? $htmlOptions['template'] : '{input} {label}';
        $separator = isset($htmlOptions['separator']) ? $htmlOptions['separator'] : "\n";
        unset($htmlOptions['template'], $htmlOptions['separator']);
        $labelOptions = isset($htmlOptions['labelOptions']) ? $htmlOptions['labelOptions'] : array();
        unset($htmlOptions['labelOptions']);
        $items = array();
        $baseID = self::getIdByName($name);
        $id = 0;
        foreach($data as $value => $label){
            $isChecked = !strcmp($value, $checked);
            $htmlOptions['value'] = $value;
            $htmlOptions['id'] = $baseID.'_'.$id++;
            $option = self::radioButton($name, $isChecked, $htmlOptions);
            $label = self::label($label, $htmlOptions['id'], $labelOptions);
            $items[] = strtr($template, array('{input}'=>$option, '{label}'=>$label));
        }
        return self::tag('section', array('id'=>$baseID), implode($separator, $items));
    }

    /**
     * Draws dropdown list
     * @param string $name
     * @param mixed $select
     * @param array $data
     * @param array $htmlOptions
     * @param array $specialOptions
     * @return string
     */
    public static function dropDownList($name, $select = '', $data = array(), $htmlOptions = array(), $specialOptions = array())
    {
        $htmlOptions['name'] = $name;
        if(!isset($htmlOptions['id'])) $htmlOptions['id'] = self::getIdByName($name);
        else if($htmlOptions['id'] === false) unset($htmlOptions['id']);

        $specialType = isset($specialOptions['type']) ? $specialOptions['type'] : '';
        $specialStep = isset($specialOptions['step']) ? (int)$specialOptions['step'] : 1;
        if($specialType == 'hours'){
            if($specialStep < 1 || $specialStep > 24) $specialStep = 1;
            for($i = 0; $i < 24; $i+=$specialStep){
                $ind = (($i < 10) ? '0' : '').$i;
                $data[$ind] = $ind;
            }
        }else if($specialType == 'minutes'){
            if($specialStep < 1 || $specialStep > 60) $specialStep = 1;
            for($i = 0; $i < 60; $i+=$specialStep){
                $ind = (($i < 10) ? '0' : '').$i;
                $data[$ind] = $ind;
            }
        }
        $options = "\n".self::listOptions($select, $data, $htmlOptions);
        return self::tag('select', $htmlOptions, $options);
    }

    /**
     * Draws dropdown list
     * @param string $name
     * @param mixed $select
     * @param array $data
     * @param array $htmlOptions
     * @return string
     */
    public static function listBox($name, $select = '', $data = array(), $htmlOptions = array())
    {
        if(!isset($htmlOptions['size'])) $htmlOptions['size'] = 4;
        if(isset($htmlOptions['multiple'])){
            if(substr($name,-2) !== '[]') $name .= '[]';
        }
        return self::dropDownList($name, $select, $data, $htmlOptions);
    }

    /**
     * Generates the list of options
     * @param mixed $selection
     * @param array $listData
     * @param array $htmlOptions
     * @return string
     */
    public static function listOptions($selection, $listData, &$htmlOptions)
    {
        $raw = isset($htmlOptions['encode']) && !$htmlOptions['encode'];
        $content = '';
        if(isset($htmlOptions['prompt'])){
            $content .= '<option value="">'.strtr($htmlOptions['prompt'], array('<'=>'&lt;', '>'=>'&gt;'))."</option>\n";
            unset($htmlOptions['prompt']);
        }
        if(isset($htmlOptions['empty'])){
            if(!is_array($htmlOptions['empty'])) $htmlOptions['empty'] = array(''=>$htmlOptions['empty']);
            foreach($htmlOptions['empty'] as $value=>$label){
                $content .= '<option value="'.self::encode($value).'">'.strtr($label,array('<'=>'&lt;', '>'=>'&gt;'))."</option>\n";
            }
            unset($htmlOptions['empty']);
        }
        if(isset($htmlOptions['options'])){
            $options = $htmlOptions['options'];
            unset($htmlOptions['options']);
        }else{
            $options = array();
        }
        $key = isset($htmlOptions['key']) ? $htmlOptions['key'] : 'primaryKey';
        if(is_array($selection)){
            foreach($selection as $i=>$item){
                if(is_object($item)) $selection[$i] = $item->$key;
            }
        }else if(is_object($selection)){
            $selection = $selection->$key;
        }
        if(!is_array($listData)) return $content;
        foreach($listData as $key => $value){
            if(is_array($value)){
                if(isset($value['optionValue'])){
                    $attributes = array('value'=>(string)$key, 'encode'=>!$raw);
                    if(isset($value['optionDisabled']) && isset($value['optionDisabled'])) $attributes['disabled'] = true;
                    if(!is_array($selection) && !strcmp($key,$selection) || is_array($selection) && in_array($key,$selection)){
                        $attributes['selected'] = 'selected';
                    }
                    if(isset($options[$key])) $attributes = array_merge($attributes, $options[$key]);
                    $content .= self::tag('option', $attributes, $raw ? (string)$value['optionValue'] : self::encode((string)$value['optionValue']))."\n";
                }else{
                    $content .= '<optgroup label="'.($raw ? $key : self::encode($key))."\">\n";
                    $dummy = array('options'=>$options);
                    if(isset($htmlOptions['encode'])) $dummy['encode'] = $htmlOptions['encode'];
                    $content .= self::listOptions($selection, $value, $dummy);
                    $content .= '</optgroup>'."\n";
                }
            }else{
                $attributes = array('value'=>(string)$key, 'encode'=>!$raw);
                if(!is_array($selection) && !strcmp($key,$selection) || is_array($selection) && in_array($key,$selection)){
                    $attributes['selected'] = 'selected';
                }
                if(isset($options[$key])) $attributes = array_merge($attributes, $options[$key]);
                $content .= self::tag('option', $attributes, $raw ? (string)$value : self::encode((string)$value))."\n";
            }
        }
        unset($htmlOptions['key']);
        return $content;
    }

    /**
     * Draws submit button
     * @param string $label
     * @param array $htmlOptions
     * @return string
     */
    public static function submitButton($label = 'submit', $htmlOptions = array())
    {
        $htmlOptions['type'] = 'submit';
        return self::button($label, $htmlOptions);
    }

    /**
     * Generates reset button
     * @param string $label
     * @param array $htmlOptions
     * @return string
     */
    public static function resetButton($label = 'reset', $htmlOptions = array())
    {
        $htmlOptions['type'] = 'reset';
        return self::button($label, $htmlOptions);
    }

    /**
     * Draws button
     * @param string $label
     * @param array $htmlOptions
     * @return string
     */
    public static function button($label = 'button', $htmlOptions = array())
    {
        if(!isset($htmlOptions['type'])) $htmlOptions['type'] = 'button';

        if($htmlOptions['type'] == 'button'){
            if(isset($htmlOptions['value'])){
                $buttonValue = $htmlOptions['value'];
                unset($htmlOptions['value']);
                unset($htmlOptions['buttonTag']);
            }else{
                $buttonValue = $label;
            }
            return self::tag('button', $htmlOptions, $buttonValue);
        }else{
            if(!isset($htmlOptions['value'])) $htmlOptions['value'] = $label;
            return self::tag('input', $htmlOptions);
        }
    }

    /**
     * Generates an image tag
     * @param string $src
     * @param string $alt
     * @param array $htmlOptions
     * @return string
     */
    public static function image($src, $alt='', $htmlOptions = array())
    {
        $htmlOptions['src'] = $src;
        $htmlOptions['alt'] = $alt;
        return self::tag('img', $htmlOptions);
    }

    /**
     * Returns a file size in bytes from the given string
     * @param mixed $fileSize
     * @return int|mixed
     */
    public static function convertFileSize($fileSize)
    {
        $return = $fileSize;
        if(!is_numeric($fileSize)){
            if(stripos($fileSize, 'm') !== false){
                $return = intval($fileSize) * 1024 * 1024;
            }else if(stripos($fileSize, 'k') !== false){
                $return = intval($fileSize) * 1024;
            }else if(stripos($fileSize, 'g') !== false){
                $return = intval($fileSize) * 1024 * 1024 * 1024;
            }
        }
        return $return;
    }

    /**
     * Returns an image width or height in pixels from the given string
     * @param mixed $fileDimension
     * @return int|mixed
     */
    public static function convertImageDimensions($fileDimension)
    {
        $return = $fileDimension;
        if(!is_numeric($fileDimension)){
            if(stripos($fileDimension, 'px') !== false){
                $return = intval($fileDimension);
            }
        }
        return $return;
    }

    /**
     * Renders the HTML tag attributes
     * @param string $htmlOptions
     * @return string
     */
    private static function _renderAttributes($htmlOptions)
    {
        // attributes that looks like attribute = "attribute"
        static $specialAttributes = array(
            'checked'  => 1,
            'declare'  => 1,
            'defer'	=> 1,
            'disabled' => 1,
            'ismap'	=> 1,
            'multiple' => 1,
            'nohref'   => 1,
            'noresize' => 1,
            'readonly' => 1,
            'selected' => 1,
            'autofocus'=> 1,
        );

        if($htmlOptions === array()) return '';

        $output = '';
        $encode = false;

        if(isset($htmlOptions['encode'])){
            $encode = (bool)$htmlOptions['encode'];
            unset($htmlOptions['encode']);
        }

        if(isset($htmlOptions['id']) && $htmlOptions['id'] === false) unset($htmlOptions['id']);
        if(isset($htmlOptions['href']) && $htmlOptions['href'] === false) unset($htmlOptions['href']);
        if(isset($htmlOptions['class']) && $htmlOptions['class'] == '') unset($htmlOptions['class']);
        if(isset($htmlOptions['style']) && $htmlOptions['style'] == '') unset($htmlOptions['style']);
        if(isset($htmlOptions['showAlways'])) unset($htmlOptions['showAlways']);

        if(is_array($htmlOptions)){
            foreach($htmlOptions as $name => $value){
                if(isset($specialAttributes[$name])){
                    if($value) $output .= ' '.$name.'="'.$name.'"';
                }else if($value !== null){
                    $output .= ' '.$name.'="'.(($encode) ? self::encode($value) : $value).'"';
                }
            }
        }

        return $output;
    }

    /**
     * Renders escaped hex string
     * @param string $string
     * @return string
     */
    private static function _escapeHex($string)
    {
        $return = '';
        for($x=0; $x < strlen($string); $x++){
            $return .= ($string[$x] == '/') ? $string[$x] : '%'.bin2hex($string[$x]);
        }
        return $return;
    }

    /**
     * Renders escaped hex entity string
     * @param string $string
     * @return string
     */
    private static function _escapeHexEntity($string)
    {
        $return = '';
        for($x=0; $x < strlen($string); $x++){
            $return .= '&#x'.bin2hex($string[$x]).';';
        }
        return $return;
    }

    public static function jsonpp($json, $istr = "\t")
    {
        $result = '';
        for($p = $q = $i = 0; isset($json[$p]); $p++){
            $json[$p] == '"' and ($p > 0 ? $json[$p-1] : '') != '\\' and $q=!$q;
            if(strchr('}]', $json[$p]) and !$q and $i--){
                strchr('{[', $json[$p-1]) or $result .= "\n" . str_repeat($istr, $i);
            }
            $result .= $json[$p];
            if(strchr(',{[', $json[$p]) and !$q){
                $i += strchr('{[', $json[$p]) === false ? 0 : 1;
                strchr('}]', $json[$p+1]) or $result .= "\n" . str_repeat($istr, $i);
            }
        }

        return str_replace('\/', '/', $result);
    }
}
