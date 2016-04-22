<?php
if (!is_dir('generate')) {
    mkdir('generate');
}   
if (!is_dir('generate/include')) {
    mkdir('generate/include');
}   

$names_to_unified = array();
$collection_names = array(
   '动物',
   '動物',
   '信息',
   '商務',
   '商务',
   '交通',
   '服裝',
   '服装',
   '表情',
   '時間',
   '时间',
   '食物',
   '符号',
   '符號',
   '星座',
   '心情',
   '庆祝',
   '慶祝',
   '頭像',
   '头像',
   '天氣',
   '天气',
   '运动',
   '運動',
   '手勢',
   '手势',
   '浪漫',
   '水果',
   '植物',
   '標誌',
   '标志',
   '天文',
   '工具',
   '音乐',
   '音樂',
   '自然',
   '一家四口',
   '活動',
   '活动',
   '建築',
   '建筑',
);
$data = file_get_contents('emoji/emoji.json');
$data = json_decode($data,true);
foreach($data as $key => $value) {
    $hex = utf8_bytes_to_hex($key);
    $hex = strtoupper($hex);
    foreach($value as $v){
        if (empty($names_to_unified[$v])) {
            $names_to_unified[$v] = array();
        }
        $names_to_unified[$v][$hex] = array(
            'hex' => $hex,
            'value' => $key,
        );
    }
}


foreach($names_to_unified as $key => $value) {
 //   if(count($value) > 1){
 //       unset($names_to_unified[$key]);
 //   } else {
        $names_to_unified[$key] = $value;
 //   }
}
$names_to_unified_line = '';
$names_to_unified_line .= "<"."?php\n";                                                                                                  
$names_to_unified_line .=  "\n";                                                                                                          
$names_to_unified_line .=  "#\n";                                                                                                       
$names_to_unified_line .=  "# WARNING:\n";                                                                                              
$names_to_unified_line .=  "# This code is auto-generated. Do not modify it manually.\n";                                               
$names_to_unified_line .=  "#\n";                                                                                                       
$names_to_unified_line .=  "\n";                                                                                                          

$names_to_unified_line .= "return array(\n";                                                                        

$unified_to_text_code_line = $names_to_unified_line;
$unified_to_text_code_keys = array();
foreach($names_to_unified as $key => $value) {
    if(in_array($key,$collection_names))continue;
    if (is_cn_traditional($key)) {
        continue;
    }

    $value = array_values($value);
    $names_to_unified_line .= '    \'[emoji:' . $key  . ']\' => \'' . $value[0]['value'] .  '\',';
    if (!in_array($value[0]['value'],$unified_to_text_code_keys)){
        $unified_to_text_code_line .= '    \'' . $value[0]['value'] .  '\'' .  '=>' .  '\'[emoji:' . $key  . ']\',' ; 
        $unified_to_text_code_keys[] = $value[0]['value'];
        $unified_to_text_code_line .= "\n";
    }


    /**
    $names_to_unified_line .= '    "' . $key  . '" => array('  ;
    $names_to_unified_line .= "\n";
    $index = 0;
    foreach($value as $v) {
        //$names_to_unified_line .= '        array(\'hex\' => "' . $v['hex'] . '",\'value\' => \'' . $v['value'] . '\'),';
        //$names_to_unified_line .= "\n";
        if ($index == 0){
            $names_to_unified_line .= '        \'hex\' => "' . $v['hex'] . '",\'value\' => \'' . $v['value'] . '\',';
            $names_to_unified_line .= "\n";
        } else {
            $names_to_unified_line .= '//        array(\'hex\' => "' . $v['hex'] . '",\'value\' => \'' . $v['value'] . '\'),';
            $names_to_unified_line .= "\n";
        }
        $index++;
    }

    $names_to_unified_line .= '    ),';
    */
    $names_to_unified_line .= "\n";
}
$names_to_unified_line .= ");\n";                                                                                                      
$unified_to_text_code_line .= ");\n";                                                                                                      
file_put_contents('generate/include/emoji_text_code_to_unified.php', $names_to_unified_line);
file_put_contents('generate/include/emoji_unified_to_text_code.php', $unified_to_text_code_line);


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
