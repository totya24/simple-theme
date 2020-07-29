<?php

Class Greedo
{
    public static $icon_collapse = 'iVBORw0KGgoAAAANSUhEUgAAAAkAAAAJCAYAAADgkQYQAAAALklEQVQoU2NkYGD4z0AAMIIU/f+PWx0jIyMDDRSBjEUHIGfQyDpcoQC3jlA4AQDdqyEBiiK/0AAAAABJRU5ErkJggg==';
    public static $icon_expand = 'iVBORw0KGgoAAAANSUhEUgAAAAkAAAAJCAYAAADgkQYQAAAAOElEQVQoU2NkYGD4z0AAMIIU/f+PWx0jIyMDhiKQILIm0hWBdKADkImkmwQzhSg3oVsJt45QOAEAglMtAZl8LS8AAAAASUVORK5CYII=';
    private static $hasArray = false;
	
    protected static function mbInternalEncoding($encoding = null)
    {
        if (function_exists('mb_internal_encoding')) {
            return $encoding ? mb_internal_encoding($encoding) : mb_internal_encoding();
        }
        // @codeCoverageIgnoreStart
        return 'UTF-8';
        // @codeCoverageIgnoreEnd
    }
	
    public static function htmlentities($string, $preserve_encoded_entities = false)
    {
        if ($preserve_encoded_entities) {
            // @codeCoverageIgnoreStart
            if (defined('HHVM_VERSION')) {
                $translation_table = get_html_translation_table(HTML_ENTITIES, ENT_QUOTES);
            } else {
                $translation_table = get_html_translation_table(HTML_ENTITIES, ENT_QUOTES, self::mbInternalEncoding());
            }
            // @codeCoverageIgnoreEnd
            $translation_table[chr(38)] = '&';
            return preg_replace('/&(?![A-Za-z]{0,4}\w{2,3};|#[0-9]{2,3};)/', '&amp;', strtr($string, $translation_table));
        }
        return htmlentities($string, ENT_QUOTES, self::mbInternalEncoding());
    }
	
    public static function var_dump($var, $return = false, $expandLevel = 1, $stripStrings = false)
    {
        self::$hasArray = false;
        $toggScript = 'var colToggle = function(toggID) {var img = document.getElementById(toggID);if (document.getElementById(toggID + "-collapsable").style.display == "none") {document.getElementById(toggID + "-collapsable").style.display = "inline";setImg(toggID, 0);var previousSibling = document.getElementById(toggID + "-collapsable").previousSibling;while (previousSibling != null && (previousSibling.nodeType != 1 || previousSibling.tagName.toLowerCase() != "br")) {previousSibling = previousSibling.previousSibling;}} else {document.getElementById(toggID + "-collapsable").style.display = "none";setImg(toggID, 1);var previousSibling = document.getElementById(toggID + "-collapsable").previousSibling; while (previousSibling != null && (previousSibling.nodeType != 1 || previousSibling.tagName.toLowerCase() != "br")) {previousSibling = previousSibling.previousSibling;}}};';
        $imgScript = 'var setImg = function(objID,imgID,addStyle) {var imgStore = ["data:image/png;base64,' . self::$icon_collapse . '", "data:image/png;base64,' . self::$icon_expand . '"];if (objID) {document.getElementById(objID).setAttribute("src", imgStore[imgID]);if (addStyle){document.getElementById(objID).setAttribute("style", "position:relative;left:-5px;top:-1px;cursor:pointer;");}}};';
        $jsCode = preg_replace('/ +/', ' ', '<script>' . $toggScript . $imgScript . '</script>');
        $html = '<pre style="margin-bottom: 18px;' .
            'background: #efefef;' .
            'border: 1px solid #333;' .
            'padding: 8px;' .
            'display: block;' .
            'font-size: 13px;' .
            'white-space: pre-wrap;' .
            'word-wrap: break-word;' .
            'color: #333;' .
            'font-family: \'Fira Code\',Monaco,Consolas,\'Courier New\',monospace;">';
        $done  = array();
        $html .= self::recursiveVarDumpHelper($var, intval($expandLevel), 0, $done, $stripStrings);
        $html .= '</pre>';
        if (self::$hasArray) {
            $html = $jsCode . $html;
        }
        if (!$return) {
            echo $html;
        }
        return $html;
    }
    /**
     * Display a variable's contents using nice HTML formatting (Without
     * the <pre> tag) and will properly display the values of variables
     * like booleans and resources. Supports collapsable arrays and objects
     * as well.
     *
     * @param  mixed $var The variable to dump
     * @return string
     */
    protected static function recursiveVarDumpHelper($var, $expLevel, $depth = 0, $done = array(), $stripStrings = false)
    {
        $html = '';
        if ($expLevel > 0) {
            $expLevel--;
            $setImg = 0;
            $setStyle = 'display:inline;';
        } elseif ($expLevel == 0) {
            $setImg = 1;
            $setStyle='display:none;';
        } elseif ($expLevel < 0) {
            $setImg = 0;
            $setStyle = 'display:inline;';
        }
        if (is_bool($var)) {
            $html .= '<span style="color:#0E4C92;">bool</span><span style="color:#666;">(</span><strong>' . (($var) ? 'true' : 'false') . '</strong><span style="color:#666;">)</span>';
        } elseif (is_int($var)) {
            $html .= '<span style="color:#0E4C92;">int</span><span style="color:#666;">(</span><strong>' . $var . '</strong><span style="color:#666;">)</span>';
        } elseif (is_float($var)) {
            $html .= '<span style="color:#0E4C92;">float</span><span style="color:#666;">(</span><strong>' . $var . '</strong><span style="color:#666;">)</span>';
        } elseif (is_string($var)) {
            $retStr = self::htmlentities($var);
            if(strlen($retStr) > 60 && $stripStrings) $retStr = str_replace("\n", "", substr($retStr, 0 , 60)) . '[...]';
            $html .= '<span style="color:#0E4C92;">string</span><span style="color:#666;">(</span>' . strlen($var) . '<span style="color:#666;">)</span> <strong>"' . $retStr . '"</strong>';
        } elseif (is_null($var)) {
            $html .= '<strong>NULL</strong>';
        } elseif (is_resource($var)) {
            $html .= '<span style="color:#0E4C92;">resource</span>("' . get_resource_type($var) . '") <strong>"' . $var . '"</strong>';
        } elseif (is_array($var)) {
            // Check for recursion
            if ($depth > 0) {
                foreach ($done as $prev) {
                    if ($prev === $var) {
                        $html .= '<span style="color:#0E4C92;">array</span>(' . count($var) . ') *RECURSION DETECTED*';
                        return $html;
                    }
                }
                // Keep track of variables we have already processed to detect recursion
                $done[] = &$var;
            }
            self::$hasArray = true;
            $uuid = 'include-php-' . uniqid() . mt_rand(1, 1000000);
            $html .= (!empty($var) ? ' <img id="' . $uuid . '" src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs%3D" onclick="javascript:colToggle(this.id);" /><script>setImg("' . $uuid . '",'.$setImg.',1);</script>' : '') . '<span style="color:#0E4C92;">array</span>(' . count($var) . ')';
            if (!empty($var)) {
                $html .= ' <span id="' . $uuid . '-collapsable" style="'.$setStyle.'"><br />[<br />';
                $indent = 4;
                $longest_key = 0;
                foreach ($var as $key => $value) {
                    if (is_string($key)) {
                        $longest_key = max($longest_key, strlen($key) + 2);
                    } else {
                        $longest_key = max($longest_key, strlen($key));
                    }
                }
                foreach ($var as $key => $value) {
                    if (is_numeric($key)) {
                        $html .= str_repeat(' ', $indent) . str_pad($key, $longest_key, ' ');
                    } else {
                        $html .= str_repeat(' ', $indent) . str_pad('"' . self::htmlentities($key) . '"', $longest_key, ' ');
                    }
                    $html .= ' => ';
                    $value = explode('<br />', self::recursiveVarDumpHelper($value, $expLevel, $depth + 1, $done, $stripStrings));
                    foreach ($value as $line => $val) {
                        if ($line != 0) {
                            $value[$line] = str_repeat(' ', $indent * 2) . $val;
                        }
                    }
                    $html .= implode('<br />', $value) . '<br />';
                }
                $html .= ']</span>';
            }
        } elseif (is_object($var)) {
            // Check for recursion
            foreach ($done as $prev) {
                if ($prev === $var) {
                    $html .= '<span style="color:#0E4C92;">object</span>(' . get_class($var) . ') *RECURSION DETECTED*';
                    return $html;
                }
            }
            // Keep track of variables we have already processed to detect recursion
            $done[] = &$var;
            self::$hasArray=true;
            $uuid = 'include-php-' . uniqid() . mt_rand(1, 1000000);
            $html .= ' <img id="' . $uuid . '" src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs%3D" onclick="javascript:colToggle(this.id);" /><script>setImg("' . $uuid . '",'.$setImg.',1);</script><span style="color:#0E4C92;">object</span>(' . get_class($var) . ') <span id="' . $uuid . '-collapsable" style="'.$setStyle.'"><br />[<br />';
            $varArray = (array) $var;
            $indent = 4;
            $longest_key = 0;
            foreach ($varArray as $key => $value) {
                if (substr($key, 0, 2) == "\0*") {
                    unset($varArray[$key]);
                    $key = 'protected:' . substr($key, 3);
                    $varArray[$key] = $value;
                } elseif (substr($key, 0, 1) == "\0") {
                    unset($varArray[$key]);
                    $key = 'private:' . substr($key, 1, strpos(substr($key, 1), "\0")) . ':' . substr($key, strpos(substr($key, 1), "\0") + 2);
                    $varArray[$key] = $value;
                }
                if (is_string($key)) {
                    $longest_key = max($longest_key, strlen($key) + 2);
                } else {
                    $longest_key = max($longest_key, strlen($key));
                }
            }
            foreach ($varArray as $key => $value) {
                if (is_numeric($key)) {
                    $html .= str_repeat(' ', $indent) . str_pad($key, $longest_key, ' ');
                } else {
                    $html .= str_repeat(' ', $indent) . str_pad('"' . self::htmlentities($key) . '"', $longest_key, ' ');
                }
                $html .= ' => ';
                $value = explode('<br />', self::recursiveVarDumpHelper($value, $expLevel, $depth + 1, $done, $stripStrings));
                foreach ($value as $line => $val) {
                    if ($line != 0) {
                        $value[$line] = str_repeat(' ', $indent * 2) . $val;
                    }
                }
                $html .= implode('<br />', $value) . '<br />';
            }
            $html .= ']</span>';
        }
        return $html;
    }

    public static function wp_dump( $data )
    {
        add_action( 'admin_notices', function() use ($data){
            echo '<div class="notice notice-success is-dismissible">';
            self::var_dump($data);
            echo '</div>';
        } );
    }

    public static function postLabels( $singular, $plural )
    {
        $lS = mb_strtolower( $singular );
        $uS = ucfirst( $lS );
        $lP = mb_strtolower( $plural );
        $uP = ucfirst( $lP );
		
        $labels = array(
            'name'=> $uP,
            'singular_name'=> $uS,
            'all_items'=> $uP,
            'add_new'=> 'Új ' . $lS,
            'add_new_item'=> 'Új '. $lS .' hozzáadása',
            'edit_item'=> 'Szerkesztés',
            'new_item'=> 'Új ' . $lS,
            'view_item'=> 'Megnéz',
            'search_items'=> 'Keresés',
            'not_found'=> 'Nincs ' . $lS,
            'not_found_in_trash'=> 'Nincs törölt ' . $lS,
            'parent_item_colon'=> 'Szülő:',
            'menu_name'=> $uP,
        );
        return $labels;
    }

    public static function taxLabels( $singular, $plural )
    {
        $lS = mb_strtolower( $singular );
        $uS = ucfirst( $lS );
        $lP = mb_strtolower( $plural );
        $uP = ucfirst( $lP );

        $labels = array(
            'name' => $uS,
            'singular_name' => $uS,
            'search_items' => 'Keresés',
            'all_items' => 'Összes listázása',
            'parent_item' => 'Szülő ' . $lS,
            'parent_item_colon' => 'Szülő:',
            'edit_item' => 'Szerkesztés',
            'update_item' => 'Módosítás',
            'add_new_item' => 'Új ' . $lS,
            'view_item' => 'Megtekintés',
            'popular_items' => 'Népszerű',
            'new_item_name' => 'Új ' . $lS,
            'separate_items_with_commas' => 'Vesszővel elválasztva',
            'add_or_remove_items' => 'Hozzárendelés',
            'choose_from_most_used' => 'Leggyakrabban használt',
            'not_found' => 'Nincs találat',
            'menu_name' => $uP,
            'name_admin_bar' => $uP,
        );
        return $labels;
    }

}