<?php namespace App\Services\Top;

class TopHelper
{
    /**
     * 签名验证
     * @param $params
     * @param $sign
     * @param $method
     * @return bool
     */
    public static function checkSign($params, $sign, $method = 'md5',$body)
    {
        $method = ($method == 'hmac') ? 'generateSignHmac' : 'generateSign';
        if ($sign == call_user_func_array([__CLASS__, $method], [$params,$body])) {
            return true;
        }
        return false;
    }

    /**
     * 生成签名 md5
     * @param $params
     * @return string
     */
    public static function generateSign($params,$body='')
    {
        ksort($params);
        $app_secret = env('TOP_APP_SECRET');
        $stringToBeSigned = $app_secret;
        foreach ($params as $k => $v) {
            if (!is_array($v) && "@" != substr($v, 0, 1)) {
                $stringToBeSigned .= "$k$v";
            }
        }
        unset($k, $v);
        if($body){
            $stringToBeSigned .= $body;
        }
        $stringToBeSigned .= $app_secret;
        return strtoupper(md5($stringToBeSigned));
    }

    /**
     * 生成签名 hmac
     * @param $params
     * @return string
     */
    public static function generateSignHmac($params,$body='')
    {
        ksort($params);
        $app_secret = env('TOP_APP_SECRET');
        $stringToBeSigned = '';
        foreach ($params as $k => $v) {
            if (!is_array($v) && "@" != substr($v, 0, 1)) {
                $stringToBeSigned .= "$k$v";
            }
        }
        unset($k, $v);
        return strtoupper(hash_hmac('md5', $stringToBeSigned, $app_secret));
    }

    public static function arrayToxml($array, $current_depth = -1, $is_getting_more_info = false, $cdata_enabled = true, $indent = '  ', $force_key = '')
    {

        $ch = '<![CDATA[';
        $cf = ']]>';
        $body = '';
        $labels_attrs = '_attrs';
        $current_indent = str_repeat($indent, max(0, $current_depth));
        $eol = ($indent != '') ? PHP_EOL : ''; // End of line

        /* Special case: $is_getting_more_info */
        if ($is_getting_more_info) {
            $return = array(
                'attr_str_for_parent' => '',
                'is_num_keys_only_for_itself' => false,
                'checking_data' => $array
            );

            $attrs_str = ''; // Reset
            if (is_array($array) && key_exists($labels_attrs, $array) && count($array[$labels_attrs])) {
                $attrs_arr = $array[$labels_attrs];
                foreach ($attrs_arr as $ak => $av) {
                    $attrs_str .= ' ' . $ak . '=' . "'{$av}'";
                }
            }
            $return['attr_str_for_parent'] = $attrs_str;


            if (is_array($array)) {
                $is_num_keys_only = true;
                foreach ($array as $k => $v) {
                    if ($k != $labels_attrs && !is_int($k)) {
                        $is_num_keys_only = false;
                    }
                }
                $return['is_num_keys_only_for_itself'] = $is_num_keys_only;

            }
            return $return;
        }


        /* Go */
        if (!is_array($array)) {
            /* Not an array */
            if (is_string($array)) {
                $body = ($cdata_enabled) ? $ch . $array . $cf : $array;
                if ($array == '') {
                    $body = $array;
                }

            } elseif (is_bool($array)) {
                $body = ($array) ? 'TRUE' : 'FALSE';

            } elseif (is_null($array)) {
                $body = '';

            } elseif (is_object($array)) {
                $body = ($cdata_enabled) ? $ch . json_encode($array) . $cf : json_encode($array);

            } else {
                $body = $array;
            }

            /* Special case: 'anyKey' => 'force_only_value:::<?xml version="8.0"?>' */
            if (is_string($array) && stripos($array, 'force_only_value:::') === 0) {
                $body = $array;
            } elseif (is_string($array) && stripos($array, 'force_value:::') === 0) {
                $body = $array;
            }

            /* Value only */
            $return = $body;

        } else {
            /* Is Array */
            $depth_for_son = $current_depth + 1;
            $indent_for_itself = str_repeat($indent, max(0, $current_depth));
            $indent_for_son = str_repeat($indent, max(0, $depth_for_son));

            $more_info_for_itself = self::arrayToxml($array, $current_depth, true);
            if (key_exists($labels_attrs, $array)) {
                unset($array[$labels_attrs]);
            }

            $return = '';
            foreach ($array as $k => $v) {
                $more_info_for_son = self::arrayToxml($v, $depth_for_son, true);
                $tag_header_for_son = '<' . $k . $more_info_for_son['attr_str_for_parent'] . '>';
                $tag_footer_for_son = '</' . $k . '>';

                if ($more_info_for_son['is_num_keys_only_for_itself']) {
                    $body_for_son = self::arrayToxml($v, $current_depth, false, $cdata_enabled, $indent, $k);
                    $return .= $body_for_son;

                } else {
                    if ($force_key != '') {
                        $tag_header_for_son = '<' . $force_key . $more_info_for_son['attr_str_for_parent'] . '>';
                        $tag_footer_for_son = '</' . $force_key . '>';
                    }
                    $body_for_son = self::arrayToxml($v, $depth_for_son, false, $cdata_enabled, $indent);
                    if (is_array($v)) {
                        $return .= $indent_for_son . $tag_header_for_son . $eol . $body_for_son . $indent_for_son . $tag_footer_for_son;

                    } else {
                        $new_row = $indent_for_son . $tag_header_for_son . $body_for_son . $tag_footer_for_son;

                        /* Special case: 'anyKey' => 'force_only_value:::<?xml version="8.0"?>' */
                        if (is_string($body_for_son) && stripos($body_for_son, 'force_only_value:::') === 0) {
                            $new_row = $indent_for_son . str_ireplace('force_only_value:::', '', $body_for_son);
                        } elseif (is_string($body_for_son) && stripos($body_for_son, 'force_value:::') === 0) {
                            $new_row = $indent_for_son . $tag_header_for_son . str_ireplace('force_value:::', '', $body_for_son) . $tag_footer_for_son;;
                        }

                        $return .= $new_row;

                    }
                }
                $return .= $eol;
            }

        }
        return $return;

    }

    public static function xml2array($xml)
    {
//        //禁止引用外部xml实体
        $xml = str_replace('&', '', $xml);
        libxml_disable_entity_loader(true);
        $result = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $result;

    }
}
