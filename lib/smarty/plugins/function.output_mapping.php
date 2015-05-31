<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Created by JetBrains PhpStorm.
 * User: teazhang
 * Date: 13-1-17
 * Time: 下午4:53
 * To change this template use File | Settings | File Templates.
 */

/**
 * Smarty {output_mapping} function plugin
 *
 * Type:     function<br>
 * Name:     output_mapping<br>
 * Input:<br>
 *           - key       (required) - index key
 *           - map       (required) - array
 * Purpose:  Prints the desc which matches some index key
 * @author teazhang <teazhang@tencent.com>
 * @param array
 * @param Smarty
 * @return string
 * @uses smarty_function_output_mapping()
 */
function smarty_function_output_mapping($params, &$smarty)
{
    require_once $smarty->_get_plugin_filepath('shared', 'escape_special_chars');

    $key = null;
    $map = null;

    foreach ($params as $_key => $_val) {
        switch ($_key) {
            case 'key':
                $$_key = (string)$_val;
                break;

            case 'map':
                $$_key = (array)$_val;
                break;

            default:
                $smarty->trigger_error("output_mapping: Params error. Must be key/map", E_USER_NOTICE);
                break;
        }
    }

    if (!isset($key) || !isset($map))
    {
        $smarty->trigger_error("output_mapping: Input valid", E_USER_ERROR);
        return ''; /* raise error here? */
    }

    return smarty_function_escape_special_chars($map[$key]);
}