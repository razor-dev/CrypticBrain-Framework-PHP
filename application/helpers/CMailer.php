<?php
/**
 * CMailer is a helper class file that provides basic maler functionality
 * 
 * PUBLIC:					PROTECTED:					PRIVATE:		
 * ----------               ----------                  ----------
 * config
 * send
 * getError
 * phpMail
 * smtpMailer
 * phpMailer
 * 
 */	  

include(dirname(__FILE__).'/../vendors/phpmailer/class.phpmailer.php');

class CMailer
{
	private static $_mailer = 'phpMail';
	private static $_error = '';

    private static $_smtpAuth = '';
	private static $_smtpSecure = '';
	private static $_smtpHost = '';
	private static $_smtpPort = '';
	private static $_smtpUsername = '';
	private static $_smtpPassword = '';
	
	/**
	 * Sets a basic configuration 
	 * @param string $params
	 */
    public static function config($params)
    {
		self::$_mailer       = isset($params['mailer']) ? $params['mailer'] : 'phpMail';
        self::$_smtpAuth     = isset($params['smtp_auth']) ? $params['smtp_auth'] : (CConfig::get('email.smtp.auth') ? CConfig::get('email.smtp.auth') : '');
		self::$_smtpSecure   = isset($params['smtp_secure']) ? $params['smtp_secure'] : (CConfig::get('email.smtp.secure') ? CConfig::get('email.smtp.secure') : '');
		self::$_smtpHost     = isset($params['smtp_host']) ? $params['smtp_host'] : CConfig::get('email.smtp.host');
		self::$_smtpPort     = isset($params['smtp_port']) ? $params['smtp_port'] : CConfig::get('email.smtp.port');
		self::$_smtpUsername = isset($params['smtp_username']) ? $params['smtp_username'] : CConfig::get('email.smtp.username');
		self::$_smtpPassword = isset($params['smtp_password']) ? $params['smtp_password'] : CConfig::get('email.smtp.password');
	}

    /**
     * Sends emails using pre-defined mailer type
     * @param string $to
     * @param string $subject
     * @param string $message
     * @param array|string $params
     * @return boolean
     */
    public static function send($to, $subject, $message, $params = '')
    {
		if(!strcasecmp(self::$_mailer, 'smtpMailer')){
			return self::smtpMailer($to, $subject, $message, $params);
		}else if(!strcasecmp(self::$_mailer, 'phpMailer')){
			return self::phpMailer($to, $subject, $message, $params);
		}else{
			return self::phpMail($to, $subject, $message, $params);
		}		
	}

	/**
	 * Returns error message
	 * @return string 
	 */
    public static function getError()
    {
		return self::$_error;
	}

    /**
     * Sends email using php mail() function
     * @param string $to
     * @param string $subject
     * @param string $message
     * @param array|string $params
     * @return boolean
     */
    public static function phpMail($to, $subject, $message, $params = '')
    {
		$charset = 'UTF-8';
		$xMailer = 'PHP-EMAIL-HELPER';
        $from = isset($params['from']) ? $params['from'] : '';
        $fromName = isset($params['from_name']) ? $params['from_name'] : '';
        $fromHeader = ($fromName) ? $fromName.' <'.$from.'>' : $from;
		$emailType = 'text/'.(CConfig::get('email.isHtml') ? 'html' : 'plain');
        $additionalParameters = ini_get('safe_mode') ? '' : '-f '.$from;
		$headers = 'From: '.$fromHeader."\r\n".
				   'Reply-To: '.$from."\r\n".
				   'Return-Path: '.$from."\r\n".
				   'MIME-Version: 1.0'."\r\n".
				   'Content-Type: '.$emailType.'; charset='.$charset."\r\n".				   
				   'X-Mailer: '.$xMailer.'/'.phpversion();
		$result = @mail($to, $subject, $message, $headers, $additionalParameters);

		if(!$result){
			if(version_compare(PHP_VERSION, '5.2.0', '>=')){
                $err = error_get_last();
				if(isset($err['message']) && $err['message'] != ''){
					self::$_error = $err['message'].' | file: '.$err['file'].' | line: '.$err['line'];
                    @trigger_error('');
				}
			}else{
				self::$_error = CrypticBrain::t('core', 'PHPMail Error: an error occurred while sending email.');
			}
		}		
		return $result;
    }

    /**
     * Sends email using php PHPMailer class (SMTP)
     * @param string $to
     * @param string $subject
     * @param string $message
     * @param array|string $params
     * @return boolean
     */
    public static function smtpMailer($to, $subject, $message, $params = '')
    {
		$from = isset($params['from']) ? $params['from'] : '';
        $fromName = isset($params['from_name']) ? $params['from_name'] : '';

		$mail = PHPMailer::Instance();

		$mail->IsSMTP();
		$mail->SMTPDebug  = 1;
		$mail->SMTPAuth   = true;
		$mail->SMTPSecure = self::$_smtpSecure;
		$mail->Host       = self::$_smtpHost;
		$mail->Port       = self::$_smtpPort;
		$mail->Username   = self::$_smtpUsername;
		$mail->Password   = self::$_smtpPassword;
		$mail->SetFrom($from, $fromName);
		$mail->AddReplyTo($from, $fromName);
		
		$recipients = explode(',', $to);
		foreach($recipients as $key){
			$mail->AddAddress($key);
		}
		
		$mail->Subject = $subject;
		$mail->AltBody = strip_tags($message);
		if(CConfig::get('email.isHtml')) $mail->MsgHTML(nl2br($message));
		
		//$mail->AddAttachment("images/test_file.gif");      // attachment
		//$mail->AddAttachment("images/test_file_thumb.gif"); // attachment

		$result = $mail->Send();
		if(!$result){
			self::$_error = $mail->ErrorInfo;
		}

		$mail->ClearAddresses();
		$mail->ClearReplyTos();
		
		return $result;		
	}

    /**
     * Sends email using php PHPMailer class (php mail() function)
     * @param string $to
     * @param string $subject
     * @param string $message
     * @param array|string $params
     * @return boolean
     */
    public static function phpMailer($to, $subject, $message, $params = '')
    {
		$from = isset($params['from']) ? $params['from'] : '';
        $fromName = isset($params['from_name']) ? $params['from_name'] : '';

		$mail = PHPMailer::Instance();
		$mail->SetFrom($from, $fromName);
		$mail->AddReplyTo($from, $fromName);

		$recipients = explode(',', $to);
		foreach($recipients as $key){
			$mail->AddAddress($key);
		}

		$mail->Subject = $subject;
		$mail->AltBody = strip_tags($message);
		if(CConfig::get('email.isHtml')) $mail->MsgHTML(nl2br($message));

		//$mail->AddAttachment("images/test_file.gif");      // attachment
		//$mail->AddAttachment("images/test_file_thumb.gif"); // attachment

		$result = $mail->Send();
		if(!$result){
			self::$_error = CrypticBrain::t('core', 'PHPMailer Error:').' '.$mail->ErrorInfo;
		}

		$mail->ClearAddresses();
		$mail->ClearReplyTos();
		
		return $result;    	
	}

    public static function defaultEmailStyle($head, $body, $footer)
    {
        return '<table style="margin:0;background-color:#f5f5f5 !important;width:600px;border:none;"><tbody valign="top"><tr><td style="padding: 20px 40px 20px 40px !important;"><table style="margin:0px !important;background-color: #fff !important;width:100% !important;border:1px solid #dadee3 !important"><tbody><tr><td style="padding: 20px !important;cursor:default;"><h2 style="color:#1c2a47;display:block;font-family:\'Helvetica Neue\', Helvetica, Arial, \'lucida grande\',tahoma,verdana,arial,sans-serif;font-size:16px;font-weight:bold;line-height:100%;margin:0px 0px 20px 0px;text-align:left;">'.$head.'</h2><p style="cursor:default;color:#282828;font-family:\'Helvetica Neue\', Helvetica, Arial, \'lucida grande\',tahoma,verdana,arial,sans-serif;font-size:12px;font-weight:600;line-height:150%;text-align:left;">'.$body.'</p><p style="margin-top:20px;padding:20px 0px 0px 0px;border-top:1px solid #dadee3;cursor:default;color:#9197a3;font-family:\'Helvetica Neue\', Helvetica, Arial, \'lucida grande\',tahoma,verdana,arial,sans-serif;font-size:12px;font-weight:600;line-height:150%;text-align:left;">'.$footer.'</p></td></tr></tbody></table></td></tr></tbody></table>';
    }
}