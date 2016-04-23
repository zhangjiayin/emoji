<?php

if (!is_dir('generate')) {
    mkdir('generate');
}   
if (!is_dir('generate/include')) {
    mkdir('generate/include');
}   


$content = file_get_contents('full_emoji_list.html');

$pattern = '#';
//index
$pattern .= '<tr><td class="rchars">(\d+)</td>';
//code
$pattern .= '.*?<td class="code"><a href=".*?" name=".*?">(.*?)</a></td>';
//chars
$pattern .= '.*?<td class="chars">(.*?)</td>';
//apple image
$pattern .= '.*?<td.*?>(.*?)</td>';
$pattern .= '.*?<td.*?>(.*?)</td>';
$pattern .= '.*?<td.*?>(.*?)</td>';
$pattern .= '.*?<td.*?>(.*?)</td>';
$pattern .= '.*?<td.*?>(.*?)</td>';
$pattern .= '.*?<td.*?>(.*?)</td>';
$pattern .= '.*?<td.*?>(.*?)</td>';
$pattern .= '.*?<td.*?>(.*?)</td>';
$pattern .= '.*?<td.*?>(.*?)</td>';
$pattern .= '.*?<td.*?>(.*?)</td>';
$pattern .= '.*?<td.*?>(.*?)</td>';
$pattern .= '.*?<td.*?>(.*?)</td>';
$pattern .= '.*?<td.*?>(.*?)</td>';
$pattern .= '.*?<td.*?>(.*?)</td>';
$pattern .= '.*?<td.*?>(.*?)</td>';
$pattern .= '#sm';
preg_match_all($pattern, $content, $matches);

$rows = array();

$en_names = include 'generate/include/emoji_en_names.php';
$new_en_names = array();
$unified_to_image_code = include 'generate/include/emoji_unified_to_image_code.php';
$new_unified_to_image_code = array();

$image_code_to_unified = include 'generate/include/emoji_image_code_to_unified.php';
$new_image_code_to_unified = array();

foreach($matches[1] as $key => $value){
    $row = array();
    $row['no']    = $matches[1][$key];
    $row['code']  = $matches[2][$key];
    $row['unified'] = trim($row['code'], 'U+');
    $row['unified'] = preg_replace('#\s*U\+#','-',$row['unified']);
    $row['brow']  = $matches[3][$key];
    $row['chart'] = $matches[4][$key];
    extratImg($row['chart'],$row['unified'],'chart');
    $row['apple'] = $matches[5][$key];
    extratImg($row['apple'],$row['unified'],'apple');
    $row['twtr']  = $matches[6][$key];
    extratImg($row['twtr'],$row['unified'],'twtr');
    $row['one']   = $matches[7][$key];
    extratImg($row['one'],$row['unified'],'one');
    $row['google']  = $matches[8][$key];
    extratImg($row['google'],$row['unified'],'google');
    $row['sams']  = $matches[9][$key];
    extratImg($row['sams'],$row['unified'],'sams');
    $row['wind']  = $matches[10][$key];
    extratImg($row['wind'],$row['unified'],'wind');
    //$row['gmail'] = $matches[11][$key];
    //extratImg($row['gmail'],$row['unified'],'gmail');
    //$row['sb']  = $matches[12][$key];
    //extratImg($row['sb'],$row['unified'],'sb');
    //$row['dcm']  = $matches[13][$key];
    //extratImg($row['dcm'],$row['unified'],'dcm');
    //$row['kddi']  = $matches[14][$key];
    //extratImg($row['kddi'],$row['unified'],'kddi');
    $row['name']  = $matches[15][$key];
    $row['year']  = $matches[16][$key];
    $row['default']  = $matches[17][$key];
    $row['annotations'] = $matches[18][$key];
    $row['annotations'] = preg_replace('#<.*?>#','',$row['annotations']);
    preg_match('#<img alt="(.*?)"#',$row['apple'],$mat);
    extratImg($row['apple'],$row['unified'],'global');
    if(empty($mat[1])) {
        extratImg($row['google'],$row['unified'],'global');
        preg_match('#<img alt="(.*?)"#',$row['google'],$mat);
    }

    $row['name'] = strip_tags($row['name']);
    //fix en names
    if(!isset($en_names[$mat[1]]) || $en_names[$mat[1]] != $row['name']){
        $new_en_names[$mat[1]]    = html_entity_decode($row['name']);
    }  else {
        $new_en_names[$mat[1]]    = $en_names[$mat[1]];
    }
    
    $image_code = '[emoji-img:' . strtolower($row['unified'])  . '.png' . ']';

    if(!isset($image_code_to_unified[$image_code]) || $image_code_to_unified[$image_code] != $mat[1]){
       $new_image_code_to_unified[$image_code] = $mat[1];
   } else {
       $new_image_code_to_unified[$image_code] = $image_code_to_unified[$image_code];
    } 

    if(!isset($unified_to_image_code[$mat[1]]) || $unified_to_image_code[$mat[1]] != $image_code){
        //$unified_to_image_code[$mat[1]]    = $image_code;
        $new_unified_to_image_code[$mat[1]] = $image_code;
    } else {
        $new_unified_to_image_code[$mat[1]] = $unified_to_image_code[$mat[1]];
    } 
    

    $rows[$mat[1]] = $row;
}

generatrFile($new_en_names, 'en_names');
generatrFile($new_unified_to_image_code, 'unified_to_image_code');
generatrFile($new_image_code_to_unified, 'image_code_to_unified');
                                                                                                                     
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
