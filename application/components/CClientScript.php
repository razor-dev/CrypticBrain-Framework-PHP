<?php
/**
 * CClientScript manages JavaScript and CSS stylesheets for views
 *
 * PUBLIC:					PROTECTED:					PRIVATE:		
 * ----------               ----------                  ----------
 * __construct
 * registerCssFile
 * registerCss
 * registerScriptFile
 * registerScript
 * render
 * renderHead
 * renderBodyBegin
 * renderBodyEnd
 *
 * STATIC:
 * ---------------------------------------------------------------
 * init
 * 
 */	  

class CClientScript extends CComponent
{
	/** The script is rendered in the <head>  */
	const POS_HEAD = 0;
	/** The script is rendered at the beginning of the <body>  */
	const POS_BODY_BEGIN = 1;
	/** The script is rendered at the end of the <body>  */
	const POS_BODY_END = 2;
	/** The script is rendered inside window onload function */
	const POS_ON_LOAD = 3;
	/** The script is rendered inside document ready function */
	const POS_DOC_READY = 4;
    
	/** @var boolean */
	public $enableJavaScript = true;
	/** @var array */
	protected $_cssFiles = array();
	/** @var array */
	protected $_css = array();
	/** @var array */
	protected $_scriptFiles = array();
	/** @var array */
	protected $_scripts = array();
	/** @var boolean */
	protected $_hasScripts = false;
    
	
    /**
	 * Class default constructor
	 */
	function __construct()
	{
        
    }

    /**
     *	Returns the instance of object
     *	@return CClientScript class
     */
	public static function init()
	{
		return parent::init(__CLASS__);
	}

	/**
	 * Registers a CSS file
	 * @param string $url 
	 * @param string $media 
	 */
	public function registerCssFile($url, $media = '')
	{
		$this->_hasScripts = true;
		$this->_cssFiles[$url] = $media;
	}

	/**
	 * Registers a piece of CSS code
	 * @param string $id 
	 * @param string $css 
	 * @param string $media 
	 */
	public function registerCss($id, $css, $media = '')
	{
		$this->_hasScripts = true;
		$this->_css[$id] = array($css, $media);
	}
    
	/**
	 * Registers a required javascript file
	 * @param string $url
	 * @param integer $position 
	 */
	public function registerScriptFile($url, $position = self::POS_HEAD)
	{
		$this->_hasScripts = true;
		$this->_scriptFiles[$position][$url] = $url;
	}

	/**
	 * Registers a piece of javascript code
	 * @param string $id 
	 * @param string $script 
	 * @param integer $position
	 */
	public function registerScript($id, $script, $position = self::POS_BODY_END)
	{
		$this->_hasScripts = true;
		$this->_scripts[$position][$id] = $script;
	}
	
	/**
	 * Renders the registered scripts in our class
	 * This method is called in View->render() class
	 * @param string &$output 
	 */
	public function render(&$output)
	{
		if(!$this->_hasScripts) return;
        $this->renderHead($output);    
		if($this->enableJavaScript){
			$this->renderBodyBegin($output);
			$this->renderBodyEnd($output);
		}
    }
    
	/**
	 * Inserts the js scripts/css in the head section
	 * @param string &$output 
	 */
	public function renderHead(&$output)
	{
		$html = '';

        if(isset($this->_scriptFiles[self::POS_HEAD])){
            foreach($this->_scriptFiles[self::POS_HEAD] as $scriptFile){
                $html .= CHtml::scriptFile($scriptFile)."\n";
            }
        }

        foreach($this->_cssFiles as $url=>$media){
            $html .= CHtml::cssFile($url, $media)."\n";
        }
        foreach($this->_css as $css){
            $html .= CHtml::css($css[0], $css[1])."\n";
        }

        if(isset($this->_scripts[self::POS_HEAD])){
            $html .= CHtml::script(implode("\n", $this->_scripts[self::POS_HEAD]))."\n";
        }

        $count = 0;
        $output = preg_replace('/(<title\b[^>]*>|<\\/head\s*>)/is', '<%%%head%%%>$1', $output, 1, $count);
        if($count){
            $output = str_replace('<%%%head%%%>', $html, $output);
        }else{
            $output = $output.$html;
        }

	}

	/**
	 * Inserts the scripts at the beginning of the <body>
	 * @param string &$output 
	 */
	public function renderBodyBegin(&$output)
	{
		$html = '';
		if(isset($this->_scriptFiles[self::POS_BODY_BEGIN])){
			foreach($this->_scriptFiles[self::POS_BODY_BEGIN] as $scriptFile){
				$html .= CHtml::scriptFile($scriptFile)."\n";
			}
		}
		if(isset($this->_scripts[self::POS_BODY_BEGIN])){
			$html .= CHtml::script(implode("\n", $this->_scripts[self::POS_BODY_BEGIN]))."\n";
		}

		if($html !== ''){
			$count = 0;
			$output = preg_replace('/(<body\b[^>]*>)/is', '$1<%%%begin%%%>', $output, 1, $count);
			if($count){
				$output = str_replace('<%%%begin%%%>', $html, $output);
			}else{
				$output = $html.$output;
			}
		}		
	}
	
	/**
	 * Inserts the scripts at the end of the <body>
	 * @param string &$output 
	 */
	public function renderBodyEnd(&$output)
	{
		if(!isset($this->_scriptFiles[self::POS_BODY_END]) &&
		   !isset($this->_scripts[self::POS_BODY_END]) &&
		   !isset($this->_scripts[self::POS_DOC_READY]) &&
		   !isset($this->_scripts[self::POS_ON_LOAD]))
			return;

		$completePage = 0;
		$output = preg_replace('/(<\\/body\s*>)/is', '<%%%end%%%>$1', $output, 1, $completePage);
		$html = '';
		if(isset($this->_scriptFiles[self::POS_BODY_END])){
			foreach($this->_scriptFiles[self::POS_BODY_END] as $scriptFile){
				$html .= CHtml::scriptFile($scriptFile)."\n";
			}
		}

		$scripts = isset($this->_scripts[self::POS_BODY_END]) ? $this->_scripts[self::POS_BODY_END] : array();
		if(isset($this->_scripts[self::POS_DOC_READY])){
			$scripts[] = implode("\n",$this->_scripts[self::POS_DOC_READY]);
		}
		if(isset($this->_scripts[self::POS_ON_LOAD])){
			$scripts[] = implode("\n",$this->_scripts[self::POS_ON_LOAD]);
		}
		if(!empty($scripts)) $html = CHtml::script(implode("\n", $scripts))."\n";

		if($completePage){
			$output = str_replace('<%%%end%%%>', $html, $output);
		}else{
			$output = $output.$html;
		}
	}
}