<?php
/**
 * Theme storage manipulations
 *
 * @package WordPress
 * @subpackage PALLADIO
 * @since PALLADIO 1.0
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) { exit; }

// Get theme variable
if (!function_exists('palladio_storage_get')) {
	function palladio_storage_get($var_name, $default='') {
		global $PALLADIO_STORAGE;
		return isset($PALLADIO_STORAGE[$var_name]) ? $PALLADIO_STORAGE[$var_name] : $default;
	}
}

// Set theme variable
if (!function_exists('palladio_storage_set')) {
	function palladio_storage_set($var_name, $value) {
		global $PALLADIO_STORAGE;
		$PALLADIO_STORAGE[$var_name] = $value;
	}
}

// Check if theme variable is empty
if (!function_exists('palladio_storage_empty')) {
	function palladio_storage_empty($var_name, $key='', $key2='') {
		global $PALLADIO_STORAGE;
		if (!empty($key) && !empty($key2))
			return empty($PALLADIO_STORAGE[$var_name][$key][$key2]);
		else if (!empty($key))
			return empty($PALLADIO_STORAGE[$var_name][$key]);
		else
			return empty($PALLADIO_STORAGE[$var_name]);
	}
}

// Check if theme variable is set
if (!function_exists('palladio_storage_isset')) {
	function palladio_storage_isset($var_name, $key='', $key2='') {
		global $PALLADIO_STORAGE;
		if (!empty($key) && !empty($key2))
			return isset($PALLADIO_STORAGE[$var_name][$key][$key2]);
		else if (!empty($key))
			return isset($PALLADIO_STORAGE[$var_name][$key]);
		else
			return isset($PALLADIO_STORAGE[$var_name]);
	}
}

// Inc/Dec theme variable with specified value
if (!function_exists('palladio_storage_inc')) {
	function palladio_storage_inc($var_name, $value=1) {
		global $PALLADIO_STORAGE;
		if (empty($PALLADIO_STORAGE[$var_name])) $PALLADIO_STORAGE[$var_name] = 0;
		$PALLADIO_STORAGE[$var_name] += $value;
	}
}

// Concatenate theme variable with specified value
if (!function_exists('palladio_storage_concat')) {
	function palladio_storage_concat($var_name, $value) {
		global $PALLADIO_STORAGE;
		if (empty($PALLADIO_STORAGE[$var_name])) $PALLADIO_STORAGE[$var_name] = '';
		$PALLADIO_STORAGE[$var_name] .= $value;
	}
}

// Get array (one or two dim) element
if (!function_exists('palladio_storage_get_array')) {
	function palladio_storage_get_array($var_name, $key, $key2='', $default='') {
		global $PALLADIO_STORAGE;
		if (empty($key2))
			return !empty($var_name) && !empty($key) && isset($PALLADIO_STORAGE[$var_name][$key]) ? $PALLADIO_STORAGE[$var_name][$key] : $default;
		else
			return !empty($var_name) && !empty($key) && isset($PALLADIO_STORAGE[$var_name][$key][$key2]) ? $PALLADIO_STORAGE[$var_name][$key][$key2] : $default;
	}
}

// Set array element
if (!function_exists('palladio_storage_set_array')) {
	function palladio_storage_set_array($var_name, $key, $value) {
		global $PALLADIO_STORAGE;
		if (!isset($PALLADIO_STORAGE[$var_name])) $PALLADIO_STORAGE[$var_name] = array();
		if ($key==='')
			$PALLADIO_STORAGE[$var_name][] = $value;
		else
			$PALLADIO_STORAGE[$var_name][$key] = $value;
	}
}

// Set two-dim array element
if (!function_exists('palladio_storage_set_array2')) {
	function palladio_storage_set_array2($var_name, $key, $key2, $value) {
		global $PALLADIO_STORAGE;
		if (!isset($PALLADIO_STORAGE[$var_name])) $PALLADIO_STORAGE[$var_name] = array();
		if (!isset($PALLADIO_STORAGE[$var_name][$key])) $PALLADIO_STORAGE[$var_name][$key] = array();
		if ($key2==='')
			$PALLADIO_STORAGE[$var_name][$key][] = $value;
		else
			$PALLADIO_STORAGE[$var_name][$key][$key2] = $value;
	}
}

// Merge array elements
if (!function_exists('palladio_storage_merge_array')) {
	function palladio_storage_merge_array($var_name, $key, $value) {
		global $PALLADIO_STORAGE;
		if (!isset($PALLADIO_STORAGE[$var_name])) $PALLADIO_STORAGE[$var_name] = array();
		if ($key==='')
			$PALLADIO_STORAGE[$var_name] = array_merge($PALLADIO_STORAGE[$var_name], $value);
		else
			$PALLADIO_STORAGE[$var_name][$key] = array_merge($PALLADIO_STORAGE[$var_name][$key], $value);
	}
}

// Add array element after the key
if (!function_exists('palladio_storage_set_array_after')) {
	function palladio_storage_set_array_after($var_name, $after, $key, $value='') {
		global $PALLADIO_STORAGE;
		if (!isset($PALLADIO_STORAGE[$var_name])) $PALLADIO_STORAGE[$var_name] = array();
		if (is_array($key))
			palladio_array_insert_after($PALLADIO_STORAGE[$var_name], $after, $key);
		else
			palladio_array_insert_after($PALLADIO_STORAGE[$var_name], $after, array($key=>$value));
	}
}

// Add array element before the key
if (!function_exists('palladio_storage_set_array_before')) {
	function palladio_storage_set_array_before($var_name, $before, $key, $value='') {
		global $PALLADIO_STORAGE;
		if (!isset($PALLADIO_STORAGE[$var_name])) $PALLADIO_STORAGE[$var_name] = array();
		if (is_array($key))
			palladio_array_insert_before($PALLADIO_STORAGE[$var_name], $before, $key);
		else
			palladio_array_insert_before($PALLADIO_STORAGE[$var_name], $before, array($key=>$value));
	}
}

// Push element into array
if (!function_exists('palladio_storage_push_array')) {
	function palladio_storage_push_array($var_name, $key, $value) {
		global $PALLADIO_STORAGE;
		if (!isset($PALLADIO_STORAGE[$var_name])) $PALLADIO_STORAGE[$var_name] = array();
		if ($key==='')
			array_push($PALLADIO_STORAGE[$var_name], $value);
		else {
			if (!isset($PALLADIO_STORAGE[$var_name][$key])) $PALLADIO_STORAGE[$var_name][$key] = array();
			array_push($PALLADIO_STORAGE[$var_name][$key], $value);
		}
	}
}

// Pop element from array
if (!function_exists('palladio_storage_pop_array')) {
	function palladio_storage_pop_array($var_name, $key='', $defa='') {
		global $PALLADIO_STORAGE;
		$rez = $defa;
		if ($key==='') {
			if (isset($PALLADIO_STORAGE[$var_name]) && is_array($PALLADIO_STORAGE[$var_name]) && count($PALLADIO_STORAGE[$var_name]) > 0) 
				$rez = array_pop($PALLADIO_STORAGE[$var_name]);
		} else {
			if (isset($PALLADIO_STORAGE[$var_name][$key]) && is_array($PALLADIO_STORAGE[$var_name][$key]) && count($PALLADIO_STORAGE[$var_name][$key]) > 0) 
				$rez = array_pop($PALLADIO_STORAGE[$var_name][$key]);
		}
		return $rez;
	}
}

// Inc/Dec array element with specified value
if (!function_exists('palladio_storage_inc_array')) {
	function palladio_storage_inc_array($var_name, $key, $value=1) {
		global $PALLADIO_STORAGE;
		if (!isset($PALLADIO_STORAGE[$var_name])) $PALLADIO_STORAGE[$var_name] = array();
		if (empty($PALLADIO_STORAGE[$var_name][$key])) $PALLADIO_STORAGE[$var_name][$key] = 0;
		$PALLADIO_STORAGE[$var_name][$key] += $value;
	}
}

// Concatenate array element with specified value
if (!function_exists('palladio_storage_concat_array')) {
	function palladio_storage_concat_array($var_name, $key, $value) {
		global $PALLADIO_STORAGE;
		if (!isset($PALLADIO_STORAGE[$var_name])) $PALLADIO_STORAGE[$var_name] = array();
		if (empty($PALLADIO_STORAGE[$var_name][$key])) $PALLADIO_STORAGE[$var_name][$key] = '';
		$PALLADIO_STORAGE[$var_name][$key] .= $value;
	}
}

// Call object's method
if (!function_exists('palladio_storage_call_obj_method')) {
	function palladio_storage_call_obj_method($var_name, $method, $param=null) {
		global $PALLADIO_STORAGE;
		if ($param===null)
			return !empty($var_name) && !empty($method) && isset($PALLADIO_STORAGE[$var_name]) ? $PALLADIO_STORAGE[$var_name]->$method(): '';
		else
			return !empty($var_name) && !empty($method) && isset($PALLADIO_STORAGE[$var_name]) ? $PALLADIO_STORAGE[$var_name]->$method($param): '';
	}
}

// Get object's property
if (!function_exists('palladio_storage_get_obj_property')) {
	function palladio_storage_get_obj_property($var_name, $prop, $default='') {
		global $PALLADIO_STORAGE;
		return !empty($var_name) && !empty($prop) && isset($PALLADIO_STORAGE[$var_name]->$prop) ? $PALLADIO_STORAGE[$var_name]->$prop : $default;
	}
}
?>