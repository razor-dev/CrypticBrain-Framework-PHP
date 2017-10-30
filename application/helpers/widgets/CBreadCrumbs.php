<?php
/**
 * CBreadCrumbs widget helper class file
 *
 * PUBLIC:					PROTECTED:					PRIVATE:
 * ----------               ----------                  ----------
 * init
 * 
 */

class CBreadCrumbs
{
    const NL = "\n";

    /**
     * Draws breadcrumbs
     * @param array $params
     * @return string
     *
     * Usage:
     *  CWidget::create('CBreadCrumbs', array(
     *      'links' => array(
     *          array('label'=>'Label A'), 'url'=>'url1/'),
     *          array('label'=>'Label B'), 'url'=>'url2/'),
     *      ),
     *      'separator' => '&nbsp;/&nbsp;',
     *      'return' => true
     *  ));
     */
    public static function init($params = array())
    {
        $output = '';
        $class = 'breadcrumbs';
        $tagName = 'div';
        $htmlOptions = array('class'=>$class);
        $links = isset($params['links']) ? $params['links'] : '';
        $separator = isset($params['separator']) ? $params['separator'] : '&raquo;';
        
        if(is_array($links)){
            $output .= CHtml::openTag($tagName, $htmlOptions).self::NL;
            $counter = 0;
            foreach($params['links'] as $item => $val){
                $url = isset($val['url']) ? $val['url'] : '';
                $label = isset($val['label']) ? $val['label'] : '';                

                if($counter) $output .= ' '.$separator.' ';
                if(!empty($url)) $output .= CHtml::link($label, $url);
                else $output .= CHtml::tag('span', array(), $label).self::NL;
                
                $counter++;
            }
            
            $output .= CHtml::closeTag($tagName).self::NL;
        }
        
        return $output;
    }
}