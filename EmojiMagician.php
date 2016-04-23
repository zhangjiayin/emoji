<?php

class EmojiMagician {
        
    public static $maps = array();

    public static function getConfig($name) {
        if (!isset($maps[$name])){
            self::$maps[$name] = include(__DIR__ . '/include/' . $name . '.php');
        }
    }
    
    private static function convert($name, $text){
        self::getConfig($name);
		return str_replace(array_keys(self::$maps[$name]), self::$maps[$name], $text);
    }
    public static function GoogleToUnified($text) {
        return self::convert('emoji_google_to_unified', $text);
    }

    public static function SoftbankToUnified($text) {
        return self::convert('emoji_softbank_to_unified', $text);
    }

    public static function UnifiedToImageCode($text) {
        return self::convert('emoji_unified_to_image_code', $text);
    }

    public static function UnifiedToTextCode($text) {
        return self::convert('emoji_unified_to_text_code', $text);
    }

    public static function TextCodeToUnified($text) {
        return self::convert('emoji_text_code_to_unified', $text);
    }

    public static function ImageCodeToUnified($text) {
        return self::convert('emoji_image_code_to_unified', $text);
    }
}
