<?php
$en_names = include 'generate/include/emoji_en_names.php';

$unified_to_category = include 'generate/include/emoji_unified_to_category.php';

$unified_to_image_code = include 'generate/include/emoji_unified_to_image_code.php';

$unified_to_text_code = include 'generate/include/emoji_unified_to_text_code.php';

$category_to_unified = array();

$category_names = array(
    'People' => '表情符号和人物',
    'Objects' => '物体',
    'Activity' => '活动',
    'Nature'  => '动物和自然',
    'other'   => '其他',
    'Symbols' => '符号',
    'Foods' => '食物和饮料',
    'Places' => '旅行和地点',
    'Flags' => '旗帜',
);
foreach($en_names as $unified => $name){
    $category = 'other';
    if (!empty($unified_to_category[$unified])) {
        $category = $unified_to_category[$unified];
    }

    if (!isset($category_to_unified[$category])) {
        $category_to_unified[$category] = array(
            'name'  => $category_names[$category],
            'list' => array(),
        );
    }
    
    $image_code = isset($unified_to_image_code[$unified]) ? $unified_to_image_code[$unified] : '';
    $text_code = isset($unified_to_text_code[$unified]) ? $unified_to_text_code[$unified] : '';
    $category_to_unified[$category]['list'][] = array(
        'unified' => $unified,
        'image_code' => $image_code,
        'text_code'  => $text_code,
        'name'  => $name,
    );
}

$file_name = 'generate/include/emoji_category_to_unified.php';
$line = '';
$line .= "<"."?php\n";                                                                                                  
$line .=  "\n";                                                                                                          
$line .=  "#\n";                                                                                                       
$line .=  "# WARNING:\n";                                                                                              
$line .=  "# This code is auto-generated. Do not modify it manually.\n";                                               
$line .=  "#\n";                                                                                                       
$line .=  "\n";                                                                                                          

$line .= "return array(\n";                                                                        
foreach($category_to_unified as $key => $value) {
    $line .= '    \'' . $key . '\'' . ' => array(';
    $line .=  "\n";                                                                                                          
    $line .= '        \'name\'' . ' => \'' . $value['name'] . '\',';
    $line .=  "\n";                                                                                                          
    $line .= '        \'list\'' . ' => array(';
    $line .=  "\n";                                                                                                          

    foreach($value['list'] as $k => $v) {
        $line .= '            array(';
        $line .=  "\n";                                                                                                          
        foreach($v as $n => $c){
            $line .= '                \''. $n. '\'' . ' => \'' . $c . '\',';
            $line .=  "\n";                                                                                                          
        }
        //       $line .=  "\n";                                                                                                          
        $line .= '            ),';
        $line .=  "\n";                                                                                                          
    }
    $line .= '        ),';
   // . '\'' . $value . '\',';
    $line .=  "\n";                                                                                                          
    $line .= '    ),';
    $line .=  "\n";                                                                                                          
}
$line .= ');';
file_put_contents($file_name,$line);
