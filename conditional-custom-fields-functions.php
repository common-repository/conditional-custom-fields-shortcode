<?php

function olt_get_cf($key, $single = false, $strict = false, $sort = 'nosort', $type = 'auto', $case = true) {
    global $post;
    
    wp_reset_query();
    
    if(!is_singular())
        return null;
    
    if(!$strict && $sort == 'nosort')
        return get_post_meta($post->ID, $key, $single);
    
    // Get an array even when $single == true and sort it first
    if($strict) {
        $a = get_post_custom($post->ID);
        $cf = $a[$key];
        
        if(!is_array($cf) || count($cf) == 0)
            return null;
    } else {
        $cf = get_post_meta($post->ID, $key, false);
        
        if($cf == '')
            return '';
    }
    
    $cf = olt_cf_sort($cf, $sort, $type, $case);
        
    if($single)
        return $cf[0];
    else
        return $cf;
}

function olt_cf_compare($mode = 'eq', $val1, $val2, $type = 'auto', $case = true) {    
    // Type casting
    switch(strtolower($type)) {
        case 'integer':
        case 'int':
            $val1 = intval($val1);
            $val2 = intval($val2);
            break;
        case 'float':
            $val1 = floatval($val1);
            $val2 = floatval($val2);
            break;
        case 'date':
            $val1 = strtotime($val1);
            $val2 = strtotime($val2);
            break;
        case 'boolean':
        case 'bool':
            $val1 = (strtolower($val1) == 'true')? true : false;
            $val2 = (strtolower($val2) == 'true')? true : false;
            break;
        case 'auto':
        default:
            // do nothing
            break;
    }
    
    if(strtolower($type) == 'string'){
        $result = ($case)? strcmp($val1, $val2) : strcasecmp($val1, $val2);
        switch(strtolower($mode)) {
            case 'gt':
                return $result > 0;
            case 'lt':
                return $result < 0;
            case 'get':
                return $result >= 0;
            case 'let':
                return $result <= 0;
            case 'neq':
                return $result != 0;
            case 'eq':
            default:
                return $result == 0;
        }
    }else{
        switch(strtolower($mode)) {
            case 'gt':
                return $val1 > $val2;
            case 'lt':
                return $val1 < $val2;
            case 'get':
                return $val1 >= $val2;
            case 'let':
                return $val1 <= $val2;
            case 'neq':
                return $val1 != $val2;
            case 'eq':
            default:
                return $val1 == $val2;
        }
    }
}

function olt_cf_sort($cf, $sort = 'nosort', $type = 'auto', $case = true) {
    if($sort == 'asc' || $sort == 'dsc') {
        switch($type) {
            case 'integer':
            case 'int':
            case 'float':
                sort($cf, SORT_NUMERIC);
                break;
            case 'string':
                if($case) {
                    sort($cf, SORT_STRING);
                } else {
                    usort($cf, create_function('$a,$b','return strcasecmp($a,$b);'));
                }
                break;
            case 'date':
                usort($cf, create_function('$a,$b',
                    'return strtotime($a) - strtotime($b);'));
                break;
            case 'boolean': // how do you sort a boolean value?!
            case 'bool':    // how do you sort a boolean value?!
            case 'auto':
            default:
                sort($cf);
                break;
        }
        return ($sort == 'asc')? $cf : array_reverse($cf);
    } elseif($sort == 'random') {
        shuffle($cf);
        return $cf;
    } else {
        // nosort, do nothing
        return $cf;
    }
}

?>