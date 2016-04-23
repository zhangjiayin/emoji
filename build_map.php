<?php
include 'functions.php';
$in = file_get_contents('emoji-data/emoji_pretty.json');
$catalog = json_decode($in, true);

if (!is_dir('generate')) {
    mkdir('generate');
}   
if (!is_dir('generate/include')) {
    mkdir('generate/include');
}   

#
# build the final maps
#

$maps = array();

$maps['names']		= make_names_map($catalog);
$maps['kaomoji']	= get_all_kaomoji($catalog);

#fprintf(STDERR, "fix Geta Mark ()  '〓' (U+3013)\n");
#$catalog = fix_geta_mark($catalog);

//$maps["unified_to_image"]	= make_mapping($catalog, 'image');
$maps["unified_to_image_code"]	= make_mapping($catalog, 'image_code');
$maps["image_code_to_unified"]	= array_flip($maps['unified_to_image_code']);
$maps["unified_to_docomo"]	= make_mapping($catalog, 'docomo');
$maps["unified_to_kddi"]	= make_mapping($catalog, 'au');
$maps["unified_to_softbank"]	= make_mapping($catalog, 'softbank');
$maps["unified_to_google"]	= make_mapping($catalog, 'google');
$maps["unified_to_category"]	= make_mapping($catalog, 'category');

$maps["docomo_to_unified"]	= make_mapping_flip($catalog, 'docomo');
$maps["kddi_to_unified"]	= make_mapping_flip($catalog, 'au');
$maps["softbank_to_unified"]	= make_mapping_flip($catalog, 'softbank');
$maps["google_to_unified"]	= make_mapping_flip($catalog, 'google');

$maps["unified_to_html"]	= make_html_map($catalog);


#
# output
# we could just use var_dump, but we get 'better' output this way
#

$line = "<"."?php\n";

$line .=  "\n";
$line .=  "#\n";
$line .=  "# WARNING:\n";
$line .=  "# This code is auto-generated. Do not modify it manually.\n";
$line .=  "#\n";
$line .=  "\n";

$line .=  "return array(\n";

$en_names_lines  = $line;
foreach ($maps['names'] as $k => $v){

    $key_enc = format_string($k);
    $name_enc = "'".AddSlashes($v)."'";
    $en_names_lines .= '    ' . "$key_enc => $name_enc,\n";

}

$en_names_lines .=  ");\n";

file_put_contents('generate/include/emoji_en_names.php', $en_names_lines);

foreach ($maps as $k => $v){
    if (!isset(${$k . 'line'})){
        ${$k . '_line'} = $line;
    }
    if ($k == 'names') continue;

    //echo "\t\t'$k' => array(\n";

    foreach ($v as $k2 => $v2){
        ${$k . '_line'} .=  "    ".format_string($k2).'=>'.format_string($v2).",\n";
    }

    ${$k . '_line'} .=  ");";
    file_put_contents('generate/include/emoji_' . $k . '.php',  ${$k . '_line'});
}

$class = file_get_contents('EmojiMagician.php');
file_put_contents('generate/EmojiMagician.php', $class);
