<?php



use mb_convert_encoding as mb_convert_encoding;
/**
 * Class for string functions
 * @author nick mullen
 */
class StringUtil {

    /**
     * Checks array for a key and returns true if value exists 
     * @param type $array
     * @param type $value
     * @return boolean
     */
    public function isValue($array, $value) {
        $result = false;
        if ( isset($array) && isset($array[$value]) ) {
            if ( $array && isset($array) && array_key_exists($value, $array) && $array[$value] && isset($value, $array) ) {
                if ( is_array($array[$value]) ) {
                    $result = true;
                } elseif ( strlen($array[$value]) > 0 ) {
                    $result = true;
                }
            }
        }
        return $result;
    }

    /**
     * Validate  file name
     * @param string $file Filename and extension 
     * @param string $ext Pipe delimited list of allowed extensions 
     * @return boolean
     */
    public function isValidFileName($file, $ext) {
 

        // check for double extension 
        $file_array = explode(".", $file);
        if ( count($file_array) !== 2 ) {
            return false;
        }
       $filename = $this->getFilename($file);
        $myExt = $this->getExtension($file);

        if ( strpos($ext, $myExt) === false ) {
            return false;
        }
        if ( !preg_match("/[\/]/", $file) && 
	     !preg_match("/[\\\\]/", $file) && 
             // preg_match('/^(?:[a-z0-9_-]|\.(?!\.))+$/iD', $filename) && 
	     preg_match("/^(?!.{256,})(?!(aux|clock\$|con|nul|prn|com[1-9]|lpt[1-9])(?:$|\.))[^ ][ \.\w-$()+=[\];#@~,&amp;']+[^\. ]$/i", $file) == true &&
             $file != '' 
             && strlen($filename) < 90 && 
             (0 == preg_match('/^[a-z0-9-]+\.ext$/', $file)) 
              && (preg_match('/[[:cntrl:]]/', $file) == false ) 
             ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Clean string, remove non Alpha numeric
     * @param string $string
     * @return string
     */
    public function cleanAlphaNumeric($string, $exceptions = '', $maxLen = 1000) {
        $regx = '/[^-a-zA-Z0-9_ ' . $exceptions . ']/';
        $trimedString = substr($string, 0, $maxLen);
        $trimedString = preg_replace($regx, '', $trimedString);
        return $trimedString;
    }

    /**
     * Clean string, remove non numeric
     * @param string $string
     * @return string
     */
    public function cleanNumeric($string) {
        $string = preg_replace('/[^-0-9_ ]/', '', $string);
        $string = filter_var($string, FILTER_VALIDATE_INT);
        return $string;
    }

    /**
     * Check that key exists within a complex array 
     * @param string $key
     * @param array $array
     * @param array $delimiter
     * @return boolean
     */
    public function keyExists($key, $array ) {
        $delimiter = array("['", "']");
        $replace = str_replace($delimiter, '-', trim($key));
        $replace = str_replace('--', '-', $replace);
        $replace = substr($replace, 1, -1);
        $keys = explode('-', $replace);

        if ( is_array($array) ) {

            foreach ($keys as $k) {
                if ( array_key_exists($k, $array) ) {
                    if ( is_array($array[$k]) ) {

                        $k_ = "['" . $k . "']";
                        $key = str_replace($k_, '', $key);
                        $r = $this->keyExists($key, $array[$k]);

                        if ( $r === FALSE ) {
                            return false;
                            break;
                        } elseif ( $r ) {
                            return true;
                            break;
                        }
                    }
                } else {
                    return false;
                    break;
                }
            }
            return true;
        }
        return true;
    }
    
    function has_string_keys(array $array) {
        return count(array_filter(array_keys($array), 'is_string')) > 0;
    }



    function get_params($scope, $keys )
    {
        $output = array();
        if (in_array ('get',$scope)) {
            foreach ($keys as $key) {
                if (isset($_GET[$key])) {
                    $output[substr(strip_tags(trim($this->clean($key))), 0, 20)] = substr(strip_tags(trim($this->clean($_GET[$key]))), 0, 50);
                }
            }
        }
        if (in_array ('post',$scope)) {
            foreach ($keys as $key) {
                if (isset($_POST[$key])) {
                    $output[substr(strip_tags(trim($this->clean($key))), 0, 20)] = substr(strip_tags(trim($this->clean($_POST[$key]))), 0, 50);
                }
            }
        }

        return $output;
    }


  public function byteValue($str){
     $strOut = '';
     for ( $pos=0; $pos < strlen($str); $pos ++ ) {
       $byte = substr($str, $pos);
       $strOut .= 'chr('.ord($byte).')'.($pos != strlen($str)-1 ?'.':'');
     }
    return $strOut;
    }




    /*
     * Clean 
     * Sanitize and decodes string 
     * @param type $string
     * @param filter $string
     * @param flags see https://www.php.net/manual/en/function.filter-var.php
     * @return type
     */
    public function clean($string, $filter = FILTER_SANITIZE_STRING, $flags = FILTER_FLAG_ENCODE_LOW,$strict = false,$debug = false) {



      // conver numeric values to 401
     // $string = $this->entitiesToNumeric($string,true);


      $string = $this->convert_ascii(trim($string));

       //echo $string.'<br>'; 
       // Replace the trouble makers 
        $string = str_replace(',', "&#44;", $string);  
        $string = str_replace(',', "&#44;", $string);  
        $string = str_replace('’', "&#39;", $string);  
$string = str_replace('‘', "&#39;", $string);  
$string = str_replace('’', "&#39;", $string);  
        $string = str_replace('’', "&#39;", $string);          
        $string = str_replace('’', "&#39;", $string);        
        $string = str_replace('’', "&#39;", $string);        
        $string = str_replace('‘', "&#39;", $string);        
        $string = str_replace('“', '&#34;', $string);        
        $string = str_replace('”', '&#34;', $string);
        $string = str_replace('â€™', "&#39;", $string);
        $string = str_replace('â€™', "&#39;", $string);
        $string = str_replace('â€˜', "&#39;", $string);
        $string = str_replace('â€œ', '&#34;', $string);
        $string = str_replace('â€', '&#34;', $string);
        $string = str_replace('â€™', "&#34;", $string);
        $string = str_replace('&aacute;', "&#225;", $string);


if ($debug){
 echo '--'.$this->byteValue($string). '--';
}
 // Convert string to UTF-8
        $string = mb_convert_encoding($string, "UTF-8",mb_detect_encoding( $string));
//echo $this->byteValue($string).'<br>';




//echo $this->byteValue($string).'<br>';

      // echo $string.'<br>'; 

        // conver numeric values to 401
        $string = $this->entitiesToNumeric($string,true);

        // decode any html elements
        $string = html_entity_decode($string, ENT_QUOTES, 'UTF-8');
        $string = html_entity_decode($string, ENT_QUOTES, 'UTF-8');
        $string = html_entity_decode($string, ENT_QUOTES, 'UTF-8');
        $string = html_entity_decode($string, ENT_QUOTES, 'UTF-8');
        
        $string = htmlentities($string, ENT_QUOTES, 'UTF-8');

 return $string;
    }






    public function getExtension($file) {
        $ext = pathinfo($file);
        return $ext['extension'];
    }

    public function getFilename($file) {
        $ext = pathinfo($file);
        return $ext['filename'];
    }




        /**
     *  Convert ascii into UTF-8 
     * @param type $string
     * @return type
     */
    public function convert_ascii($string) {
        // don't ask
        $search[]  = chr(248).chr(38).chr(97).chr(97).chr(99).chr(117).chr(116).chr(101).chr(59);
        $replace[] = '&#341;&#225;';


        $search[] = chr(195).chr(162).chr(194).chr(128).chr(194).chr(153);
        $replace[] = '&#34;';

        // Replace Single Curly Quotes
        $search[] = chr(226) . chr(128) . chr(152);
        $replace[] = "&#39;";
        $search[] = chr(226) . chr(128) . chr(153);
        $replace[] = "&#39;";

        // Replace Smart Double Curly Quotes
        $search[] = chr(226) . chr(128) . chr(156);
        $replace[] = '&#34;';
        $search[] = chr(226) . chr(128) . chr(157);
        $replace[] = '&#34;';
        // Replace En Dash
        $search[] = chr(226) . chr(128) . chr(147);
        $replace[] = '&#45;&#45;';
        // Replace Em Dash
        $search[] = chr(226) . chr(128) . chr(148);
        $replace[] = '&#45;&#45;&#45;';
        // Replace Bullet
        $search[] = chr(226) . chr(128) . chr(162);
        $replace[] = '&#42;';

        // Replace Ellipsis with three consecutive dots
        $search[] = chr(226) . chr(128) . chr(166);
        $replace[] = '&#46;&#46;&#46;';
        // á 
        $search[] = chr(195) . chr(161);
        $replace[] = "&#345;";

        //  ń
        $search[] = chr(197) . chr(132);
        $replace[] = "&#324;";


        $search[] = chr(132) . chr(115);
        $replace[] = "&#324;";


        
         // ř
        $search[] = chr(197) . chr(153);
        $replace[] = "&#345;";

        // ż

        $search[] =  chr(191) . chr(189) ;
        $replace[] = "&#380;";

      // ż

        $search[] =  chr(191) . chr(189);
        $replace[] = "&#380;";

	// LATIN CAPITAL LETTER A WITH GRAVE À
        $search[] = chr(195) . chr(128);
	$replace[] = "&#192;";

	// LATIN CAPITAL LETTER A WITH ACUTE Á
        $search[] = chr(195) . chr(129);
	$replace[] = "&#193;";

	// LATIN CAPITAL LETTER A WITH CIRCUMFLEX Â
        $search[] = chr(195) . chr(130);
	$replace[] = "&#194;";

	// LATIN CAPITAL LETTER A WITH TILDE Ã
        $search[] = chr(195) . chr(131);
	$replace[] = "&#195;";

	// LATIN CAPITAL LETTER A WITH DIAERESIS Ä
        $search[] = chr(195) . chr(132);
	$replace[] = "&#196;";

	// LATIN CAPITAL LETTER A WITH RING ABOVE Å
        $search[] = chr(195) . chr(133);
	$replace[] = "&#197;";

	// LATIN CAPITAL LETTER AE Æ
        $search[] = chr(195) . chr(134);
	$replace[] = "&#198;";

	// LATIN CAPITAL LETTER C WITH CEDILLA Ç
        $search[] = chr(195) . chr(135);
	$replace[] = "&#199;";

	// LATIN CAPITAL LETTER E WITH GRAVE È
        $search[] = chr(195) . chr(136);
	$replace[] = "&#200;";

	// LATIN CAPITAL LETTER E WITH ACUTE É
        $search[] = chr(195) . chr(137);
	$replace[] = "&#201;";

	// LATIN CAPITAL LETTER E WITH CIRCUMFLEX Ê
        $search[] = chr(195) . chr(138);
	$replace[] = "&#202;";

	// LATIN CAPITAL LETTER E WITH DIAERESIS Ë
        $search[] = chr(195) . chr(139);
	$replace[] = "&#203;";

	// LATIN CAPITAL LETTER I WITH GRAVE Ì
        $search[] = chr(195) . chr(140);
	$replace[] = "&#204;";

	// LATIN CAPITAL LETTER I WITH ACUTE Í
        $search[] = chr(195) . chr(141);
	$replace[] = "&#205;";

	// LATIN CAPITAL LETTER I WITH CIRCUMFLEX Î
        $search[] = chr(195) . chr(142);
	$replace[] = "&#206;";

	// LATIN CAPITAL LETTER I WITH DIAERESIS Ï
        $search[] = chr(195) . chr(143);
	$replace[] = "&#207;";

	// LATIN CAPITAL LETTER ETH Ð
        $search[] = chr(195) . chr(144);
	$replace[] = "&#208;";

	// LATIN CAPITAL LETTER N WITH TILDE Ñ
        $search[] = chr(195) . chr(145);
	$replace[] = "&#209;";

	// LATIN CAPITAL LETTER O WITH GRAVE Ò
        $search[] = chr(195) . chr(146);
	$replace[] = "&#210;";

	// LATIN CAPITAL LETTER O WITH ACUTE Ó
        $search[] = chr(195) . chr(147);
	$replace[] = "&#211;";

	// LATIN CAPITAL LETTER O WITH CIRCUMFLEX Ô
        $search[] = chr(195) . chr(148);
	$replace[] = "&#212;";

	// LATIN CAPITAL LETTER O WITH TILDE Õ
        $search[] = chr(195) . chr(149);
	$replace[] = "&#213;";

	// LATIN CAPITAL LETTER O WITH DIAERESIS Ö
        $search[] = chr(195) . chr(150);
	$replace[] = "&#214;";

	// LATIN CAPITAL LETTER O WITH STROKE Ø
        $search[] = chr(195) . chr(152);
	$replace[] = "&#216;";

	// LATIN CAPITAL LETTER U WITH GRAVE Ù
        $search[] = chr(195) . chr(153);
	$replace[] = "&#217;";

	// LATIN CAPITAL LETTER U WITH ACUTE Ú
        $search[] = chr(195) . chr(154);
	$replace[] = "&#218;";

	// LATIN CAPITAL LETTER U WITH CIRCUMFLEX Û
        $search[] = chr(195) . chr(155);
	$replace[] = "&#219;";

	// LATIN CAPITAL LETTER U WITH DIAERESIS Ü
        $search[] = chr(195) . chr(156);
	$replace[] = "&#220;";

	// LATIN CAPITAL LETTER Y WITH ACUTE Ý
        $search[] = chr(195) . chr(157);
	$replace[] = "&#221;";

	// LATIN CAPITAL LETTER THORN Þ
        $search[] = chr(195) . chr(158);
	$replace[] = "&#222;";

	// LATIN SMALL LETTER SHARP S
        $search[] = chr(195) . chr(159);
	$replace[] = "&#223;";

	// LATIN SMALL LETTER A WITH GRAVE à
        $search[] = chr(195) . chr(160);
	$replace[] = "&#224;";

	// LATIN SMALL LETTER A WITH ACUTE á
        $search[] = chr(195) . chr(161);
	$replace[] = "&#225;";

	// LATIN SMALL LETTER A WITH CIRCUMFLEX â
        $search[] = chr(195) . chr(162);
	$replace[] = "&#226;";

	// LATIN SMALL LETTER A WITH TILDE ã
        $search[] = chr(195) . chr(163);
	$replace[] = "&#227;";

	// LATIN SMALL LETTER A WITH DIAERESIS ä
        $search[] = chr(195) . chr(164);
	$replace[] = "&#228;";

	// LATIN SMALL LETTER A WITH RING ABOVE å
        $search[] = chr(195) . chr(165);
	$replace[] = "&#229;";

	// LATIN SMALL LETTER AE æ
        $search[] = chr(195) . chr(166);
	$replace[] = "&#230;";

	// LATIN SMALL LETTER C WITH CEDILLA ç
        $search[] = chr(195) . chr(167);
	$replace[] = "&#231;";

	// LATIN SMALL LETTER E WITH GRAVE è
        $search[] = chr(195) . chr(168);
	$replace[] = "&#232;";

	// LATIN SMALL LETTER E WITH ACUTE é
        $search[] = chr(195) . chr(169);
	$replace[] = "&#233;";

	// LATIN SMALL LETTER E WITH CIRCUMFLEX ê
        $search[] = chr(195) . chr(170);
	$replace[] = "&#234;";

	// LATIN SMALL LETTER E WITH DIAERESIS ë
        $search[] = chr(195) . chr(171);
	$replace[] = "&#235;";

	// LATIN SMALL LETTER I WITH GRAVE ì
        $search[] = chr(195) . chr(172);
	$replace[] = "&#236;";

	// LATIN SMALL LETTER I WITH ACUTE í
        $search[] = chr(195) . chr(173);
	$replace[] = "&#237;";

	// LATIN SMALL LETTER I WITH CIRCUMFLEX î
        $search[] = chr(195) . chr(174);
	$replace[] = "&#238;";

	// LATIN SMALL LETTER I WITH DIAERESIS ï
        $search[] = chr(195) . chr(175);
	$replace[] = "&#239;";

	// LATIN SMALL LETTER ETH ð
        $search[] = chr(195) . chr(176);
	$replace[] = "&#240;";

	// LATIN SMALL LETTER N WITH TILDE ñ
        $search[] = chr(195) . chr(177);
	$replace[] = "&#241;";

	// LATIN SMALL LETTER O WITH GRAVE ò
        $search[] = chr(195) . chr(178);
	$replace[] = "&#242;";

	// LATIN SMALL LETTER O WITH ACUTE ó
        $search[] = chr(195) . chr(179);	
	$replace[] = "&#243;";

	// LATIN SMALL LETTER O WITH CIRCUMFLEX ô
        $search[] = chr(195) . chr(180);
	$replace[] = "&#244;";

	// LATIN SMALL LETTER O WITH TILDE õ
        $search[] = chr(195) . chr(181);
	$replace[] = "&#245;";

	// LATIN SMALL LETTER O WITH DIAERESIS ö
        $search[] = chr(195) . chr(182);
	$replace[] = "&#246;";

	// DIVISION SIGN ÷
        $search[] = chr(195) . chr(183);
	$replace[] = "&#247;";

	// LATIN SMALL LETTER O WITH STROKE ø
        $search[] = chr(195) . chr(184);
	$replace[] = "&#248;";

	// LATIN SMALL LETTER U WITH GRAVE ù
        $search[] = chr(195) . chr(185);
	$replace[] = "&#249;";

	// LATIN SMALL LETTER U WITH ACUTE ú
        $search[] = chr(195) . chr(186);
	$replace[] = "&#250;";

	// LATIN SMALL LETTER U WITH CIRCUMFLEX û
        $search[] = chr(195) . chr(187);
	$replace[] = "&#251;";

	// LATIN SMALL LETTER U WITH DIAERESIS ü
        $search[] = chr(195) . chr(188);
	$replace[] = "&#252;";

      

	// LATIN SMALL LETTER Y WITH ACUTE ý
        $search[] = chr(195) . chr(189);
	$replace[] = "&#253;";

	// LATIN SMALL LETTER THORN þ
        $search[] = chr(195) . chr(190);
	$replace[] = "&#254;";

	// LATIN SMALL LETTER Y WITH DIAERESIS ÿ
        $search[] = chr(195) . chr(191);	
	$replace[] = "&#255;";


        // °
        $search[] = chr(194) . chr(176);	
	$replace[] = "&#xc2b0;";



        // Replace Middle Dot
        $search[] = chr(194) . chr(183);
        $replace[] = '&#42;';

        $search[] = chr(145);
        $replace[] = "&#39;";

        $search[] = chr(146); 
        $replace[] = "&#39;";

        $search[] = chr(147); 
        $replace[] = '&#34;';

        $search[] = chr(148);
        $replace[] = '&#34;'; 

        $search[] = chr(151);
        $replace[] = '-';

        $search[] =  chr(188);
        $replace[] = "&#380;";


        $search[] =  chr(132);
        $replace[] = "&#324;";

 $search[] =  chr(128);
        $replace[] = "&#39;";
 $search[] =  chr(152);
        $replace[] = "&#39;";

 $search[] =  chr(153);
        $replace[] = "&#39;";

 $search[] =  chr(191);
        $replace[] = "&#39;";

 $search[] =  chr(176);
        $replace[] = "&#176;";
// ü
	 $search[] =  chr(252);
        $replace[] = "&#252;";
//ö

 $search[] =  chr(246);
        $replace[] = "&#246;";

        // Apply Replacements
        $string = str_replace($search, $replace, $string);
        return $string;
    }

   /**
    * convert entities html To Numeric
    *
    */
   public  function entitiesToNumeric($string,$reverse = false ){

	$HTML401NamedToNumeric = array(
	    '&nbsp;'     => '&#160;',  # no-break space = non-breaking space, U+00A0 ISOnum
	    '&iexcl;'    => '&#161;',  # inverted exclamation mark, U+00A1 ISOnum
	    '&cent;'     => '&#162;',  # cent sign, U+00A2 ISOnum
	    '&pound;'    => '&#163;',  # pound sign, U+00A3 ISOnum
	    '&curren;'   => '&#164;',  # currency sign, U+00A4 ISOnum
	    '&yen;'      => '&#165;',  # yen sign = yuan sign, U+00A5 ISOnum
	    '&brvbar;'   => '&#166;',  # broken bar = broken vertical bar, U+00A6 ISOnum
	    '&sect;'     => '&#167;',  # section sign, U+00A7 ISOnum
	    '&uml;'      => '&#168;',  # diaeresis = spacing diaeresis, U+00A8 ISOdia
	    '&copy;'     => '&#169;',  # copyright sign, U+00A9 ISOnum
	    '&ordf;'     => '&#170;',  # feminine ordinal indicator, U+00AA ISOnum
	    '&laquo;'    => '&#171;',  # left-pointing double angle quotation mark = left pointing guillemet, U+00AB ISOnum
	    '&not;'      => '&#172;',  # not sign, U+00AC ISOnum
	    '&shy;'      => '&#173;',  # soft hyphen = discretionary hyphen, U+00AD ISOnum
	    '&reg;'      => '&#174;',  # registered sign = registered trade mark sign, U+00AE ISOnum
	    '&macr;'     => '&#175;',  # macron = spacing macron = overline = APL overbar, U+00AF ISOdia
	    '&deg;'      => '&#176;',  # degree sign, U+00B0 ISOnum
	    '&plusmn;'   => '&#177;',  # plus-minus sign = plus-or-minus sign, U+00B1 ISOnum
	    '&sup2;'     => '&#178;',  # superscript two = superscript digit two = squared, U+00B2 ISOnum
	    '&sup3;'     => '&#179;',  # superscript three = superscript digit three = cubed, U+00B3 ISOnum
	    '&acute;'    => '&#180;',  # acute accent = spacing acute, U+00B4 ISOdia
	    '&micro;'    => '&#181;',  # micro sign, U+00B5 ISOnum
	    '&para;'     => '&#182;',  # pilcrow sign = paragraph sign, U+00B6 ISOnum
	    '&middot;'   => '&#183;',  # middle dot = Georgian comma = Greek middle dot, U+00B7 ISOnum
	    '&cedil;'    => '&#184;',  # cedilla = spacing cedilla, U+00B8 ISOdia
	    '&sup1;'     => '&#185;',  # superscript one = superscript digit one, U+00B9 ISOnum
	    '&ordm;'     => '&#186;',  # masculine ordinal indicator, U+00BA ISOnum
	    '&raquo;'    => '&#187;',  # right-pointing double angle quotation mark = right pointing guillemet, U+00BB ISOnum
	    '&frac14;'   => '&#188;',  # vulgar fraction one quarter = fraction one quarter, U+00BC ISOnum
	    '&frac12;'   => '&#189;',  # vulgar fraction one half = fraction one half, U+00BD ISOnum
	    '&frac34;'   => '&#190;',  # vulgar fraction three quarters = fraction three quarters, U+00BE ISOnum
	    '&iquest;'   => '&#191;',  # inverted question mark = turned question mark, U+00BF ISOnum
	    '&Agrave;'   => '&#192;',  # latin capital letter A with grave = latin capital letter A grave, U+00C0 ISOlat1
	    '&Aacute;'   => '&#193;',  # latin capital letter A with acute, U+00C1 ISOlat1
	    '&Acirc;'    => '&#194;',  # latin capital letter A with circumflex, U+00C2 ISOlat1
	    '&Atilde;'   => '&#195;',  # latin capital letter A with tilde, U+00C3 ISOlat1
	    '&Auml;'     => '&#196;',  # latin capital letter A with diaeresis, U+00C4 ISOlat1
	    '&Aring;'    => '&#197;',  # latin capital letter A with ring above = latin capital letter A ring, U+00C5 ISOlat1
	    '&AElig;'    => '&#198;',  # latin capital letter AE = latin capital ligature AE, U+00C6 ISOlat1
	    '&Ccedil;'   => '&#199;',  # latin capital letter C with cedilla, U+00C7 ISOlat1
	    '&Egrave;'   => '&#200;',  # latin capital letter E with grave, U+00C8 ISOlat1
	    '&Eacute;'   => '&#201;',  # latin capital letter E with acute, U+00C9 ISOlat1
	    '&Ecirc;'    => '&#202;',  # latin capital letter E with circumflex, U+00CA ISOlat1
	    '&Euml;'     => '&#203;',  # latin capital letter E with diaeresis, U+00CB ISOlat1
	    '&Igrave;'   => '&#204;',  # latin capital letter I with grave, U+00CC ISOlat1
	    '&Iacute;'   => '&#205;',  # latin capital letter I with acute, U+00CD ISOlat1
	    '&Icirc;'    => '&#206;',  # latin capital letter I with circumflex, U+00CE ISOlat1
	    '&Iuml;'     => '&#207;',  # latin capital letter I with diaeresis, U+00CF ISOlat1
	    '&ETH;'      => '&#208;',  # latin capital letter ETH, U+00D0 ISOlat1
	    '&Ntilde;'   => '&#209;',  # latin capital letter N with tilde, U+00D1 ISOlat1
	    '&Ograve;'   => '&#210;',  # latin capital letter O with grave, U+00D2 ISOlat1
	    '&Oacute;'   => '&#211;',  # latin capital letter O with acute, U+00D3 ISOlat1
	    '&Ocirc;'    => '&#212;',  # latin capital letter O with circumflex, U+00D4 ISOlat1
	    '&Otilde;'   => '&#213;',  # latin capital letter O with tilde, U+00D5 ISOlat1
	    '&Ouml;'     => '&#214;',  # latin capital letter O with diaeresis, U+00D6 ISOlat1
	    '&times;'    => '&#215;',  # multiplication sign, U+00D7 ISOnum
	    '&Oslash;'   => '&#216;',  # latin capital letter O with stroke = latin capital letter O slash, U+00D8 ISOlat1
	    '&Ugrave;'   => '&#217;',  # latin capital letter U with grave, U+00D9 ISOlat1
	    '&Uacute;'   => '&#218;',  # latin capital letter U with acute, U+00DA ISOlat1
	    '&Ucirc;'    => '&#219;',  # latin capital letter U with circumflex, U+00DB ISOlat1
	    '&Uuml;'     => '&#220;',  # latin capital letter U with diaeresis, U+00DC ISOlat1
	    '&Yacute;'   => '&#221;',  # latin capital letter Y with acute, U+00DD ISOlat1
	    '&THORN;'    => '&#222;',  # latin capital letter THORN, U+00DE ISOlat1
	    '&szlig;'    => '&#223;',  # latin small letter sharp s = ess-zed, U+00DF ISOlat1
	    '&agrave;'   => '&#224;',  # latin small letter a with grave = latin small letter a grave, U+00E0 ISOlat1
	    '&aacute;'   => '&#225;',  # latin small letter a with acute, U+00E1 ISOlat1
	    '&acirc;'    => '&#226;',  # latin small letter a with circumflex, U+00E2 ISOlat1
	    '&atilde;'   => '&#227;',  # latin small letter a with tilde, U+00E3 ISOlat1
	    '&auml;'     => '&#228;',  # latin small letter a with diaeresis, U+00E4 ISOlat1
	    '&aring;'    => '&#229;',  # latin small letter a with ring above = latin small letter a ring, U+00E5 ISOlat1
	    '&aelig;'    => '&#230;',  # latin small letter ae = latin small ligature ae, U+00E6 ISOlat1
	    '&ccedil;'   => '&#231;',  # latin small letter c with cedilla, U+00E7 ISOlat1
	    '&egrave;'   => '&#232;',  # latin small letter e with grave, U+00E8 ISOlat1
	    '&eacute;'   => '&#233;',  # latin small letter e with acute, U+00E9 ISOlat1
	    '&ecirc;'    => '&#234;',  # latin small letter e with circumflex, U+00EA ISOlat1
	    '&euml;'     => '&#235;',  # latin small letter e with diaeresis, U+00EB ISOlat1
	    '&igrave;'   => '&#236;',  # latin small letter i with grave, U+00EC ISOlat1
	    '&iacute;'   => '&#237;',  # latin small letter i with acute, U+00ED ISOlat1
	    '&icirc;'    => '&#238;',  # latin small letter i with circumflex, U+00EE ISOlat1
	    '&iuml;'     => '&#239;',  # latin small letter i with diaeresis, U+00EF ISOlat1
	    '&eth;'      => '&#240;',  # latin small letter eth, U+00F0 ISOlat1
	    '&ntilde;'   => '&#241;',  # latin small letter n with tilde, U+00F1 ISOlat1
	    '&ograve;'   => '&#242;',  # latin small letter o with grave, U+00F2 ISOlat1
	    '&oacute;'   => '&#243;',  # latin small letter o with acute, U+00F3 ISOlat1
	    '&ocirc;'    => '&#244;',  # latin small letter o with circumflex, U+00F4 ISOlat1
	    '&otilde;'   => '&#245;',  # latin small letter o with tilde, U+00F5 ISOlat1
	    '&ouml;'     => '&#246;',  # latin small letter o with diaeresis, U+00F6 ISOlat1
	    '&divide;'   => '&#247;',  # division sign, U+00F7 ISOnum
	    '&oslash;'   => '&#248;',  # latin small letter o with stroke, = latin small letter o slash, U+00F8 ISOlat1
	    '&ugrave;'   => '&#249;',  # latin small letter u with grave, U+00F9 ISOlat1
	    '&uacute;'   => '&#250;',  # latin small letter u with acute, U+00FA ISOlat1
	    '&ucirc;'    => '&#251;',  # latin small letter u with circumflex, U+00FB ISOlat1
	    '&uuml;'     => '&#252;',  # latin small letter u with diaeresis, U+00FC ISOlat1
	    '&yacute;'   => '&#253;',  # latin small letter y with acute, U+00FD ISOlat1
	    '&thorn;'    => '&#254;',  # latin small letter thorn, U+00FE ISOlat1
	    '&yuml;'     => '&#255;',  # latin small letter y with diaeresis, U+00FF ISOlat1
	    '&fnof;'     => '&#402;',  # latin small f with hook = function = florin, U+0192 ISOtech
	    '&Alpha;'    => '&#913;',  # greek capital letter alpha, U+0391
	    '&Beta;'     => '&#914;',  # greek capital letter beta, U+0392
	    '&Gamma;'    => '&#915;',  # greek capital letter gamma, U+0393 ISOgrk3
	    '&Delta;'    => '&#916;',  # greek capital letter delta, U+0394 ISOgrk3
	    '&Epsilon;'  => '&#917;',  # greek capital letter epsilon, U+0395
	    '&Zeta;'     => '&#918;',  # greek capital letter zeta, U+0396
	    '&Eta;'      => '&#919;',  # greek capital letter eta, U+0397
	    '&Theta;'    => '&#920;',  # greek capital letter theta, U+0398 ISOgrk3
	    '&Iota;'     => '&#921;',  # greek capital letter iota, U+0399
	    '&Kappa;'    => '&#922;',  # greek capital letter kappa, U+039A
	    '&Lambda;'   => '&#923;',  # greek capital letter lambda, U+039B ISOgrk3
	    '&Mu;'       => '&#924;',  # greek capital letter mu, U+039C
	    '&Nu;'       => '&#925;',  # greek capital letter nu, U+039D
	    '&Xi;'       => '&#926;',  # greek capital letter xi, U+039E ISOgrk3
	    '&Omicron;'  => '&#927;',  # greek capital letter omicron, U+039F
	    '&Pi;'       => '&#928;',  # greek capital letter pi, U+03A0 ISOgrk3
	    '&Rho;'      => '&#929;',  # greek capital letter rho, U+03A1
	    '&Sigma;'    => '&#931;',  # greek capital letter sigma, U+03A3 ISOgrk3
	    '&Tau;'      => '&#932;',  # greek capital letter tau, U+03A4
	    '&Upsilon;'  => '&#933;',  # greek capital letter upsilon, U+03A5 ISOgrk3
	    '&Phi;'      => '&#934;',  # greek capital letter phi, U+03A6 ISOgrk3
	    '&Chi;'      => '&#935;',  # greek capital letter chi, U+03A7
	    '&Psi;'      => '&#936;',  # greek capital letter psi, U+03A8 ISOgrk3
	    '&Omega;'    => '&#937;',  # greek capital letter omega, U+03A9 ISOgrk3
	    '&alpha;'    => '&#945;',  # greek small letter alpha, U+03B1 ISOgrk3
	    '&beta;'     => '&#946;',  # greek small letter beta, U+03B2 ISOgrk3
	    '&gamma;'    => '&#947;',  # greek small letter gamma, U+03B3 ISOgrk3
	    '&delta;'    => '&#948;',  # greek small letter delta, U+03B4 ISOgrk3
	    '&epsilon;'  => '&#949;',  # greek small letter epsilon, U+03B5 ISOgrk3
	    '&zeta;'     => '&#950;',  # greek small letter zeta, U+03B6 ISOgrk3
	    '&eta;'      => '&#951;',  # greek small letter eta, U+03B7 ISOgrk3
	    '&theta;'    => '&#952;',  # greek small letter theta, U+03B8 ISOgrk3
	    '&iota;'     => '&#953;',  # greek small letter iota, U+03B9 ISOgrk3
	    '&kappa;'    => '&#954;',  # greek small letter kappa, U+03BA ISOgrk3
	    '&lambda;'   => '&#955;',  # greek small letter lambda, U+03BB ISOgrk3
	    '&mu;'       => '&#956;',  # greek small letter mu, U+03BC ISOgrk3
	    '&nu;'       => '&#957;',  # greek small letter nu, U+03BD ISOgrk3
	    '&xi;'       => '&#958;',  # greek small letter xi, U+03BE ISOgrk3
	    '&omicron;'  => '&#959;',  # greek small letter omicron, U+03BF NEW
	    '&pi;'       => '&#960;',  # greek small letter pi, U+03C0 ISOgrk3
	    '&rho;'      => '&#961;',  # greek small letter rho, U+03C1 ISOgrk3
	    '&sigmaf;'   => '&#962;',  # greek small letter final sigma, U+03C2 ISOgrk3
	    '&sigma;'    => '&#963;',  # greek small letter sigma, U+03C3 ISOgrk3
	    '&tau;'      => '&#964;',  # greek small letter tau, U+03C4 ISOgrk3
	    '&upsilon;'  => '&#965;',  # greek small letter upsilon, U+03C5 ISOgrk3
	    '&phi;'      => '&#966;',  # greek small letter phi, U+03C6 ISOgrk3
	    '&chi;'      => '&#967;',  # greek small letter chi, U+03C7 ISOgrk3
	    '&psi;'      => '&#968;',  # greek small letter psi, U+03C8 ISOgrk3
	    '&omega;'    => '&#969;',  # greek small letter omega, U+03C9 ISOgrk3
	    '&thetasym;' => '&#977;',  # greek small letter theta symbol, U+03D1 NEW
	    '&upsih;'    => '&#978;',  # greek upsilon with hook symbol, U+03D2 NEW
	    '&piv;'      => '&#982;',  # greek pi symbol, U+03D6 ISOgrk3
	    '&bull;'     => '&#8226;', # bullet = black small circle, U+2022 ISOpub
	    '&hellip;'   => '&#8230;', # horizontal ellipsis = three dot leader, U+2026 ISOpub
	    '&prime;'    => '&#8242;', # prime = minutes = feet, U+2032 ISOtech
	    '&Prime;'    => '&#8243;', # double prime = seconds = inches, U+2033 ISOtech
	    '&oline;'    => '&#8254;', # overline = spacing overscore, U+203E NEW
	    '&frasl;'    => '&#8260;', # fraction slash, U+2044 NEW
	    '&weierp;'   => '&#8472;', # script capital P = power set = Weierstrass p, U+2118 ISOamso
	    '&image;'    => '&#8465;', # blackletter capital I = imaginary part, U+2111 ISOamso
	    '&real;'     => '&#8476;', # blackletter capital R = real part symbol, U+211C ISOamso
	    '&trade;'    => '&#8482;', # trade mark sign, U+2122 ISOnum
	    '&alefsym;'  => '&#8501;', # alef symbol = first transfinite cardinal, U+2135 NEW
	    '&larr;'     => '&#8592;', # leftwards arrow, U+2190 ISOnum
	    '&uarr;'     => '&#8593;', # upwards arrow, U+2191 ISOnum
	    '&rarr;'     => '&#8594;', # rightwards arrow, U+2192 ISOnum
	    '&darr;'     => '&#8595;', # downwards arrow, U+2193 ISOnum
	    '&harr;'     => '&#8596;', # left right arrow, U+2194 ISOamsa
	    '&crarr;'    => '&#8629;', # downwards arrow with corner leftwards = carriage return, U+21B5 NEW
	    '&lArr;'     => '&#8656;', # leftwards double arrow, U+21D0 ISOtech
	    '&uArr;'     => '&#8657;', # upwards double arrow, U+21D1 ISOamsa
	    '&rArr;'     => '&#8658;', # rightwards double arrow, U+21D2 ISOtech
	    '&dArr;'     => '&#8659;', # downwards double arrow, U+21D3 ISOamsa
	    '&hArr;'     => '&#8660;', # left right double arrow, U+21D4 ISOamsa
	    '&forall;'   => '&#8704;', # for all, U+2200 ISOtech
	    '&part;'     => '&#8706;', # partial differential, U+2202 ISOtech
	    '&exist;'    => '&#8707;', # there exists, U+2203 ISOtech
	    '&empty;'    => '&#8709;', # empty set = null set = diameter, U+2205 ISOamso
	    '&nabla;'    => '&#8711;', # nabla = backward difference, U+2207 ISOtech
	    '&isin;'     => '&#8712;', # element of, U+2208 ISOtech
	    '&notin;'    => '&#8713;', # not an element of, U+2209 ISOtech
	    '&ni;'       => '&#8715;', # contains as member, U+220B ISOtech
	    '&prod;'     => '&#8719;', # n-ary product = product sign, U+220F ISOamsb
	    '&sum;'      => '&#8721;', # n-ary sumation, U+2211 ISOamsb
	    '&minus;'    => '&#8722;', # minus sign, U+2212 ISOtech
	    '&lowast;'   => '&#8727;', # asterisk operator, U+2217 ISOtech
	    '&radic;'    => '&#8730;', # square root = radical sign, U+221A ISOtech
	    '&prop;'     => '&#8733;', # proportional to, U+221D ISOtech
	    '&infin;'    => '&#8734;', # infinity, U+221E ISOtech
	    '&ang;'      => '&#8736;', # angle, U+2220 ISOamso
	    '&and;'      => '&#8743;', # logical and = wedge, U+2227 ISOtech
	    '&or;'       => '&#8744;', # logical or = vee, U+2228 ISOtech
	    '&cap;'      => '&#8745;', # intersection = cap, U+2229 ISOtech
	    '&cup;'      => '&#8746;', # union = cup, U+222A ISOtech
	    '&int;'      => '&#8747;', # integral, U+222B ISOtech
	    '&there4;'   => '&#8756;', # therefore, U+2234 ISOtech
	    '&sim;'      => '&#8764;', # tilde operator = varies with = similar to, U+223C ISOtech
	    '&cong;'     => '&#8773;', # approximately equal to, U+2245 ISOtech
	    '&asymp;'    => '&#8776;', # almost equal to = asymptotic to, U+2248 ISOamsr
	    '&ne;'       => '&#8800;', # not equal to, U+2260 ISOtech
	    '&equiv;'    => '&#8801;', # identical to, U+2261 ISOtech
	    '&le;'       => '&#8804;', # less-than or equal to, U+2264 ISOtech
	    '&ge;'       => '&#8805;', # greater-than or equal to, U+2265 ISOtech
	    '&sub;'      => '&#8834;', # subset of, U+2282 ISOtech
	    '&sup;'      => '&#8835;', # superset of, U+2283 ISOtech
	    '&nsub;'     => '&#8836;', # not a subset of, U+2284 ISOamsn
	    '&sube;'     => '&#8838;', # subset of or equal to, U+2286 ISOtech
	    '&supe;'     => '&#8839;', # superset of or equal to, U+2287 ISOtech
	    '&oplus;'    => '&#8853;', # circled plus = direct sum, U+2295 ISOamsb
	    '&otimes;'   => '&#8855;', # circled times = vector product, U+2297 ISOamsb
	    '&perp;'     => '&#8869;', # up tack = orthogonal to = perpendicular, U+22A5 ISOtech
	    '&sdot;'     => '&#8901;', # dot operator, U+22C5 ISOamsb
	    '&lceil;'    => '&#8968;', # left ceiling = apl upstile, U+2308 ISOamsc
	    '&rceil;'    => '&#8969;', # right ceiling, U+2309 ISOamsc
	    '&lfloor;'   => '&#8970;', # left floor = apl downstile, U+230A ISOamsc
	    '&rfloor;'   => '&#8971;', # right floor, U+230B ISOamsc
	    '&lang;'     => '&#9001;', # left-pointing angle bracket = bra, U+2329 ISOtech
	    '&rang;'     => '&#9002;', # right-pointing angle bracket = ket, U+232A ISOtech
	    '&loz;'      => '&#9674;', # lozenge, U+25CA ISOpub
	    '&spades;'   => '&#9824;', # black spade suit, U+2660 ISOpub
	    '&clubs;'    => '&#9827;', # black club suit = shamrock, U+2663 ISOpub
	    '&hearts;'   => '&#9829;', # black heart suit = valentine, U+2665 ISOpub
	    '&diams;'    => '&#9830;', # black diamond suit, U+2666 ISOpub
	    '&quot;'     => '&#34;',   # quotation mark = APL quote, U+0022 ISOnum
	    '&amp;'      => '&#38;',   # ampersand, U+0026 ISOnum
	    '&lt;'       => '&#60;',   # less-than sign, U+003C ISOnum
	    '&gt;'       => '&#62;',   # greater-than sign, U+003E ISOnum
	    '&OElig;'    => '&#338;',  # latin capital ligature OE, U+0152 ISOlat2
	    '&oelig;'    => '&#339;',  # latin small ligature oe, U+0153 ISOlat2
	    '&Scaron;'   => '&#352;',  # latin capital letter S with caron, U+0160 ISOlat2
	    '&scaron;'   => '&#353;',  # latin small letter s with caron, U+0161 ISOlat2
	    '&Yuml;'     => '&#376;',  # latin capital letter Y with diaeresis, U+0178 ISOlat2
	    '&circ;'     => '&#710;',  # modifier letter circumflex accent, U+02C6 ISOpub
	    '&tilde;'    => '&#732;',  # small tilde, U+02DC ISOdia
	    '&ensp;'     => '&#8194;', # en space, U+2002 ISOpub
	    '&emsp;'     => '&#8195;', # em space, U+2003 ISOpub
	    '&thinsp;'   => '&#8201;', # thin space, U+2009 ISOpub
	    '&zwnj;'     => '&#8204;', # zero width non-joiner, U+200C NEW RFC 2070
	    '&zwj;'      => '&#8205;', # zero width joiner, U+200D NEW RFC 2070
	    '&lrm;'      => '&#8206;', # left-to-right mark, U+200E NEW RFC 2070
	    '&rlm;'      => '&#8207;', # right-to-left mark, U+200F NEW RFC 2070
	    '&ndash;'    => '&#8211;', # en dash, U+2013 ISOpub
	    '&mdash;'    => '&#8212;', # em dash, U+2014 ISOpub
	    '&lsquo;'    => '&#8216;', # left single quotation mark, U+2018 ISOnum
	    '&rsquo;'    => '&#8217;', # right single quotation mark, U+2019 ISOnum
	    '&sbquo;'    => '&#8218;', # single low-9 quotation mark, U+201A NEW
	    '&ldquo;'    => '&#8220;', # left double quotation mark, U+201C ISOnum
	    '&rdquo;'    => '&#8221;', # right double quotation mark, U+201D ISOnum
	    '&bdquo;'    => '&#8222;', # double low-9 quotation mark, U+201E NEW
	    '&dagger;'   => '&#8224;', # dagger, U+2020 ISOpub
	    '&Dagger;'   => '&#8225;', # double dagger, U+2021 ISOpub
	    '&permil;'   => '&#8240;', # per mille sign, U+2030 ISOtech
	    '&lsaquo;'   => '&#8249;', # single left-pointing angle quotation mark, U+2039 ISO proposed
	    '&rsaquo;'   => '&#8250;', # single right-pointing angle quotation mark, U+203A ISO proposed
	    '&euro;'     => '&#8364;', # euro sign, U+20AC NEW
	);
	if($reverse){
           return strtr($string,  array_flip($HTML401NamedToNumeric));
        }else{
	   return strtr($string, $HTML401NamedToNumeric);
        }
    }

   /**
    * Search an array for a value
    **/
   function arraySearch($array, $key, $value)
   {
      foreach($array as $item)
      {
         if ( $item[$key] === $value ){
            return $item;
         }
      }
      return false;
   }



    /*
      function after ($this, $inthat)
      {
      if (!is_bool(strpos($inthat, $this))){
      return substr($inthat, strpos($inthat,$this)+strlen($this));
      };

      function after_last ($this, $inthat)
      {
      if (!is_bool(strrevpos($inthat, $this))){
      return substr($inthat, strrevpos($inthat, $this)+strlen($this));
      };

      function before ($this, $inthat)
      {
      return substr($inthat, 0, strpos($inthat, $this));
      };

      function before_last ($this, $inthat)
      {
      return substr($inthat, 0, strrevpos($inthat, $this));
      };

      function between ($this, $that, $inthat)
      {
      return before ($that, after($this, $inthat));
      };

      function between_last ($this, $that, $inthat)
      {
      return after_last($this, before_last($that, $inthat));
      };

      // use strrevpos function in case your php version does not include it
      function strrevpos($instr, $needle)
      {
      $rev_pos = strpos (strrev($instr), strrev($needle));
      if ($rev_pos===false) return false;
      else return strlen($instr) - $rev_pos - strlen($needle);
      }
      };
     */
}

