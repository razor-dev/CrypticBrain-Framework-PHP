<?php
/**
 * CDebug core class file
 *
 * PUBLIC:					PROTECTED:					PRIVATE:
 * ----------               ----------                  ----------
 *
 * STATIC:
 * ---------------------------------------------------------------
 * init                                                 _getFormattedMicrotime
 * write
 * addMessage
 * getMessage
 * displayInfo
 *
 */

class CDebug
{
    /** @var string */
    private static $_startTime;
    /** @var string */
    private static $_endTime;
    /** @var array */
    private static $_arrGeneral;
    /** @var array */
    private static $_arrParams;
    /** @var array */
    private static $_arrWarnings;
    /** @var array */
    private static $_arrErrors;
    /** @var array */
    private static $_arrQueries;
    /** @var array */
    private static $_arrServer;

    /**
     * Class init constructor
     */
    public static function init()
    {
        self::$_startTime = self::formattedMicrotime();
        if(APP_MODE != 'debug') return false;
    }

    /**
     * Add message to the stack
     * @param string $type
     * @param string $key
     * @param string $val
     * @param string $storeType
     * @return bool
     */
    public static function addMessage($type = 'params', $key = '', $val = '', $storeType = '')
    {
        if(APP_MODE != 'debug') return false;

        if($storeType == 'session'){
            CrypticBrain::app()->getSession()->set('debug-'.$type, $val);
        }

        if($type == 'general'){
            self::$_arrGeneral[$key][] = CFilter::sanitize('string', $val);
        }elseif($type == 'params'){
            self::$_arrParams[$key] = CFilter::sanitize('string', $val);
        }elseif($type == 'errors'){
            self::$_arrErrors[$key][] = CFilter::sanitize('string', $val);
        }elseif($type == 'warnings'){
            self::$_arrWarnings[$key][] = CFilter::sanitize('string', $val);
        }elseif($type == 'queries'){
            self::$_arrQueries[$key][] = CFilter::sanitize('string', $val);
        }elseif($type == 'server'){
            self::$_arrServer[$key][] = $val;
        }
    }

    /**
     * Get message from the stack
     * @param string $type
     * @param string $key
     * @return string
     */
    public static function getMessage($type = 'params', $key = '')
    {
        $output = '';

        if($type == 'errors') $output = isset(self::$_arrErrors[$key]) ? self::$_arrErrors[$key] : '';

        return $output;
    }

    /**
     * Display debug info on the screen
     */
    public static function displayInfo()
    {
        if(APP_MODE != 'debug') return false;

        self::$_endTime = self::formattedMicrotime();
        $nl = "\n";

        if($debugError = CrypticBrain::app()->getSession()->get('debug-errors')){
            CrypticBrain::app()->getSession()->remove('debug-errors');
        }
        if($debugWarning = CrypticBrain::app()->getSession()->get('debug-warnings')){
            CrypticBrain::app()->getSession()->remove('debug-warnings');
        }

        $debugContent = $nl.'<style>#debug-panel{position:fixed;bottom:0;left:0;z-index:2000;width:100%;height:21px;font-size:12px;font-family:\'Segoe UI\', sans-serif;color:#abafb4;}#debug-panel.active{height:230px;}.debug-title{position:relative;background:#393c3e;height:20px;line-height:20px;border-top:1px solid #262829;border-bottom:1px solid #242627;-webkit-box-shadow:inset 0 1px 0 #525556;-moz-box-shadow:inset 0 1px 0 #525556;box-shadow:inset 0 1px 0 #525556;font-size:inherit;z-index:2;}.debug-title .show-panel{margin-left:5px;margin-right:5px;display:block;float:left;cursor:pointer;}.debug-tabs{float:left;background:#3c3f41;width:30px;height:100%;border-right:1px solid #323232;list-style-type:none;}.debug-tabs li{display:block;width:22px;height:22px;border:1px solid transparent;overflow:hidden;margin:3px 0 0 3px;}.debug-tabs li a{display:block;width:22px;height:22px;text-decoration:none;}.debug-tabs li a > i{display:block;width:22px;height:22px;line-height:22px;font-size:16px;color:#abafb4;text-align:center;}.debug-tabs li a > label{display:none;position:absolute;top:0;left:65px;z-index:3;height:23px;line-height:23px;color:#b9b9b9;}.debug-tabs li:hover{background:#5b5d5f;border:1px solid #7a8084;}.debug-tabs li.active{background:#555a5c!important;border:1px solid #7a8084!important;}.debug-content{display:none;background:#2b2b2b;height:181px;font-size:12px;font-family:Courier, sans-serif;overflow-y:auto;overflow-x:hidden;padding:3px;}.debug-content pre{font-size:inherit;font-family:Courier, sans-serif;white-space:pre;word-wrap:break-word;}.debug-footer{padding-right:10px;position:absolute;bottom:0;left:0;right:0;background:#3c3f41;height:20px;line-height:20px;border-top:1px solid #464646;z-index:1;text-align:right;}.debug-tabs li:hover a > label,.debug-content.active{display:block;}</style>';
        $debugContent .= $nl.'<script type="text/javascript">function changeDebugTab(tab){var tabs=document.getElementsByClassName("debug-content");var handles=document.getElementsByClassName("handle-debug-tab");for(var i=0;i<tabs.length;i++){if(tabs[i].classList.contains("active")){tabs[i].classList.remove("active");}}for(var j=0;j<handles.length;j++){if(handles[j].classList.contains("active")){handles[j].classList.remove("active");}}document.querySelector("[tabId=" + tab + "]").classList.add("active");document.getElementById(tab).classList.add("active");}function handleDebugPanel(){var panel=document.getElementById("debug-panel");if(panel.classList.contains("active")){panel.classList.remove("active");}else{panel.classList.add("active");}}</script>
		<div id="debug-panel">
			<div class="debug-title"><span class="show-panel" onclick="handleDebugPanel()" title="Show or Hide"><i data-icon="&#xe2d0;"></i></span> Debug log</div>
			<ul class="debug-tabs">
				<li tabId="debug-general" class="handle-debug-tab active"><a href="javascript:changeDebugTab(\'debug-general\')"><i data-icon="&#xe023;"></i><label>> '.CrypticBrain::t('core', 'General').'</label></a></li>
				<li tabId="debug-params" class="handle-debug-tab"><a href="javascript:changeDebugTab(\'debug-params\')"><i data-icon="&#xe205;"></i><label>> '.CrypticBrain::t('core', 'Params').'</label></a></li>
				<li tabId="debug-warnings" class="handle-debug-tab"><a href="javascript:changeDebugTab(\'debug-warnings\')"><i data-icon="&#xe036;"></i><label>> '.CrypticBrain::t('core', 'Warnings').'</label></a></li>
				<li tabId="debug-errors" class="handle-debug-tab"><a href="javascript:changeDebugTab(\'debug-errors\')"><i data-icon="&#xe20a;"></i><label>> '.CrypticBrain::t('core', 'Errors').'</label></a></li>
				<li tabId="debug-sql-queries" class="handle-debug-tab"><a href="javascript:changeDebugTab(\'debug-sql-queries\')"><i data-icon="&#xe0be;"></i><label>> '.CrypticBrain::t('core', 'SQL Queries').'</label></a></li>
				<li tabId="debug-server" class="handle-debug-tab"><a href="javascript:changeDebugTab(\'debug-server\')"><i data-icon="&#xe0aa;"></i><label>> '.CrypticBrain::t('core', 'Server responses').'</label></a></li>
			</ul>
			<div class="debug-footer">
			    PARAMS: '.count(self::$_arrParams).'&nbsp;&nbsp;&nbsp;
			    WARNINGS: '.count(self::$_arrWarnings).'&nbsp;&nbsp;&nbsp;
			    ERRORS: '.count(self::$_arrErrors).'&nbsp;&nbsp;&nbsp;
			    QUERIES: '.count(self::$_arrQueries).'&nbsp;&nbsp;&nbsp;
			    SERVER RESPONSES: '.count(self::$_arrServer).'
			</div>
		    <div id="debug-general" class="debug-content active">
			    '.CrypticBrain::t('core', 'Total running time').': '.round((float)self::$_endTime - (float)self::$_startTime, 6).' '.CrypticBrain::t('i18n', 'time.abbreviated.seconds').'<br />
			    '.CrypticBrain::t('core', 'Memory usage').': '.self::getMemoryUsage().'<br />
			    '.CrypticBrain::t('core', 'Version').': '.CrypticBrain::getVersion().'<br /><br />';
        if(count(self::$_arrGeneral) > 0){
            $debugContent .= '<pre>';
            $debugContent .= CHtml::jsonpp(json_encode(self::$_arrGeneral));
            $debugContent .= '</pre>';
        }
        if(isset($_POST)){
            $debugContent .= '<pre>POST:';
            $debugContent .= CHtml::jsonpp(json_encode(array_map('strip_tags', $_POST)));
            $debugContent .= '</pre>';
        }
        $debugContent .= '</div>
		    <div id="debug-params" class="debug-content">';
        if(count(self::$_arrParams) > 0){
            $debugContent .= '<pre>';
            $debugContent .= str_replace("\/", "/", CHtml::jsonpp(json_encode(self::$_arrParams)));
            $debugContent .= '</pre>';
        }
        $debugContent .= '</div>
		    <div id="debug-warnings" class="debug-content">';
        if(count(self::$_arrWarnings) > 0){
            $debugContent .= '<pre>';
            $debugContent .= CHtml::jsonpp(json_encode(self::$_arrWarnings));
            $debugContent .= '</pre>';
        }
        $debugContent .= '</div>
    		<div id="debug-errors" class="debug-content">';
        if(count(self::$_arrErrors) > 0){
            foreach(self::$_arrErrors as $key => $msg){
                $debugContent .= '<pre>';
                $debugContent .= $key.'. '.$msg[0];
                $debugContent .= '</pre>';
            }
        }
        $debugContent .= '</div>
    		<div id="debug-sql-queries" class="debug-content">';
        if(count(self::$_arrQueries) > 0){
            foreach(self::$_arrQueries as $msgKey => $msgVal){
                $debugContent .= $msgKey.'<br />';
                $debugContent .= $msgVal[0].'<br /><br />';
            }
        }
        $debugContent .= '</div>
            <div id="debug-server" class="debug-content">';
        if(count(self::$_arrServer) > 0){
            foreach(self::$_arrServer as $key => $msg){
                $debugContent .= '<pre>';
                $debugContent .= $key . ':';
                $debugContent .= CHtml::jsonpp(json_encode($msg));
                $debugContent .= '</pre>';
            }
        }
        $debugContent .= '</div>
        </div>';

        echo $debugContent;
    }

    /**
     * Display debug info on the screen
     */
    public static function shortInfo()
    {
        self::$_endTime = self::formattedMicrotime();

        $content = ' <span>'.CrypticBrain::t('core', 'Time').':</span> '.round((float)self::$_endTime - (float)self::$_startTime, 3).' '.CrypticBrain::t('i18n', 'time.abbreviated.seconds').'.';
        $content .= ' <span>'.CrypticBrain::t('core', 'Memory').':</span> '.self::getMemoryUsage();
        return $content;
    }

    /**
     * Get formatted microtime
     * @return float
     */
    private static function formattedMicrotime()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }

    /**
     * Get memory usage
     * @param int|mixed $memory
     * @return string
     */
    public static function convertMemory($memory)
    {
        if(!CValidator::isInteger($memory)){
            $memory = str_replace('m', '', $memory * 1024 * 1024);
        }

        $unit = array('b', 'kb', 'mb');
        return round($memory / pow(1024, ($i = floor(log($memory, 1024)))), 2).' '.$unit[$i];
    }

    /**
     * Get memory usage
     * @return string
     */
    private static function getMemoryUsage()
    {
        return self::convertMemory(memory_get_usage(true));
    }

    public static function percent2Color($value)
    {
        $first = (1 - ($value / 100)) * 255;
        $second = ($value / 100) * 255;

        $diff = abs($first - $second);
        $influence = (255 - $diff) / 2;
        $first = intval($first + $influence);
        $second = intval($second + $influence);

        $firstHex = str_pad(dechex($first), 2,0, STR_PAD_LEFT);
        $secondHex = str_pad(dechex($second), 2,0, STR_PAD_LEFT);

        return $secondHex.$firstHex.'00';
    }
}