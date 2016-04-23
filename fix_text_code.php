<?php
include 'functions.php';

$emoji_unified_to_text_code = include 'generate/include/emoji_unified_to_text_code.php';
$emoji_text_code_to_unified = include 'generate/include/emoji_text_code_to_unified.php';


foreach($emoji_unified_to_text_code as $key => $value){
    if (!isset($emoji_text_code_to_unified[$value])) {
        $emoji_text_code_to_unified[$value] = $key;
    }
    if ($key != $emoji_text_code_to_unified[$value]){
        unset($emoji_unified_to_text_code[$key]);
        unset($emoji_text_code_to_unified[$value]);
    }
}
foreach($emoji_text_code_to_unified as $key => $value){
    if (!isset($emoji_unified_to_text_code[$value])) {
        $emoji_unified_to_text_code[$value] = $key;
    }
    if ($key != $emoji_unified_to_text_code[$value]){
        unset($emoji_text_code_to_unified[$key]);
        unset($emoji_unified_to_text_code[$value]);
    }
}
$emoji_unified_to_text_code = array_unique($emoji_unified_to_text_code);
$emoji_text_code_to_unified = array_unique($emoji_text_code_to_unified);
echo count($emoji_unified_to_text_code);
echo "\n";
echo count($emoji_text_code_to_unified);
generatrFile($emoji_text_code_to_unified, 'text_code_to_unified');
generatrFile($emoji_unified_to_text_code, 'unified_to_text_code');
