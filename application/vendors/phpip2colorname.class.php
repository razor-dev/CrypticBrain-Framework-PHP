<?php
/**
 * phpIp2ColorName class
 *
 */
class phpIp2ColorName
{
	function __construct($ip)
	{
		$this->ip = $ip;
		$this->ipArr = $this->getIpArr();
		$this->rgbArr = array($this->ipArr[0], $this->ipArr[1], $this->ipArr[2]);		
		$this->determineColor();
	}

	public $ip = '';

	public $accuracy = 0;

	private $ipArr = array();

	private $rgbArr = array();

	private $colorIndex = null;

	/**
	 * 0 - HTML color mame
	 * 1 - human readable color name
	 * 2 - HTML color hex value
	 * 3 - integer - first octet value
	 * 4 - integer - second octet value
	 * 5 - integer - third octet value
	 *
	 * @var array
	 */
	private $colors = array(
		array('AliceBlue','Alice Blue','F0F8FF',240,248,255),
		array('AntiqueWhite','Antique White','FAEBD7',250,235,215),
		array('Aqua','Aqua','00FFFF',0,255,255),
		array('Aquamarine','Aquamarine','7FFFD4',127,255,212),
		array('Azure','Azure','F0FFFF',240,255,255),
		array('Beige','Beige','F5F5DC',245,245,220),
		array('Bisque','Bisque','FFE4C4',255,228,196),
		array('Black','Black','000000',0,0,0),
		array('BlanchedAlmond','Blanched Almond','FFEBCD',255,235,205),
		array('Blue','Blue','0000FF',0,0,255),
		array('BlueViolet','Blue Violet','8A2BE2',138,43,226),
		array('Brown','Brown','A52A2A',165,42,42),
		array('BurlyWood','Burly Wood','DEB887',222,184,135),
		array('CadetBlue','Cadet Blue','5F9EA0',95,158,160),
		array('Chartreuse','Chartreuse','7FFF00',127,255,0),
		array('Chocolate','Chocolate','D2691E',210,105,30),
		array('Coral','Coral','FF7F50',255,127,80),
		array('CornflowerBlue','Cornflower Blue','6495ED',100,149,237),
		array('Cornsilk','Cornsilk','FFF8DC',255,248,220),
		array('Crimson','Crimson','DC143C',220,20,60),
		array('Cyan','Cyan','00FFFF',0,255,255),
		array('DarkBlue','Dark Blue','00008B',0,0,139),
		array('DarkCyan','Dark Cyan','008B8B',0,139,139),
		array('DarkGoldenRod','Dark Golden Rod','B8860B',184,134,11),
		array('DarkGray','Dark Gray','A9A9A9',169,169,169),
		array('DarkGreen','Dark Green','006400',0,100,0),
		array('DarkKhaki','Dark Khaki','BDB76B',189,183,107),
		array('DarkMagenta','Dark Magenta','8B008B',139,0,139),
		array('DarkOliveGreen','Dark Olive Green','556B2F',85,107,47),
		array('Darkorange','Darkorange','FF8C00',255,140,0),
		array('DarkOrchid','Dark Orchid','9932CC',153,50,204),
		array('DarkRed','Dark Red','8B0000',139,0,0),
		array('DarkSalmon','Dark Salmon','E9967A',233,150,122),
		array('DarkSeaGreen','Dark Sea Green','8FBC8F',143,188,143),
		array('DarkSlateBlue','Dark Slate Blue','483D8B',72,61,139),
		array('DarkSlateGray','Dark Slate Gray','2F4F4F',47,79,79),
		array('DarkTurquoise','Dark Turquoise','00CED1',0,206,209),
		array('DarkViolet','Dark Violet','9400D3',148,0,211),
		array('DeepPink','Deep Pink','FF1493',255,20,147),
		array('DeepSkyBlue','DeepSkyBlue','00BFFF',0,191,255),
		array('DimGray','Dim Gray','696969',105,105,105),
		array('DimGrey','Dim Grey','696969',105,105,105),
		array('DodgerBlue','Dodger Blue','1E90FF',30,144,255),
		array('FireBrick','Fire Brick','B22222',178,34,34),
		array('FloralWhite','Floral White','FFFAF0',255,250,240),
		array('ForestGreen','Forest Green','228B22',34,139,34),
		array('Fuchsia','Fuchsia','FF00FF',255,0,255),
		array('Gainsboro','Gainsboro','DCDCDC',220,220,220),
		array('GhostWhite','Ghost White','F8F8FF',248,248,255),
		array('Gold','Gold','FFD700',255,215,0),
		array('GoldenRod','Golden Rod','DAA520',218,165,32),
		array('Gray','Gray','808080',128,128,128),
		array('Grey','Grey','808080',128,128,128),
		array('Green','Green','008000',0,128,0),
		array('GreenYellow','Green Yellow','ADFF2F',173,255,47),
		array('HoneyDew','Honey Dew','F0FFF0',240,255,240),
		array('HotPink','Hot Pink','FF69B4',255,105,180),
		array('IndianRed','Indian Red','CD5C5C',205,92,92),
		array('Indigo','Indigo','4B0082',75,0,130),
		array('Ivory','Ivory','FFFFF0',255,255,240),
		array('Khaki','Khaki','F0E68C',240,230,140),
		array('Lavender','Lavender','E6E6FA',230,230,250),
		array('LavenderBlush','Lavender Blush','FFF0F5',255,240,245),
		array('LawnGreen','Lawn Green','7CFC00',124,252,0),
		array('LemonChiffon','Lemon Chiffon','FFFACD',255,250,205),
		array('LightBlue','Light Blue','ADD8E6',173,216,230),
		array('LightCoral','Light Coral','F08080',240,128,128),
		array('LightCyan','Light Cyan','E0FFFF',224,255,255),
		array('LightGoldenRodYellow','Light GoldenRod Yellow','FAFAD2',250,250,210),
		array('LightGray','Light Gray','D3D3D3',211,211,211),
		array('LightGreen','Light Green','90EE90',144,238,144),
		array('LightPink','Light Pink','FFB6C1',255,182,193),
		array('LightSalmon','Light Salmon','FFA07A',255,160,122),
		array('LightSeaGreen','Light Sea Green','20B2AA',32,178,170),
		array('LightSkyBlue','Light Sky Blue','87CEFA',135,206,250),
		array('LightSlateGray','Light Slate Gray','778899',119,136,153),
		array('LightSteelBlue','Light Steel Blue','B0C4DE',176,196,222),
		array('LightYellow','Light Yellow','FFFFE0',255,255,224),
		array('Lime','Lime','00FF00',0,255,0),
		array('LimeGreen','Lime Green','32CD32',50,205,50),
		array('Linen','Linen','FAF0E6',250,240,230),
		array('Magenta','Magenta','FF00FF',255,0,255),
		array('Maroon','Maroon','800000',128,0,0),
		array('MediumAquaMarine','Medium Aqua Marine','66CDAA',102,205,170),
		array('MediumBlue','Medium Blue','0000CD',0,0,205),
		array('MediumOrchid','Medium Orchid','BA55D3',186,85,211),
		array('MediumPurple','Medium Purple','9370D8',147,112,216),
		array('MediumSeaGreen','Medium Sea Green','3CB371',60,179,113),
		array('MediumSlateBlue','Medium Slate Blue','7B68EE',123,104,238),
		array('MediumSpringGreen','Medium Spring Green','00FA9A',0,250,154),
		array('MediumTurquoise','Medium Turquoise','48D1CC',72,209,204),
		array('MediumVioletRed','Medium Violet Red','C71585',199,21,133),
		array('MidnightBlue','Midnight Blue','191970',25,25,112),
		array('MintCream','Mint Cream','F5FFFA',245,255,250),
		array('MistyRose','Misty Rose','FFE4E1',255,228,225),
		array('Moccasin','Moccasin','FFE4B5',255,228,181),
		array('NavajoWhite','Navajo White','FFDEAD',255,222,173),
		array('Navy','Navy','000080',0,0,128),
		array('OldLace','Old Lace','FDF5E6',253,245,230),
		array('Olive','Olive','808000',128,128,0),
		array('OliveDrab','Olive Drab','6B8E23',107,142,35),
		array('Orange','Orange','FFA500',255,165,0),
		array('OrangeRed','Orange Red','FF4500',255,69,0),
		array('Orchid','Orchid','DA70D6',218,112,214),
		array('PaleGoldenRod','Pale Golden Rod','EEE8AA',238,232,170),
		array('PaleGreen','Pale Green','98FB98',152,251,152),
		array('PaleTurquoise','Pale Turquoise','AFEEEE',175,238,238),
		array('PaleVioletRed','Pale Violet Red','D87093',216,112,147),
		array('PapayaWhip','Papaya Whip','FFEFD5',255,239,213),
		array('PeachPuff','Peach Puff','FFDAB9',255,218,185),
		array('Peru','Peru','CD853F',205,133,63),
		array('Pink','Pink','FFC0CB',255,192,203),
		array('Plum','Plum','DDA0DD',221,160,221),
		array('PowderBlue','Powder Blue','B0E0E6',176,224,230),
		array('Purple','Purple','800080',128,0,128),
		array('Red','Red','FF0000',255,0,0),
		array('RosyBrown','Rosy Brown','BC8F8F',188,143,143),
		array('RoyalBlue','Royal Blue','4169E1',65,105,225),
		array('SaddleBrown','Saddle Brown','8B4513',139,69,19),
		array('Salmon','Salmon','FA8072',250,128,114),
		array('SandyBrown','Sandy Brown','F4A460',244,164,96),
		array('SeaGreen','Sea Green','2E8B57',46,139,87),
		array('SeaShell','Sea Shell','FFF5EE',255,245,238),
		array('Sienna','Sienna','A0522D',160,82,45),
		array('Silver','Silver','C0C0C0',192,192,192),
		array('SkyBlue','Sky Blue','87CEEB',135,206,235),
		array('SlateBlue','Slate Blue','6A5ACD',106,90,205),
		array('SlateGray','Slate Gray','708090',112,128,144),
		array('Snow','Snow','FFFAFA',255,250,250),
		array('SpringGreen','Spring Green','00FF7F',0,255,127),
		array('SteelBlue','Steel Blue','4682B4',70,130,180),
		array('Tan','Tan','D2B48C',210,180,140),
		array('Teal','Teal','008080',0,128,128),
		array('Thistle','Thistle','D8BFD8',216,191,216),
		array('Tomato','Tomato','FF6347',255,99,71),
		array('Turquoise','Turquoise','40E0D0',64,224,208),
		array('Violet','Violet','EE82EE',238,130,238),
		array('Wheat','Wheat','F5DEB3',245,222,179),
		array('White','White','FFFFFF',255,255,255),
		array('WhiteSmoke','White Smoke','F5F5F5',245,245,245),
		array('Yellow','Yellow','FFFF00',255,255,0),
		array('YellowGreen','Yellow Green','9ACD32',154,205,50),
	);
	
	/**
	 * Returns IP address in array of integer values
	 * @return array
	 */
	private function getIpArr()
	{
		$vars = explode('.', $this->ip);
		return array(
			intval($vars[0]),
			intval($vars[1]),
			intval($vars[2]),
			intval($vars[3])
		);
	}

	/**
	 * Determine most similar color for given IP address
	 * Returns determined color index from colors array
	 * @return integer
	 */
	private function determineColor()
	{
		if($this->rgbArr[0] < 0){
			$this->rgbArr[0] = 0;
		}
		if($this->rgbArr[1] < 0){
			$this->rgbArr[1] = 0;
		}
		if($this->rgbArr[2] < 0){
			$this->rgbArr[2] = 0;
		}

		$minVar = 255 + 255 + 255 + 1;
		$minIndex = null;
		for($i = 0, $count = count($this->colors); $i < $count; $i++){
			$this->colors[$i][6] = abs( $this->colors[$i][3] - $this->rgbArr[0]) + abs($this->colors[$i][4] - $this->rgbArr[1]) + abs($this->colors[$i][5] - $this->rgbArr[2]);
			if($this->colors[$i][6] < $minVar){
				$minVar = $this->colors[$i][6];
				$minIndex = $i;
			}
		}
		$this->colorIndex = $minIndex;
		$this->accuracy = round(($minVar / (255 + 255 + 255)) * 100, 2);
		return intval($this->accuracy);
	}
	
	/**
	 * Returns HTML color hex value
	 * @param mixed $prefix
	 * @return string
	 */
	public function getColorHexValue($prefix = '#')
	{
		if(!is_null($this->colorIndex) && isset($this->colors[$this->colorIndex])){
			return $prefix.$this->colors[$this->colorIndex][2];
		}else{
			return '';
		}
	}
}