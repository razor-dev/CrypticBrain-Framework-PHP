<?php
/**
 * CIp is a helper class
 *
 *
 * PUBLIC:					PROTECTED:					PRIVATE:
 * ----------               ----------                  ----------
 * getBinaryIp
 * convertIpStringToBinary
 * convertHexToBin
 * convertIpBinaryToString
 *
 */

include(dirname(__FILE__).'/../vendors/phpip2colorname.class.php');
 
class CIp
{
    /**
     * Gets the binary form of the provided IP.
     * Binary IPs are IPv4 or IPv6 IPs compressed into 4 or 16 bytes.
     * @param null $ip
     * @param bool $invalidValue
     * @return bool|string
     */
    public static function getBinaryIp($ip = null, $invalidValue = false)
    {
        if(!$ip){
           $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : null;
        }

        $ip = $ip ? self::convertIpStringToBinary($ip) : false;
        return $ip !== false ? $ip : $invalidValue;
    }

    /**
     * Converts a string based IP (v4 or v6) to a 4 or 16 byte string.
     * @param $ip
     * @return bool|mixed|string
     */
    public static function convertIpStringToBinary($ip)
    {
        $originalIp = $ip;
        $ip = trim($ip);

        if(strpos($ip, ':') !== false){
            // IPv6
            if(preg_match('#:(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})$#', $ip, $match)){
                // embedded IPv4
                $long = ip2long($match[1]);
                if(!$long){
                    return false;
                }

                $hex = str_pad(dechex($long), 8, '0', STR_PAD_LEFT);
                $v4chunks = str_split($hex, 4);
                $ip = str_replace($match[0], ":$v4chunks[0]:$v4chunks[1]", $ip);
            }

            if(strpos($ip, '::') !== false){
                if(substr_count($ip, '::') > 1){
                    // ambiguous
                    return false;
                }

                $delims = substr_count($ip, ':');
                if($delims > 7){
                    return false;
                }

                $ip = str_replace('::', str_repeat(':0', 8 - $delims) . ':', $ip);
                if($ip[0] == ':'){
                    $ip = '0' . $ip;
                }
            }

            $ip = strtolower($ip);
            $parts = explode(':', $ip);

            if(count($parts) != 8){
                return false;
            }

            foreach($parts as &$part){
                $len = strlen($part);
                if($len > 4 or preg_match('/[^0-9a-f]/', $part)){
                    return false;
                }

                if($len < 4){
                    $part = str_repeat('0', 4 - $len) . $part;
                }
            }

            $hex = implode('', $parts);
            if(strlen($hex) != 32){
                return false;
            }

            return self::convertHexToBin($hex);
        }else if(strpos($ip, '.')){
            // IPv4
            if(!preg_match('#(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})#', $ip, $match)){
                return false;
            }

            $long = ip2long($match[1]);
            if(!$long){
                return false;
            }

            return self::convertHexToBin(
                str_pad(dechex($long), 8, '0', STR_PAD_LEFT)
            );
        }else if(strlen($ip) == 4 || strlen($ip) == 16){
            // already binary encoded
            return $ip;
        }else if(is_numeric($originalIp) && $originalIp < pow(2, 32)){
            // IPv4 as integer
            return self::convertHexToBin(
                str_pad(dechex($originalIp), 8, '0', STR_PAD_LEFT)
            );
        }else{
            return false;
        }
    }

    /**
     * Converts a hex string to binary
     * @param $hex
     * @return string
     */
	public static function convertHexToBin($hex)
    {
        if(function_exists('hex2bin')){
            return hex2bin($hex);
        }

        $len = strlen($hex);

        if($len % 2){
            trigger_error('Hexadecimal input string must have an even length', E_USER_WARNING);
        }

        if(strspn($hex, '0123456789abcdefABCDEF') != $len){
            trigger_error('Input string must be hexadecimal string', E_USER_WARNING);
        }

        return pack('H*', $hex);
    }

    /**
     * Converts a binary string containing IPv4 or v6 data to a printable/human readable version.
     * @param $ip
     * @param bool $shorten
     * @return bool|string
     */
    public static function convertIpBinaryToString($ip, $shorten = true)
    {
        if(strlen($ip) == 4){
            // IPv4
            $parts = array();
            foreach(str_split($ip) AS $char){
                $parts[] = ord($char);
            }

            return implode('.', $parts);
        }else if(strlen($ip) == 16){
            // IPv6
            $parts = array();
            $chunks = str_split($ip);
            for($i = 0; $i < 16; $i += 2){
                $char1 = $chunks[$i];
                $char2 = $chunks[$i + 1];
                $part = sprintf('%02x%02x', ord($char1), ord($char2));

                if($shorten){
                    $part = ltrim($part, '0');
                    if(!strlen($part)){
                        $part = '0';
                    }
                }
                $parts[] = $part;
            }

            $output = implode(':', $parts);
            if($shorten){
                $output = preg_replace('/((^0|:0){2,})(.*)$/', ':$3', $output);
                if(substr($output, -1) === ':' && (strlen($output) == 1 || substr($output, -2, 1) !== ':')){
                    $output .= ':';
                }
            }

            return strtolower($output);
        }else if(preg_match('/^[0-9]+$/', $ip)){
            return long2ip($ip + 0);
        }else{
            return false;
        }
    }
	
	public static function convertIpToColor($ip)
	{
		$IpColor = new phpIp2ColorName($ip);
		
		return $IpColor->getColorHexValue();
	}
}