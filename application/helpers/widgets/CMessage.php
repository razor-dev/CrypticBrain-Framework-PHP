<?php
/**
 * CMessage widget helper class file
 *
 * PUBLIC:					PROTECTED:					PRIVATE:		
 * ----------               ----------                  ----------
 * init
 * 
 */	  

class CMessage
{
    const NL = "\n";

    /**
     * Draws message box
     * @param array $params
     * @return mixed
     */
    public static function init($params = array())
    {
        $type = isset($params[0]) ? $params[0] : '';
        $text = isset($params[1]) ? $params[1] : '';
        $output = '';
        $htmlOptions = isset($params[2]) ? $params[2] : array();
        $allowedTypes = array('info', 'success', 'error', 'warning', 'validation');
        
        if(in_array($type, $allowedTypes)){
            $htmlOptions['class'] = 'alert alert-'.$type;
            $output .= CHtml::openTag('div', $htmlOptions);
            $output .= $text;
            $output .= CHtml::closeTag('div').self::NL;
        }
		
        return $output;
    }
}