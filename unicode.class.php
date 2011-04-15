<?php
/**
 * 将字符串转换成unicode编码
 *
 * @param string $input
 * @param string $input_charset
 * @return string
 */
function str_to_unicode($input, $input_charset = 'gbk')
{
	$input = iconv($input_charset, "gbk", $input);
	preg_match_all("/[\x80-\xff]?./", $input, $ar);
	$b = array_map('utf8_unicode_', $ar[0]);
	$outstr = join(",", $b);
	return $outstr;
}

function utf8_unicode_($c, $input_charset = 'gbk')
{
	$c = iconv($input_charset, 'utf-8', $c);
	return utf8_unicode($c);
}

// utf8 -> unicode
function utf8_unicode($c)
{
	//var_dump(ord($c[0]));
	//var_dump(ord($c[0]) & 0x3f);
	switch(strlen($c)) {
		case 1:
			//return $c;
			$n = ord($c[0]);
			break;
		case 2:
			$n = (ord($c[0]) & 0x3f) << 6;
			$n += ord($c[1]) & 0x3f;
			break;
		case 3:
			$n = (ord($c[0]) & 0x1f) << 12;
			$n += (ord($c[1]) & 0x3f) << 6;
			$n += ord($c[2]) & 0x3f;
			break;
		case 4:
			$n = (ord($c[0]) & 0x0f) << 18;
			$n += (ord($c[1]) & 0x3f) << 12;
			$n += (ord($c[2]) & 0x3f) << 6;
			$n += ord($c[3]) & 0x3f;
			break;
	}
	//return "&#$n;";
	//var_dump($n);
	return "U+".base_convert($n, 10, 16);
}

/**
 * 将unicode字符转换成普通编码字符
 *
 * @param string $str
 * @param string $out_charset
 * @return string
 */
function str_from_unicode($str, $out_charset = 'gbk')
{
	$str = preg_replace_callback("|U\+([0-9a-f]{1,4})|", 'unicode2utf8_', $str);
	$str = iconv("UTF-8", $out_charset, $str);
	return $str;
}

function unicode2utf8_($c)
{
	return unicode2utf8($c[1]);
}

function unicode2utf8($c)
{
	$c = base_convert($c, 16, 10);
	$str="";
	if ($c < 0x80) {
		$str.=chr($c);
	} else if ($c < 0x800) {
		$str.=chr(0xC0 | $c>>6);
		$str.=chr(0x80 | $c & 0x3F);
	} else if ($c < 0x10000) {
		$str.=chr(0xE0 | $c>>12);
		$str.=chr(0x80 | $c>>6 & 0x3F);
		$str.=chr(0x80 | $c & 0x3F);
	} else if ($c < 0x200000) {
		$str.=chr(0xF0 | $c>>18);
		$str.=chr(0x80 | $c>>12 & 0x3F);
		$str.=chr(0x80 | $c>>6 & 0x3F);
		$str.=chr(0x80 | $c & 0x3F);
	}
	return $str;
}

