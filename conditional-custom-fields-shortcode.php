<?php

/*
Plugin Name: Conditional Custom Fields Shortcode
Plugin URI: #TODO
Description: Use custom field values in you pages or posts. With conditional supports which enables basic templating with custom fields.
Author: Godfrey Chan (UBC OLT)
Version: 0.5
Author URI: http://www.chancancode.com/
*/

require_once('conditional-custom-fields-functions.php');

/* [sc name="key"|"just the key"
       default="string"
       single="true|false"
       separator="string"
       placeholder="string"
       sort="nosort|asc|dsc|random"
       type="string|int|integer|float|bool|boolean|date|auto"
       case="true|false"
   ]
     (optional) template
   [/sc] */

function olt_cf_sc_handler($atts = array(), $content = null) {
    wp_reset_query();
    
    if(!is_singular())
        return;
    
    extract( shortcode_atts( array(
        'name' => (isset($atts[0]))? $atts[0] : '',
        'default' => '',
        'single' => false,
        'separator' => ',',
        'placeholder' => '%value%',
        'sort' => 'nosort',
        'type' => 'auto'
    ), $atts ) );
    
    if($single == 'true' || $single == true) {
        $single = true;
    } else {
        $single = false;
    }
    
    $cf = olt_get_cf($name, $single, false, $sort, $type, $case);
    
    if($cf == '')
        return $default;
        
    if(!$single)
        $cf = implode($separator,$cf);
    
    if(is_null($content) || $content == '') {
        // Tempory fix for shortcode API bug, it should be just is_null()
        return $cf;
    } else {
        return do_shortcode(str_replace($placeholder, $cf, $content));
    }
}

/* [if-sc-def name="key1,key2,key3"|"key1" "key2" "key3"
       logic="and|or"
   ]
     content
   [/if-sc-def] */

function olt_cf_def_sc_handler($atts, $content = null, $def = true) {
    wp_reset_query();
    
    if(!is_singular())
        return;
    
    if(is_null($content) || $content == '')
        return;
    
    // Clean up attrs
    
    $keys = array();
    
    foreach($atts as $key => $val) {
        if(is_numeric($key)) {
            $keys[] = $val;
        }
    }
    
    extract( shortcode_atts( array(
        'name' => $keys,
        'locic' => ($def)? 'and' : 'or'
    ), $atts ) );
    
    if(!is_array($name))
        $name = explode(',', $name);
    
    if(count($name) == 0)
        return;
    
    $logic = strtolower($logic);
    
    if($logic != 'and' && $logic != 'or')
        $logic = ($def)? 'and' : 'or';
    
    $result = false;
    
    foreach($name as $key) {
        // Use single and strict
        if(!is_null(olt_get_cf($key, true, true))) {
            // Defined
            $result = $def;
            if(($def && $logic == 'or') || (!$def && $logic == 'and'))
                break;
        } else {
            // Undefined
            $result = !$def;
            if(($def && $logic == 'and') || (!$def && $logic == 'or'))
                break;
        }
    }
    
    if($result) {
        return do_shortcode($content);
    }
}

/* [if-sc-ndef name="key1,key2,key3"|"key1" "key2" "key3"
       logic="and|or"
   ]
     content
   [/if-sc-ndef] */

function olt_cf_ndef_sc_handler($atts, $content = null) {
    // Dispatch to its buddy (handler for if-cf-def... same logic)
    return olt_cf_def_sc_handler($atts, $content, false);
}

/* [if-sc-eq name="key" value="value1,value2" | "key" "value1" "value2"
             logic="and|or"
             sort="nosort|asc|dsc|random"
             type="string|int|integer|float|bool|boolean|date|auto"
             case="true|false"
   ]
     content
   [/if-sc-eq] */

function olt_cf_eq_sc_handler($atts, $content = null, $mode = 'eq') {
    wp_reset_query();
    
    if(!is_singular())
        return;
    
    if(is_null($content) || $content == '')
        return;
    
    // Clean up attrs
    
    $values = array();
    
    foreach($atts as $key => $val) {
        if(is_numeric($key)) {
            $values[] = $val;
        }
    }
    
    extract( shortcode_atts( array(
        'name' => (isset($atts[0]))? array_shift($values) : '',
        'value' => $values,
        'logic' => 'or',
        'sort' => 'nosort',
        'type' => 'auto',
        'case' => true
    ), $atts ) );
    
    if($name == '')
        return;
    
    if(!is_array($value))
        $value = explode(',', $value);
    
    $logic = strtolower($logic);
    
    if($logic != 'and' && $logic != 'or')
        $logic = ($def)? 'and' : 'or';
    
    $result = false;
    
    $cf = olt_get_cf($name, true);
        
    foreach($value as $val) {
        if(olt_cf_compare($mode, $cf, $val, $type)) {
            $result = true;
            if($logic == 'or')
                break;
        } else {
            $result = false;
            if($logic == 'and')
                break;
        }
    }
    
    if($result) {
        return do_shortcode($content);
    }
}

/* [if-sc-neq name="key" value="value1,value2" | "key" "value1" "value2"
              logic="and|or"
              sort="nosort|asc|dsc|random"
              type="string|int|integer|float|bool|boolean|date|auto"
              case="true|false"
   ]
     content
   [/if-sc-neq] */

function olt_cf_neq_sc_handler($atts, $content = null) {
    // Dispatch to its buddy (handler for if-cf-eq... same logic)
    return olt_cf_eq_sc_handler($atts, $content, 'neq');
}

/* [if-sc-gt name="key" value="value1,value2" | "key" "value1" "value2"
             logic="and|or"
             sort="nosort|asc|dsc|random"
             type="string|int|integer|float|bool|boolean|date|auto"
             case="true|false"
   ]
     content
   [/if-sc-gt] */

function olt_cf_gt_sc_handler($atts, $content = null) {
    // Dispatch to its buddy (handler for if-cf-eq... same logic)
    return olt_cf_eq_sc_handler($atts, $content, 'gt');
}

/* [if-sc-lt name="key" value="value1,value2" | "key" "value1" "value2"
             logic="and|or"
             sort="nosort|asc|dsc|random"
             type="string|int|integer|float|bool|boolean|date|auto"
             case="true|false"
   ]
     content
   [/if-sc-lt] */

function olt_cf_lt_sc_handler($atts, $content = null) {
    // Dispatch to its buddy (handler for if-cf-eq... same logic)
    return olt_cf_eq_sc_handler($atts, $content, 'lt');
}

/* [if-sc-get name="key" value="value1,value2" | "key" "value1" "value2"
              logic="and|or"
              sort="nosort|asc|dsc|random"
              type="string|int|integer|float|bool|boolean|date|auto"
              case="true|false"
   ]
     content
   [/if-sc-get] */

function olt_cf_get_sc_handler($atts, $content = null) {
    // Dispatch to its buddy (handler for if-cf-eq... same logic)
    return olt_cf_eq_sc_handler($atts, $content, 'get');
}

/* [if-sc-let name="key" value="value1,value2" | "key" "value1" "value2"
              logic="and|or"
              sort="nosort|asc|dsc|random"
              type="string|int|integer|float|bool|boolean|date|auto"
              case="true|false"
   ]
     content
   [/if-sc-let] */

function olt_cf_let_sc_handler($atts, $content = null) {
    // Dispatch to its buddy (handler for if-cf-eq... same logic)
    return olt_cf_eq_sc_handler($atts, $content, 'let');
}

function olt_ccfs_init() {    
    add_shortcode('cf', 'olt_cf_sc_handler');
    //add_shortcode('if-cf', 'olt_cf_def_sc_handler');
    add_shortcode('if-cf-def', 'olt_cf_def_sc_handler');
    add_shortcode('if-cf-ndef', 'olt_cf_ndef_sc_handler');
    add_shortcode('if-cf-eq', 'olt_cf_eq_sc_handler');
    add_shortcode('if-cf-neq', 'olt_cf_neq_sc_handler');
    add_shortcode('if-cf-lt', 'olt_cf_lt_sc_handler');
    add_shortcode('if-cf-gt', 'olt_cf_gt_sc_handler');
    add_shortcode('if-cf-let', 'olt_cf_let_sc_handler');
    add_shortcode('if-cf-get', 'olt_cf_get_sc_handler');
    
    // Move wpautop to priority 12 (after do_shortcode)
    remove_filter('the_content','wpautop',10);
    add_filter('the_content','wpautop',12);
}

add_action('init','olt_ccfs_init');