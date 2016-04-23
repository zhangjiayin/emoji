<?php
include 'functions.php';
if (!is_dir('generate')) {
    mkdir('generate');
}   
if (!is_dir('generate/include')) {
    mkdir('generate/include');
}   

$names_to_unified = array();
$collection_names = array(
   '动物', '動物', '信息', '商務', '商务', '交通', '服裝', '服装', '表情', '時間', '时间', '食物', '符号', '符號', '星座', '心情', '庆祝', '慶祝', '頭像', '头像', '天氣', '天气', '运动', '運動', '手勢', '手势', '浪漫', '水果', '植物', '標誌', '标志', '天文', '工具', '音乐', '音樂', '自然', '活動', '活动', '建築', '建筑',);
$data = file_get_contents('emoji/emoji.json');

$data = json_decode($data,true);
foreach($data as $key => $value) {
    $hex = utf8_bytes_to_hex($key);
    $hex = strtoupper($hex);
    $value = array_reverse($value);
    foreach($value as $v){

        if (is_cn_traditional($v)) { 
            continue;
        }
        if (empty($names_to_unified[$v])) {
            $names_to_unified[$v] = array();
        }
        $names_to_unified[$v][$hex] = array(
            'hex' => $hex,
            'value' => $key,
        );
    }
}

/**
foreach($names_to_unified as $key => $value) {
 //   if(count($value) > 1){
 //       unset($names_to_unified[$key]);
 //   } else {
        $names_to_unified[$key] = $value;
 //   }
}
 */
$names_to_unified_line = '';
$names_to_unified_line .= "<"."?php\n";                                                                                                  
$names_to_unified_line .=  "\n";                                                                                                          
$names_to_unified_line .=  "#\n";                                                                                                       
$names_to_unified_line .=  "# WARNING:\n";                                                                                              
$names_to_unified_line .=  "# This code is auto-generated. Do not modify it manually.\n";                                               
$names_to_unified_line .=  "#\n";                                                                                                       
$names_to_unified_line .=  "\n";                                                                                                          

$names_to_unified_line .= "return array(\n";                                                                        
$line_pre = $names_to_unified_line;
$unified_to_text_code_line = $names_to_unified_line;
$unified_to_text_code_keys = array();
foreach($names_to_unified as $key => $value) {
    if(in_array($key,$collection_names))continue;
    if (is_cn_traditional($key)) {
        continue;
    }

    $value = array_values($value);
    foreach($value as $k => $v ){
        if (!in_array($v['value'],$unified_to_text_code_keys) && !is_cn_traditional($v['value'])){
            $names_to_unified_line .= '    \'[emoji:' . $key  . ']\' => \'' . $v['value'] .  '\',';
            $unified_to_text_code_line .= '    \'' . $v['value'] .  '\'' .  '=>' .  '\'[emoji:' . $key  . ']\',' ; 
            $unified_to_text_code_keys[] = $v['value'];
            $unified_to_text_code_line .= "\n";
            break;
        }
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

$names_to_unified = include 'generate/include/emoji_text_code_to_unified.php';
$unified_to_names = include 'generate/include/emoji_unified_to_text_code.php';

$content = file_get_contents('zh.py');

preg_match_all('#EmojiAnnotations\(emoji=\'(.*?)\'.*?name=\'(.*?)\'#', $content,$matches);
foreach($matches[1] as $key => $value) {
    $unified = $value;
    $text = $matches[2][$key];
    $names_to_unified['[emoji:' .  $text . ']'] = $unified;
    $unified_to_names[$unified] = '[emoji:' .  $text . ']';
}


$names_to_unified_line = $line_pre;

foreach($names_to_unified as $key => $value) {
    $names_to_unified_line .= '    \'' . $key . '\' => \'' . $value . "',\n";
}

$names_to_unified_line .= ');' . "\n";

$unified_to_text_code_line = $line_pre;

foreach($unified_to_names as $key => $value) {
    $unified_to_text_code_line .= '    \'' . $key . '\' => \'' . $value . "',\n";
}

$unified_to_text_code_line .= ');' . "\n";
//echo $names_to_unified_line;
file_put_contents('generate/include/emoji_text_code_to_unified.php', $names_to_unified_line);
file_put_contents('generate/include/emoji_unified_to_text_code.php', $unified_to_text_code_line);


