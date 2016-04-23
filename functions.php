<?php

function unicode_hex_chars($str){                                                                                   
    $out = '';                                                                                                      
    $cps = explode('-', $str);                                                                                      
    foreach ($cps as $cp){                                                                                          
        $out .= sprintf('%x', hexdec($cp));                                                                         
    }                                                                                                               
    return $out;                                                                                                    
}

function extratImg($string, $name,$type) {
    if(!is_dir('official/')) {
        mkdir('official/');
    }

    if(!is_dir('official/'.$type)) {
        mkdir('official/' . $type);
    }
    $string = trim($string);
    if($string == 'missing') {
        return;
    }
    preg_match('#src="data:image/png;base64,(.*?)"#',$string, $matches);
    if (!empty($matches)) {
        file_put_contents('official/'. $type . '/' . strtolower($name) . '.png', base64_decode($matches[1]));
    }
}

function generatrFile($var, $name) {
    $file_name = 'generate/include/emoji_' . $name . '.php';

    $line = '';
    $line .= "<"."?php\n";                                                                                                  
    $line .=  "\n";                                                                                                          
    $line .=  "#\n";                                                                                                       
    $line .=  "# WARNING:\n";                                                                                              
    $line .=  "# This code is auto-generated. Do not modify it manually.\n";                                               
    $line .=  "#\n";                                                                                                       
    $line .=  "\n";                                                                                                          

    $line .= "return array(\n";                                                                        
    foreach($var as $key => $value) {
        $line .= '    \'' . $key . '\'' . ' => ' . '\'' . $value . '\',';
        $line .=  "\n";                                                                                                          
    }
    $line .= ');';
    file_put_contents($file_name,$line);

}

##########################################################################################

function get_all_kaomoji($mapping){
    $arr = array();

    foreach ($mapping as $map){
        if (isset($map['docomo']['kaomoji']) ) {
            $arr[ $map['docomo']['kaomoji'] ] = '1';
        }

        if (isset($map['au']['kaomoji']) ) {
            $arr[ $map['au']['kaomoji'] ] = '1';
        }

        if (isset($map['softbank']['kaomoji']) ) {
            $arr[ $map['softbank']['kaomoji'] ] = '1';
        }
    }

    return array_keys($arr);
}

function make_names_map($map){

    $out = array();
    foreach ($map as $row){

        $bytes = unicode_bytes($row['unified']);

        $out[$bytes] = $row['name'];
    }

    return $out;
}

function make_html_map($map){

    $out = array();
    foreach ($map as $row){

        $hex = unicode_hex_chars($row['unified']);
        $bytes = unicode_bytes($row['unified']);

        $out[$bytes] = "<span class=\"emoji-outer emoji-sizer\"><span class=\"emoji-inner emoji$hex\"></span></span>";
    }

    return $out;
}


function make_mapping($mapping, $dest){

    $result = array();

    foreach ($mapping as $map){

        $src_char = unicode_bytes($map['unified']);
        if ($dest == "image_code"){
            $dest_char = '[emoji-img:' . $map['image'] . ']';
        } else if (!empty($map[$dest])){
            if (in_array($dest, array('image','category'))) {
                $dest_char = $map[$dest];
            } else {
                $dest_char = unicode_bytes($map[$dest]);
            }
        }else{
            $dest_char = '';
        }

        $result[$src_char] = $dest_char;
    }

    return $result;
}

function make_mapping_flip($mapping, $src){
    $result = make_mapping($mapping, $src);
    $result = array_flip($result);
    unset($result[""]);
    return $result;
}

function unicode_bytes($str){

    $out = '';

    $cps = explode('-', $str);
    foreach ($cps as $cp){
        $out .= emoji_utf8_bytes(hexdec($cp));
    }

    return $out;
}

function emoji_utf8_bytes($cp){

    if ($cp > 0x10000){
        # 4 bytes
        return	chr(0xF0 | (($cp & 0x1C0000) >> 18)).
            chr(0x80 | (($cp & 0x3F000) >> 12)).
            chr(0x80 | (($cp & 0xFC0) >> 6)).
            chr(0x80 | ($cp & 0x3F));
    }else if ($cp > 0x800){
        # 3 bytes
        return	chr(0xE0 | (($cp & 0xF000) >> 12)).
            chr(0x80 | (($cp & 0xFC0) >> 6)).
            chr(0x80 | ($cp & 0x3F));
    }else if ($cp > 0x80){
        # 2 bytes
        return	chr(0xC0 | (($cp & 0x7C0) >> 6)).
            chr(0x80 | ($cp & 0x3F));
    }else{
        # 1 byte
        return chr($cp);
    }
}

function format_string($s){
    $out = ''; 
    for ($i=0; $i<strlen($s); $i++){
        $c = ord(substr($s,$i,1));
        if ($c >= 0x20 && $c < 0x80 && !in_array($c, array(34, 39, 92))){
            $out .= chr($c);
        }else{
            $out .= sprintf('\\x%02x', $c);
        }   
    }   
    return '"'.$out.'"';
}   

function encode_points($points){                                                                                    
    $bits = array();                                                                                                
    if (is_array($points)){                                                                                         
        foreach ($points as $p){                                                                                    
            $bits[] = sprintf('%04X', $p);                                                                          
        }                                                                                                           
    }                                                                                                               
    if (!count($bits)) return null;                                                                                 
    return implode('-', $bits);                                                                                     
}                                                                                                                   

function utf8_bytes_to_uni_hex($utf8_bytes){                                                                        

    $bytes = array();                                                                                               

    foreach (str_split($utf8_bytes) as $ch){                                                                        
        $bytes[] = ord($ch);                                                                                        
    }                                                                                                               

    $codepoint = 0;                                                                                                 
    if (count($bytes) == 1) $codepoint = $bytes[0];                                                                 
    if (count($bytes) == 2) $codepoint = (($bytes[0] & 0x1F) << 6) | ($bytes[1] & 0x3F);                            
    if (count($bytes) == 3) $codepoint = (($bytes[0] & 0x0F) << 12) | (($bytes[1] & 0x3F) << 6) | ($bytes[2] & 0x3F);
    if (count($bytes) == 4) $codepoint = (($bytes[0] & 0x07) << 18) | (($bytes[1] & 0x3F) << 12) | (($bytes[2] & 0x3F) << 6) | ($bytes[3] & 0x3F);
    if (count($bytes) == 5) $codepoint = (($bytes[0] & 0x03) << 24) | (($bytes[1] & 0x3F) << 18) | (($bytes[2] & 0x3F) << 12) | (($bytes[3] & 0x3F) << 6) | ($bytes[4] & 0x3F);
    if (count($bytes) == 6) $codepoint = (($bytes[0] & 0x01) << 30) | (($bytes[1] & 0x3F) << 24) | (($bytes[2] & 0x3F) << 18) | (($bytes[3] & 0x3F) << 12) | (($bytes[4] & 0x3F) << 6) | ($bytes[5]     & 0x3F);

    $str = sprintf('%x', $codepoint);                                                                               
    return str_pad($str, 4, '0', STR_PAD_LEFT);                                                                     
}                                                                                                                   

function utf8_bytes_to_hex($str){                                                                                   
    mb_internal_encoding('UTF-8');                                                                                  
    $out = array();                                                                                                 
    while (strlen($str)){                                                                                           
        $out[] = utf8_bytes_to_uni_hex(mb_substr($str, 0, 1));                                                      
        $str = mb_substr($str, 1);                                                                                  
    }                                                                                                               
    return implode('-', $out);                                                                                      
}

function is_cn_traditional($str) {
    $strGbk = iconv("UTF-8", "GBK//IGNORE", $str);
    $strGb2312 = iconv("UTF-8", "GB2312//IGNORE", $str);
    if ($strGbk == $strGb2312) {
        return false;
    } else {
        return true;
    }
}
