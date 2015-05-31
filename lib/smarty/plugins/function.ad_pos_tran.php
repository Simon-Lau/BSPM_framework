<?php

/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
  2013-12
 */
function smarty_function_ad_pos_tran($params, &$smarty) {
    require_once $smarty->_get_plugin_filepath('shared', 'escape_special_chars');

    $key = null;
    $values = null;
    $template = "<div class='dropdown open'>
             <a href='#' class='dropdown-toggle hdDetail' role='button' data-toggle='dropdown'>
             %s<b class='caret'></b>
             </a>
             <ul class='dropdown-menu' role='menu' aria-labelledby='dLabel'>
             %s
             </ul></div>";
    $template_li = "<li>%s</li>";
    foreach ($params as $_key => $_val) {
        switch ($_key) {
            case 'key':
                $$_key = (string) $_val;
                break;
            case 'values':
                $$_key = (array) $_val;
                break;
            case 'name':
                $$_key = (string) $_val;
                break;
            case 'pullval':
                $$_key = (array) $_val;
                break;
            default:
                break;
        }
    }

    if (!isset($values) || !isset($key))
        return '';

    if (empty($values[$key])) {
        if ($key == '有效') {
            return '<span style="background-color:#00ffff">有效</span>';
        } else if ($key == '无效') {
            return '<span style="background-color:#999999">无效</span>';
        }
        return $key;
    } else {
        if ($values[$key] == '有效') {
            return '<span style="background-color:#00ffff">有效</span>';
        } else if ($values[$key] == '无效') {
            return '<span style="background-color:#999999">无效</span>';
        }
        if ($name == 'ad_index_group_id') {
            $result = $values[$key] . "(ID:<span style='color:red'>$key</span>)";
            $s = '';
            if (!empty($pullval)) {
                $tmp = reset($pullval);
                $tmp = explode(';', $tmp);
                foreach ($tmp as $v) {
                    $s.=sprintf($template_li, $v);
                }
            }
            if (!empty($s)) {
                $result = sprintf($template, $result, $s);
            }
            return $result;
        } else if ($name == 'policy_id') {
            $result = $values[$key] . "(ID:<span style='color:red'>$key</span>)";
            $s = '';
            if (!empty($pullval)) {
                $s.=sprintf($template_li, '对同一用户:' . $pullval['int_s_uin'] . '(秒)');
                $s.=sprintf($template_li, '同一广告:' . $pullval['int_s_ad'] . '(秒)');
                $s.=sprintf($template_li, '同一广告点击后:' . $pullval['int_c_ad'] . '(秒)');
                $s.=sprintf($template_li, '同一广告主:' . $pullval['int_s_ad_uin'] . '(秒)');
                $s.=sprintf($template_li, '同一广告主点击后:' . $pullval['int_c_ad_uin'] . '(秒)');
            }
            if (!empty($s)) {
                $result = sprintf($template, $result, $s);
            }
            return $result;
        }
        return $values[$key];
    }
}

/* vim: set expandtab: */
?>
