<?php

/**
 * @brief 문자열 처리 클래스.
 *
 * @author noggong@gmail.com
 */
class StringHelper
{
    /**
     * @brief bytes 를 크기에 따라 K, M, G의 단위를 붙여 반환한다.
     *
     * 예제) echo StringUtil::formatSize(8594234.032, 1)   // 8.2 M
     *
     * @param $bytes float 바이트 크기.
     * @param $decimals int 소숫점 자릿수. 기본값 0
     * @param $minUnit string 최소단위. 'G', 'M', 'K', ''. 기본값. 'K'
     * @param $space string 바이드와 단위 사이의 공백 여부 기본값. false
     * @return string 포맷된 문자열.
     */
    static function formatSize($bytes, $decimals = 0, $minUnit = 'K', $space = false)
    {
        $minUnit = strtoupper($minUnit);

        if ($bytes >= 1048576000 || $minUnit == "G") {
            $str = number_format($bytes / 1048576000.0, $decimals);
            $unit = "GB";
        } else if ($bytes >= 1048576 || $minUnit == "M") {
            $str = number_format($bytes / 1048576.0, $decimals);
            $unit = "MB";
        } else if ($bytes >= 1024 || $minUnit == "K") {
            $str = number_format($bytes / 1024.0, $decimals);
            if ($str == "0.0") $str = 0;
            $unit = "KB";
        } else {
            $str = number_format($bytes, $decimals);
            $unit = "K";
        }

//error_log("[StringUtil][formatSize] bytes=$bytes, str=$str, unit=$unit");

        return $str . ($space ? " " : "") . $unit;
    }


    /**
     * @brief bytes 를 크기에 따라 K, M, G의 단위를 붙여 반환한다.
     *
     * 예제) echo StringUtil::simpleFormatSize(8594234.032, 1)   // 8.2 M
     *
     * @param $bytes float 바이트 크기.
     * @param $decimals int 소숫점 자릿수. 기본값 0
     * @param $minUnit string최소단위. 'G', 'M', 'K', ''. 기본값. 'K'
     * @param $space string 바이드와 단위 사이의 공백 여부 기본값. false
     * @return string 포맷된 문자열.
     */
    static function simpleFormatSize($bytes, $decimals = 0, $minUnit = 'K', $space = false)
    {
        $minUnit = strtoupper($minUnit);

        if ($bytes >= 1048576000 || $minUnit == "G") {
            $str = number_format($bytes / 1048576000.0, $decimals);
            $unit = "G";
        } else if ($bytes >= 1048576 || $minUnit == "M") {
            $str = number_format($bytes / 1048576.0, $decimals);
            $unit = "M";
        } else if ($bytes >= 1024 || $minUnit == "K") {
            $str = number_format($bytes / 1024.0, $decimals);
            if ($str == "0.0") $str = 0;
            $unit = "K";
        } else {
            $str = number_format($bytes, $decimals);
            $unit = "";
        }

//error_log("[StringUtil][formatSize] bytes=$bytes, str=$str, unit=$unit");

        return $str . ($space ? " " : "") . $unit;
    }


    /**
     * @brief bytes 를 M 바이트 크기로 포매팅하여 반환한다.
     *
     * 예제) echo StringUtil::formatSize(8594234.032, 1)   // 8.2 M
     *
     * @param float $bytes 바이트 크기.
     * @param int $decimals 소숫점 자릿수. 기본값 0
     * @param int $decimals 최소값. 기본값 0.1
     * @return string 포맷된 문자열.
     */
    static function formatSizeM($bytes, $decimals = 0, $minDefaultValue = 0.1)
    {
        $megaSize = $bytes / 1048576.0;

        //error_log("[StringUtil][formatSizeM] bytes=$bytes, mega=$megaSize");
        if ($megaSize < $minDefaultValue) $megaSize = $minDefaultValue;

        $str = number_format($megaSize, $decimals);
        $unit = "MB";

        return $str . " " . $unit;
    }

    /**
     * @brief bytes 를 M 바이트 크기로 포매팅하여 반환한다.
     */
    static function formatStringToSize($stringSize)
    {
        $stringSize = str_replace(",", "", trim($stringSize));
        if (eregi("(.*)(GB$|G$|MB$|M$|KB$|K$)", $stringSize, $regs)) {
            if ($regs[2] == "GB" || $regs[2] == "G")
                return $regs[1] * 1048576000;
            else if ($regs[2] == "MB" || $regs[2] == "M")
                return $regs[1] * 1048576;
            else if ($regs[2] == "KB" || $regs[2] == "K")
                return $regs[1] * 1024;
        }
        return $stringSize;
    }

    /**
     * @brief toSQLSafe '나 \, \n에 대해 escpae 처리를 한다.
     * @param $s string
     * @return string sql-safe
     */
    static function toSQLSafe($s)
    {
        if (is_array($s)) {
            for ($i = 0; $i < count($s); $i++) {
                $rtn = array();
                $rtn[] = self::toSQLSafe($s[$i]);
            }
            return $rtn;
        } else {
            static $search = array("\\", "\r", "\n", "'", '"');
            static $replace = array("\\\\", "\\r", "\\n", "\\'", '\\"');

            $s = str_replace($search, $replace, $s);
            return $s;
        }
    }


    /**
     * @brief removeBadHTML
     * remove bad html and javascript code.
     * example)
     * <pre>
     *   <ul>
     *     <li><script...>...</script>
     *     <li><script...>...</script
     *     <li><?.....? >
     *     <li><link...>
     *     <li><meta...>
     *     <li><base...>
     *     <li><... onXXX='...'...>
     *     <li><... onXXX="..."...>
     *     <li><... onXXX=...>
     *     <li><img...onerror=..>
     *     <li><style...type="text/javascript">...</style>
     *     <li>url(...)...
     *     <li>expression(...)...
     *     <li>"((vb|java|live|j)script:mocha):#..."
     *     <li>'((vb|java|live|j)script:mocha):#...'
     *     <li>(vb|java|live|j)script.encode:...
     *     <li>&#0000115&#0000099&#0000114&#0000105&#0000112&#0000116
     *     <li>&#x73&#x63&#x72&#x69&#x70&#x74
     *     <li>@import
     *   </ul>
     * </pre>
     *
     * @param $rawhtml string unsafe html
     * @return string safe html
     */
    static function removeBadHTML($rawhtml)
    {
        static $pre_search = array('/<(%[0-9]+)+/');
        static $pre_replace = array('<');
        $rawhtml = preg_replace($pre_search, $pre_replace, $rawhtml);


        // ------------------------------------------------
        // 나쁜 코드 제거
        // ------------------------------------------------
        static $rbh_search = array(
            "'<script[^>]*?>.*?</script[^>]*>'si"  // 자바 스크립트 제거
        , "'<script[^>]*?>.*?</script'si"  // 자바 스크립트 제거
        , "'<iframe[^>]*?>.*?</iframe[^>]*>'si"  // iframe 제거
        , "'<\?.*?\?>'si"                   // php코드 블럭 제거
        , "'<link[^>]+>'si"  // link tag 제거
        , "'<meta[^>]+>'si"  // meta tag 제거
        , "'<base[^>]*>'i"
        , "'<([^>]+)[ \t]+on[a-zA-Z]+=\'[^\']*\''si"  // <... onXXX='xxx' ... 제거
        , "'<([^>]+)[ \t]+on[a-zA-Z]+=\"[^\"]*\"'si"  // <... onXXX="xxx" ... 제거
        , "'<([^>]+)[ \t]+on[a-zA-Z]+=[^ \t]+'si"  // <... onXXX="xxx" ...> 제거
        , "'<img([^>]+)onerror[ \t]*=[ \t]*'si"  // <img.. onError=... 에서 onError=제거
            //,"'<style[^>]+type=\"[^\"]*text/javascript\"[^\"]*[^>]+>.*?</style[^>]*>'si"
            //,"'<style[^>]*>.*?</style[^>]*>'si"
        , '/url\([^\)]+\)/si'  // <div style="background-image: url(javascript:[code]);"> 제거
        , "/e[\\\ \t;]*x[\\\ \t;]*p[\\\ \t;]*r[\\\ \t;]*e[\\\ \t;]*s[\\\ \t;]*s[\\\ \t;]*i[\\\ \t;]*o[\\\ \t;]*n\(/si"  // <div style="background-image: expression([code]);"> 제거
        , "'((v[\\\ \t;]*b|j[\\\ \t;]*a[\\\ \t;]*v[\\\ \t\n;]*a|l[\\\ \t;]*i[\\\ \t;]*v[\\\ \t;]*e|j)[\\\ \t;]*s[\\\ \t;]*c[\\\ \t;]*r[\\\ \t;]*i[\\\ \t;]*p[\\\ \t;]*t|mocha)[\\\ \t;]*[:#]'si" // "javascript:[code]"  제거
        , "'(v[\\\ \t;]*b|j[\\\ \t;]*a[\\\ \t;]*v[\\\ \t;]*a|live|j)script.encode:'si" // 'javascript:[code]'  제거
        , "'&#0000115&#0000099&#0000114&#0000105&#0000112&#0000116's"
        , "'&#x73&#x63&#x72&#x69&#x70&#x74'si"
        , '/@import/i'
        , '/<[\/]?[a-zA-Z]+:[a-zA-Z0-9]+>/'  // xml태그가 에디터 오류발생 임시제거 <ABC:DEF2>, </ABC:DEF2>
        );

        static $rbh_replace = array(
            "<!--script removed-->"
        , "<!--unclosed script removed-->"
        , "<!--iframe removed-->"
        , "<!--php removed-->"
        , "<link>"
        , "<!--meta removed-->" // <meta....>
        , "" // base
        , "<\$1 _r1_ "
        , "<\$1 _r2_ "
        , "<\$1 _r3_ "
        , "<img\$1" // <img onerror
            //,"" // <style>....</style>
            //,"" // <style>....</style>
        , ''  // url()
        , 'void('  // expression()
        , 'no_script:'   // 'javascript:...'
        , ':'   // 'VBScript.Encode:...'
        , '&#0000115&#0000099&#0000114&#0000105&#0000112 &#0000116'
        , '&#x73&#x63&#x72&#x69&#x70 &#x74'
        , '@ import'
        , ''
        );

        $safehtml = preg_replace($rbh_search, $rbh_replace, $rawhtml);


        return $safehtml;
    }


    /**
     * @brief toPrintableHTML
     *
     * @param $html string unsafe html
     * @return string safe html
     */
    static function toPrintableHTML($html)
    {
        static $tph_search = array(
            "'&'"
        , "'<'"
        , "'>'"
        , "'\"'"
        );

        static $tph_replace = array(
            "&amp;"
        , "&lt;"
        , "&gt;"
        , "&quot;"
        );

        return preg_replace($tph_search, $tph_replace, $html);
    }


    /**
     * @brief Association배열을 JSON형식으로 변환한다.
     * @param $ary .
     * @param $ary .
     * @return JSON코드.
     */
    static function assocToJson($ary, $needSafeWrap = false)
    {
        if (!is_array($ary)) return;

        $keys = array_keys($ary);
        $cnt = count($keys);
        $json = "{";

        for ($i = 0; $i < $cnt; $i++) {
            $key = $keys[$i];

            if (is_numeric($ary[$key]))
                $value = $ary[$key];
            else if (is_bool($ary[$key]))
                $value = $ary[$key] ? "true" : "false";
            else
                $value = "\"" . str_replace("\"", "\\\"", $ary[$key]) . "\"";

            $json .= $key . ":{$value}";
            if ($i < $cnt - 1) $json .= ",";
        }

        $json .= "}";

        if ($needSafeWrap) $json = "(" . $json . ")";

        return $json;
    }


    /**
     * @brief Mulit바이트 문자 substr
     *
     * 사용법:
     * echo substrWideChar("한글제목", 5);      // 결과: 한글...
     * echo substrWideChar("한글제목", 5, "");  // 결과: 한글
     *
     * @param $raw string 자를 문자열
     * @param $maxBytes integer 최대바이트
     * @param $omittingString string 잘릴 때 뒤에 붙이는 생략표시 문자열. 기본값 ...
     * @param $multiByteSize integer Multi-byte크기. 기본 2.
     * @return string 크기에 맞게 잘린 문자열.
     */
    static function substrWideChar($raw, $maxBytes, $omittingString = "...", $multiByteSize = 2)
    {
        $length = strlen($raw);
        $omittingLen = strlen($omittingString);
        $maxBytes -= $omittingLen;

        if ($length <= $maxBytes) return $raw;

        for ($i = $safe_len = 0; $i < $length - 1; $i++) {
            if (ord($raw[$i]) > 127) {
                $safe_len += $multiByteSize;
                $i++;
            } else $safe_len++;

            if ($safe_len == $maxBytes) break;
            if ($safe_len > $maxBytes) {
                $safe_len -= $multiByteSize;
                break;
            }
        }

        $short = substr($raw, 0, $safe_len);
        return $short . $omittingString;
    }


    /**
     * @brief UTF-8 문자셋의 문자열에 대한 깨지지 않는 substring 메소드.
     *
     * 사용법:
     * echo substrUTF8("한글제목", 5);      // 결과: 한글...
     * echo substrUTF8("한글제목", 5, "");  // 결과: 한글
     *
     * @param $raw string 자를 문자열(UTF-8)
     * @param $maxBytes integer 최대바이트
     * @param $omittingString string 잘릴 때 뒤에 붙이는 생략표시 문자열. 기본값 ...
     * @return string 크기에 맞게 잘린 문자열.
     */
    static function substrUTF8($raw, $maxBytes, $omittingString = "...")
    {
        $char1has = 0xC0;
        $char2has = 0xE0;
        $char3has = 0xF0;

        $charByteWidth = 0;
        $charWidth = 0;
        $utfcharbyte = 2;
        $returnlen = 0;
        $byteslen = strlen($raw);

        for ($cutPos = 0; $cutPos < $byteslen;) {
            $charAscii = (int)ord($raw[$cutPos]);

            // ------------------------------------------------------
            // 몇 바이트 짜리 문자인지 확인.
            // ------------------------------------------------------
            if (($charAscii & $char3has) == $char3has) {
                $charByteWidth = 4;
                $charWidth = $utfcharbyte;
            } else if (($charAscii & $char2has) == $char2has) {
                $charByteWidth = 3;
                $charWidth = $utfcharbyte;
            } else if (($charAscii & $char1has) == $char1has) {
                $charByteWidth = 2;
                $charWidth = $utfcharbyte;
            } else {
                $charByteWidth = 1;
                $charWidth = 1;
            }

            // ------------------------------------------------------
            // 최대크기를 넘지 않았는지 확인.
            // ------------------------------------------------------
            if (($returnlen + $charWidth) == $maxBytes) {
                // 현재 읽은 문자를 포함할 경우 최대 byte와 같다면, 현재까지만 포함하고 더이상 읽지 않음.
                $cutPos += $charByteWidth;
                break;
            } else if (($returnlen + $charWidth) > $maxBytes) {
                // 현재 읽은 문자를 포함할 경우 최대 byte보다 크다면, 현재 문자는 읽지 않는다.
                break;
            } else {
                $cutPos += $charByteWidth;
                $returnlen += $charWidth;
            }
            // ------------------------------------------------------
        }

        if ($cutPos < ($byteslen))
            $ret = substr($raw, 0, $cutPos) . $omittingString;
        else
            $ret = $raw;

        return $ret;
    }


    /**
     * @brief UTF-8 문자셋의 문자열을 글자단위 배열을 반환한다.
     *
     *
     * @param $raw string 자를 문자열(UTF-8)
     * @return Array 문자 배열.
     */
    function splitCharsUTF8($raw)
    {
        $char1has = 0xC0;
        $char2has = 0xE0;
        $char3has = 0xF0;

        $charByteWidth = 0;

        $byteslen = strlen($raw);
        $chars = array();

        for ($cutPos = 0; $cutPos < $byteslen;) {
            $charAscii = (int)ord($raw[$cutPos]);

            // ------------------------------------------------------
            // 몇 바이트 짜리 문자인지 확인.
            // ------------------------------------------------------
            if (($charAscii & $char3has) == $char3has) {
                $charByteWidth = 4;
            } else if (($charAscii & $char2has) == $char2has) {
                $charByteWidth = 3;
            } else if (($charAscii & $char1has) == $char1has) {
                $charByteWidth = 2;
            } else {
                $charByteWidth = 1;
            }
            // ------------------------------------------------------

            $chars[] = substr($raw, $cutPos, $charByteWidth);
            $cutPos += $charByteWidth;
        }

        return $chars;
    }


    /**
     * @brief UTF-8 문자셋의 문자열에 대한 깨지지 않는 substring을 이용한 paging 메소드.
     *
     * 사용법:
     * echo substrUTF8("한글제목", 5, paging);      // 결과: 한글...
     * echo substrUTF8("한글제목", 5, paging);  // 결과: 한글
     *
     * @param $raw string 자를 문자열(UTF-8)
     * @param $maxBytes integer 최대바이트
     * @param $omittingString string 잘릴 때 뒤에 붙이는 생략표시 문자열. 기본값 ...
     * @return string 크기에 맞게 잘린 문자열.
     */
    static function substrWideCharPaging($raw, $splitBytes)
    {
        $ret = array();
        $tmp_raw = $raw;
        if (strlen($raw) < $splitBytes) {
            $ret[] = $raw;
        } else {
            while (trim($tmp_raw) != '') {
                $page_str = self::substrWideChar($tmp_raw, $splitBytes, '');
                $ret[] = trim($page_str);
                if (strlen($tmp_raw) > $splitBytes) {
                    $next_pos = strlen($page_str);
                    $tmp_raw = substr($tmp_raw, $next_pos);
                } else {
                    break;
                }
            }
        }
        return $ret;
    }


    /**
     * @brief
     *
     */
    static function _strpos($rawHTML, $needle)
    {
        if (!$rawHTML || !$needle) return false;
        $t = stristr($rawHTML, $needle);
        if ($t) return false;

        $pos = strlen($rawHTML) - strlen($t);
        return $pos;
    }


    /**
     * @brief 특정 태그사이의 모든 string을 제거한다.
     * @param $rawHTML string HTML
     * @param $openTag string 여는 태그
     * @param $closeTag string 닫는 태그
     * @return string 특정 태그가 제거된 $rawHTML
     */
    static function removeTag($rawHTML, $openTag, $closeTag)
    {
        if (!$rawHTML || !$openTag || !$closeTag) {
            return;
        }

        $t = $rawHTML;

        while ($t) {
            $p1 = StringUtil::_strpos($t, $openTag);
            $p2 = StringUtil::_strpos($t, $closeTag);
            if (!is_integer($p1) || !is_integer($p2) || $p2 <= $p1) break;

            $t = substr($t, 0, $p1) . substr($t, $p2 + strlen($closeTag));
        }

        return $t;
    }


    /**
     * @brief escapeURL
     *
     */
    static function escapeURL($rawstr)
    {
        $escaped = "";
        $len = strlen($rawstr);

        for ($i = 0; $i < $len; $i++) {
            if (ord($rawstr[$i]) & 0x80) {
                $escaped .= $rawstr[$i];
                $i++;
                $escaped .= $rawstr[$i];
            } else if ($rawstr[$i] == "/" || $rawstr[$i] == "."
                || ord($rawstr[$i]) == 0x20
                || (ord($rawstr[$i]) > 0x2F && ord($rawstr[$i]) < 0x3A)
                || (ord($rawstr[$i]) > 0x40 && ord($rawstr[$i]) < 0x5B)
                || (ord($rawstr[$i]) > 0x60 && ord($rawstr[$i]) < 0x7B)
            ) {
                $escaped .= $rawstr[$i];
            } else if (ord($rawstr[$i]) > 0x0F) {
                $escaped .= "%" . dechex(ord($rawstr[$i]));
            } else {
                $escaped .= "%0" . dechex(ord($rawstr[$i]));
            }
        }

        return $escaped;
    }


    /**
     * @brief 어떤 문자열이 특정 문자열로 시작하는지 검사한다.
     *
     * <pre>
     * example)
     *   startsWith("www.abc.com", "www") // return true
     * </pre>
     *
     * @param $src string 비교할 문자열.
     * @param $testStr string 비교될 문자열.
     * @param $ignoreCase boolean 대소문자구분여부. 기본값 대소문자 구분.
     * @return boolean 특정 문자열로 시작하는지 여부.
     */
    static function startsWith($src, $testStr, $ignoreCase = false)
    {
        $srcLength = strlen($src);
        $testLength = strlen($testStr);

        if ($srcLength < $testLength)
            return false;

        if ($ignoreCase)
            return strtolower(substr($src, 0, $testLength)) == strtolower($testStr);
        else
            return substr($src, 0, $testLength) == $testStr;
    }


    /**
     * @brief 어떤 문자열이 특정 문자열로 끝나는지 검사한다.
     *
     * <pre>
     * example)
     *   endsWith("www.abc.com", ".com") // return true
     * </pre>
     *
     * @param  $src string비교할 문자열.
     * @param $testStr string 비교될 문자열.
     * @param $ignoreCase boolean 대소문자구분여부. 기본값 대소문자 구분.
     * @return boolean 특정 문자열로 시작하는지 여부.
     */
    static function endsWith($src, $testStr, $ignoreCase = false)
    {
        $srcLength = strlen($src);
        $testLength = strlen($testStr);

        if ($srcLength < $testLength)
            return false;

        if ($ignoreCase)
            return strtolower(substr($src, ($srcLength - $testLength))) == strtolower($testStr);
        else
            return substr($src, ($srcLength - $testLength)) == $testStr;
    }


    /**
     * 이메일주소의 형식이 맞는지 확인.
     */
    public static function isValidEmailFormat($email)
    {
        return eregi('^[_a-zA-Z0-9\-]+[_a-zA-Z0-9\.\-]*@[a-zA-Z0-9\-]+(\.[a-zA-Z0-9\-]+)+$', $email);
    }


    /**
     * 이메일주소의 @앞부분 형식이 맞는지 확인.
     */
    public static function isValidEmailAccountFormat($account)
    {
        return eregi('^[_a-zA-Z0-9\-]+[_a-zA-Z0-9\.\-]*$', $account);
    }


    public static function buildSetQuery($val, $fieldName, &$queryArray, $isNumeric = false)
    {
        if (is_null($val))
            return;

        if ($isNumeric)
            $queryArray[] = $fieldName . '=' . StringUtil::toSQLSafe($val);
        else
            $queryArray[] = $fieldName . '=\'' . StringUtil::toSQLSafe($val) . '\'';
    }


    public static function validate($val, $validPattern, $defaultValue)
    {
        // 패턴이 없고 $val이 설정되어 있지 않으면
        // 바로 $defaultValue를 반환한다.
        if ($validPattern == '') {
            if (!isset($val))
                return $defaultValue;

            return $val;
        }

        if (!isset($val) || preg_match('/^' . $validPattern . '$/', $val) < 1)
            return $defaultValue;

        return $val;
    }


    /**
     * alias of validate
     */
    public static function validateParam($val, $validPattern, $defaultValue)
    {
        return self::validate($val, $validPattern, $defaultValue);
    }


    /**
     * 날짜 형식(YYYY-MM-DD HH:II:SS)의 검사
     * 날짜 형식의 범위에 맞는지도 검사한다.
     *
     * @param $val string 검사할 날짜 형식의 문자열
     * @param $default mixed 형식에 맞지 않을 경우 반환값.
     * @return 형식에 맞을 경우 $val, 아니면 $default
     */
    public static function validateDateStr($val, $default)
    {
        return self::validate($val, '[1-9][\d]{3}\-(1[0-2]|0[1-9])\-([0-2][\d]|3[0-1])( ([0-1][\d]|2[0-3]):[0-5][0-9]:[0-5][0-9])?', $default);
    }


    /**
     * 숫자 형식 검사
     *
     * @param $val string 검사할 문자열
     * @param $default mixed 형식에 맞지 않을 경우 반환값.
     * @return 형식에 맞을 경우 $val, 아니면 $default
     */
    public static function validateNum($val, $default)
    {
        return self::validate($val, '[\d]+', $default);
    }


    /**
     * 숫자,숫자 형식 검사
     *
     * @param $val string 검사할 문자열
     * @param $default mixed 형식에 맞지 않을 경우 반환값.
     * @return 형식에 맞을 경우 $val, 아니면 $default
     */
    public static function validateNumsWithComma($val, $default)
    {
        return self::validate($val, '[\d]+(,[\d]+)*', $default);
    }


    /**
     * 날짜유효성체크
     *
     * @param $date string YYYY-MM-DD
     *
     * @return boolean
     */
    public static function checkDateValidation($date)
    {
        if (self::validateDateStr($date, false) === false) {
            return false;
        }

        $tmp = explode("-", $date);

        return checkdate(intval($tmp[1]), intval($tmp[2]), intval($tmp[0]));
    }

    /**
     * string 내 http:// 혹은 www 가 있으면 링크로 바꿔줌.
     *
     * @param string $string 대상 string
     * @param target $target 링크 target
     *
     * @return string
     */
    public static function replaceStringToHttp($string, $target = "_blank")
    {
        $string = preg_replace('@((https?://)?([-\w]+\.[-\w\.]+)+\w(:\d+)?(/([-\w/_\.]*(\?\S+)?)?)*)@', '<a href="$1" target="' . $target . '">$1</a>', $string);
        $string = str_replace("href=\"www.", "href=\"http://www.", $string);
        return $string;
    }

    /**
     * string 내 aaa.bbb.ccc 가 있으면 링크로 바꿔줌.
     *
     * @param string $string 대상 string
     * @param target $target 링크 target
     *
     * @return string
     */
    public static function replaceWideStringToHttp($string, $target = "_blank")
    {
        //error_log($string);
        $regx = '((https?://)+([\S]+))e';
        //$rep = "'<a href=\"'.(strlen('\\2')>0?'\\2':'http://').'\\3\" target=\"$target\">\\2\\3</a>'";
        $rep = "'<a href=\"\\0\" target=\"$target\">\\0</a>'";

        //print_r($string);
        //preg_match_all($regx, $string, $arr_tmp);
        /*for ($i=0;$i<count($arr_tmp[0]);$i++) {
            var_dump('|'.$arr_tmp[0][$i].'|');
        }*/
        //print_r($arr_tmp);

        $string = preg_replace($regx, $rep, $string);

        //error_log($string);

        return $string;

    }

    /**
     * string 내 이미지 태그안에 width= height= 제거
     *
     * @param string $string 대상 string
     *
     * @return string
     */
    public static function removeSizeInImageTag($content)
    {

        $pattern = '/(<img[\s]*.*)(width=(\'|\")?\d+(px)?(\'|\")?)(.*>)/i';
        $content = preg_replace($pattern, "$1$6", $content);
        $pattern = '/(<img[\s]*.*)(height=(\'|\")?\d+(px)?(\'|\")?)(.*>)/i';
        $content = preg_replace($pattern, "$1$6", $content);
//		$content = preg_replace($pattern,"1: $1\n2: $2\n3: $3\n4: $4\n5: $5\n6: $6\n7: $7",$content);

        return $content;
    }

    /**
     * //img.tourtips.com/으로 캐쉬로 불러오는 이미지들에게 type를 넣어서 repalce 해준다
     *
     * @param string $string 대상 string
     * @param string $tp m:모바일 w :웹
     * @return string
     */
    public static function settingCashType($content, $tp = 'm')
    {
        $pattern = '/<img[^>]*src=[\'|"]?([^>\"\']+)[\'|"]?[^>]*>/i';
        $content = preg_replace($pattern, "<img src=\"$1&tp=" . $tp . "\">", $content);
        return $content;
    }

    /**
     * 외부 이지미 링크 캐쉬서버 통할수 있도록 image src replace해준다
     *
     * @param string $string 대상 string
     * @param int $bid 게시판 아이디
     * @param int $cid 게시물 아이디
     * @return string
     */
    public static function settingOutLinkImage($content, $bid, $cid)
    {
        //이미지 소스 가져온다
        $pattern = '@<img\s[^>]*src\s*=\s*(["\'])?(http[^\s>]+?)\1@i';
        preg_match_all($pattern, $content, $match);

        if (is_array($match[2]) || sizeof($match[2]) > 0) {
            foreach ($match[2] as $u) {
                //s:이미지 주소에 img.tourtips.com 이 있으면 thu 로 보내지 않음으로 예외처리한다.
                if (strpos($u, 'img.tourtips.com') !== false) {
                    continue;
                }
                //e:이미지 주소에 img.tourtips.com 이 있으면 thu 로 보내지 않음으로 예외처리한다.

                //url과 게시판 번호 게시물 번호를 묶어서 url 인코딩 한다.
                $url = rawurlencode(base64_encode($u . ':::' . $bid . ':::' . $cid));
                $content = str_replace($u, '//thu.tourtips.com/ap/board/outlink/?u=' . $url, $content);
            }

        }
//		error_log(print_r($match,true));
        return $content;
    }

    /**
     * 문자열내 링크태그의 타켓을 전부 새창으로 바꿔준다
     *
     * @param string $str 대상문자열
     * @return string
     */
    public static function changeAnchorTargetToBlank($str)
    {
        $src = '/<a[^>]*href=[\'"]?([^>\'"]+)[\'"]?[^>]*>/i';
        $dest = '<a href="$1" target="_blank">';
        return $str = preg_replace($src, $dest, $str);
    }

    /**
     * @brief [0-9a-zA-Z] 문자열에 대한 shorten 문자열 생성
     *
     * <pre>
     * example)
     *    $id = '10000001DB8';
     *    echo $id."\n";
     *    $str = usn2num($id);
     *    echo $str."\n";
     *    $f = alphaStr($str);
     *    echo $f."\n";
     *    $f1 = alphaStr($f,true);
     *    echo $f1."\n";
     *    $out = num2usn($f1);
     *    echo $out."\n";
     * </pre>
     *
     * @param $str string 변환 문자열.
     * @param $toNum boolean 숫자로 변환.
     * @param $padd boolean 패딩 범위.
     * @param $salt string  암호화 문자.
     * @return boolean 특정 문자열로 시작하는지 여부.
     */
    public static function alphaStr($str, $toNum = false, $padd = false, $salt = null)
    {
        $out = '';
        $strdex = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $base = strlen($strdex);
        if ($salt !== null) {
            // Although this function's purpose is to just make the
            // ID short - and not so much secure,
            // with this patch by Simon Franz (http://blog.snaky.org/)
            // you can optionally supply a password to make it harder
            // to calculate the corresponding numeric ID
            for ($n = 0; $n < strlen($strdex); $n++) {
                $i[] = substr($strdex, $n, 1);
            }
            $pass_hash = hash('sha256', $salt);
            $pass_hash = (strlen($pass_hash) < strlen($strdex) ? hash('sha512', $salt) : $pass_hash);
            for ($n = 0; $n < strlen($strdex); $n++) {
                $p[] = substr($pass_hash, $n, 1);
            }
            array_multisort($p, SORT_DESC, $i);
            $strdex = implode($i);
        }
        if ($toNum) {
            // Digital number  <<--  alphabet letter code
            $len = strlen($str) - 1;
            for ($t = $len; $t >= 0; $t--) {
                $bcp = bcpow($base, $len - $t);
                $out = $out + strpos($strdex, substr($str, $t, 1)) * $bcp;
            }
            if (is_numeric($padd)) {
                $padd--;
                if ($padd > 0) {
                    $out -= pow($base, $padd);
                }
            }
        } else {
            // Digital number  -->>  alphabet letter code
            if (is_numeric($padd)) {
                $padd--;
                if ($padd > 0) {
                    $str += pow($base, $padd);
                }
            }
            for ($t = ($str != 0 ? floor(log($str, $base)) : 0); $t >= 0; $t--) {
                $bcp = bcpow($base, $t);
                $a = floor($str / $bcp) % $base;
                $out = $out . substr($strdex, $a, 1);
                $str = $str - ($a * $bcp);
            }
        }
        return $out;
    }

    /**
     * //사용예
     * echo '기본 : ' . get_random_string() . '<br />';
     * echo '숫자만 : ' . get_random_string( 5, '09') . '<br />';
     * echo '숫자만 30글자 : ' . get_random_string('09', 30) . '<br />';
     * echo '소문자만 : ' . get_random_string(5,'az') . '<br />';
     * echo '대문자만 : ' . get_random_string(5,'AZ') . '<br />';
     * echo '소문자+대문자 : ' . get_random_string(5,'azAZ') . '<br />';
     * echo '소문자+숫자 : ' . get_random_string(5,'az09') . '<br />';
     * echo '대문자+숫자 : ' . get_random_string(5,'AZ09') . '<br />';
     * echo '소문자+대문자+숫자 : ' . get_random_string(5,'azAZ09') . '<br />';
     * echo '특수문자만 : ' . get_random_string(5,'$') . '<br />';
     * echo '숫자+특수문자 : ' . get_random_string(5,'09$') . '<br />';
     * echo '소문자+특수문자 : ' . get_random_string(5,'az$') . '<br />';
     * echo '대문자+특수문자 : ' . get_random_string(5,'AZ$') . '<br />';
     * echo '소문자+대문자+특수문자 : ' . get_random_string(5,'azAZ$') . '<br />';
     * echo '소문자+대문자+숫자+특수문자 : ' . get_random_string(5,'azAZ09$') . '<br />';
     * @param int $len
     * @param string $type
     * @return string
     */
    public static function getRandomString($len = 10, $type = '')
    {
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numeric = '0123456789';
        $special = '`~!@#$%^&*()-_=+\\|[{]};:\'",<.>/?';
        $key = '';
        $token = '';
        if ($type == '') {
            $key = $lowercase . $uppercase . $numeric;
        } else {
            if (strpos($type, '09') > -1) $key .= $numeric;
            if (strpos($type, 'az') > -1) $key .= $lowercase;
            if (strpos($type, 'AZ') > -1) $key .= $uppercase;
            if (strpos($type, '$') > -1) $key .= $special;
        }
        for ($i = 0; $i < $len; $i++) {
            $token .= $key[mt_rand(0, strlen($key) - 1)];
        }
        return $token;
    }

    /**
     * 특정 문자열을 항상 같은 값의 숫자로 반환하게 하기 위한 함수.
     * @param string $string
     * @return int
     */
    public static function getStringHash($string)
    {
        $hash = substr(md5($string), 0, 4);
        return base_convert($hash, 16, 2) % 32;

    }
}

