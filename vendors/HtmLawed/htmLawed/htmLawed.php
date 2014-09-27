<?php

/*
htmLawed 1.1.18, 2 August 2014
Copyright Santosh Patnaik
Dual licensed with LGPL 3 and GPL 2+
A PHP Labware internal utility; www.bioinformatics.org/phplabware/internal_utilities/htmLawed

See htmLawed_README.txt/htm
*/

function htmLawed($t, $C=1, $S=array()){
$C = is_array($C) ? $C : array();
if(!empty($C['valid_xhtml'])){
 $C['elements'] = empty($C['elements']) ? '*-center-dir-font-isindex-menu-s-strike-u' : $C['elements'];
 $C['make_tag_strict'] = isset($C['make_tag_strict']) ? $C['make_tag_strict'] : 2;
 $C['xml:lang'] = isset($C['xml:lang']) ? $C['xml:lang'] : 2;
}
// config eles
$e = array('a'=>1, 'abbr'=>1, 'acronym'=>1, 'address'=>1, 'applet'=>1, 'area'=>1, 'b'=>1, 'bdo'=>1, 'big'=>1, 'blockquote'=>1, 'br'=>1, 'button'=>1, 'caption'=>1, 'center'=>1, 'cite'=>1, 'code'=>1, 'col'=>1, 'colgroup'=>1, 'dd'=>1, 'del'=>1, 'dfn'=>1, 'dir'=>1, 'div'=>1, 'dl'=>1, 'dt'=>1, 'em'=>1, 'embed'=>1, 'fieldset'=>1, 'font'=>1, 'form'=>1, 'h1'=>1, 'h2'=>1, 'h3'=>1, 'h4'=>1, 'h5'=>1, 'h6'=>1, 'hr'=>1, 'i'=>1, 'iframe'=>1, 'img'=>1, 'input'=>1, 'ins'=>1, 'isindex'=>1, 'kbd'=>1, 'label'=>1, 'legend'=>1, 'li'=>1, 'map'=>1, 'menu'=>1, 'noscript'=>1, 'object'=>1, 'ol'=>1, 'optgroup'=>1, 'option'=>1, 'p'=>1, 'param'=>1, 'pre'=>1, 'q'=>1, 'rb'=>1, 'rbc'=>1, 'rp'=>1, 'rt'=>1, 'rtc'=>1, 'ruby'=>1, 's'=>1, 'samp'=>1, 'script'=>1, 'select'=>1, 'small'=>1, 'span'=>1, 'strike'=>1, 'strong'=>1, 'sub'=>1, 'sup'=>1, 'table'=>1, 'tbody'=>1, 'td'=>1, 'textarea'=>1, 'tfoot'=>1, 'th'=>1, 'thead'=>1, 'tr'=>1, 'tt'=>1, 'u'=>1, 'ul'=>1, 'var'=>1, 'semantics'=>1, 'annotation'=>1, 'math'=>1, 'mrow'=>1, 'msup'=>1, 'mi'=>1, 'mo'=>1, 'mn'=>1, 'menclose'=>1, 'merror'=>1, 'mfenced'=>1, 'mfrac'=>1, 'mglyph'=>1, 'mlabeledtr'=>1, 'mmultiscripts'=>1, 'mover'=>1, 'mpadded'=>1, 'mphantom'=>1, 'mroot'=>1, 'mspace'=>1, 'msqrt'=>1, 'mstyle'=>1, 'msub'=>1, 'msubsup'=>1, 'mtable'=>1, 'mtd'=>1, 'mtext'=>1, 'mtr'=>1, 'munder'=>1, 'munderover'=>1); // 86/deprecated+embed+ruby
if(!empty($C['safe'])){
 unset($e['applet'], $e['embed'], $e['iframe'], $e['object'], $e['script']);
}
$x = !empty($C['elements']) ? str_replace(array("\n", "\r", "\t", ' '), '', $C['elements']) : '*';
if($x == '-*'){$e = array();}
elseif(strpos($x, '*') === false){$e = array_flip(explode(',', $x));}
else{
 if(isset($x[1])){
  preg_match_all('`(?:^|-|\+)[^\-+]+?(?=-|\+|$)`', $x, $m, PREG_SET_ORDER);
  for($i=count($m); --$i>=0;){$m[$i] = $m[$i][0];}
  foreach($m as $v){
   if($v[0] == '+'){$e[substr($v, 1)] = 1;}
   if($v[0] == '-' && isset($e[($v = substr($v, 1))]) && !in_array('+'. $v, $m)){unset($e[$v]);}
  }
 }
}
$C['elements'] =& $e;
// config attrs
$x = !empty($C['deny_attribute']) ? str_replace(array("\n", "\r", "\t", ' '), '', $C['deny_attribute']) : '';
$x = array_flip((isset($x[0]) && $x[0] == '*') ? explode('-', $x) : explode(',', $x. (!empty($C['safe']) ? ',on*' : '')));
if(isset($x['on*'])){
 unset($x['on*']);
 $x += array('onblur'=>1, 'onchange'=>1, 'onclick'=>1, 'ondblclick'=>1, 'onfocus'=>1, 'onkeydown'=>1, 'onkeypress'=>1, 'onkeyup'=>1, 'onmousedown'=>1, 'onmousemove'=>1, 'onmouseout'=>1, 'onmouseover'=>1, 'onmouseup'=>1, 'onreset'=>1, 'onselect'=>1, 'onsubmit'=>1);
}
$C['deny_attribute'] = $x;
// config URL
$x = (isset($C['schemes'][2]) && strpos($C['schemes'], ':')) ? strtolower($C['schemes']) : 'href: aim, feed, file, ftp, gopher, http, https, irc, mailto, news, nntp, sftp, ssh, telnet; *:file, http, https';
$C['schemes'] = array();
foreach(explode(';', str_replace(array(' ', "\t", "\r", "\n"), '', $x)) as $v){
 $x = $x2 = null; list($x, $x2) = explode(':', $v, 2);
 if($x2){$C['schemes'][$x] = array_flip(explode(',', $x2));}
}
if(!isset($C['schemes']['*'])){$C['schemes']['*'] = array('file'=>1, 'http'=>1, 'https'=>1,);}
if(!empty($C['safe']) && empty($C['schemes']['style'])){$C['schemes']['style'] = array('!'=>1);}
$C['abs_url'] = isset($C['abs_url']) ? $C['abs_url'] : 0;
if(!isset($C['base_url']) or !preg_match('`^[a-zA-Z\d.+\-]+://[^/]+/(.+?/)?$`', $C['base_url'])){
 $C['base_url'] = $C['abs_url'] = 0;
}
// config rest
$C['and_mark'] = empty($C['and_mark']) ? 0 : 1;
$C['anti_link_spam'] = (isset($C['anti_link_spam']) && is_array($C['anti_link_spam']) && count($C['anti_link_spam']) == 2 && (empty($C['anti_link_spam'][0]) or hl_regex($C['anti_link_spam'][0])) && (empty($C['anti_link_spam'][1]) or hl_regex($C['anti_link_spam'][1]))) ? $C['anti_link_spam'] : 0;
$C['anti_mail_spam'] = isset($C['anti_mail_spam']) ? $C['anti_mail_spam'] : 0;
$C['balance'] = isset($C['balance']) ? (bool)$C['balance'] : 1;
$C['cdata'] = isset($C['cdata']) ? $C['cdata'] : (empty($C['safe']) ? 3 : 0);
$C['clean_ms_char'] = empty($C['clean_ms_char']) ? 0 : $C['clean_ms_char'];
$C['comment'] = isset($C['comment']) ? $C['comment'] : (empty($C['safe']) ? 3 : 0);
$C['css_expression'] = empty($C['css_expression']) ? 0 : 1;
$C['direct_list_nest'] = empty($C['direct_list_nest']) ? 0 : 1;
$C['hexdec_entity'] = isset($C['hexdec_entity']) ? $C['hexdec_entity'] : 1;
$C['hook'] = (!empty($C['hook']) && function_exists($C['hook'])) ? $C['hook'] : 0;
$C['hook_tag'] = (!empty($C['hook_tag']) && function_exists($C['hook_tag'])) ? $C['hook_tag'] : 0;
$C['keep_bad'] = isset($C['keep_bad']) ? $C['keep_bad'] : 6;
$C['lc_std_val'] = isset($C['lc_std_val']) ? (bool)$C['lc_std_val'] : 1;
$C['make_tag_strict'] = isset($C['make_tag_strict']) ? $C['make_tag_strict'] : 1;
$C['named_entity'] = isset($C['named_entity']) ? (bool)$C['named_entity'] : 1;
$C['no_deprecated_attr'] = isset($C['no_deprecated_attr']) ? $C['no_deprecated_attr'] : 1;
$C['parent'] = isset($C['parent'][0]) ? strtolower($C['parent']) : 'body';
$C['show_setting'] = !empty($C['show_setting']) ? $C['show_setting'] : 0;
$C['style_pass'] = empty($C['style_pass']) ? 0 : 1;
$C['tidy'] = empty($C['tidy']) ? 0 : $C['tidy'];
$C['unique_ids'] = isset($C['unique_ids']) ? $C['unique_ids'] : 1;
$C['xml:lang'] = isset($C['xml:lang']) ? $C['xml:lang'] : 0;

if(isset($GLOBALS['C'])){$reC = $GLOBALS['C'];}
$GLOBALS['C'] = $C;
$S = is_array($S) ? $S : hl_spec($S);
if(isset($GLOBALS['S'])){$reS = $GLOBALS['S'];}
$GLOBALS['S'] = $S;

$t = preg_replace('`[\x00-\x08\x0b-\x0c\x0e-\x1f]`', '', $t);
if($C['clean_ms_char']){
 $x = array("\x7f"=>'', "\x80"=>'&#8364;', "\x81"=>'', "\x83"=>'&#402;', "\x85"=>'&#8230;', "\x86"=>'&#8224;', "\x87"=>'&#8225;', "\x88"=>'&#710;', "\x89"=>'&#8240;', "\x8a"=>'&#352;', "\x8b"=>'&#8249;', "\x8c"=>'&#338;', "\x8d"=>'', "\x8e"=>'&#381;', "\x8f"=>'', "\x90"=>'', "\x95"=>'&#8226;', "\x96"=>'&#8211;', "\x97"=>'&#8212;', "\x98"=>'&#732;', "\x99"=>'&#8482;', "\x9a"=>'&#353;', "\x9b"=>'&#8250;', "\x9c"=>'&#339;', "\x9d"=>'', "\x9e"=>'&#382;', "\x9f"=>'&#376;');
 $x = $x + ($C['clean_ms_char'] == 1 ? array("\x82"=>'&#8218;', "\x84"=>'&#8222;', "\x91"=>'&#8216;', "\x92"=>'&#8217;', "\x93"=>'&#8220;', "\x94"=>'&#8221;') : array("\x82"=>'\'', "\x84"=>'"', "\x91"=>'\'', "\x92"=>'\'', "\x93"=>'"', "\x94"=>'"'));
 $t = strtr($t, $x);
}
if($C['cdata'] or $C['comment']){$t = preg_replace_callback('`<!(?:(?:--.*?--)|(?:\[CDATA\[.*?\]\]))>`sm', 'hl_cmtcd', $t);}
$t = preg_replace_callback('`&amp;([A-Za-z][A-Za-z0-9]{1,30}|#(?:[0-9]{1,8}|[Xx][0-9A-Fa-f]{1,7}));`', 'hl_ent', str_replace('&', '&amp;', $t));
if($C['unique_ids'] && !isset($GLOBALS['hl_Ids'])){$GLOBALS['hl_Ids'] = array();}
if($C['hook']){$t = $C['hook']($t, $C, $S);}
if($C['show_setting'] && preg_match('`^[a-z][a-z0-9_]*$`i', $C['show_setting'])){
 $GLOBALS[$C['show_setting']] = array('config'=>$C, 'spec'=>$S, 'time'=>microtime());
}
// main
$t = preg_replace_callback('`<(?:(?:\s|$)|(?:[^>]*(?:>|$)))|>`m', 'hl_tag', $t);
$t = $C['balance'] ? hl_bal($t, $C['keep_bad'], $C['parent']) : $t;
$t = (($C['cdata'] or $C['comment']) && strpos($t, "\x01") !== false) ? str_replace(array("\x01", "\x02", "\x03", "\x04", "\x05"), array('', '', '&', '<', '>'), $t) : $t;
$t = $C['tidy'] ? hl_tidy($t, $C['tidy'], $C['parent']) : $t;
unset($C, $e);
if(isset($reC)){$GLOBALS['C'] = $reC;}
if(isset($reS)){$GLOBALS['S'] = $reS;}
return $t;
// eof
}

function hl_attrval($t, $p){
// check attr val against $S
$o = 1; $l = strlen($t);
foreach($p as $k=>$v){
 switch($k){
  case 'maxlen':if($l > $v){$o = 0;}
  break; case 'minlen': if($l < $v){$o = 0;}
  break; case 'maxval': if((float)($t) > $v){$o = 0;}
  break; case 'minval': if((float)($t) < $v){$o = 0;}
  break; case 'match': if(!preg_match($v, $t)){$o = 0;}
  break; case 'nomatch': if(preg_match($v, $t)){$o = 0;}
  break; case 'oneof':
   $m = 0;
   foreach(explode('|', $v) as $n){if($t == $n){$m = 1; break;}}
   $o = $m;
  break; case 'noneof':
   $m = 1;
   foreach(explode('|', $v) as $n){if($t == $n){$m = 0; break;}}
   $o = $m;
  break; default:
  break;
 }
 if(!$o){break;}
}
return ($o ? $t : (isset($p['default']) ? $p['default'] : 0));
// eof
}

function hl_bal($t, $do=1, $in='div'){
// balance tags
// by content
$cB = array('blockquote'=>1, 'form'=>1, 'map'=>1, 'noscript'=>1, 'math'=>1); // Block
$cE = array('area'=>1, 'br'=>1, 'col'=>1, 'embed'=>1, 'hr'=>1, 'img'=>1, 'input'=>1, 'isindex'=>1, 'param'=>1); // Empty
$cF = array('button'=>1, 'del'=>1, 'div'=>1, 'dd'=>1, 'fieldset'=>1, 'iframe'=>1, 'ins'=>1, 'li'=>1, 'noscript'=>1, 'object'=>1, 'td'=>1, 'th'=>1); // Flow; later context-wise dynamic move of ins & del to $cI
$cI = array('a'=>1, 'abbr'=>1, 'acronym'=>1, 'address'=>1, 'b'=>1, 'bdo'=>1, 'big'=>1, 'caption'=>1, 'cite'=>1, 'code'=>1, 'dfn'=>1, 'dt'=>1, 'em'=>1, 'font'=>1, 'h1'=>1, 'h2'=>1, 'h3'=>1, 'h4'=>1, 'h5'=>1, 'h6'=>1, 'i'=>1, 'kbd'=>1, 'label'=>1, 'legend'=>1, 'p'=>1, 'pre'=>1, 'q'=>1, 'rb'=>1, 'rt'=>1, 's'=>1, 'samp'=>1, 'small'=>1, 'span'=>1, 'strike'=>1, 'strong'=>1, 'sub'=>1, 'sup'=>1, 'tt'=>1, 'u'=>1, 'var'=>1, 'math'=>1, 'mi'=>1, 'mn'=>1, 'mo'=>1, 'mrow'=>1, 'msup'=>1, 'annotation'=>1, 'semantics'=>1, 'menclose'=>1, 'merror'=>1, 'mfenced'=>1, 'mfrac'=>1, 'mglyph'=>1, 'mlabeledtr'=>1, 'mmultiscripts'=>1, 'mover'=>1, 'mpadded'=>1, 'mphantom'=>1, 'mroot'=>1, 'mspace'=>1, 'msqrt'=>1, 'mstyle'=>1, 'msub'=>1, 'msubsup'=>1, 'mtable'=>1, 'mtd'=>1, 'mtext'=>1, 'mtr'=>1, 'munder'=>1, 'munderover'=>1); // Inline
$cN = array('a'=>array('a'=>1), 'button'=>array('a'=>1, 'button'=>1, 'fieldset'=>1, 'form'=>1, 'iframe'=>1, 'input'=>1, 'label'=>1, 'select'=>1, 'textarea'=>1), 'fieldset'=>array('fieldset'=>1), 'form'=>array('form'=>1), 'label'=>array('label'=>1), 'noscript'=>array('script'=>1), 'pre'=>array('big'=>1, 'font'=>1, 'img'=>1, 'object'=>1, 'script'=>1, 'small'=>1, 'sub'=>1, 'sup'=>1), 'rb'=>array('ruby'=>1), 'rt'=>array('ruby'=>1)); // Illegal
$cN2 = array_keys($cN);
$cR = array('blockquote'=>1, 'dir'=>1, 'dl'=>1, 'form'=>1, 'map'=>1, 'menu'=>1, 'noscript'=>1, 'ol'=>1, 'optgroup'=>1, 'rbc'=>1, 'rtc'=>1, 'ruby'=>1, 'select'=>1, 'table'=>1, 'tbody'=>1, 'tfoot'=>1, 'thead'=>1, 'tr'=>1, 'ul'=>1);
$cS = array('colgroup'=>array('col'=>1), 'dir'=>array('li'=>1), 'dl'=>array('dd'=>1, 'dt'=>1), 'menu'=>array('li'=>1), 'ol'=>array('li'=>1), 'optgroup'=>array('option'=>1), 'option'=>array('#pcdata'=>1), 'rbc'=>array('rb'=>1), 'rp'=>array('#pcdata'=>1), 'rtc'=>array('rt'=>1), 'ruby'=>array('rb'=>1, 'rbc'=>1, 'rp'=>1, 'rt'=>1, 'rtc'=>1), 'select'=>array('optgroup'=>1, 'option'=>1), 'script'=>array('#pcdata'=>1), 'table'=>array('caption'=>1, 'col'=>1, 'colgroup'=>1, 'tfoot'=>1, 'tbody'=>1, 'tr'=>1, 'thead'=>1), 'tbody'=>array('tr'=>1), 'tfoot'=>array('tr'=>1), 'textarea'=>array('#pcdata'=>1), 'thead'=>array('tr'=>1), 'tr'=>array('td'=>1, 'th'=>1), 'ul'=>array('li'=>1)); // Specific - immediate parent-child
if($GLOBALS['C']['direct_list_nest']){$cS['ol'] = $cS['ul'] += array('ol'=>1, 'ul'=>1);}
$cO = array('address'=>array('p'=>1), 'applet'=>array('param'=>1), 'blockquote'=>array('script'=>1), 'fieldset'=>array('legend'=>1, '#pcdata'=>1), 'form'=>array('script'=>1), 'map'=>array('area'=>1), 'object'=>array('param'=>1, 'embed'=>1)); // Other
$cT = array('colgroup'=>1, 'dd'=>1, 'dt'=>1, 'li'=>1, 'option'=>1, 'p'=>1, 'td'=>1, 'tfoot'=>1, 'th'=>1, 'thead'=>1, 'tr'=>1); // Omitable closing
// block/inline type; ins & del both type; #pcdata: text
$eB = array('address'=>1, 'blockquote'=>1, 'center'=>1, 'del'=>1, 'dir'=>1, 'dl'=>1, 'div'=>1, 'fieldset'=>1, 'form'=>1, 'ins'=>1, 'h1'=>1, 'h2'=>1, 'h3'=>1, 'h4'=>1, 'h5'=>1, 'h6'=>1, 'hr'=>1, 'isindex'=>1, 'menu'=>1, 'noscript'=>1, 'ol'=>1, 'p'=>1, 'pre'=>1, 'table'=>1, 'ul'=>1);
$eI = array('#pcdata'=>1, 'a'=>1, 'abbr'=>1, 'acronym'=>1, 'applet'=>1, 'b'=>1, 'bdo'=>1, 'big'=>1, 'br'=>1, 'button'=>1, 'cite'=>1, 'code'=>1, 'del'=>1, 'dfn'=>1, 'em'=>1, 'embed'=>1, 'font'=>1, 'i'=>1, 'iframe'=>1, 'img'=>1, 'input'=>1, 'ins'=>1, 'kbd'=>1, 'label'=>1, 'map'=>1, 'object'=>1, 'q'=>1, 'ruby'=>1, 's'=>1, 'samp'=>1, 'select'=>1, 'script'=>1, 'small'=>1, 'span'=>1, 'strike'=>1, 'strong'=>1, 'sub'=>1, 'sup'=>1, 'textarea'=>1, 'tt'=>1, 'u'=>1, 'var'=>1, 'math'=>1, 'mn'=>1, 'mi'=>1, 'mo'=>1, 'mrow' => 1, 'msup'=>1, 'semantics'=>1, 'annotation'=>1, 'menclose'=>1, 'merror'=>1, 'mfenced'=>1, 'mfrac'=>1, 'mglyph'=>1, 'mlabeledtr'=>1, 'mmultiscripts'=>1, 'mover'=>1, 'mpadded'=>1, 'mphantom'=>1, 'mroot'=>1, 'mspace'=>1, 'msqrt'=>1, 'mstyle'=>1, 'msub'=>1, 'msubsup'=>1, 'mtable'=>1, 'mtd'=>1, 'mtext'=>1, 'mtr'=>1, 'munder'=>1, 'munderover'=>1);
$eN = array('a'=>1, 'big'=>1, 'button'=>1, 'fieldset'=>1, 'font'=>1, 'form'=>1, 'iframe'=>1, 'img'=>1, 'input'=>1, 'label'=>1, 'object'=>1, 'ruby'=>1, 'script'=>1, 'select'=>1, 'small'=>1, 'sub'=>1, 'sup'=>1, 'textarea'=>1); // Exclude from specific ele; $cN values
$eO = array('area'=>1, 'caption'=>1, 'col'=>1, 'colgroup'=>1, 'dd'=>1, 'dt'=>1, 'legend'=>1, 'li'=>1, 'optgroup'=>1, 'option'=>1, 'param'=>1, 'rb'=>1, 'rbc'=>1, 'rp'=>1, 'rt'=>1, 'rtc'=>1, 'script'=>1, 'tbody'=>1, 'td'=>1, 'tfoot'=>1, 'thead'=>1, 'th'=>1, 'tr'=>1); // Missing in $eB & $eI
$eF = $eB + $eI;

// $in sets allowed child
$in = ((isset($eF[$in]) && $in != '#pcdata') or isset($eO[$in])) ? $in : 'div';
if(isset($cE[$in])){
 return (!$do ? '' : str_replace(array('<', '>'), array('&lt;', '&gt;'), $t));
}
if(isset($cS[$in])){$inOk = $cS[$in];}
elseif(isset($cI[$in])){$inOk = $eI; $cI['del'] = 1; $cI['ins'] = 1;}
elseif(isset($cF[$in])){$inOk = $eF; unset($cI['del'], $cI['ins']);}
elseif(isset($cB[$in])){$inOk = $eB; unset($cI['del'], $cI['ins']);}
if(isset($cO[$in])){$inOk = $inOk + $cO[$in];}
if(isset($cN[$in])){$inOk = array_diff_assoc($inOk, $cN[$in]);}

$t = explode('<', $t);
$ok = $q = array(); // $q seq list of open non-empty ele
ob_start();

for($i=-1, $ci=count($t); ++$i<$ci;){
 // allowed $ok in parent $p
 if($ql = count($q)){
  $p = array_pop($q);
  $q[] = $p;
  if(isset($cS[$p])){$ok = $cS[$p];}
  elseif(isset($cI[$p])){$ok = $eI; $cI['del'] = 1; $cI['ins'] = 1;}
  elseif(isset($cF[$p])){$ok = $eF; unset($cI['del'], $cI['ins']);}
  elseif(isset($cB[$p])){$ok = $eB; unset($cI['del'], $cI['ins']);}
  if(isset($cO[$p])){$ok = $ok + $cO[$p];}
  if(isset($cN[$p])){$ok = array_diff_assoc($ok, $cN[$p]);}
 }else{$ok = $inOk; unset($cI['del'], $cI['ins']);}
 // bad tags, & ele content
 if(isset($e) && ($do == 1 or (isset($ok['#pcdata']) && ($do == 3 or $do == 5)))){
  echo '&lt;', $s, $e, $a, '&gt;';
 }
 if(isset($x[0])){
  if(strlen(trim($x)) && (($ql && isset($cB[$p])) or (isset($cB[$in]) && !$ql))){
   echo '<div>', $x, '</div>';
  }
  elseif($do < 3 or isset($ok['#pcdata'])){echo $x;}
  elseif(strpos($x, "\x02\x04")){
   foreach(preg_split('`(\x01\x02[^\x01\x02]+\x02\x01)`', $x, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY) as $v){
    echo (substr($v, 0, 2) == "\x01\x02" ? $v : ($do > 4 ? preg_replace('`\S`', '', $v) : ''));
   }
  }elseif($do > 4){echo preg_replace('`\S`', '', $x);}
 }
 // get markup
 if(!preg_match('`^(/?)([a-z1-6]+)([^>]*)>(.*)`sm', $t[$i], $r)){$x = $t[$i]; continue;}
 $s = null; $e = null; $a = null; $x = null; list($all, $s, $e, $a, $x) = $r;
 // close tag
 if($s){
  if(isset($cE[$e]) or !in_array($e, $q)){continue;} // Empty/unopen
  if($p == $e){array_pop($q); echo '</', $e, '>'; unset($e); continue;} // Last open
  $add = ''; // Nesting - close open tags that need to be
  for($j=-1, $cj=count($q); ++$j<$cj;){  
   if(($d = array_pop($q)) == $e){break;}
   else{$add .= "</{$d}>";}
  }
  echo $add, '</', $e, '>'; unset($e); continue;
 }
 // open tag
 // $cB ele needs $eB ele as child
 if(isset($cB[$e]) && strlen(trim($x))){
  $t[$i] = "{$e}{$a}>";
  array_splice($t, $i+1, 0, 'div>'. $x); unset($e, $x); ++$ci; --$i; continue;
 }
 if((($ql && isset($cB[$p])) or (isset($cB[$in]) && !$ql)) && !isset($eB[$e]) && !isset($ok[$e])){
  array_splice($t, $i, 0, 'div>'); unset($e, $x); ++$ci; --$i; continue;
 }
 // if no open ele, $in = parent; mostly immediate parent-child relation should hold
 if(!$ql or !isset($eN[$e]) or !array_intersect($q, $cN2)){
  if(!isset($ok[$e])){
   if($ql && isset($cT[$p])){echo '</', array_pop($q), '>'; unset($e, $x); --$i;}
   continue;
  }
  if(!isset($cE[$e])){$q[] = $e;}
  echo '<', $e, $a, '>'; unset($e); continue;
 }
 // specific parent-child
 if(isset($cS[$p][$e])){
  if(!isset($cE[$e])){$q[] = $e;}
  echo '<', $e, $a, '>'; unset($e); continue;
 }
 // nesting
 $add = '';
 $q2 = array();
 for($k=-1, $kc=count($q); ++$k<$kc;){
  $d = $q[$k];
  $ok2 = array();
  if(isset($cS[$d])){$q2[] = $d; continue;}
  $ok2 = isset($cI[$d]) ? $eI : $eF;
  if(isset($cO[$d])){$ok2 = $ok2 + $cO[$d];}
  if(isset($cN[$d])){$ok2 = array_diff_assoc($ok2, $cN[$d]);}
  if(!isset($ok2[$e])){
   if(!$k && !isset($inOk[$e])){continue 2;}
   $add = "</{$d}>";
   for(;++$k<$kc;){$add = "</{$q[$k]}>{$add}";}
   break;
  }
  else{$q2[] = $d;}
 }
 $q = $q2;
 if(!isset($cE[$e])){$q[] = $e;}
 echo $add, '<', $e, $a, '>'; unset($e); continue;
}

// end
if($ql = count($q)){
 $p = array_pop($q);
 $q[] = $p;
 if(isset($cS[$p])){$ok = $cS[$p];}
 elseif(isset($cI[$p])){$ok = $eI; $cI['del'] = 1; $cI['ins'] = 1;}
 elseif(isset($cF[$p])){$ok = $eF; unset($cI['del'], $cI['ins']);}
 elseif(isset($cB[$p])){$ok = $eB; unset($cI['del'], $cI['ins']);}
 if(isset($cO[$p])){$ok = $ok + $cO[$p];}
 if(isset($cN[$p])){$ok = array_diff_assoc($ok, $cN[$p]);}
}else{$ok = $inOk; unset($cI['del'], $cI['ins']);}
if(isset($e) && ($do == 1 or (isset($ok['#pcdata']) && ($do == 3 or $do == 5)))){
 echo '&lt;', $s, $e, $a, '&gt;';
}
if(isset($x[0])){
 if(strlen(trim($x)) && (($ql && isset($cB[$p])) or (isset($cB[$in]) && !$ql))){
  echo '<div>', $x, '</div>';
 }
 elseif($do < 3 or isset($ok['#pcdata'])){echo $x;}
 elseif(strpos($x, "\x02\x04")){
  foreach(preg_split('`(\x01\x02[^\x01\x02]+\x02\x01)`', $x, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY) as $v){
   echo (substr($v, 0, 2) == "\x01\x02" ? $v : ($do > 4 ? preg_replace('`\S`', '', $v) : ''));
  }
 }elseif($do > 4){echo preg_replace('`\S`', '', $x);}
}
while(!empty($q) && ($e = array_pop($q))){echo '</', $e, '>';}
$o = ob_get_contents();
ob_end_clean();
return $o;
// eof
}

function hl_cmtcd($t){
// comment/CDATA sec handler
$t = $t[0];
global $C;
if(!($v = $C[$n = $t[3] == '-' ? 'comment' : 'cdata'])){return $t;}
if($v == 1){return '';}
if($n == 'comment'){
 if(substr(($t = preg_replace('`--+`', '-', substr($t, 4, -3))), -1) != ' '){$t .= ' ';}
}
else{$t = substr($t, 1, -1);}
$t = $v == 2 ? str_replace(array('&', '<', '>'), array('&amp;', '&lt;', '&gt;'), $t) : $t;
return str_replace(array('&', '<', '>'), array("\x03", "\x04", "\x05"), ($n == 'comment' ? "\x01\x02\x04!--$t--\x05\x02\x01" : "\x01\x01\x04$t\x05\x01\x01"));
// eof
}

function hl_ent($t){
// entitity handler
global $C;
$t = $t[1];
static $U = array('quot'=>1,'amp'=>1,'lt'=>1,'gt'=>1);
static $N = array('fnof'=>'402', 'Alpha'=>'913', 'Beta'=>'914', 'Gamma'=>'915', 'Delta'=>'916', 'Epsilon'=>'917', 'Zeta'=>'918', 'Eta'=>'919', 'Theta'=>'920', 'Iota'=>'921', 'Kappa'=>'922', 'Lambda'=>'923', 'Mu'=>'924', 'Nu'=>'925', 'Xi'=>'926', 'Omicron'=>'927', 'Pi'=>'928', 'Rho'=>'929', 'Sigma'=>'931', 'Tau'=>'932', 'Upsilon'=>'933', 'Phi'=>'934', 'Chi'=>'935', 'Psi'=>'936', 'Omega'=>'937', 'alpha'=>'945', 'beta'=>'946', 'gamma'=>'947', 'delta'=>'948', 'epsilon'=>'949', 'zeta'=>'950', 'eta'=>'951', 'theta'=>'952', 'iota'=>'953', 'kappa'=>'954', 'lambda'=>'955', 'mu'=>'956', 'nu'=>'957', 'xi'=>'958', 'omicron'=>'959', 'pi'=>'960', 'rho'=>'961', 'sigmaf'=>'962', 'sigma'=>'963', 'tau'=>'964', 'upsilon'=>'965', 'phi'=>'966', 'chi'=>'967', 'psi'=>'968', 'omega'=>'969', 'thetasym'=>'977', 'upsih'=>'978', 'piv'=>'982', 'bull'=>'8226', 'hellip'=>'8230', 'prime'=>'8242', 'Prime'=>'8243', 'oline'=>'8254', 'frasl'=>'8260', 'weierp'=>'8472', 'image'=>'8465', 'real'=>'8476', 'trade'=>'8482', 'alefsym'=>'8501', 'larr'=>'8592', 'uarr'=>'8593', 'rarr'=>'8594', 'darr'=>'8595', 'harr'=>'8596', 'crarr'=>'8629', 'lArr'=>'8656', 'uArr'=>'8657', 'rArr'=>'8658', 'dArr'=>'8659', 'hArr'=>'8660', 'forall'=>'8704', 'part'=>'8706', 'exist'=>'8707', 'empty'=>'8709', 'nabla'=>'8711', 'isin'=>'8712', 'notin'=>'8713', 'ni'=>'8715', 'prod'=>'8719', 'sum'=>'8721', 'minus'=>'8722', 'lowast'=>'8727', 'radic'=>'8730', 'prop'=>'8733', 'infin'=>'8734', 'ang'=>'8736', 'and'=>'8743', 'or'=>'8744', 'cap'=>'8745', 'cup'=>'8746', 'int'=>'8747', 'there4'=>'8756', 'sim'=>'8764', 'cong'=>'8773', 'asymp'=>'8776', 'ne'=>'8800', 'equiv'=>'8801', 'le'=>'8804', 'ge'=>'8805', 'sub'=>'8834', 'sup'=>'8835', 'nsub'=>'8836', 'sube'=>'8838', 'supe'=>'8839', 'oplus'=>'8853', 'otimes'=>'8855', 'perp'=>'8869', 'sdot'=>'8901', 'lceil'=>'8968', 'rceil'=>'8969', 'lfloor'=>'8970', 'rfloor'=>'8971', 'lang'=>'9001', 'rang'=>'9002', 'loz'=>'9674', 'spades'=>'9824', 'clubs'=>'9827', 'hearts'=>'9829', 'diams'=>'9830', 'apos'=>'39',  'OElig'=>'338', 'oelig'=>'339', 'Scaron'=>'352', 'scaron'=>'353', 'Yuml'=>'376', 'circ'=>'710', 'tilde'=>'732', 'ensp'=>'8194', 'emsp'=>'8195', 'thinsp'=>'8201', 'zwnj'=>'8204', 'zwj'=>'8205', 'lrm'=>'8206', 'rlm'=>'8207', 'ndash'=>'8211', 'mdash'=>'8212', 'lsquo'=>'8216', 'rsquo'=>'8217', 'sbquo'=>'8218', 'ldquo'=>'8220', 'rdquo'=>'8221', 'bdquo'=>'8222', 'dagger'=>'8224', 'Dagger'=>'8225', 'permil'=>'8240', 'lsaquo'=>'8249', 'rsaquo'=>'8250', 'euro'=>'8364', 'nbsp'=>'160', 'iexcl'=>'161', 'cent'=>'162', 'pound'=>'163', 'curren'=>'164', 'yen'=>'165', 'brvbar'=>'166', 'sect'=>'167', 'uml'=>'168', 'copy'=>'169', 'ordf'=>'170', 'laquo'=>'171', 'not'=>'172', 'shy'=>'173', 'reg'=>'174', 'macr'=>'175', 'deg'=>'176', 'plusmn'=>'177', 'sup2'=>'178', 'sup3'=>'179', 'acute'=>'180', 'micro'=>'181', 'para'=>'182', 'middot'=>'183', 'cedil'=>'184', 'sup1'=>'185', 'ordm'=>'186', 'raquo'=>'187', 'frac14'=>'188', 'frac12'=>'189', 'frac34'=>'190', 'iquest'=>'191', 'Agrave'=>'192', 'Aacute'=>'193', 'Acirc'=>'194', 'Atilde'=>'195', 'Auml'=>'196', 'Aring'=>'197', 'AElig'=>'198', 'Ccedil'=>'199', 'Egrave'=>'200', 'Eacute'=>'201', 'Ecirc'=>'202', 'Euml'=>'203', 'Igrave'=>'204', 'Iacute'=>'205', 'Icirc'=>'206', 'Iuml'=>'207', 'ETH'=>'208', 'Ntilde'=>'209', 'Ograve'=>'210', 'Oacute'=>'211', 'Ocirc'=>'212', 'Otilde'=>'213', 'Ouml'=>'214', 'times'=>'215', 'Oslash'=>'216', 'Ugrave'=>'217', 'Uacute'=>'218', 'Ucirc'=>'219', 'Uuml'=>'220', 'Yacute'=>'221', 'THORN'=>'222', 'szlig'=>'223', 'agrave'=>'224', 'aacute'=>'225', 'acirc'=>'226', 'atilde'=>'227', 'auml'=>'228', 'aring'=>'229', 'aelig'=>'230', 'ccedil'=>'231', 'egrave'=>'232', 'eacute'=>'233', 'ecirc'=>'234', 'euml'=>'235', 'igrave'=>'236', 'iacute'=>'237', 'icirc'=>'238', 'iuml'=>'239', 'eth'=>'240', 'ntilde'=>'241', 'ograve'=>'242', 'oacute'=>'243', 'ocirc'=>'244', 'otilde'=>'245', 'ouml'=>'246', 'divide'=>'247', 'oslash'=>'248', 'ugrave'=>'249', 'uacute'=>'250', 'ucirc'=>'251', 'uuml'=>'252', 'yacute'=>'253', 'thorn'=>'254', 'yuml'=>'255',
	'Alpha' => '&#x0391;',
	'Beta' => '&#x0392;',
	'Epsilon' => '&#x0395;',
	'Zeta' => '&#x0396;',
	'Eta' => '&#x0397;',
	'Iota' => '&#x0399;',
	'Kappa' => '&#x039A;',
	'Mu' => '&#x039C;',
	'Nu' => '&#x039D;',
	'Omicron' => '&#x039F;',
	'Rho' => '&#x03A1;',
	'Tau' => '&#x03A4;',
	'Chi' => '&#x03A7;',
	'epsilon' => '&#x03B5;',
	'zeta' => '&#x03B6;',
	'omicron' => '&#x03BF;',
	'sigmaf' => '&#x03C2;',
	'thetasym' => '&#x03D1;',
	'upsih' => '&#x03D2;',
	'oline' => '&#x203E;',
	'frasl' => '&#x2044;',
	'alefsym' => '&#x2135;',
	'crarr' => '&#x21B5;',
	'empty' => '&#x2205;',
	'amp' => '&#x0026;',
	'lt' => '&#x003C;',
	'zwnj' => '&#x200C;',
	'zwj' => '&#x200D;',
	'lrm' => '&#x200E;',
	'rlm' => '&#x200F;',
	'sbquo' => '&#x201A;',
	'bdquo' => '&#x201E;',
	'lsaquo' => '&#x2039;',
	'rsaquo' => '&#x203A;',
	'euro' => '&#x20AC;',
	'angzarr' => '&#x0237C;',
	'cirmid' => '&#x02AEF;',
	'cudarrl' => '&#x02938;',
	'cudarrr' => '&#x02935;',
	'cularr' => '&#x021B6;',
	'cularrp' => '&#x0293D;',
	'curarr' => '&#x021B7;',
	'curarrm' => '&#x0293C;',
	'Darr' => '&#x021A1;',
	'dArr' => '&#x021D3;',
	'ddarr' => '&#x021CA;',
	'DDotrahd' => '&#x02911;',
	'dfisht' => '&#x0297F;',
	'dHar' => '&#x02965;',
	'dharl' => '&#x021C3;',
	'dharr' => '&#x021C2;',
	'duarr' => '&#x021F5;',
	'duhar' => '&#x0296F;',
	'dzigrarr' => '&#x027FF;',
	'erarr' => '&#x02971;',
	'hArr' => '&#x021D4;',
	'harr' => '&#x02194;',
	'harrcir' => '&#x02948;',
	'harrw' => '&#x021AD;',
	'hoarr' => '&#x021FF;',
	'imof' => '&#x022B7;',
	'lAarr' => '&#x021DA;',
	'Larr' => '&#x0219E;',
	'larrbfs' => '&#x0291F;',
	'larrfs' => '&#x0291D;',
	'larrhk' => '&#x021A9;',
	'larrlp' => '&#x021AB;',
	'larrpl' => '&#x02939;',
	'larrsim' => '&#x02973;',
	'larrtl' => '&#x021A2;',
	'lAtail' => '&#x0291B;',
	'latail' => '&#x02919;',
	'lBarr' => '&#x0290E;',
	'lbarr' => '&#x0290C;',
	'ldca' => '&#x02936;',
	'ldrdhar' => '&#x02967;',
	'ldrushar' => '&#x0294B;',
	'ldsh' => '&#x021B2;',
	'lfisht' => '&#x0297C;',
	'lHar' => '&#x02962;',
	'lhard' => '&#x021BD;',
	'lharu' => '&#x021BC;',
	'lharul' => '&#x0296A;',
	'llarr' => '&#x021C7;',
	'llhard' => '&#x0296B;',
	'loarr' => '&#x021FD;',
	'lrarr' => '&#x021C6;',
	'lrhar' => '&#x021CB;',
	'lrhard' => '&#x0296D;',
	'lsh' => '&#x021B0;',
	'lurdshar' => '&#x0294A;',
	'luruhar' => '&#x02966;',
	'Map' => '&#x02905;',
	'map' => '&#x021A6;',
	'midcir' => '&#x02AF0;',
	'mumap' => '&#x022B8;',
	'nearhk' => '&#x02924;',
	'neArr' => '&#x021D7;',
	'nearr' => '&#x02197;',
	'nesear' => '&#x02928;',
	'nhArr' => '&#x021CE;',
	'nharr' => '&#x021AE;',
	'nlArr' => '&#x021CD;',
	'nlarr' => '&#x0219A;',
	'nrArr' => '&#x021CF;',
	'nrarr' => '&#x0219B;',
	'nrarrc' => '&#x02933;&#x00338;',
	'nrarrw' => '&#x0219D;&#x00338;',
	'nvHarr' => '&#x02904;',
	'nvlArr' => '&#x02902;',
	'nvrArr' => '&#x02903;',
	'nwarhk' => '&#x02923;',
	'nwArr' => '&#x021D6;',
	'nwarr' => '&#x02196;',
	'nwnear' => '&#x02927;',
	'olarr' => '&#x021BA;',
	'orarr' => '&#x021BB;',
	'origof' => '&#x022B6;',
	'rAarr' => '&#x021DB;',
	'Rarr' => '&#x021A0;',
	'rarrap' => '&#x02975;',
	'rarrbfs' => '&#x02920;',
	'rarrc' => '&#x02933;',
	'rarrfs' => '&#x0291E;',
	'rarrhk' => '&#x021AA;',
	'rarrlp' => '&#x021AC;',
	'rarrpl' => '&#x02945;',
	'rarrsim' => '&#x02974;',
	'Rarrtl' => '&#x02916;',
	'rarrtl' => '&#x021A3;',
	'rarrw' => '&#x0219D;',
	'rAtail' => '&#x0291C;',
	'ratail' => '&#x0291A;',
	'RBarr' => '&#x02910;',
	'rBarr' => '&#x0290F;',
	'rbarr' => '&#x0290D;',
	'rdca' => '&#x02937;',
	'rdldhar' => '&#x02969;',
	'rdsh' => '&#x021B3;',
	'rfisht' => '&#x0297D;',
	'rHar' => '&#x02964;',
	'rhard' => '&#x021C1;',
	'rharu' => '&#x021C0;',
	'rharul' => '&#x0296C;',
	'rlarr' => '&#x021C4;',
	'rlhar' => '&#x021CC;',
	'roarr' => '&#x021FE;',
	'rrarr' => '&#x021C9;',
	'rsh' => '&#x021B1;',
	'ruluhar' => '&#x02968;',
	'searhk' => '&#x02925;',
	'seArr' => '&#x021D8;',
	'searr' => '&#x02198;',
	'seswar' => '&#x02929;',
	'simrarr' => '&#x02972;',
	'slarr' => '&#x02190;',
	'srarr' => '&#x02192;',
	'swarhk' => '&#x02926;',
	'swArr' => '&#x021D9;',
	'swarr' => '&#x02199;',
	'swnwar' => '&#x0292A;',
	'Uarr' => '&#x0219F;',
	'uArr' => '&#x021D1;',
	'Uarrocir' => '&#x02949;',
	'udarr' => '&#x021C5;',
	'udhar' => '&#x0296E;',
	'ufisht' => '&#x0297E;',
	'uHar' => '&#x02963;',
	'uharl' => '&#x021BF;',
	'uharr' => '&#x021BE;',
	'uuarr' => '&#x021C8;',
	'vArr' => '&#x021D5;',
	'varr' => '&#x02195;',
	'xhArr' => '&#x027FA;',
	'xharr' => '&#x027F7;',
	'xlArr' => '&#x027F8;',
	'xlarr' => '&#x027F5;',
	'xmap' => '&#x027FC;',
	'xrArr' => '&#x027F9;',
	'xrarr' => '&#x027F6;',
	'zigrarr' => '&#x021DD;',
	'ac' => '&#x0223E;',
	'acE' => '&#x0223E;&#x00333;',
	'amalg' => '&#x02A3F;',
	'barvee' => '&#x022BD;',
	'Barwed' => '&#x02306;',
	'barwed' => '&#x02305;',
	'bsolb' => '&#x029C5;',
	'Cap' => '&#x022D2;',
	'capand' => '&#x02A44;',
	'capbrcup' => '&#x02A49;',
	'capcap' => '&#x02A4B;',
	'capcup' => '&#x02A47;',
	'capdot' => '&#x02A40;',
	'caps' => '&#x02229;&#x0FE00;',
	'ccaps' => '&#x02A4D;',
	'ccups' => '&#x02A4C;',
	'ccupssm' => '&#x02A50;',
	'coprod' => '&#x02210;',
	'Cup' => '&#x022D3;',
	'cupbrcap' => '&#x02A48;',
	'cupcap' => '&#x02A46;',
	'cupcup' => '&#x02A4A;',
	'cupdot' => '&#x0228D;',
	'cupor' => '&#x02A45;',
	'cups' => '&#x0222A;&#x0FE00;',
	'cuvee' => '&#x022CE;',
	'cuwed' => '&#x022CF;',
	'Dagger' => '&#x02021;',
	'dagger' => '&#x02020;',
	'diam' => '&#x022C4;',
	'divonx' => '&#x022C7;',
	'eplus' => '&#x02A71;',
	'hercon' => '&#x022B9;',
	'intcal' => '&#x022BA;',
	'iprod' => '&#x02A3C;',
	'loplus' => '&#x02A2D;',
	'lotimes' => '&#x02A34;',
	'lthree' => '&#x022CB;',
	'ltimes' => '&#x022C9;',
	'midast' => '&#x0002A;',
	'minusb' => '&#x0229F;',
	'minusd' => '&#x02238;',
	'minusdu' => '&#x02A2A;',
	'ncap' => '&#x02A43;',
	'ncup' => '&#x02A42;',
	'oast' => '&#x0229B;',
	'ocir' => '&#x0229A;',
	'odash' => '&#x0229D;',
	'odiv' => '&#x02A38;',
	'odot' => '&#x02299;',
	'odsold' => '&#x029BC;',
	'ofcir' => '&#x029BF;',
	'ogt' => '&#x029C1;',
	'ohbar' => '&#x029B5;',
	'olcir' => '&#x029BE;',
	'olt' => '&#x029C0;',
	'omid' => '&#x029B6;',
	'ominus' => '&#x02296;',
	'opar' => '&#x029B7;',
	'operp' => '&#x029B9;',
	'oplus' => '&#x02295;',
	'osol' => '&#x02298;',
	'Otimes' => '&#x02A37;',
	'otimes' => '&#x02297;',
	'otimesas' => '&#x02A36;',
	'ovbar' => '&#x0233D;',
	'plusacir' => '&#x02A23;',
	'plusb' => '&#x0229E;',
	'pluscir' => '&#x02A22;',
	'plusdo' => '&#x02214;',
	'plusdu' => '&#x02A25;',
	'pluse' => '&#x02A72;',
	'plussim' => '&#x02A26;',
	'plustwo' => '&#x02A27;',
	'prod' => '&#x0220F;',
	'race' => '&#x029DA;',
	'roplus' => '&#x02A2E;',
	'rotimes' => '&#x02A35;',
	'rthree' => '&#x022CC;',
	'rtimes' => '&#x022CA;',
	'sdot' => '&#x022C5;',
	'sdotb' => '&#x022A1;',
	'setmn' => '&#x02216;',
	'simplus' => '&#x02A24;',
	'smashp' => '&#x02A33;',
	'solb' => '&#x029C4;',
	'sqcap' => '&#x02293;',
	'sqcaps' => '&#x02293;&#x0FE00;',
	'sqcup' => '&#x02294;',
	'sqcups' => '&#x02294;&#x0FE00;',
	'ssetmn' => '&#x02216;',
	'sstarf' => '&#x022C6;',
	'subdot' => '&#x02ABD;',
	'sum' => '&#x02211;',
	'supdot' => '&#x02ABE;',
	'timesb' => '&#x022A0;',
	'timesbar' => '&#x02A31;',
	'timesd' => '&#x02A30;',
	'tridot' => '&#x025EC;',
	'triminus' => '&#x02A3A;',
	'triplus' => '&#x02A39;',
	'trisb' => '&#x029CD;',
	'tritime' => '&#x02A3B;',
	'uplus' => '&#x0228E;',
	'veebar' => '&#x022BB;',
	'wedbar' => '&#x02A5F;',
	'wreath' => '&#x02240;',
	'xcap' => '&#x022C2;',
	'xcirc' => '&#x025EF;',
	'xcup' => '&#x022C3;',
	'xdtri' => '&#x025BD;',
	'xodot' => '&#x02A00;',
	'xoplus' => '&#x02A01;',
	'xotime' => '&#x02A02;',
	'xsqcup' => '&#x02A06;',
	'xuplus' => '&#x02A04;',
	'xutri' => '&#x025B3;',
	'xvee' => '&#x022C1;',
	'xwedge' => '&#x022C0;',
	'dlcorn' => '&#x0231E;',
	'drcorn' => '&#x0231F;',
	'gtlPar' => '&#x02995;',
	'langd' => '&#x02991;',
	'lbrke' => '&#x0298B;',
	'lbrksld' => '&#x0298F;',
	'lbrkslu' => '&#x0298D;',
	'lceil' => '&#x02308;',
	'lfloor' => '&#x0230A;',
	'lmoust' => '&#x023B0;',
	'lparlt' => '&#x02993;',
	'ltrPar' => '&#x02996;',
	'rangd' => '&#x02992;',
	'rbrke' => '&#x0298C;',
	'rbrksld' => '&#x0298E;',
	'rbrkslu' => '&#x02990;',
	'rceil' => '&#x02309;',
	'rfloor' => '&#x0230B;',
	'rmoust' => '&#x023B1;',
	'rpargt' => '&#x02994;',
	'ulcorn' => '&#x0231C;',
	'urcorn' => '&#x0231D;',
	'gnap' => '&#x02A8A;',
	'gnE' => '&#x02269;',
	'gne' => '&#x02A88;',
	'gnsim' => '&#x022E7;',
	'gvnE' => '&#x02269;&#x0FE00;',
	'lnap' => '&#x02A89;',
	'lnE' => '&#x02268;',
	'lne' => '&#x02A87;',
	'lnsim' => '&#x022E6;',
	'lvnE' => '&#x02268;&#x0FE00;',
	'nap' => '&#x02249;',
	'napE' => '&#x02A70;&#x00338;',
	'napid' => '&#x0224B;&#x00338;',
	'ncong' => '&#x02247;',
	'ncongdot' => '&#x02A6D;&#x00338;',
	'nequiv' => '&#x02262;',
	'ngE' => '&#x02267;&#x00338;',
	'nge' => '&#x02271;',
	'nges' => '&#x02A7E;&#x00338;',
	'nGg' => '&#x022D9;&#x00338;',
	'ngsim' => '&#x02275;',
	'nGt' => '&#x0226B;&#x020D2;',
	'ngt' => '&#x0226F;',
	'nGtv' => '&#x0226B;&#x00338;',
	'nlE' => '&#x02266;&#x00338;',
	'nle' => '&#x02270;',
	'nles' => '&#x02A7D;&#x00338;',
	'nLl' => '&#x022D8;&#x00338;',
	'nlsim' => '&#x02274;',
	'nLt' => '&#x0226A;&#x020D2;',
	'nlt' => '&#x0226E;',
	'nltri' => '&#x022EA;',
	'nltrie' => '&#x022EC;',
	'nLtv' => '&#x0226A;&#x00338;',
	'nmid' => '&#x02224;',
	'npar' => '&#x02226;',
	'npr' => '&#x02280;',
	'nprcue' => '&#x022E0;',
	'npre' => '&#x02AAF;&#x00338;',
	'nrtri' => '&#x022EB;',
	'nrtrie' => '&#x022ED;',
	'nsc' => '&#x02281;',
	'nsccue' => '&#x022E1;',
	'nsce' => '&#x02AB0;&#x00338;',
	'nsim' => '&#x02241;',
	'nsime' => '&#x02244;',
	'nsmid' => '&#x02224;',
	'nspar' => '&#x02226;',
	'nsqsube' => '&#x022E2;',
	'nsqsupe' => '&#x022E3;',
	'nsub' => '&#x02284;',
	'nsubE' => '&#x02AC5;&#x00338;',
	'nsube' => '&#x02288;',
	'nsup' => '&#x02285;',
	'nsupE' => '&#x02AC6;&#x00338;',
	'nsupe' => '&#x02289;',
	'ntgl' => '&#x02279;',
	'ntlg' => '&#x02278;',
	'nvap' => '&#x0224D;&#x020D2;',
	'nVDash' => '&#x022AF;',
	'nVdash' => '&#x022AE;',
	'nvDash' => '&#x022AD;',
	'nvdash' => '&#x022AC;',
	'nvge' => '&#x02265;&#x020D2;',
	'nvgt' => '&#x0003E;&#x020D2;',
	'nvle' => '&#x02264;&#x020D2;',
	'nvltrie' => '&#x022B4;&#x020D2;',
	'nvrtrie' => '&#x022B5;&#x020D2;',
	'nvsim' => '&#x0223C;&#x020D2;',
	'parsim' => '&#x02AF3;',
	'prnap' => '&#x02AB9;',
	'prnE' => '&#x02AB5;',
	'prnsim' => '&#x022E8;',
	'rnmid' => '&#x02AEE;',
	'scnap' => '&#x02ABA;',
	'scnE' => '&#x02AB6;',
	'scnsim' => '&#x022E9;',
	'simne' => '&#x02246;',
	'solbar' => '&#x0233F;',
	'subnE' => '&#x02ACB;',
	'subne' => '&#x0228A;',
	'supnE' => '&#x02ACC;',
	'supne' => '&#x0228B;',
	'vnsub' => '&#x02282;&#x020D2;',
	'vnsup' => '&#x02283;&#x020D2;',
	'vsubnE' => '&#x02ACB;&#x0FE00;',
	'vsubne' => '&#x0228A;&#x0FE00;',
	'vsupnE' => '&#x02ACC;&#x0FE00;',
	'vsupne' => '&#x0228B;&#x0FE00;',
	'ang' => '&#x02220;',
	'ange' => '&#x029A4;',
	'angmsd' => '&#x02221;',
	'angmsdaa' => '&#x029A8;',
	'angmsdab' => '&#x029A9;',
	'angmsdac' => '&#x029AA;',
	'angmsdad' => '&#x029AB;',
	'angmsdae' => '&#x029AC;',
	'angmsdaf' => '&#x029AD;',
	'angmsdag' => '&#x029AE;',
	'angmsdah' => '&#x029AF;',
	'angrtvb' => '&#x022BE;',
	'angrtvbd' => '&#x0299D;',
	'bbrk' => '&#x023B5;',
	'bbrktbrk' => '&#x023B6;',
	'bemptyv' => '&#x029B0;',
	'beth' => '&#x02136;',
	'boxbox' => '&#x029C9;',
	'bprime' => '&#x02035;',
	'bsemi' => '&#x0204F;',
	'cemptyv' => '&#x029B2;',
	'cirE' => '&#x029C3;',
	'cirscir' => '&#x029C2;',
	'comp' => '&#x02201;',
	'daleth' => '&#x02138;',
	'demptyv' => '&#x029B1;',
	'ell' => '&#x02113;',
	'empty' => '&#x02205;',
	'emptyv' => '&#x02205;',
	'gimel' => '&#x02137;',
	'iiota' => '&#x02129;',
	'image' => '&#x02111;',
	'imath' => '&#x00131;',
	'jmath' => '&#x0006A;',
	'laemptyv' => '&#x029B4;',
	'lltri' => '&#x025FA;',
	'lrtri' => '&#x022BF;',
	'mho' => '&#x02127;',
	'nang' => '&#x02220;&#x020D2;',
	'nexist' => '&#x02204;',
	'oS' => '&#x024C8;',
	'planck' => '&#x0210F;',
	'plankv' => '&#x0210F;',
	'raemptyv' => '&#x029B3;',
	'range' => '&#x029A5;',
	'real' => '&#x0211C;',
	'tbrk' => '&#x023B4;',
	'trpezium' => '&#x0FFFD;',
	'ultri' => '&#x025F8;',
	'urtri' => '&#x025F9;',
	'vzigzag' => '&#x0299A;',
	'weierp' => '&#x02118;',
	'apE' => '&#x02A70;',
	'ape' => '&#x0224A;',
	'apid' => '&#x0224B;',
	'asymp' => '&#x02248;',
	'Barv' => '&#x02AE7;',
	'bcong' => '&#x0224C;',
	'bepsi' => '&#x003F6;',
	'bowtie' => '&#x022C8;',
	'bsim' => '&#x0223D;',
	'bsime' => '&#x022CD;',
	'bsolhsub' => '&#x0005C;&#x02282;',
	'bump' => '&#x0224E;',
	'bumpE' => '&#x02AAE;',
	'bumpe' => '&#x0224F;',
	'cire' => '&#x02257;',
	'Colon' => '&#x02237;',
	'Colone' => '&#x02A74;',
	'colone' => '&#x02254;',
	'congdot' => '&#x02A6D;',
	'csub' => '&#x02ACF;',
	'csube' => '&#x02AD1;',
	'csup' => '&#x02AD0;',
	'csupe' => '&#x02AD2;',
	'cuepr' => '&#x022DE;',
	'cuesc' => '&#x022DF;',
	'Dashv' => '&#x02AE4;',
	'dashv' => '&#x022A3;',
	'easter' => '&#x02A6E;',
	'ecir' => '&#x02256;',
	'ecolon' => '&#x02255;',
	'eDDot' => '&#x02A77;',
	'eDot' => '&#x02251;',
	'efDot' => '&#x02252;',
	'eg' => '&#x02A9A;',
	'egs' => '&#x02A96;',
	'egsdot' => '&#x02A98;',
	'el' => '&#x02A99;',
	'els' => '&#x02A95;',
	'elsdot' => '&#x02A97;',
	'equest' => '&#x0225F;',
	'equivDD' => '&#x02A78;',
	'erDot' => '&#x02253;',
	'esdot' => '&#x02250;',
	'Esim' => '&#x02A73;',
	'esim' => '&#x02242;',
	'fork' => '&#x022D4;',
	'forkv' => '&#x02AD9;',
	'frown' => '&#x02322;',
	'gap' => '&#x02A86;',
	'gE' => '&#x02267;',
	'gEl' => '&#x02A8C;',
	'gel' => '&#x022DB;',
	'ges' => '&#x02A7E;',
	'gescc' => '&#x02AA9;',
	'gesdot' => '&#x02A80;',
	'gesdoto' => '&#x02A82;',
	'gesdotol' => '&#x02A84;',
	'gesl' => '&#x022DB;&#x0FE00;',
	'gesles' => '&#x02A94;',
	'Gg' => '&#x022D9;',
	'gl' => '&#x02277;',
	'gla' => '&#x02AA5;',
	'glE' => '&#x02A92;',
	'glj' => '&#x02AA4;',
	'gsim' => '&#x02273;',
	'gsime' => '&#x02A8E;',
	'gsiml' => '&#x02A90;',
	'Gt' => '&#x0226B;',
	'gtcc' => '&#x02AA7;',
	'gtcir' => '&#x02A7A;',
	'gtdot' => '&#x022D7;',
	'gtquest' => '&#x02A7C;',
	'gtrarr' => '&#x02978;',
	'homtht' => '&#x0223B;',
	'lap' => '&#x02A85;',
	'lat' => '&#x02AAB;',
	'late' => '&#x02AAD;',
	'lates' => '&#x02AAD;&#x0FE00;',
	'lE' => '&#x02266;',
	'lEg' => '&#x02A8B;',
	'leg' => '&#x022DA;',
	'les' => '&#x02A7D;',
	'lescc' => '&#x02AA8;',
	'lesdot' => '&#x02A7F;',
	'lesdoto' => '&#x02A81;',
	'lesdotor' => '&#x02A83;',
	'lesg' => '&#x022DA;&#x0FE00;',
	'lesges' => '&#x02A93;',
	'lg' => '&#x02276;',
	'lgE' => '&#x02A91;',
	'Ll' => '&#x022D8;',
	'lsim' => '&#x02272;',
	'lsime' => '&#x02A8D;',
	'lsimg' => '&#x02A8F;',
	'Lt' => '&#x0226A;',
	'ltcc' => '&#x02AA6;',
	'ltcir' => '&#x02A79;',
	'ltdot' => '&#x022D6;',
	'ltlarr' => '&#x02976;',
	'ltquest' => '&#x02A7B;',
	'ltrie' => '&#x022B4;',
	'mcomma' => '&#x02A29;',
	'mDDot' => '&#x0223A;',
	'mid' => '&#x02223;',
	'mlcp' => '&#x02ADB;',
	'models' => '&#x022A7;',
	'mstpos' => '&#x0223E;',
	'Pr' => '&#x02ABB;',
	'pr' => '&#x0227A;',
	'prap' => '&#x02AB7;',
	'prcue' => '&#x0227C;',
	'prE' => '&#x02AB3;',
	'pre' => '&#x02AAF;',
	'prsim' => '&#x0227E;',
	'prurel' => '&#x022B0;',
	'ratio' => '&#x02236;',
	'rtrie' => '&#x022B5;',
	'rtriltri' => '&#x029CE;',
	'Sc' => '&#x02ABC;',
	'sc' => '&#x0227B;',
	'scap' => '&#x02AB8;',
	'sccue' => '&#x0227D;',
	'scE' => '&#x02AB4;',
	'sce' => '&#x02AB0;',
	'scsim' => '&#x0227F;',
	'sdote' => '&#x02A66;',
	'sfrown' => '&#x02322;',
	'simg' => '&#x02A9E;',
	'simgE' => '&#x02AA0;',
	'siml' => '&#x02A9D;',
	'simlE' => '&#x02A9F;',
	'smid' => '&#x02223;',
	'smile' => '&#x02323;',
	'smt' => '&#x02AAA;',
	'smte' => '&#x02AAC;',
	'smtes' => '&#x02AAC;&#x0FE00;',
	'spar' => '&#x02225;',
	'sqsub' => '&#x0228F;',
	'sqsube' => '&#x02291;',
	'sqsup' => '&#x02290;',
	'sqsupe' => '&#x02292;',
	'ssmile' => '&#x02323;',
	'Sub' => '&#x022D0;',
	'subE' => '&#x02AC5;',
	'subedot' => '&#x02AC3;',
	'submult' => '&#x02AC1;',
	'subplus' => '&#x02ABF;',
	'subrarr' => '&#x02979;',
	'subsim' => '&#x02AC7;',
	'subsub' => '&#x02AD5;',
	'subsup' => '&#x02AD3;',
	'Sup' => '&#x022D1;',
	'supdsub' => '&#x02AD8;',
	'supE' => '&#x02AC6;',
	'supedot' => '&#x02AC4;',
	'suphsol' => '&#x02283;&#x0002F;',
	'suphsub' => '&#x02AD7;',
	'suplarr' => '&#x0297B;',
	'supmult' => '&#x02AC2;',
	'supplus' => '&#x02AC0;',
	'supsim' => '&#x02AC8;',
	'supsub' => '&#x02AD4;',
	'supsup' => '&#x02AD6;',
	'thkap' => '&#x02248;',
	'thksim' => '&#x0223C;',
	'topfork' => '&#x02ADA;',
	'trie' => '&#x0225C;',
	'twixt' => '&#x0226C;',
	'Vbar' => '&#x02AEB;',
	'vBar' => '&#x02AE8;',
	'vBarv' => '&#x02AE9;',
	'VDash' => '&#x022AB;',
	'Vdash' => '&#x022A9;',
	'vDash' => '&#x022A8;',
	'vdash' => '&#x022A2;',
	'Vdashl' => '&#x02AE6;',
	'vltri' => '&#x022B2;',
	'vprop' => '&#x0221D;',
	'vrtri' => '&#x022B3;',
	'Vvdash' => '&#x022AA;',
	'alpha' => '&#x003B1;',
	'beta' => '&#x003B2;',
	'chi' => '&#x003C7;',
	'Delta' => '&#x00394;',
	'delta' => '&#x003B4;',
	'epsi' => '&#x003F5;',
	'epsiv' => '&#x003B5;',
	'eta' => '&#x003B7;',
	'Gamma' => '&#x00393;',
	'gamma' => '&#x003B3;',
	'Gammad' => '&#x003DC;',
	'gammad' => '&#x003DD;',
	'iota' => '&#x003B9;',
	'kappa' => '&#x003BA;',
	'kappav' => '&#x003F0;',
	'Lambda' => '&#x0039B;',
	'lambda' => '&#x003BB;',
	'mu' => '&#x003BC;',
	'nu' => '&#x003BD;',
	'Omega' => '&#x003A9;',
	'omega' => '&#x003C9;',
	'Phi' => '&#x003A6;',
	'phi' => '&#x003D5;',
	'phiv' => '&#x003C6;',
	'Pi' => '&#x003A0;',
	'pi' => '&#x003C0;',
	'piv' => '&#x003D6;',
	'Psi' => '&#x003A8;',
	'psi' => '&#x003C8;',
	'rho' => '&#x003C1;',
	'rhov' => '&#x003F1;',
	'Sigma' => '&#x003A3;',
	'sigma' => '&#x003C3;',
	'sigmav' => '&#x003C2;',
	'tau' => '&#x003C4;',
	'Theta' => '&#x00398;',
	'theta' => '&#x003B8;',
	'thetav' => '&#x003D1;',
	'Upsi' => '&#x003D2;',
	'upsi' => '&#x003C5;',
	'Xi' => '&#x0039E;',
	'xi' => '&#x003BE;',
	'zeta' => '&#x003B6;',
	'Afr' => '&#x1D504;',
	'afr' => '&#x1D51E;',
	'Bfr' => '&#x1D505;',
	'bfr' => '&#x1D51F;',
	'Cfr' => '&#x0212D;',
	'cfr' => '&#x1D520;',
	'Dfr' => '&#x1D507;',
	'dfr' => '&#x1D521;',
	'Efr' => '&#x1D508;',
	'efr' => '&#x1D522;',
	'Ffr' => '&#x1D509;',
	'ffr' => '&#x1D523;',
	'Gfr' => '&#x1D50A;',
	'gfr' => '&#x1D524;',
	'Hfr' => '&#x0210C;',
	'hfr' => '&#x1D525;',
	'Ifr' => '&#x02111;',
	'ifr' => '&#x1D526;',
	'Jfr' => '&#x1D50D;',
	'jfr' => '&#x1D527;',
	'Kfr' => '&#x1D50E;',
	'kfr' => '&#x1D528;',
	'Lfr' => '&#x1D50F;',
	'lfr' => '&#x1D529;',
	'Mfr' => '&#x1D510;',
	'mfr' => '&#x1D52A;',
	'Nfr' => '&#x1D511;',
	'nfr' => '&#x1D52B;',
	'Ofr' => '&#x1D512;',
	'ofr' => '&#x1D52C;',
	'Pfr' => '&#x1D513;',
	'pfr' => '&#x1D52D;',
	'Qfr' => '&#x1D514;',
	'qfr' => '&#x1D52E;',
	'Rfr' => '&#x0211C;',
	'rfr' => '&#x1D52F;',
	'Sfr' => '&#x1D516;',
	'sfr' => '&#x1D530;',
	'Tfr' => '&#x1D517;',
	'tfr' => '&#x1D531;',
	'Ufr' => '&#x1D518;',
	'ufr' => '&#x1D532;',
	'Vfr' => '&#x1D519;',
	'vfr' => '&#x1D533;',
	'Wfr' => '&#x1D51A;',
	'wfr' => '&#x1D534;',
	'Xfr' => '&#x1D51B;',
	'xfr' => '&#x1D535;',
	'Yfr' => '&#x1D51C;',
	'yfr' => '&#x1D536;',
	'Zfr' => '&#x02128;',
	'zfr' => '&#x1D537;',
	'Aopf' => '&#x1D538;',
	'Bopf' => '&#x1D539;',
	'Copf' => '&#x02102;',
	'Dopf' => '&#x1D53B;',
	'Eopf' => '&#x1D53C;',
	'Fopf' => '&#x1D53D;',
	'Gopf' => '&#x1D53E;',
	'Hopf' => '&#x0210D;',
	'Iopf' => '&#x1D540;',
	'Jopf' => '&#x1D541;',
	'Kopf' => '&#x1D542;',
	'Lopf' => '&#x1D543;',
	'Mopf' => '&#x1D544;',
	'Nopf' => '&#x02115;',
	'Oopf' => '&#x1D546;',
	'Popf' => '&#x02119;',
	'Qopf' => '&#x0211A;',
	'Ropf' => '&#x0211D;',
	'Sopf' => '&#x1D54A;',
	'Topf' => '&#x1D54B;',
	'Uopf' => '&#x1D54C;',
	'Vopf' => '&#x1D54D;',
	'Wopf' => '&#x1D54E;',
	'Xopf' => '&#x1D54F;',
	'Yopf' => '&#x1D550;',
	'Zopf' => '&#x02124;',
	'Ascr' => '&#x1D49C;',
	'ascr' => '&#x1D4B6;',
	'Bscr' => '&#x0212C;',
	'bscr' => '&#x1D4B7;',
	'Cscr' => '&#x1D49E;',
	'cscr' => '&#x1D4B8;',
	'Dscr' => '&#x1D49F;',
	'dscr' => '&#x1D4B9;',
	'Escr' => '&#x02130;',
	'escr' => '&#x0212F;',
	'Fscr' => '&#x02131;',
	'fscr' => '&#x1D4BB;',
	'Gscr' => '&#x1D4A2;',
	'gscr' => '&#x0210A;',
	'Hscr' => '&#x0210B;',
	'hscr' => '&#x1D4BD;',
	'Iscr' => '&#x02110;',
	'iscr' => '&#x1D4BE;',
	'Jscr' => '&#x1D4A5;',
	'jscr' => '&#x1D4BF;',
	'Kscr' => '&#x1D4A6;',
	'kscr' => '&#x1D4C0;',
	'Lscr' => '&#x02112;',
	'lscr' => '&#x1D4C1;',
	'Mscr' => '&#x02133;',
	'mscr' => '&#x1D4C2;',
	'Nscr' => '&#x1D4A9;',
	'nscr' => '&#x1D4C3;',
	'Oscr' => '&#x1D4AA;',
	'oscr' => '&#x02134;',
	'Pscr' => '&#x1D4AB;',
	'pscr' => '&#x1D4C5;',
	'Qscr' => '&#x1D4AC;',
	'qscr' => '&#x1D4C6;',
	'Rscr' => '&#x0211B;',
	'rscr' => '&#x1D4C7;',
	'Sscr' => '&#x1D4AE;',
	'sscr' => '&#x1D4C8;',
	'Tscr' => '&#x1D4AF;',
	'tscr' => '&#x1D4C9;',
	'Uscr' => '&#x1D4B0;',
	'uscr' => '&#x1D4CA;',
	'Vscr' => '&#x1D4B1;',
	'vscr' => '&#x1D4CB;',
	'Wscr' => '&#x1D4B2;',
	'wscr' => '&#x1D4CC;',
	'Xscr' => '&#x1D4B3;',
	'xscr' => '&#x1D4CD;',
	'Yscr' => '&#x1D4B4;',
	'yscr' => '&#x1D4CE;',
	'Zscr' => '&#x1D4B5;',
	'zscr' => '&#x1D4CF;',
	'acd' => '&#x0223F;',
	'aleph' => '&#x02135;',
	'And' => '&#x02A53;',
	'and' => '&#x02227;',
	'andand' => '&#x02A55;',
	'andd' => '&#x02A5C;',
	'andslope' => '&#x02A58;',
	'andv' => '&#x02A5A;',
	'angrt' => '&#x0221F;',
	'angsph' => '&#x02222;',
	'angst' => '&#x0212B;',
	'ap' => '&#x02248;',
	'apacir' => '&#x02A6F;',
	'awconint' => '&#x02233;',
	'awint' => '&#x02A11;',
	'becaus' => '&#x02235;',
	'bernou' => '&#x0212C;',
	'bne' => '&#x0003D;&#x020E5;',
	'bnequiv' => '&#x02261;&#x020E5;',
	'bNot' => '&#x02AED;',
	'bnot' => '&#x02310;',
	'bottom' => '&#x022A5;',
	'cap' => '&#x02229;',
	'Cconint' => '&#x02230;',
	'cirfnint' => '&#x02A10;',
	'compfn' => '&#x02218;',
	'cong' => '&#x02245;',
	'Conint' => '&#x0222F;',
	'conint' => '&#x0222E;',
	'ctdot' => '&#x022EF;',
	'cup' => '&#x0222A;',
	'cwconint' => '&#x02232;',
	'cwint' => '&#x02231;',
	'cylcty' => '&#x0232D;',
	'disin' => '&#x022F2;',
	'Dot' => '&#x000A8;',
	'DotDot' => '&#x020DC;',
	'dsol' => '&#x029F6;',
	'dtdot' => '&#x022F1;',
	'dwangle' => '&#x029A6;',
	'elinters' => '&#x0FFFD;',
	'epar' => '&#x022D5;',
	'eparsl' => '&#x029E3;',
	'equiv' => '&#x02261;',
	'eqvparsl' => '&#x029E5;',
	'exist' => '&#x02203;',
	'fltns' => '&#x025B1;',
	'fnof' => '&#x00192;',
	'forall' => '&#x02200;',
	'fpartint' => '&#x02A0D;',
	'ge' => '&#x02265;',
	'hamilt' => '&#x0210B;',
	'iff' => '&#x021D4;',
	'iinfin' => '&#x029DC;',
	'imped' => '&#x001B5;',
	'infin' => '&#x0221E;',
	'infintie' => '&#x029DD;',
	'Int' => '&#x0222C;',
	'int' => '&#x0222B;',
	'intlarhk' => '&#x02A17;',
	'isin' => '&#x02208;',
	'isindot' => '&#x022F5;',
	'isinE' => '&#x022F9;',
	'isins' => '&#x022F4;',
	'isinsv' => '&#x022F3;',
	'isinv' => '&#x02208;',
	'lagran' => '&#x02112;',
	'Lang' => '&#x0300A;',
	'lang' => '&#x02329;',
	'lArr' => '&#x021D0;',
	'lbbrk' => '&#x03014;',
	'le' => '&#x02264;',
	'loang' => '&#x03018;',
	'lobrk' => '&#x0301A;',
	'lopar' => '&#x02985;',
	'lowast' => '&#x02217;',
	'minus' => '&#x02212;',
	'mnplus' => '&#x02213;',
	'nabla' => '&#x02207;',
	'ne' => '&#x02260;',
	'nedot' => '&#x02250;&#x00338;',
	'nhpar' => '&#x02AF2;',
	'ni' => '&#x0220B;',
	'nis' => '&#x022FC;',
	'nisd' => '&#x022FA;',
	'niv' => '&#x0220B;',
	'Not' => '&#x02AEC;',
	'notin' => '&#x02209;',
	'notindot' => '&#x022F5;&#x00338;',
	'notinE' => '&#x022F9;&#x00338;',
	'notinva' => '&#x02209;',
	'notinvb' => '&#x022F7;',
	'notinvc' => '&#x022F6;',
	'notni' => '&#x0220C;',
	'notniva' => '&#x0220C;',
	'notnivb' => '&#x022FE;',
	'notnivc' => '&#x022FD;',
	'nparsl' => '&#x02AFD;&#x020E5;',
	'npart' => '&#x02202;&#x00338;',
	'npolint' => '&#x02A14;',
	'nvinfin' => '&#x029DE;',
	'olcross' => '&#x029BB;',
	'Or' => '&#x02A54;',
	'or' => '&#x02228;',
	'ord' => '&#x02A5D;',
	'order' => '&#x02134;',
	'oror' => '&#x02A56;',
	'orslope' => '&#x02A57;',
	'orv' => '&#x02A5B;',
	'par' => '&#x02225;',
	'parsl' => '&#x02AFD;',
	'part' => '&#x02202;',
	'permil' => '&#x02030;',
	'perp' => '&#x022A5;',
	'pertenk' => '&#x02031;',
	'phmmat' => '&#x02133;',
	'pointint' => '&#x02A15;',
	'Prime' => '&#x02033;',
	'prime' => '&#x02032;',
	'profalar' => '&#x0232E;',
	'profline' => '&#x02312;',
	'profsurf' => '&#x02313;',
	'prop' => '&#x0221D;',
	'qint' => '&#x02A0C;',
	'qprime' => '&#x02057;',
	'quatint' => '&#x02A16;',
	'radic' => '&#x0221A;',
	'Rang' => '&#x0300B;',
	'rang' => '&#x0232A;',
	'rArr' => '&#x021D2;',
	'rbbrk' => '&#x03015;',
	'roang' => '&#x03019;',
	'robrk' => '&#x0301B;',
	'ropar' => '&#x02986;',
	'rppolint' => '&#x02A12;',
	'scpolint' => '&#x02A13;',
	'sim' => '&#x0223C;',
	'simdot' => '&#x02A6A;',
	'sime' => '&#x02243;',
	'smeparsl' => '&#x029E4;',
	'square' => '&#x025A1;',
	'squarf' => '&#x025AA;',
	'strns' => '&#x000AF;',
	'sub' => '&#x02282;',
	'sube' => '&#x02286;',
	'sup' => '&#x02283;',
	'supe' => '&#x02287;',
	'tdot' => '&#x020DB;',
	'there4' => '&#x02234;',
	'tint' => '&#x0222D;',
	'top' => '&#x022A4;',
	'topbot' => '&#x02336;',
	'topcir' => '&#x02AF1;',
	'tprime' => '&#x02034;',
	'utdot' => '&#x022F0;',
	'uwangle' => '&#x029A7;',
	'vangrt' => '&#x0299C;',
	'veeeq' => '&#x0225A;',
	'Verbar' => '&#x02016;',
	'wedgeq' => '&#x02259;',
	'xnis' => '&#x022FB;',
	'boxDL' => '&#x02557;',
	'boxDl' => '&#x02556;',
	'boxdL' => '&#x02555;',
	'boxdl' => '&#x02510;',
	'boxDR' => '&#x02554;',
	'boxDr' => '&#x02553;',
	'boxdR' => '&#x02552;',
	'boxdr' => '&#x0250C;',
	'boxH' => '&#x02550;',
	'boxh' => '&#x02500;',
	'boxHD' => '&#x02566;',
	'boxHd' => '&#x02564;',
	'boxhD' => '&#x02565;',
	'boxhd' => '&#x0252C;',
	'boxHU' => '&#x02569;',
	'boxHu' => '&#x02567;',
	'boxhU' => '&#x02568;',
	'boxhu' => '&#x02534;',
	'boxUL' => '&#x0255D;',
	'boxUl' => '&#x0255C;',
	'boxuL' => '&#x0255B;',
	'boxul' => '&#x02518;',
	'boxUR' => '&#x0255A;',
	'boxUr' => '&#x02559;',
	'boxuR' => '&#x02558;',
	'boxur' => '&#x02514;',
	'boxV' => '&#x02551;',
	'boxv' => '&#x02502;',
	'boxVH' => '&#x0256C;',
	'boxVh' => '&#x0256B;',
	'boxvH' => '&#x0256A;',
	'boxvh' => '&#x0253C;',
	'boxVL' => '&#x02563;',
	'boxVl' => '&#x02562;',
	'boxvL' => '&#x02561;',
	'boxvl' => '&#x02524;',
	'boxVR' => '&#x02560;',
	'boxVr' => '&#x0255F;',
	'boxvR' => '&#x0255E;',
	'boxvr' => '&#x0251C;',
	'Acy' => '&#x00410;',
	'acy' => '&#x00430;',
	'Bcy' => '&#x00411;',
	'bcy' => '&#x00431;',
	'CHcy' => '&#x00427;',
	'chcy' => '&#x00447;',
	'Dcy' => '&#x00414;',
	'dcy' => '&#x00434;',
	'Ecy' => '&#x0042D;',
	'ecy' => '&#x0044D;',
	'Fcy' => '&#x00424;',
	'fcy' => '&#x00444;',
	'Gcy' => '&#x00413;',
	'gcy' => '&#x00433;',
	'HARDcy' => '&#x0042A;',
	'hardcy' => '&#x0044A;',
	'Icy' => '&#x00418;',
	'icy' => '&#x00438;',
	'IEcy' => '&#x00415;',
	'iecy' => '&#x00435;',
	'IOcy' => '&#x00401;',
	'iocy' => '&#x00451;',
	'Jcy' => '&#x00419;',
	'jcy' => '&#x00439;',
	'Kcy' => '&#x0041A;',
	'kcy' => '&#x0043A;',
	'KHcy' => '&#x00425;',
	'khcy' => '&#x00445;',
	'Lcy' => '&#x0041B;',
	'lcy' => '&#x0043B;',
	'Mcy' => '&#x0041C;',
	'mcy' => '&#x0043C;',
	'Ncy' => '&#x0041D;',
	'ncy' => '&#x0043D;',
	'numero' => '&#x02116;',
	'Ocy' => '&#x0041E;',
	'ocy' => '&#x0043E;',
	'Pcy' => '&#x0041F;',
	'pcy' => '&#x0043F;',
	'Rcy' => '&#x00420;',
	'rcy' => '&#x00440;',
	'Scy' => '&#x00421;',
	'scy' => '&#x00441;',
	'SHCHcy' => '&#x00429;',
	'shchcy' => '&#x00449;',
	'SHcy' => '&#x00428;',
	'shcy' => '&#x00448;',
	'SOFTcy' => '&#x0042C;',
	'softcy' => '&#x0044C;',
	'Tcy' => '&#x00422;',
	'tcy' => '&#x00442;',
	'TScy' => '&#x00426;',
	'tscy' => '&#x00446;',
	'Ucy' => '&#x00423;',
	'ucy' => '&#x00443;',
	'Vcy' => '&#x00412;',
	'vcy' => '&#x00432;',
	'YAcy' => '&#x0042F;',
	'yacy' => '&#x0044F;',
	'Ycy' => '&#x0042B;',
	'ycy' => '&#x0044B;',
	'YUcy' => '&#x0042E;',
	'yucy' => '&#x0044E;',
	'Zcy' => '&#x00417;',
	'zcy' => '&#x00437;',
	'ZHcy' => '&#x00416;',
	'zhcy' => '&#x00436;',
	'DJcy' => '&#x00402;',
	'djcy' => '&#x00452;',
	'DScy' => '&#x00405;',
	'dscy' => '&#x00455;',
	'DZcy' => '&#x0040F;',
	'dzcy' => '&#x0045F;',
	'GJcy' => '&#x00403;',
	'gjcy' => '&#x00453;',
	'Iukcy' => '&#x00406;',
	'iukcy' => '&#x00456;',
	'Jsercy' => '&#x00408;',
	'jsercy' => '&#x00458;',
	'Jukcy' => '&#x00404;',
	'jukcy' => '&#x00454;',
	'KJcy' => '&#x0040C;',
	'kjcy' => '&#x0045C;',
	'LJcy' => '&#x00409;',
	'ljcy' => '&#x00459;',
	'NJcy' => '&#x0040A;',
	'njcy' => '&#x0045A;',
	'TSHcy' => '&#x0040B;',
	'tshcy' => '&#x0045B;',
	'Ubrcy' => '&#x0040E;',
	'ubrcy' => '&#x0045E;',
	'YIcy' => '&#x00407;',
	'yicy' => '&#x00457;',
	'acute' => '&#x000B4;',
	'breve' => '&#x002D8;',
	'caron' => '&#x002C7;',
	'cedil' => '&#x000B8;',
	'circ' => '&#x002C6;',
	'dblac' => '&#x002DD;',
	'die' => '&#x000A8;',
	'dot' => '&#x002D9;',
	'grave' => '&#x00060;',
	'macr' => '&#x000AF;',
	'ogon' => '&#x002DB;',
	'ring' => '&#x002DA;',
	'tilde' => '&#x002DC;',
	'uml' => '&#x000A8;',
	'Aacute' => '&#x000C1;',
	'aacute' => '&#x000E1;',
	'Acirc' => '&#x000C2;',
	'acirc' => '&#x000E2;',
	'AElig' => '&#x000C6;',
	'aelig' => '&#x000E6;',
	'Agrave' => '&#x000C0;',
	'agrave' => '&#x000E0;',
	'Aring' => '&#x000C5;',
	'aring' => '&#x000E5;',
	'Atilde' => '&#x000C3;',
	'atilde' => '&#x000E3;',
	'Auml' => '&#x000C4;',
	'auml' => '&#x000E4;',
	'Ccedil' => '&#x000C7;',
	'ccedil' => '&#x000E7;',
	'Eacute' => '&#x000C9;',
	'eacute' => '&#x000E9;',
	'Ecirc' => '&#x000CA;',
	'ecirc' => '&#x000EA;',
	'Egrave' => '&#x000C8;',
	'egrave' => '&#x000E8;',
	'ETH' => '&#x000D0;',
	'eth' => '&#x000F0;',
	'Euml' => '&#x000CB;',
	'euml' => '&#x000EB;',
	'Iacute' => '&#x000CD;',
	'iacute' => '&#x000ED;',
	'Icirc' => '&#x000CE;',
	'icirc' => '&#x000EE;',
	'Igrave' => '&#x000CC;',
	'igrave' => '&#x000EC;',
	'Iuml' => '&#x000CF;',
	'iuml' => '&#x000EF;',
	'Ntilde' => '&#x000D1;',
	'ntilde' => '&#x000F1;',
	'Oacute' => '&#x000D3;',
	'oacute' => '&#x000F3;',
	'Ocirc' => '&#x000D4;',
	'ocirc' => '&#x000F4;',
	'Ograve' => '&#x000D2;',
	'ograve' => '&#x000F2;',
	'Oslash' => '&#x000D8;',
	'oslash' => '&#x000F8;',
	'Otilde' => '&#x000D5;',
	'otilde' => '&#x000F5;',
	'Ouml' => '&#x000D6;',
	'ouml' => '&#x000F6;',
	'szlig' => '&#x000DF;',
	'THORN' => '&#x000DE;',
	'thorn' => '&#x000FE;',
	'Uacute' => '&#x000DA;',
	'uacute' => '&#x000FA;',
	'Ucirc' => '&#x000DB;',
	'ucirc' => '&#x000FB;',
	'Ugrave' => '&#x000D9;',
	'ugrave' => '&#x000F9;',
	'Uuml' => '&#x000DC;',
	'uuml' => '&#x000FC;',
	'Yacute' => '&#x000DD;',
	'yacute' => '&#x000FD;',
	'yuml' => '&#x000FF;',
	'Abreve' => '&#x00102;',
	'abreve' => '&#x00103;',
	'Amacr' => '&#x00100;',
	'amacr' => '&#x00101;',
	'Aogon' => '&#x00104;',
	'aogon' => '&#x00105;',
	'Cacute' => '&#x00106;',
	'cacute' => '&#x00107;',
	'Ccaron' => '&#x0010C;',
	'ccaron' => '&#x0010D;',
	'Ccirc' => '&#x00108;',
	'ccirc' => '&#x00109;',
	'Cdot' => '&#x0010A;',
	'cdot' => '&#x0010B;',
	'Dcaron' => '&#x0010E;',
	'dcaron' => '&#x0010F;',
	'Dstrok' => '&#x00110;',
	'dstrok' => '&#x00111;',
	'Ecaron' => '&#x0011A;',
	'ecaron' => '&#x0011B;',
	'Edot' => '&#x00116;',
	'edot' => '&#x00117;',
	'Emacr' => '&#x00112;',
	'emacr' => '&#x00113;',
	'ENG' => '&#x0014A;',
	'eng' => '&#x0014B;',
	'Eogon' => '&#x00118;',
	'eogon' => '&#x00119;',
	'gacute' => '&#x001F5;',
	'Gbreve' => '&#x0011E;',
	'gbreve' => '&#x0011F;',
	'Gcedil' => '&#x00122;',
	'Gcirc' => '&#x0011C;',
	'gcirc' => '&#x0011D;',
	'Gdot' => '&#x00120;',
	'gdot' => '&#x00121;',
	'Hcirc' => '&#x00124;',
	'hcirc' => '&#x00125;',
	'Hstrok' => '&#x00126;',
	'hstrok' => '&#x00127;',
	'Idot' => '&#x00130;',
	'IJlig' => '&#x00132;',
	'ijlig' => '&#x00133;',
	'Imacr' => '&#x0012A;',
	'imacr' => '&#x0012B;',
	'inodot' => '&#x00131;',
	'Iogon' => '&#x0012E;',
	'iogon' => '&#x0012F;',
	'Itilde' => '&#x00128;',
	'itilde' => '&#x00129;',
	'Jcirc' => '&#x00134;',
	'jcirc' => '&#x00135;',
	'Kcedil' => '&#x00136;',
	'kcedil' => '&#x00137;',
	'kgreen' => '&#x00138;',
	'Lacute' => '&#x00139;',
	'lacute' => '&#x0013A;',
	'Lcaron' => '&#x0013D;',
	'lcaron' => '&#x0013E;',
	'Lcedil' => '&#x0013B;',
	'lcedil' => '&#x0013C;',
	'Lmidot' => '&#x0013F;',
	'lmidot' => '&#x00140;',
	'Lstrok' => '&#x00141;',
	'lstrok' => '&#x00142;',
	'Nacute' => '&#x00143;',
	'nacute' => '&#x00144;',
	'napos' => '&#x00149;',
	'Ncaron' => '&#x00147;',
	'ncaron' => '&#x00148;',
	'Ncedil' => '&#x00145;',
	'ncedil' => '&#x00146;',
	'Odblac' => '&#x00150;',
	'odblac' => '&#x00151;',
	'OElig' => '&#x00152;',
	'oelig' => '&#x00153;',
	'Omacr' => '&#x0014C;',
	'omacr' => '&#x0014D;',
	'Racute' => '&#x00154;',
	'racute' => '&#x00155;',
	'Rcaron' => '&#x00158;',
	'rcaron' => '&#x00159;',
	'Rcedil' => '&#x00156;',
	'rcedil' => '&#x00157;',
	'Sacute' => '&#x0015A;',
	'sacute' => '&#x0015B;',
	'Scaron' => '&#x00160;',
	'scaron' => '&#x00161;',
	'Scedil' => '&#x0015E;',
	'scedil' => '&#x0015F;',
	'Scirc' => '&#x0015C;',
	'scirc' => '&#x0015D;',
	'Tcaron' => '&#x00164;',
	'tcaron' => '&#x00165;',
	'Tcedil' => '&#x00162;',
	'tcedil' => '&#x00163;',
	'Tstrok' => '&#x00166;',
	'tstrok' => '&#x00167;',
	'Ubreve' => '&#x0016C;',
	'ubreve' => '&#x0016D;',
	'Udblac' => '&#x00170;',
	'udblac' => '&#x00171;',
	'Umacr' => '&#x0016A;',
	'umacr' => '&#x0016B;',
	'Uogon' => '&#x00172;',
	'uogon' => '&#x00173;',
	'Uring' => '&#x0016E;',
	'uring' => '&#x0016F;',
	'Utilde' => '&#x00168;',
	'utilde' => '&#x00169;',
	'Wcirc' => '&#x00174;',
	'wcirc' => '&#x00175;',
	'Ycirc' => '&#x00176;',
	'ycirc' => '&#x00177;',
	'Yuml' => '&#x00178;',
	'Zacute' => '&#x00179;',
	'zacute' => '&#x0017A;',
	'Zcaron' => '&#x0017D;',
	'zcaron' => '&#x0017E;',
	'Zdot' => '&#x0017B;',
	'zdot' => '&#x0017C;',
	'apos' => '&#x00027;',
	'ast' => '&#x0002A;',
	'brvbar' => '&#x000A6;',
	'bsol' => '&#x0005C;',
	'cent' => '&#x000A2;',
	'colon' => '&#x0003A;',
	'comma' => '&#x0002C;',
	'commat' => '&#x00040;',
	'copy' => '&#x000A9;',
	'curren' => '&#x000A4;',
	'darr' => '&#x02193;',
	'deg' => '&#x000B0;',
	'divide' => '&#x000F7;',
	'dollar' => '&#x00024;',
	'equals' => '&#x0003D;',
	'excl' => '&#x00021;',
	'frac12' => '&#x000BD;',
	'frac14' => '&#x000BC;',
	'frac18' => '&#x0215B;',
	'frac34' => '&#x000BE;',
	'frac38' => '&#x0215C;',
	'frac58' => '&#x0215D;',
	'frac78' => '&#x0215E;',
	'gt' => '&#x0003E;',
	'half' => '&#x000BD;',
	'horbar' => '&#x02015;',
	'hyphen' => '&#x02010;',
	'iexcl' => '&#x000A1;',
	'iquest' => '&#x000BF;',
	'laquo' => '&#x000AB;',
	'larr' => '&#x02190;',
	'lcub' => '&#x0007B;',
	'ldquo' => '&#x0201C;',
	'lowbar' => '&#x0005F;',
	'lpar' => '&#x00028;',
	'lsqb' => '&#x0005B;',
	'lsquo' => '&#x02018;',
	'micro' => '&#x000B5;',
	'middot' => '&#x000B7;',
	'nbsp' => '&#x000A0;',
	'not' => '&#x000AC;',
	'num' => '&#x00023;',
	'ohm' => '&#x02126;',
	'ordf' => '&#x000AA;',
	'ordm' => '&#x000BA;',
	'para' => '&#x000B6;',
	'percnt' => '&#x00025;',
	'period' => '&#x0002E;',
	'plus' => '&#x0002B;',
	'plusmn' => '&#x000B1;',
	'pound' => '&#x000A3;',
	'quest' => '&#x0003F;',
	'quot' => '&#x00022;',
	'raquo' => '&#x000BB;',
	'rarr' => '&#x02192;',
	'rcub' => '&#x0007D;',
	'rdquo' => '&#x0201D;',
	'reg' => '&#x000AE;',
	'rpar' => '&#x00029;',
	'rsqb' => '&#x0005D;',
	'rsquo' => '&#x02019;',
	'sect' => '&#x000A7;',
	'semi' => '&#x0003B;',
	'shy' => '&#x000AD;',
	'sol' => '&#x0002F;',
	'sung' => '&#x0266A;',
	'sup1' => '&#x000B9;',
	'sup2' => '&#x000B2;',
	'sup3' => '&#x000B3;',
	'times' => '&#x000D7;',
	'trade' => '&#x02122;',
	'uarr' => '&#x02191;',
	'verbar' => '&#x0007C;',
	'yen' => '&#x000A5;',
	'blank' => '&#x02423;',
	'blk12' => '&#x02592;',
	'blk14' => '&#x02591;',
	'blk34' => '&#x02593;',
	'block' => '&#x02588;',
	'bull' => '&#x02022;',
	'caret' => '&#x02041;',
	'check' => '&#x02713;',
	'cir' => '&#x025CB;',
	'clubs' => '&#x02663;',
	'copysr' => '&#x02117;',
	'cross' => '&#x02717;',
	'Dagger' => '&#x02021;',
	'dagger' => '&#x02020;',
	'dash' => '&#x02010;',
	'diams' => '&#x02666;',
	'dlcrop' => '&#x0230D;',
	'drcrop' => '&#x0230C;',
	'dtri' => '&#x025BF;',
	'dtrif' => '&#x025BE;',
	'emsp' => '&#x02003;',
	'emsp13' => '&#x02004;',
	'emsp14' => '&#x02005;',
	'ensp' => '&#x02002;',
	'female' => '&#x02640;',
	'ffilig' => '&#x0FB03;',
	'fflig' => '&#x0FB00;',
	'ffllig' => '&#x0FB04;',
	'filig' => '&#x0FB01;',
	'flat' => '&#x0266D;',
	'fllig' => '&#x0FB02;',
	'frac13' => '&#x02153;',
	'frac15' => '&#x02155;',
	'frac16' => '&#x02159;',
	'frac23' => '&#x02154;',
	'frac25' => '&#x02156;',
	'frac35' => '&#x02157;',
	'frac45' => '&#x02158;',
	'frac56' => '&#x0215A;',
	'hairsp' => '&#x0200A;',
	'hearts' => '&#x02665;',
	'hellip' => '&#x02026;',
	'hybull' => '&#x02043;',
	'incare' => '&#x02105;',
	'ldquor' => '&#x0201E;',
	'lhblk' => '&#x02584;',
	'loz' => '&#x025CA;',
	'lozf' => '&#x029EB;',
	'lsquor' => '&#x0201A;',
	'ltri' => '&#x025C3;',
	'ltrif' => '&#x025C2;',
	'male' => '&#x02642;',
	'malt' => '&#x02720;',
	'marker' => '&#x025AE;',
	'mdash' => '&#x02014;',
	'mldr' => '&#x02026;',
	'natur' => '&#x0266E;',
	'ndash' => '&#x02013;',
	'nldr' => '&#x02025;',
	'numsp' => '&#x02007;',
	'phone' => '&#x0260E;',
	'puncsp' => '&#x02008;',
	'rdquor' => '&#x0201D;',
	'rect' => '&#x025AD;',
	'rsquor' => '&#x02019;',
	'rtri' => '&#x025B9;',
	'rtrif' => '&#x025B8;',
	'rx' => '&#x0211E;',
	'sext' => '&#x02736;',
	'sharp' => '&#x0266F;',
	'spades' => '&#x02660;',
	'squ' => '&#x025A1;',
	'squf' => '&#x025AA;',
	'star' => '&#x02606;',
	'starf' => '&#x02605;',
	'target' => '&#x02316;',
	'telrec' => '&#x02315;',
	'thinsp' => '&#x02009;',
	'uhblk' => '&#x02580;',
	'ulcrop' => '&#x0230F;',
	'urcrop' => '&#x0230E;',
	'utri' => '&#x025B5;',
	'utrif' => '&#x025B4;',
	'vellip' => '&#x022EE;',
	'af' => '&#x02061;',
	'aopf' => '&#x1D552;',
	'asympeq' => '&#x0224D;',
	'bopf' => '&#x1D553;',
	'copf' => '&#x1D554;',
	'Cross' => '&#x02A2F;',
	'DD' => '&#x02145;',
	'dd' => '&#x02146;',
	'dopf' => '&#x1D555;',
	'DownArrowBar' => '&#x02913;',
	'DownBreve' => '&#x00311;',
	'DownLeftRightVector' => '&#x02950;',
	'DownLeftTeeVector' => '&#x0295E;',
	'DownLeftVectorBar' => '&#x02956;',
	'DownRightTeeVector' => '&#x0295F;',
	'DownRightVectorBar' => '&#x02957;',
	'ee' => '&#x02147;',
	'EmptySmallSquare' => '&#x025FB;',
	'EmptyVerySmallSquare' => '&#x025AB;',
	'eopf' => '&#x1D556;',
	'Equal' => '&#x02A75;',
	'FilledSmallSquare' => '&#x025FC;',
	'FilledVerySmallSquare' => '&#x025AA;',
	'fopf' => '&#x1D557;',
	'gopf' => '&#x1D558;',
	'GreaterGreater' => '&#x02AA2;',
	'Hat' => '&#x0005E;',
	'hopf' => '&#x1D559;',
	'HorizontalLine' => '&#x02500;',
	'ic' => '&#x02063;',
	'ii' => '&#x02148;',
	'iopf' => '&#x1D55A;',
	'it' => '&#x02062;',
	'jopf' => '&#x1D55B;',
	'kopf' => '&#x1D55C;',
	'larrb' => '&#x021E4;',
	'LeftDownTeeVector' => '&#x02961;',
	'LeftDownVectorBar' => '&#x02959;',
	'LeftRightVector' => '&#x0294E;',
	'LeftTeeVector' => '&#x0295A;',
	'LeftTriangleBar' => '&#x029CF;',
	'LeftUpDownVector' => '&#x02951;',
	'LeftUpTeeVector' => '&#x02960;',
	'LeftUpVectorBar' => '&#x02958;',
	'LeftVectorBar' => '&#x02952;',
	'LessLess' => '&#x02AA1;',
	'lopf' => '&#x1D55D;',
	'mapstodown' => '&#x021A7;',
	'mapstoleft' => '&#x021A4;',
	'mapstoup' => '&#x021A5;',
	'MediumSpace' => '&#x0205F;',
	'mopf' => '&#x1D55E;',
	'nbump' => '&#x0224E;&#x00338;',
	'nbumpe' => '&#x0224F;&#x00338;',
	'nesim' => '&#x02242;&#x00338;',
	'NewLine' => '&#x0000A;',
	'NoBreak' => '&#x02060;',
	'nopf' => '&#x1D55F;',
	'NotCupCap' => '&#x0226D;',
	'NotHumpEqual' => '&#x0224F;&#x00338;',
	'NotLeftTriangleBar' => '&#x029CF;&#x00338;',
	'NotNestedGreaterGreater' => '&#x02AA2;&#x00338;',
	'NotNestedLessLess' => '&#x02AA1;&#x00338;',
	'NotRightTriangleBar' => '&#x029D0;&#x00338;',
	'NotSquareSubset' => '&#x0228F;&#x00338;',
	'NotSquareSuperset' => '&#x02290;&#x00338;',
	'NotSucceedsTilde' => '&#x0227F;&#x00338;',
	'oopf' => '&#x1D560;',
	'OverBar' => '&#x000AF;',
	'OverBrace' => '&#x0FE37;',
	'OverBracket' => '&#x023B4;',
	'OverParenthesis' => '&#x0FE35;',
	'planckh' => '&#x0210E;',
	'popf' => '&#x1D561;',
	'Product' => '&#x0220F;',
	'qopf' => '&#x1D562;',
	'rarrb' => '&#x021E5;',
	'RightDownTeeVector' => '&#x0295D;',
	'RightDownVectorBar' => '&#x02955;',
	'RightTeeVector' => '&#x0295B;',
	'RightTriangleBar' => '&#x029D0;',
	'RightUpDownVector' => '&#x0294F;',
	'RightUpTeeVector' => '&#x0295C;',
	'RightUpVectorBar' => '&#x02954;',
	'RightVectorBar' => '&#x02953;',
	'ropf' => '&#x1D563;',
	'RoundImplies' => '&#x02970;',
	'RuleDelayed' => '&#x029F4;',
	'sopf' => '&#x1D564;',
	'Tab' => '&#x00009;',
	'ThickSpace' => '&#x02009;&#x0200A;&#x0200A;',
	'topf' => '&#x1D565;',
	'UnderBar' => '&#x00332;',
	'UnderBrace' => '&#x0FE38;',
	'UnderBracket' => '&#x023B5;',
	'UnderParenthesis' => '&#x0FE36;',
	'uopf' => '&#x1D566;',
	'UpArrowBar' => '&#x02912;',
	'Upsilon' => '&#x003A5;',
	'VerticalLine' => '&#x0007C;',
	'VerticalSeparator' => '&#x02758;',
	'vopf' => '&#x1D567;',
	'wopf' => '&#x1D568;',
	'xopf' => '&#x1D569;',
	'yopf' => '&#x1D56A;',
	'ZeroWidthSpace' => '&#x0200B;',
	'zopf' => '&#x1D56B;',
	'angle' => '&#x02220;',
	'ApplyFunction' => '&#x02061;',
	'approx' => '&#x02248;',
	'approxeq' => '&#x0224A;',
	'Assign' => '&#x02254;',
	'backcong' => '&#x0224C;',
	'backepsilon' => '&#x003F6;',
	'backprime' => '&#x02035;',
	'backsim' => '&#x0223D;',
	'backsimeq' => '&#x022CD;',
	'Backslash' => '&#x02216;',
	'barwedge' => '&#x02305;',
	'Because' => '&#x02235;',
	'because' => '&#x02235;',
	'Bernoullis' => '&#x0212C;',
	'between' => '&#x0226C;',
	'bigcap' => '&#x022C2;',
	'bigcirc' => '&#x025EF;',
	'bigcup' => '&#x022C3;',
	'bigodot' => '&#x02A00;',
	'bigoplus' => '&#x02A01;',
	'bigotimes' => '&#x02A02;',
	'bigsqcup' => '&#x02A06;',
	'bigstar' => '&#x02605;',
	'bigtriangledown' => '&#x025BD;',
	'bigtriangleup' => '&#x025B3;',
	'biguplus' => '&#x02A04;',
	'bigvee' => '&#x022C1;',
	'bigwedge' => '&#x022C0;',
	'bkarow' => '&#x0290D;',
	'blacklozenge' => '&#x029EB;',
	'blacksquare' => '&#x025AA;',
	'blacktriangle' => '&#x025B4;',
	'blacktriangledown' => '&#x025BE;',
	'blacktriangleleft' => '&#x025C2;',
	'blacktriangleright' => '&#x025B8;',
	'bot' => '&#x022A5;',
	'boxminus' => '&#x0229F;',
	'boxplus' => '&#x0229E;',
	'boxtimes' => '&#x022A0;',
	'Breve' => '&#x002D8;',
	'bullet' => '&#x02022;',
	'Bumpeq' => '&#x0224E;',
	'bumpeq' => '&#x0224F;',
	'CapitalDifferentialD' => '&#x02145;',
	'Cayleys' => '&#x0212D;',
	'Cedilla' => '&#x000B8;',
	'CenterDot' => '&#x000B7;',
	'centerdot' => '&#x000B7;',
	'checkmark' => '&#x02713;',
	'circeq' => '&#x02257;',
	'circlearrowleft' => '&#x021BA;',
	'circlearrowright' => '&#x021BB;',
	'circledast' => '&#x0229B;',
	'circledcirc' => '&#x0229A;',
	'circleddash' => '&#x0229D;',
	'CircleDot' => '&#x02299;',
	'circledR' => '&#x000AE;',
	'circledS' => '&#x024C8;',
	'CircleMinus' => '&#x02296;',
	'CirclePlus' => '&#x02295;',
	'CircleTimes' => '&#x02297;',
	'ClockwiseContourIntegral' => '&#x02232;',
	'CloseCurlyDoubleQuote' => '&#x0201D;',
	'CloseCurlyQuote' => '&#x02019;',
	'clubsuit' => '&#x02663;',
	'coloneq' => '&#x02254;',
	'complement' => '&#x02201;',
	'complexes' => '&#x02102;',
	'Congruent' => '&#x02261;',
	'ContourIntegral' => '&#x0222E;',
	'Coproduct' => '&#x02210;',
	'CounterClockwiseContourIntegral' => '&#x02233;',
	'CupCap' => '&#x0224D;',
	'curlyeqprec' => '&#x022DE;',
	'curlyeqsucc' => '&#x022DF;',
	'curlyvee' => '&#x022CE;',
	'curlywedge' => '&#x022CF;',
	'curvearrowleft' => '&#x021B6;',
	'curvearrowright' => '&#x021B7;',
	'dbkarow' => '&#x0290F;',
	'ddagger' => '&#x02021;',
	'ddotseq' => '&#x02A77;',
	'Del' => '&#x02207;',
	'DiacriticalAcute' => '&#x000B4;',
	'DiacriticalDot' => '&#x002D9;',
	'DiacriticalDoubleAcute' => '&#x002DD;',
	'DiacriticalGrave' => '&#x00060;',
	'DiacriticalTilde' => '&#x002DC;',
	'Diamond' => '&#x022C4;',
	'diamond' => '&#x022C4;',
	'diamondsuit' => '&#x02666;',
	'DifferentialD' => '&#x02146;',
	'digamma' => '&#x003DD;',
	'div' => '&#x000F7;',
	'divideontimes' => '&#x022C7;',
	'doteq' => '&#x02250;',
	'doteqdot' => '&#x02251;',
	'DotEqual' => '&#x02250;',
	'dotminus' => '&#x02238;',
	'dotplus' => '&#x02214;',
	'dotsquare' => '&#x022A1;',
	'doublebarwedge' => '&#x02306;',
	'DoubleContourIntegral' => '&#x0222F;',
	'DoubleDot' => '&#x000A8;',
	'DoubleDownArrow' => '&#x021D3;',
	'DoubleLeftArrow' => '&#x021D0;',
	'DoubleLeftRightArrow' => '&#x021D4;',
	'DoubleLeftTee' => '&#x02AE4;',
	'DoubleLongLeftArrow' => '&#x027F8;',
	'DoubleLongLeftRightArrow' => '&#x027FA;',
	'DoubleLongRightArrow' => '&#x027F9;',
	'DoubleRightArrow' => '&#x021D2;',
	'DoubleRightTee' => '&#x022A8;',
	'DoubleUpArrow' => '&#x021D1;',
	'DoubleUpDownArrow' => '&#x021D5;',
	'DoubleVerticalBar' => '&#x02225;',
	'DownArrow' => '&#x02193;',
	'Downarrow' => '&#x021D3;',
	'downarrow' => '&#x02193;',
	'DownArrowUpArrow' => '&#x021F5;',
	'downdownarrows' => '&#x021CA;',
	'downharpoonleft' => '&#x021C3;',
	'downharpoonright' => '&#x021C2;',
	'DownLeftVector' => '&#x021BD;',
	'DownRightVector' => '&#x021C1;',
	'DownTee' => '&#x022A4;',
	'DownTeeArrow' => '&#x021A7;',
	'drbkarow' => '&#x02910;',
	'Element' => '&#x02208;',
	'emptyset' => '&#x02205;',
	'eqcirc' => '&#x02256;',
	'eqcolon' => '&#x02255;',
	'eqsim' => '&#x02242;',
	'eqslantgtr' => '&#x02A96;',
	'eqslantless' => '&#x02A95;',
	'EqualTilde' => '&#x02242;',
	'Equilibrium' => '&#x021CC;',
	'Exists' => '&#x02203;',
	'expectation' => '&#x02130;',
	'ExponentialE' => '&#x02147;',
	'exponentiale' => '&#x02147;',
	'fallingdotseq' => '&#x02252;',
	'ForAll' => '&#x02200;',
	'Fouriertrf' => '&#x02131;',
	'geq' => '&#x02265;',
	'geqq' => '&#x02267;',
	'geqslant' => '&#x02A7E;',
	'gg' => '&#x0226B;',
	'ggg' => '&#x022D9;',
	'gnapprox' => '&#x02A8A;',
	'gneq' => '&#x02A88;',
	'gneqq' => '&#x02269;',
	'GreaterEqual' => '&#x02265;',
	'GreaterEqualLess' => '&#x022DB;',
	'GreaterFullEqual' => '&#x02267;',
	'GreaterLess' => '&#x02277;',
	'GreaterSlantEqual' => '&#x02A7E;',
	'GreaterTilde' => '&#x02273;',
	'gtrapprox' => '&#x02A86;',
	'gtrdot' => '&#x022D7;',
	'gtreqless' => '&#x022DB;',
	'gtreqqless' => '&#x02A8C;',
	'gtrless' => '&#x02277;',
	'gtrsim' => '&#x02273;',
	'gvertneqq' => '&#x02269;&#x0FE00;',
	'Hacek' => '&#x002C7;',
	'hbar' => '&#x0210F;',
	'heartsuit' => '&#x02665;',
	'HilbertSpace' => '&#x0210B;',
	'hksearow' => '&#x02925;',
	'hkswarow' => '&#x02926;',
	'hookleftarrow' => '&#x021A9;',
	'hookrightarrow' => '&#x021AA;',
	'hslash' => '&#x0210F;',
	'HumpDownHump' => '&#x0224E;',
	'HumpEqual' => '&#x0224F;',
	'iiiint' => '&#x02A0C;',
	'iiint' => '&#x0222D;',
	'Im' => '&#x02111;',
	'ImaginaryI' => '&#x02148;',
	'imagline' => '&#x02110;',
	'imagpart' => '&#x02111;',
	'Implies' => '&#x021D2;',
	'in' => '&#x02208;',
	'integers' => '&#x02124;',
	'Integral' => '&#x0222B;',
	'intercal' => '&#x022BA;',
	'Intersection' => '&#x022C2;',
	'intprod' => '&#x02A3C;',
	'InvisibleComma' => '&#x02063;',
	'InvisibleTimes' => '&#x02062;',
	'langle' => '&#x02329;',
	'Laplacetrf' => '&#x02112;',
	'lbrace' => '&#x0007B;',
	'lbrack' => '&#x0005B;',
	'LeftAngleBracket' => '&#x02329;',
	'LeftArrow' => '&#x02190;',
	'Leftarrow' => '&#x021D0;',
	'leftarrow' => '&#x02190;',
	'LeftArrowBar' => '&#x021E4;',
	'LeftArrowRightArrow' => '&#x021C6;',
	'leftarrowtail' => '&#x021A2;',
	'LeftCeiling' => '&#x02308;',
	'LeftDoubleBracket' => '&#x0301A;',
	'LeftDownVector' => '&#x021C3;',
	'LeftFloor' => '&#x0230A;',
	'leftharpoondown' => '&#x021BD;',
	'leftharpoonup' => '&#x021BC;',
	'leftleftarrows' => '&#x021C7;',
	'LeftRightArrow' => '&#x02194;',
	'Leftrightarrow' => '&#x021D4;',
	'leftrightarrow' => '&#x02194;',
	'leftrightarrows' => '&#x021C6;',
	'leftrightharpoons' => '&#x021CB;',
	'leftrightsquigarrow' => '&#x021AD;',
	'LeftTee' => '&#x022A3;',
	'LeftTeeArrow' => '&#x021A4;',
	'leftthreetimes' => '&#x022CB;',
	'LeftTriangle' => '&#x022B2;',
	'LeftTriangleEqual' => '&#x022B4;',
	'LeftUpVector' => '&#x021BF;',
	'LeftVector' => '&#x021BC;',
	'leq' => '&#x02264;',
	'leqq' => '&#x02266;',
	'leqslant' => '&#x02A7D;',
	'lessapprox' => '&#x02A85;',
	'lessdot' => '&#x022D6;',
	'lesseqgtr' => '&#x022DA;',
	'lesseqqgtr' => '&#x02A8B;',
	'LessEqualGreater' => '&#x022DA;',
	'LessFullEqual' => '&#x02266;',
	'LessGreater' => '&#x02276;',
	'lessgtr' => '&#x02276;',
	'lesssim' => '&#x02272;',
	'LessSlantEqual' => '&#x02A7D;',
	'LessTilde' => '&#x02272;',
	'll' => '&#x0226A;',
	'llcorner' => '&#x0231E;',
	'Lleftarrow' => '&#x021DA;',
	'lmoustache' => '&#x023B0;',
	'lnapprox' => '&#x02A89;',
	'lneq' => '&#x02A87;',
	'lneqq' => '&#x02268;',
	'LongLeftArrow' => '&#x027F5;',
	'Longleftarrow' => '&#x027F8;',
	'longleftarrow' => '&#x027F5;',
	'LongLeftRightArrow' => '&#x027F7;',
	'Longleftrightarrow' => '&#x027FA;',
	'longleftrightarrow' => '&#x027F7;',
	'longmapsto' => '&#x027FC;',
	'LongRightArrow' => '&#x027F6;',
	'Longrightarrow' => '&#x027F9;',
	'longrightarrow' => '&#x027F6;',
	'looparrowleft' => '&#x021AB;',
	'looparrowright' => '&#x021AC;',
	'LowerLeftArrow' => '&#x02199;',
	'LowerRightArrow' => '&#x02198;',
	'lozenge' => '&#x025CA;',
	'lrcorner' => '&#x0231F;',
	'Lsh' => '&#x021B0;',
	'lvertneqq' => '&#x02268;&#x0FE00;',
	'maltese' => '&#x02720;',
	'mapsto' => '&#x021A6;',
	'measuredangle' => '&#x02221;',
	'Mellintrf' => '&#x02133;',
	'MinusPlus' => '&#x02213;',
	'mp' => '&#x02213;',
	'multimap' => '&#x022B8;',
	'napprox' => '&#x02249;',
	'natural' => '&#x0266E;',
	'naturals' => '&#x02115;',
	'nearrow' => '&#x02197;',
	'NegativeMediumSpace' => '&#x0200B;',
	'NegativeThickSpace' => '&#x0200B;',
	'NegativeThinSpace' => '&#x0200B;',
	'NegativeVeryThinSpace' => '&#x0200B;',
	'NestedGreaterGreater' => '&#x0226B;',
	'NestedLessLess' => '&#x0226A;',
	'nexists' => '&#x02204;',
	'ngeq' => '&#x02271;',
	'ngeqq' => '&#x02267;&#x00338;',
	'ngeqslant' => '&#x02A7E;&#x00338;',
	'ngtr' => '&#x0226F;',
	'nLeftarrow' => '&#x021CD;',
	'nleftarrow' => '&#x0219A;',
	'nLeftrightarrow' => '&#x021CE;',
	'nleftrightarrow' => '&#x021AE;',
	'nleq' => '&#x02270;',
	'nleqq' => '&#x02266;&#x00338;',
	'nleqslant' => '&#x02A7D;&#x00338;',
	'nless' => '&#x0226E;',
	'NonBreakingSpace' => '&#x000A0;',
	'NotCongruent' => '&#x02262;',
	'NotDoubleVerticalBar' => '&#x02226;',
	'NotElement' => '&#x02209;',
	'NotEqual' => '&#x02260;',
	'NotEqualTilde' => '&#x02242;&#x00338;',
	'NotExists' => '&#x02204;',
	'NotGreater' => '&#x0226F;',
	'NotGreaterEqual' => '&#x02271;',
	'NotGreaterFullEqual' => '&#x02266;&#x00338;',
	'NotGreaterGreater' => '&#x0226B;&#x00338;',
	'NotGreaterLess' => '&#x02279;',
	'NotGreaterSlantEqual' => '&#x02A7E;&#x00338;',
	'NotGreaterTilde' => '&#x02275;',
	'NotHumpDownHump' => '&#x0224E;&#x00338;',
	'NotLeftTriangle' => '&#x022EA;',
	'NotLeftTriangleEqual' => '&#x022EC;',
	'NotLess' => '&#x0226E;',
	'NotLessEqual' => '&#x02270;',
	'NotLessGreater' => '&#x02278;',
	'NotLessLess' => '&#x0226A;&#x00338;',
	'NotLessSlantEqual' => '&#x02A7D;&#x00338;',
	'NotLessTilde' => '&#x02274;',
	'NotPrecedes' => '&#x02280;',
	'NotPrecedesEqual' => '&#x02AAF;&#x00338;',
	'NotPrecedesSlantEqual' => '&#x022E0;',
	'NotReverseElement' => '&#x0220C;',
	'NotRightTriangle' => '&#x022EB;',
	'NotRightTriangleEqual' => '&#x022ED;',
	'NotSquareSubsetEqual' => '&#x022E2;',
	'NotSquareSupersetEqual' => '&#x022E3;',
	'NotSubset' => '&#x02282;&#x020D2;',
	'NotSubsetEqual' => '&#x02288;',
	'NotSucceeds' => '&#x02281;',
	'NotSucceedsEqual' => '&#x02AB0;&#x00338;',
	'NotSucceedsSlantEqual' => '&#x022E1;',
	'NotSuperset' => '&#x02283;&#x020D2;',
	'NotSupersetEqual' => '&#x02289;',
	'NotTilde' => '&#x02241;',
	'NotTildeEqual' => '&#x02244;',
	'NotTildeFullEqual' => '&#x02247;',
	'NotTildeTilde' => '&#x02249;',
	'NotVerticalBar' => '&#x02224;',
	'nparallel' => '&#x02226;',
	'nprec' => '&#x02280;',
	'npreceq' => '&#x02AAF;&#x00338;',
	'nRightarrow' => '&#x021CF;',
	'nrightarrow' => '&#x0219B;',
	'nshortmid' => '&#x02224;',
	'nshortparallel' => '&#x02226;',
	'nsimeq' => '&#x02244;',
	'nsubset' => '&#x02282;&#x020D2;',
	'nsubseteq' => '&#x02288;',
	'nsubseteqq' => '&#x02AC5;&#x00338;',
	'nsucc' => '&#x02281;',
	'nsucceq' => '&#x02AB0;&#x00338;',
	'nsupset' => '&#x02283;&#x020D2;',
	'nsupseteq' => '&#x02289;',
	'nsupseteqq' => '&#x02AC6;&#x00338;',
	'ntriangleleft' => '&#x022EA;',
	'ntrianglelefteq' => '&#x022EC;',
	'ntriangleright' => '&#x022EB;',
	'ntrianglerighteq' => '&#x022ED;',
	'nwarrow' => '&#x02196;',
	'oint' => '&#x0222E;',
	'OpenCurlyDoubleQuote' => '&#x0201C;',
	'OpenCurlyQuote' => '&#x02018;',
	'orderof' => '&#x02134;',
	'parallel' => '&#x02225;',
	'PartialD' => '&#x02202;',
	'pitchfork' => '&#x022D4;',
	'PlusMinus' => '&#x000B1;',
	'pm' => '&#x000B1;',
	'Poincareplane' => '&#x0210C;',
	'prec' => '&#x0227A;',
	'precapprox' => '&#x02AB7;',
	'preccurlyeq' => '&#x0227C;',
	'Precedes' => '&#x0227A;',
	'PrecedesEqual' => '&#x02AAF;',
	'PrecedesSlantEqual' => '&#x0227C;',
	'PrecedesTilde' => '&#x0227E;',
	'preceq' => '&#x02AAF;',
	'precnapprox' => '&#x02AB9;',
	'precneqq' => '&#x02AB5;',
	'precnsim' => '&#x022E8;',
	'precsim' => '&#x0227E;',
	'primes' => '&#x02119;',
	'Proportion' => '&#x02237;',
	'Proportional' => '&#x0221D;',
	'propto' => '&#x0221D;',
	'quaternions' => '&#x0210D;',
	'questeq' => '&#x0225F;',
	'rangle' => '&#x0232A;',
	'rationals' => '&#x0211A;',
	'rbrace' => '&#x0007D;',
	'rbrack' => '&#x0005D;',
	'Re' => '&#x0211C;',
	'realine' => '&#x0211B;',
	'realpart' => '&#x0211C;',
	'reals' => '&#x0211D;',
	'ReverseElement' => '&#x0220B;',
	'ReverseEquilibrium' => '&#x021CB;',
	'ReverseUpEquilibrium' => '&#x0296F;',
	'RightAngleBracket' => '&#x0232A;',
	'RightArrow' => '&#x02192;',
	'Rightarrow' => '&#x021D2;',
	'rightarrow' => '&#x02192;',
	'RightArrowBar' => '&#x021E5;',
	'RightArrowLeftArrow' => '&#x021C4;',
	'rightarrowtail' => '&#x021A3;',
	'RightCeiling' => '&#x02309;',
	'RightDoubleBracket' => '&#x0301B;',
	'RightDownVector' => '&#x021C2;',
	'RightFloor' => '&#x0230B;',
	'rightharpoondown' => '&#x021C1;',
	'rightharpoonup' => '&#x021C0;',
	'rightleftarrows' => '&#x021C4;',
	'rightleftharpoons' => '&#x021CC;',
	'rightrightarrows' => '&#x021C9;',
	'rightsquigarrow' => '&#x0219D;',
	'RightTee' => '&#x022A2;',
	'RightTeeArrow' => '&#x021A6;',
	'rightthreetimes' => '&#x022CC;',
	'RightTriangle' => '&#x022B3;',
	'RightTriangleEqual' => '&#x022B5;',
	'RightUpVector' => '&#x021BE;',
	'RightVector' => '&#x021C0;',
	'risingdotseq' => '&#x02253;',
	'rmoustache' => '&#x023B1;',
	'Rrightarrow' => '&#x021DB;',
	'Rsh' => '&#x021B1;',
	'searrow' => '&#x02198;',
	'setminus' => '&#x02216;',
	'ShortDownArrow' => '&#x02193;',
	'ShortLeftArrow' => '&#x02190;',
	'shortmid' => '&#x02223;',
	'shortparallel' => '&#x02225;',
	'ShortRightArrow' => '&#x02192;',
	'ShortUpArrow' => '&#x02191;',
	'simeq' => '&#x02243;',
	'SmallCircle' => '&#x02218;',
	'smallsetminus' => '&#x02216;',
	'spadesuit' => '&#x02660;',
	'Sqrt' => '&#x0221A;',
	'sqsubset' => '&#x0228F;',
	'sqsubseteq' => '&#x02291;',
	'sqsupset' => '&#x02290;',
	'sqsupseteq' => '&#x02292;',
	'Square' => '&#x025A1;',
	'SquareIntersection' => '&#x02293;',
	'SquareSubset' => '&#x0228F;',
	'SquareSubsetEqual' => '&#x02291;',
	'SquareSuperset' => '&#x02290;',
	'SquareSupersetEqual' => '&#x02292;',
	'SquareUnion' => '&#x02294;',
	'Star' => '&#x022C6;',
	'straightepsilon' => '&#x003F5;',
	'straightphi' => '&#x003D5;',
	'Subset' => '&#x022D0;',
	'subset' => '&#x02282;',
	'subseteq' => '&#x02286;',
	'subseteqq' => '&#x02AC5;',
	'SubsetEqual' => '&#x02286;',
	'subsetneq' => '&#x0228A;',
	'subsetneqq' => '&#x02ACB;',
	'succ' => '&#x0227B;',
	'succapprox' => '&#x02AB8;',
	'succcurlyeq' => '&#x0227D;',
	'Succeeds' => '&#x0227B;',
	'SucceedsEqual' => '&#x02AB0;',
	'SucceedsSlantEqual' => '&#x0227D;',
	'SucceedsTilde' => '&#x0227F;',
	'succeq' => '&#x02AB0;',
	'succnapprox' => '&#x02ABA;',
	'succneqq' => '&#x02AB6;',
	'succnsim' => '&#x022E9;',
	'succsim' => '&#x0227F;',
	'SuchThat' => '&#x0220B;',
	'Sum' => '&#x02211;',
	'Superset' => '&#x02283;',
	'SupersetEqual' => '&#x02287;',
	'Supset' => '&#x022D1;',
	'supset' => '&#x02283;',
	'supseteq' => '&#x02287;',
	'supseteqq' => '&#x02AC6;',
	'supsetneq' => '&#x0228B;',
	'supsetneqq' => '&#x02ACC;',
	'swarrow' => '&#x02199;',
	'Therefore' => '&#x02234;',
	'therefore' => '&#x02234;',
	'thickapprox' => '&#x02248;',
	'thicksim' => '&#x0223C;',
	'ThinSpace' => '&#x02009;',
	'Tilde' => '&#x0223C;',
	'TildeEqual' => '&#x02243;',
	'TildeFullEqual' => '&#x02245;',
	'TildeTilde' => '&#x02248;',
	'toea' => '&#x02928;',
	'tosa' => '&#x02929;',
	'triangle' => '&#x025B5;',
	'triangledown' => '&#x025BF;',
	'triangleleft' => '&#x025C3;',
	'trianglelefteq' => '&#x022B4;',
	'triangleq' => '&#x0225C;',
	'triangleright' => '&#x025B9;',
	'trianglerighteq' => '&#x022B5;',
	'TripleDot' => '&#x020DB;',
	'twoheadleftarrow' => '&#x0219E;',
	'twoheadrightarrow' => '&#x021A0;',
	'ulcorner' => '&#x0231C;',
	'Union' => '&#x022C3;',
	'UnionPlus' => '&#x0228E;',
	'UpArrow' => '&#x02191;',
	'Uparrow' => '&#x021D1;',
	'uparrow' => '&#x02191;',
	'UpArrowDownArrow' => '&#x021C5;',
	'UpDownArrow' => '&#x02195;',
	'Updownarrow' => '&#x021D5;',
	'updownarrow' => '&#x02195;',
	'UpEquilibrium' => '&#x0296E;',
	'upharpoonleft' => '&#x021BF;',
	'upharpoonright' => '&#x021BE;',
	'UpperLeftArrow' => '&#x02196;',
	'UpperRightArrow' => '&#x02197;',
	'upsilon' => '&#x003C5;',
	'UpTee' => '&#x022A5;',
	'UpTeeArrow' => '&#x021A5;',
	'upuparrows' => '&#x021C8;',
	'urcorner' => '&#x0231D;',
	'varepsilon' => '&#x003B5;',
	'varkappa' => '&#x003F0;',
	'varnothing' => '&#x02205;',
	'varphi' => '&#x003C6;',
	'varpi' => '&#x003D6;',
	'varpropto' => '&#x0221D;',
	'varrho' => '&#x003F1;',
	'varsigma' => '&#x003C2;',
	'varsubsetneq' => '&#x0228A;&#x0FE00;',
	'varsubsetneqq' => '&#x02ACB;&#x0FE00;',
	'varsupsetneq' => '&#x0228B;&#x0FE00;',
	'varsupsetneqq' => '&#x02ACC;&#x0FE00;',
	'vartheta' => '&#x003D1;',
	'vartriangleleft' => '&#x022B2;',
	'vartriangleright' => '&#x022B3;',
	'Vee' => '&#x022C1;',
	'vee' => '&#x02228;',
	'Vert' => '&#x02016;',
	'vert' => '&#x0007C;',
	'VerticalBar' => '&#x02223;',
	'VerticalTilde' => '&#x02240;',
	'VeryThinSpace' => '&#x0200A;',
	'Wedge' => '&#x022C0;',
	'wedge' => '&#x02227;',
	'wp' => '&#x02118;',
	'wr' => '&#x02240;',
	'zeetrf' => '&#x02128;'
		  );
if($t[0] != '#'){
 return ($C['and_mark'] ? "\x06" : '&'). (isset($U[$t]) ? $t : (isset($N[$t]) ? (!$C['named_entity'] ? '#'. ($C['hexdec_entity'] > 1 ? 'x'. dechex($N[$t]) : $N[$t]) : $t) : 'amp;'. $t)). ';';
}
if(($n = ctype_digit($t = substr($t, 1)) ? intval($t) : hexdec(substr($t, 1))) < 9 or ($n > 13 && $n < 32) or $n == 11 or $n == 12 or ($n > 126 && $n < 160 && $n != 133) or ($n > 55295 && ($n < 57344 or ($n > 64975 && $n < 64992) or $n == 65534 or $n == 65535 or $n > 1114111))){
 return ($C['and_mark'] ? "\x06" : '&'). "amp;#{$t};";
}
return ($C['and_mark'] ? "\x06" : '&'). '#'. (((ctype_digit($t) && $C['hexdec_entity'] < 2) or !$C['hexdec_entity']) ? $n : 'x'. dechex($n)). ';';
// eof
}

function hl_prot($p, $c=null){
// check URL scheme
global $C;
$b = $a = '';
if($c == null){$c = 'style'; $b = $p[1]; $a = $p[3]; $p = trim($p[2]);}
$c = isset($C['schemes'][$c]) ? $C['schemes'][$c] : $C['schemes']['*'];
static $d = 'denied:';
if(isset($c['!']) && substr($p, 0, 7) != $d){$p = "$d$p";}
if(isset($c['*']) or !strcspn($p, '#?;') or (substr($p, 0, 7) == $d)){return "{$b}{$p}{$a}";} // All ok, frag, query, param
if(preg_match('`^([^:?[@!$()*,=/\'\]]+?)(:|&#(58|x3a);|%3a|\\\\0{0,4}3a).`i', $p, $m) && !isset($c[strtolower($m[1])])){ // Denied prot
 return "{$b}{$d}{$p}{$a}";
}
if($C['abs_url']){
 if($C['abs_url'] == -1 && strpos($p, $C['base_url']) === 0){ // Make url rel
  $p = substr($p, strlen($C['base_url']));
 }elseif(empty($m[1])){ // Make URL abs
  if(substr($p, 0, 2) == '//'){$p = substr($C['base_url'], 0, strpos($C['base_url'], ':')+1). $p;}
  elseif($p[0] == '/'){$p = preg_replace('`(^.+?://[^/]+)(.*)`', '$1', $C['base_url']). $p;}
  elseif(strcspn($p, './')){$p = $C['base_url']. $p;}
  else{
   preg_match('`^([a-zA-Z\d\-+.]+://[^/]+)(.*)`', $C['base_url'], $m);
   $p = preg_replace('`(?<=/)\./`', '', $m[2]. $p);
   while(preg_match('`(?<=/)([^/]{3,}|[^/.]+?|\.[^/.]|[^/.]\.)/\.\./`', $p)){
    $p = preg_replace('`(?<=/)([^/]{3,}|[^/.]+?|\.[^/.]|[^/.]\.)/\.\./`', '', $p);
   }
   $p = $m[1]. $p;
  }
 }
}
return "{$b}{$p}{$a}";
// eof
}

function hl_regex($p){
// ?regex
if(empty($p)){return 0;}
if($t = ini_get('track_errors')){$o = isset($php_errormsg) ? $php_errormsg : null;}
else{ini_set('track_errors', 1);}
unset($php_errormsg);
if(($d = ini_get('display_errors'))){ini_set('display_errors', 0);}
preg_match($p, '');
if($d){ini_set('display_errors', 1);}
$r = isset($php_errormsg) ? 0 : 1;
if($t){$php_errormsg = isset($o) ? $o : null;}
else{ini_set('track_errors', 0);}
return $r;
// eof
}

function hl_spec($t){
// final $spec
$s = array();
$t = str_replace(array("\t", "\r", "\n", ' '), '', preg_replace_callback('/"(?>(`.|[^"])*)"/sm', create_function('$m', 'return substr(str_replace(array(";", "|", "~", " ", ",", "/", "(", ")", \'`"\'), array("\x01", "\x02", "\x03", "\x04", "\x05", "\x06", "\x07", "\x08", "\""), $m[0]), 1, -1);'), trim($t))); 
for($i = count(($t = explode(';', $t))); --$i>=0;){
 $w = $t[$i];
 if(empty($w) or ($e = strpos($w, '=')) === false or !strlen(($a =  substr($w, $e+1)))){continue;}
 $y = $n = array();
 foreach(explode(',', $a) as $v){
  if(!preg_match('`^([a-z:\-\*]+)(?:\((.*?)\))?`i', $v, $m)){continue;}
  if(($x = strtolower($m[1])) == '-*'){$n['*'] = 1; continue;}
  if($x[0] == '-'){$n[substr($x, 1)] = 1; continue;}
  if(!isset($m[2])){$y[$x] = 1; continue;}
  foreach(explode('/', $m[2]) as $m){
   if(empty($m) or ($p = strpos($m, '=')) == 0 or $p < 5){$y[$x] = 1; continue;}
   $y[$x][strtolower(substr($m, 0, $p))] = str_replace(array("\x01", "\x02", "\x03", "\x04", "\x05", "\x06", "\x07", "\x08"), array(";", "|", "~", " ", ",", "/", "(", ")"), substr($m, $p+1));
  }
  if(isset($y[$x]['match']) && !hl_regex($y[$x]['match'])){unset($y[$x]['match']);}
  if(isset($y[$x]['nomatch']) && !hl_regex($y[$x]['nomatch'])){unset($y[$x]['nomatch']);}
 }
 if(!count($y) && !count($n)){continue;}
 foreach(explode(',', substr($w, 0, $e)) as $v){
  if(!strlen(($v = strtolower($v)))){continue;}
  if(count($y)){$s[$v] = $y;}
  if(count($n)){$s[$v]['n'] = $n;}
 }
}
return $s;
// eof
}

function hl_tag($t){
// tag/attribute handler
global $C;
$t = $t[0];
// invalid < >
if($t == '< '){return '&lt; ';}
if($t == '>'){return '&gt;';}
if(!preg_match('`^<(/?)([a-zA-Z][a-zA-Z1-6]*)([^>]*?)\s?>$`m', $t, $m)){
 return str_replace(array('<', '>'), array('&lt;', '&gt;'), $t);
}elseif(!isset($C['elements'][($e = strtolower($m[2]))])){
 return (($C['keep_bad']%2) ? str_replace(array('<', '>'), array('&lt;', '&gt;'), $t) : '');
}
// attr string
$a = str_replace(array("\n", "\r", "\t"), ' ', trim($m[3]));
// tag transform
static $eD = array('applet'=>1, 'center'=>1, 'dir'=>1, 'embed'=>1, 'font'=>1, 'isindex'=>1, 'menu'=>1, 's'=>1, 'strike'=>1, 'u'=>1); // Deprecated
if($C['make_tag_strict'] && isset($eD[$e])){
 $trt = hl_tag2($e, $a, $C['make_tag_strict']);
 if(!$e){return (($C['keep_bad']%2) ? str_replace(array('<', '>'), array('&lt;', '&gt;'), $t) : '');}
}
// close tag
static $eE = array('area'=>1, 'br'=>1, 'col'=>1, 'embed'=>1, 'hr'=>1, 'img'=>1, 'input'=>1, 'isindex'=>1, 'param'=>1); // Empty ele
if(!empty($m[1])){
 return (!isset($eE[$e]) ? (empty($C['hook_tag']) ? "</$e>" : $C['hook_tag']($e)) : (($C['keep_bad'])%2 ? str_replace(array('<', '>'), array('&lt;', '&gt;'), $t) : ''));
}

// open tag & attr
static $aN = array('abbr'=>array('td'=>1, 'th'=>1), 'accept-charset'=>array('form'=>1), 'accept'=>array('form'=>1, 'input'=>1), 'accesskey'=>array('a'=>1, 'area'=>1, 'button'=>1, 'input'=>1, 'label'=>1, 'legend'=>1, 'textarea'=>1), 'action'=>array('form'=>1), 'align'=>array('caption'=>1, 'embed'=>1, 'applet'=>1, 'iframe'=>1, 'img'=>1, 'input'=>1, 'object'=>1, 'legend'=>1, 'table'=>1, 'hr'=>1, 'div'=>1, 'h1'=>1, 'h2'=>1, 'h3'=>1, 'h4'=>1, 'h5'=>1, 'h6'=>1, 'p'=>1, 'col'=>1, 'colgroup'=>1, 'tbody'=>1, 'td'=>1, 'tfoot'=>1, 'th'=>1, 'thead'=>1, 'tr'=>1), 'alt'=>array('applet'=>1, 'area'=>1, 'img'=>1, 'input'=>1), 'archive'=>array('applet'=>1, 'object'=>1), 'axis'=>array('td'=>1, 'th'=>1), 'bgcolor'=>array('embed'=>1, 'table'=>1, 'tr'=>1, 'td'=>1, 'th'=>1), 'border'=>array('table'=>1, 'img'=>1, 'object'=>1), 'bordercolor'=>array('table'=>1, 'td'=>1, 'tr'=>1), 'cellpadding'=>array('table'=>1), 'cellspacing'=>array('table'=>1), 'char'=>array('col'=>1, 'colgroup'=>1, 'tbody'=>1, 'td'=>1, 'tfoot'=>1, 'th'=>1, 'thead'=>1, 'tr'=>1), 'charoff'=>array('col'=>1, 'colgroup'=>1, 'tbody'=>1, 'td'=>1, 'tfoot'=>1, 'th'=>1, 'thead'=>1, 'tr'=>1), 'charset'=>array('a'=>1, 'script'=>1), 'checked'=>array('input'=>1), 'cite'=>array('blockquote'=>1, 'q'=>1, 'del'=>1, 'ins'=>1), 'classid'=>array('object'=>1), 'clear'=>array('br'=>1), 'code'=>array('applet'=>1), 'codebase'=>array('object'=>1, 'applet'=>1), 'codetype'=>array('object'=>1), 'color'=>array('font'=>1), 'cols'=>array('textarea'=>1), 'colspan'=>array('td'=>1, 'th'=>1), 'compact'=>array('dir'=>1, 'dl'=>1, 'menu'=>1, 'ol'=>1, 'ul'=>1), 'coords'=>array('area'=>1, 'a'=>1), 'data'=>array('object'=>1), 'datetime'=>array('del'=>1, 'ins'=>1), 'declare'=>array('object'=>1), 'defer'=>array('script'=>1), 'dir'=>array('bdo'=>1), 'disabled'=>array('button'=>1, 'input'=>1, 'optgroup'=>1, 'option'=>1, 'select'=>1, 'textarea'=>1), 'enctype'=>array('form'=>1), 'face'=>array('font'=>1), 'flashvars'=>array('embed'=>1), 'for'=>array('label'=>1), 'frame'=>array('table'=>1), 'frameborder'=>array('iframe'=>1), 'headers'=>array('td'=>1, 'th'=>1), 'height'=>array('embed'=>1, 'iframe'=>1, 'td'=>1, 'th'=>1, 'img'=>1, 'object'=>1, 'applet'=>1), 'href'=>array('a'=>1, 'area'=>1), 'hreflang'=>array('a'=>1), 'hspace'=>array('applet'=>1, 'img'=>1, 'object'=>1), 'ismap'=>array('img'=>1, 'input'=>1), 'label'=>array('option'=>1, 'optgroup'=>1), 'language'=>array('script'=>1), 'longdesc'=>array('img'=>1, 'iframe'=>1), 'marginheight'=>array('iframe'=>1), 'marginwidth'=>array('iframe'=>1), 'maxlength'=>array('input'=>1), 'method'=>array('form'=>1), 'model'=>array('embed'=>1), 'multiple'=>array('select'=>1), 'name'=>array('button'=>1, 'embed'=>1, 'textarea'=>1, 'applet'=>1, 'select'=>1, 'form'=>1, 'iframe'=>1, 'img'=>1, 'a'=>1, 'input'=>1, 'object'=>1, 'map'=>1, 'param'=>1), 'nohref'=>array('area'=>1), 'noshade'=>array('hr'=>1), 'nowrap'=>array('td'=>1, 'th'=>1), 'object'=>array('applet'=>1), 'onblur'=>array('a'=>1, 'area'=>1, 'button'=>1, 'input'=>1, 'label'=>1, 'select'=>1, 'textarea'=>1), 'onchange'=>array('input'=>1, 'select'=>1, 'textarea'=>1), 'onfocus'=>array('a'=>1, 'area'=>1, 'button'=>1, 'input'=>1, 'label'=>1, 'select'=>1, 'textarea'=>1), 'onreset'=>array('form'=>1), 'onselect'=>array('input'=>1, 'textarea'=>1), 'onsubmit'=>array('form'=>1), 'pluginspage'=>array('embed'=>1), 'pluginurl'=>array('embed'=>1), 'prompt'=>array('isindex'=>1), 'readonly'=>array('textarea'=>1, 'input'=>1), 'rel'=>array('a'=>1), 'rev'=>array('a'=>1), 'rows'=>array('textarea'=>1), 'rowspan'=>array('td'=>1, 'th'=>1), 'rules'=>array('table'=>1), 'scope'=>array('td'=>1, 'th'=>1), 'scrolling'=>array('iframe'=>1), 'selected'=>array('option'=>1), 'shape'=>array('area'=>1, 'a'=>1), 'size'=>array('hr'=>1, 'font'=>1, 'input'=>1, 'select'=>1), 'span'=>array('col'=>1, 'colgroup'=>1), 'src'=>array('embed'=>1, 'script'=>1, 'input'=>1, 'iframe'=>1, 'img'=>1), 'standby'=>array('object'=>1), 'start'=>array('ol'=>1), 'summary'=>array('table'=>1), 'tabindex'=>array('a'=>1, 'area'=>1, 'button'=>1, 'input'=>1, 'object'=>1, 'select'=>1, 'textarea'=>1), 'target'=>array('a'=>1, 'area'=>1, 'form'=>1), 'type'=>array('a'=>1, 'embed'=>1, 'object'=>1, 'param'=>1, 'script'=>1, 'input'=>1, 'li'=>1, 'ol'=>1, 'ul'=>1, 'button'=>1), 'usemap'=>array('img'=>1, 'input'=>1, 'object'=>1), 'valign'=>array('col'=>1, 'colgroup'=>1, 'tbody'=>1, 'td'=>1, 'tfoot'=>1, 'th'=>1, 'thead'=>1, 'tr'=>1), 'value'=>array('input'=>1, 'option'=>1, 'param'=>1, 'button'=>1, 'li'=>1), 'valuetype'=>array('param'=>1), 'vspace'=>array('applet'=>1, 'img'=>1, 'object'=>1), 'width'=>array('embed'=>1, 'hr'=>1, 'iframe'=>1, 'img'=>1, 'object'=>1, 'table'=>1, 'td'=>1, 'th'=>1, 'applet'=>1, 'col'=>1, 'colgroup'=>1, 'pre'=>1), 'wmode'=>array('embed'=>1), 'xml:space'=>array('pre'=>1, 'script'=>1, 'style'=>1)); // Ele-specific
static $aNE = array('checked'=>1, 'compact'=>1, 'declare'=>1, 'defer'=>1, 'disabled'=>1, 'ismap'=>1, 'multiple'=>1, 'nohref'=>1, 'noresize'=>1, 'noshade'=>1, 'nowrap'=>1, 'readonly'=>1, 'selected'=>1); // Empty
static $aNP = array('action'=>1, 'cite'=>1, 'classid'=>1, 'codebase'=>1, 'data'=>1, 'href'=>1, 'longdesc'=>1, 'model'=>1, 'pluginspage'=>1, 'pluginurl'=>1, 'usemap'=>1); // Need scheme check; excludes style, on* & src
static $aNU = array('class'=>array('param'=>1, 'script'=>1), 'dir'=>array('applet'=>1, 'bdo'=>1, 'br'=>1, 'iframe'=>1, 'param'=>1, 'script'=>1), 'id'=>array('script'=>1), 'lang'=>array('applet'=>1, 'br'=>1, 'iframe'=>1, 'param'=>1, 'script'=>1), 'xml:lang'=>array('applet'=>1, 'br'=>1, 'iframe'=>1, 'param'=>1, 'script'=>1), 'onclick'=>array('applet'=>1, 'bdo'=>1, 'br'=>1, 'font'=>1, 'iframe'=>1, 'isindex'=>1, 'param'=>1, 'script'=>1), 'ondblclick'=>array('applet'=>1, 'bdo'=>1, 'br'=>1, 'font'=>1, 'iframe'=>1, 'isindex'=>1, 'param'=>1, 'script'=>1), 'onkeydown'=>array('applet'=>1, 'bdo'=>1, 'br'=>1, 'font'=>1, 'iframe'=>1, 'isindex'=>1, 'param'=>1, 'script'=>1), 'onkeypress'=>array('applet'=>1, 'bdo'=>1, 'br'=>1, 'font'=>1, 'iframe'=>1, 'isindex'=>1, 'param'=>1, 'script'=>1), 'onkeyup'=>array('applet'=>1, 'bdo'=>1, 'br'=>1, 'font'=>1, 'iframe'=>1, 'isindex'=>1, 'param'=>1, 'script'=>1), 'onmousedown'=>array('applet'=>1, 'bdo'=>1, 'br'=>1, 'font'=>1, 'iframe'=>1, 'isindex'=>1, 'param'=>1, 'script'=>1), 'onmousemove'=>array('applet'=>1, 'bdo'=>1, 'br'=>1, 'font'=>1, 'iframe'=>1, 'isindex'=>1, 'param'=>1, 'script'=>1), 'onmouseout'=>array('applet'=>1, 'bdo'=>1, 'br'=>1, 'font'=>1, 'iframe'=>1, 'isindex'=>1, 'param'=>1, 'script'=>1), 'onmouseover'=>array('applet'=>1, 'bdo'=>1, 'br'=>1, 'font'=>1, 'iframe'=>1, 'isindex'=>1, 'param'=>1, 'script'=>1), 'onmouseup'=>array('applet'=>1, 'bdo'=>1, 'br'=>1, 'font'=>1, 'iframe'=>1, 'isindex'=>1, 'param'=>1, 'script'=>1), 'style'=>array('param'=>1, 'script'=>1), 'title'=>array('param'=>1, 'script'=>1)); // Univ & exceptions

if($C['lc_std_val']){
 // predef attr vals for $eAL & $aNE ele
 static $aNL = array('all'=>1, 'baseline'=>1, 'bottom'=>1, 'button'=>1, 'center'=>1, 'char'=>1, 'checkbox'=>1, 'circle'=>1, 'col'=>1, 'colgroup'=>1, 'cols'=>1, 'data'=>1, 'default'=>1, 'file'=>1, 'get'=>1, 'groups'=>1, 'hidden'=>1, 'image'=>1, 'justify'=>1, 'left'=>1, 'ltr'=>1, 'middle'=>1, 'none'=>1, 'object'=>1, 'password'=>1, 'poly'=>1, 'post'=>1, 'preserve'=>1, 'radio'=>1, 'rect'=>1, 'ref'=>1, 'reset'=>1, 'right'=>1, 'row'=>1, 'rowgroup'=>1, 'rows'=>1, 'rtl'=>1, 'submit'=>1, 'text'=>1, 'top'=>1);
 static $eAL = array('a'=>1, 'area'=>1, 'bdo'=>1, 'button'=>1, 'col'=>1, 'form'=>1, 'img'=>1, 'input'=>1, 'object'=>1, 'optgroup'=>1, 'option'=>1, 'param'=>1, 'script'=>1, 'select'=>1, 'table'=>1, 'td'=>1, 'tfoot'=>1, 'th'=>1, 'thead'=>1, 'tr'=>1, 'xml:space'=>1);
 $lcase = isset($eAL[$e]) ? 1 : 0;
}

$depTr = 0;
if($C['no_deprecated_attr']){
 // dep attr:applicable ele
 static $aND = array('align'=>array('caption'=>1, 'div'=>1, 'h1'=>1, 'h2'=>1, 'h3'=>1, 'h4'=>1, 'h5'=>1, 'h6'=>1, 'hr'=>1, 'img'=>1, 'input'=>1, 'legend'=>1, 'object'=>1, 'p'=>1, 'table'=>1), 'bgcolor'=>array('table'=>1, 'td'=>1, 'th'=>1, 'tr'=>1), 'border'=>array('img'=>1, 'object'=>1), 'bordercolor'=>array('table'=>1, 'td'=>1, 'tr'=>1), 'clear'=>array('br'=>1), 'compact'=>array('dl'=>1, 'ol'=>1, 'ul'=>1), 'height'=>array('td'=>1, 'th'=>1), 'hspace'=>array('img'=>1, 'object'=>1), 'language'=>array('script'=>1), 'name'=>array('a'=>1, 'form'=>1, 'iframe'=>1, 'img'=>1, 'map'=>1), 'noshade'=>array('hr'=>1), 'nowrap'=>array('td'=>1, 'th'=>1), 'size'=>array('hr'=>1), 'start'=>array('ol'=>1), 'type'=>array('li'=>1, 'ol'=>1, 'ul'=>1), 'value'=>array('li'=>1), 'vspace'=>array('img'=>1, 'object'=>1), 'width'=>array('hr'=>1, 'pre'=>1, 'td'=>1, 'th'=>1));
 static $eAD = array('a'=>1, 'br'=>1, 'caption'=>1, 'div'=>1, 'dl'=>1, 'form'=>1, 'h1'=>1, 'h2'=>1, 'h3'=>1, 'h4'=>1, 'h5'=>1, 'h6'=>1, 'hr'=>1, 'iframe'=>1, 'img'=>1, 'input'=>1, 'legend'=>1, 'li'=>1, 'map'=>1, 'object'=>1, 'ol'=>1, 'p'=>1, 'pre'=>1, 'script'=>1, 'table'=>1, 'td'=>1, 'th'=>1, 'tr'=>1, 'ul'=>1);
 $depTr = isset($eAD[$e]) ? 1 : 0;
}

// attr name-vals
if(strpos($a, "\x01") !== false){$a = preg_replace('`\x01[^\x01]*\x01`', '', $a);} // No comment/CDATA sec
$mode = 0; $a = trim($a, ' /'); $aA = array();
while(strlen($a)){
 $w = 0;
 switch($mode){
  case 0: // Name
   if(preg_match('`^[a-zA-Z][\-a-zA-Z:]+`', $a, $m)){
    $nm = strtolower($m[0]);
    $w = $mode = 1; $a = ltrim(substr_replace($a, '', 0, strlen($m[0])));
   }
  break; case 1:
   if($a[0] == '='){ // =
    $w = 1; $mode = 2; $a = ltrim($a, '= ');
   }else{ // No val
    $w = 1; $mode = 0; $a = ltrim($a);
    $aA[$nm] = '';
   }
  break; case 2: // Val
   if(preg_match('`^((?:"[^"]*")|(?:\'[^\']*\')|(?:\s*[^\s"\']+))(.*)`', $a, $m)){
    $a = ltrim($m[2]); $m = $m[1]; $w = 1; $mode = 0;
    $aA[$nm] = trim(str_replace('<', '&lt;', ($m[0] == '"' or $m[0] == '\'') ? substr($m, 1, -1) : $m));
   }
  break;
 }
 if($w == 0){ // Parse errs, deal with space, " & '
  $a = preg_replace('`^(?:"[^"]*("|$)|\'[^\']*(\'|$)|\S)*\s*`', '', $a);
  $mode = 0;
 }
}
if($mode == 1){$aA[$nm] = '';}

// clean attrs
global $S;
$rl = isset($S[$e]) ? $S[$e] : array();
$a = array(); $nfr = 0;
foreach($aA as $k=>$v){
  if(((isset($C['deny_attribute']['*']) ? isset($C['deny_attribute'][$k]) : !isset($C['deny_attribute'][$k])) && (isset($aN[$k][$e]) or (isset($aNU[$k]) && !isset($aNU[$k][$e]))) && !isset($rl['n'][$k]) && !isset($rl['n']['*'])) or isset($rl[$k])){
  if(isset($aNE[$k])){$v = $k;}
  elseif(!empty($lcase) && (($e != 'button' or $e != 'input') or $k == 'type')){ // Rather loose but ?not cause issues
   $v = (isset($aNL[($v2 = strtolower($v))])) ? $v2 : $v;
  }
  if($k == 'style' && !$C['style_pass']){
   if(false !== strpos($v, '&#')){
    static $sC = array('&#x20;'=>' ', '&#32;'=>' ', '&#x45;'=>'e', '&#69;'=>'e', '&#x65;'=>'e', '&#101;'=>'e', '&#x58;'=>'x', '&#88;'=>'x', '&#x78;'=>'x', '&#120;'=>'x', '&#x50;'=>'p', '&#80;'=>'p', '&#x70;'=>'p', '&#112;'=>'p', '&#x53;'=>'s', '&#83;'=>'s', '&#x73;'=>'s', '&#115;'=>'s', '&#x49;'=>'i', '&#73;'=>'i', '&#x69;'=>'i', '&#105;'=>'i', '&#x4f;'=>'o', '&#79;'=>'o', '&#x6f;'=>'o', '&#111;'=>'o', '&#x4e;'=>'n', '&#78;'=>'n', '&#x6e;'=>'n', '&#110;'=>'n', '&#x55;'=>'u', '&#85;'=>'u', '&#x75;'=>'u', '&#117;'=>'u', '&#x52;'=>'r', '&#82;'=>'r', '&#x72;'=>'r', '&#114;'=>'r', '&#x4c;'=>'l', '&#76;'=>'l', '&#x6c;'=>'l', '&#108;'=>'l', '&#x28;'=>'(', '&#40;'=>'(', '&#x29;'=>')', '&#41;'=>')', '&#x20;'=>':', '&#32;'=>':', '&#x22;'=>'"', '&#34;'=>'"', '&#x27;'=>"'", '&#39;'=>"'", '&#x2f;'=>'/', '&#47;'=>'/', '&#x2a;'=>'*', '&#42;'=>'*', '&#x5c;'=>'\\', '&#92;'=>'\\');
    $v = strtr($v, $sC);
   }
   $v = preg_replace_callback('`(url(?:\()(?: )*(?:\'|"|&(?:quot|apos);)?)(.+?)((?:\'|"|&(?:quot|apos);)?(?: )*(?:\)))`iS', 'hl_prot', $v);
   $v = !$C['css_expression'] ? preg_replace('`expression`i', ' ', preg_replace('`\\\\\S|(/|(%2f))(\*|(%2a))`i', ' ', $v)) : $v;
  }elseif(isset($aNP[$k]) or strpos($k, 'src') !== false or $k[0] == 'o'){
   $v = str_replace("\xad", ' ', (strpos($v, '&') !== false ? str_replace(array('&#xad;', '&#173;', '&shy;'), ' ', $v) : $v));
   $v = hl_prot($v, $k);
   if($k == 'href'){ // X-spam
    if($C['anti_mail_spam'] && strpos($v, 'mailto:') === 0){
     $v = str_replace('@', htmlspecialchars($C['anti_mail_spam']), $v);
    }elseif($C['anti_link_spam']){
     $r1 = $C['anti_link_spam'][1];
     if(!empty($r1) && preg_match($r1, $v)){continue;}
     $r0 = $C['anti_link_spam'][0];
     if(!empty($r0) && preg_match($r0, $v)){
      if(isset($a['rel'])){
       if(!preg_match('`\bnofollow\b`i', $a['rel'])){$a['rel'] .= ' nofollow';}
      }elseif(isset($aA['rel'])){
       if(!preg_match('`\bnofollow\b`i', $aA['rel'])){$nfr = 1;}
      }else{$a['rel'] = 'nofollow';}
     }
    }
   }
  }
  if(isset($rl[$k]) && is_array($rl[$k]) && ($v = hl_attrval($v, $rl[$k])) === 0){continue;}
  $a[$k] = str_replace('"', '&quot;', $v);
 }
}
if($nfr){$a['rel'] = isset($a['rel']) ? $a['rel']. ' nofollow' : 'nofollow';}

// rqd attr
static $eAR = array('area'=>array('alt'=>'area'), 'bdo'=>array('dir'=>'ltr'), 'form'=>array('action'=>''), 'img'=>array('src'=>'', 'alt'=>'image'), 'map'=>array('name'=>''), 'optgroup'=>array('label'=>''), 'param'=>array('name'=>''), 'script'=>array('type'=>'text/javascript'), 'textarea'=>array('rows'=>'10', 'cols'=>'50'));
if(isset($eAR[$e])){
 foreach($eAR[$e] as $k=>$v){
  if(!isset($a[$k])){$a[$k] = isset($v[0]) ? $v : $k;}
 }
}

// depr attrs
if($depTr){
 $c = array();
 foreach($a as $k=>$v){
  if($k == 'style' or !isset($aND[$k][$e])){continue;}
  if($k == 'align'){
   unset($a['align']);
   if($e == 'img' && ($v == 'left' or $v == 'right')){$c[] = 'float: '. $v;}
   elseif(($e == 'div' or $e == 'table') && $v == 'center'){$c[] = 'margin: auto';}
   else{$c[] = 'text-align: '. $v;}
  }elseif($k == 'bgcolor'){
   unset($a['bgcolor']);
   $c[] = 'background-color: '. $v;
  }elseif($k == 'border'){
   unset($a['border']); $c[] = "border: {$v}px";
  }elseif($k == 'bordercolor'){
   unset($a['bordercolor']); $c[] = 'border-color: '. $v;
  }elseif($k == 'clear'){
   unset($a['clear']); $c[] = 'clear: '. ($v != 'all' ? $v : 'both');
  }elseif($k == 'compact'){
   unset($a['compact']); $c[] = 'font-size: 85%';
  }elseif($k == 'height' or $k == 'width'){
   unset($a[$k]); $c[] = $k. ': '. ($v[0] != '*' ? $v. (ctype_digit($v) ? 'px' : '') : 'auto');
  }elseif($k == 'hspace'){
   unset($a['hspace']); $c[] = "margin-left: {$v}px; margin-right: {$v}px";
  }elseif($k == 'language' && !isset($a['type'])){
   unset($a['language']);
   $a['type'] = 'text/'. strtolower($v);
  }elseif($k == 'name'){
   if($C['no_deprecated_attr'] == 2 or ($e != 'a' && $e != 'map')){unset($a['name']);}
   if(!isset($a['id']) && preg_match('`[a-zA-Z][a-zA-Z\d.:_\-]*`', $v)){$a['id'] = $v;}
  }elseif($k == 'noshade'){
   unset($a['noshade']); $c[] = 'border-style: none; border: 0; background-color: gray; color: gray';
  }elseif($k == 'nowrap'){
   unset($a['nowrap']); $c[] = 'white-space: nowrap';
  }elseif($k == 'size'){
   unset($a['size']); $c[] = 'size: '. $v. 'px';
  }elseif($k == 'start' or $k == 'value'){
   unset($a[$k]);
  }elseif($k == 'type'){
   unset($a['type']);
   static $ol_type = array('i'=>'lower-roman', 'I'=>'upper-roman', 'a'=>'lower-latin', 'A'=>'upper-latin', '1'=>'decimal');
   $c[] = 'list-style-type: '. (isset($ol_type[$v]) ? $ol_type[$v] : 'decimal');
  }elseif($k == 'vspace'){
   unset($a['vspace']); $c[] = "margin-top: {$v}px; margin-bottom: {$v}px";
  }
 }
 if(count($c)){
  $c = implode('; ', $c);
  $a['style'] = isset($a['style']) ? rtrim($a['style'], ' ;'). '; '. $c. ';': $c. ';';
 }
}
// unique ID
if($C['unique_ids'] && isset($a['id'])){
 if(!preg_match('`^[A-Za-z][A-Za-z0-9_\-.:]*$`', ($id = $a['id'])) or (isset($GLOBALS['hl_Ids'][$id]) && $C['unique_ids'] == 1)){unset($a['id']);
 }else{
  while(isset($GLOBALS['hl_Ids'][$id])){$id = $C['unique_ids']. $id;}
  $GLOBALS['hl_Ids'][($a['id'] = $id)] = 1;
 }
}
// xml:lang
if($C['xml:lang'] && isset($a['lang'])){
 $a['xml:lang'] = isset($a['xml:lang']) ? $a['xml:lang'] : $a['lang'];
 if($C['xml:lang'] == 2){unset($a['lang']);}
}
// for transformed tag
if(!empty($trt)){
 $a['style'] = isset($a['style']) ? rtrim($a['style'], ' ;'). '; '. $trt : $trt;
}
// return with empty ele /
if(empty($C['hook_tag'])){
 $aA = '';
 foreach($a as $k=>$v){$aA .= " {$k}=\"{$v}\"";}
 return "<{$e}{$aA}". (isset($eE[$e]) ? ' /' : ''). '>';
}
else{return $C['hook_tag']($e, $a);}
// eof
}

function hl_tag2(&$e, &$a, $t=1){
// transform tag
if($e == 'center'){$e = 'div'; return 'text-align: center;';}
if($e == 'dir' or $e == 'menu'){$e = 'ul'; return '';}
if($e == 's' or $e == 'strike'){$e = 'span'; return 'text-decoration: line-through;';}
if($e == 'u'){$e = 'span'; return 'text-decoration: underline;';}
static $fs = array('0'=>'xx-small', '1'=>'xx-small', '2'=>'small', '3'=>'medium', '4'=>'large', '5'=>'x-large', '6'=>'xx-large', '7'=>'300%', '-1'=>'smaller', '-2'=>'60%', '+1'=>'larger', '+2'=>'150%', '+3'=>'200%', '+4'=>'300%');
if($e == 'font'){
 $a2 = '';
 if(preg_match('`face\s*=\s*(\'|")([^=]+?)\\1`i', $a, $m) or preg_match('`face\s*=(\s*)(\S+)`i', $a, $m)){
  $a2 .= ' font-family: '. str_replace('"', '\'', trim($m[2])). ';';
 }
 if(preg_match('`color\s*=\s*(\'|")?(.+?)(\\1|\s|$)`i', $a, $m)){
  $a2 .= ' color: '. trim($m[2]). ';';
 }
 if(preg_match('`size\s*=\s*(\'|")?(.+?)(\\1|\s|$)`i', $a, $m) && isset($fs[($m = trim($m[2]))])){
  $a2 .= ' font-size: '. $fs[$m]. ';';
 }
 $e = 'span'; return ltrim($a2);
}
if($t == 2){$e = 0; return 0;}
return '';
// eof
}

function hl_tidy($t, $w, $p){
// Tidy/compact HTM
if(strpos(' pre,script,textarea', "$p,")){return $t;}
$t = preg_replace('`\s+`', ' ', preg_replace_callback(array('`(<(!\[CDATA\[))(.+?)(\]\]>)`sm', '`(<(!--))(.+?)(-->)`sm', '`(<(pre|script|textarea)[^>]*?>)(.+?)(</\2>)`sm'), create_function('$m', 'return $m[1]. str_replace(array("<", ">", "\n", "\r", "\t", " "), array("\x01", "\x02", "\x03", "\x04", "\x05", "\x07"), $m[3]). $m[4];'), $t));
if(($w = strtolower($w)) == -1){
 return str_replace(array("\x01", "\x02", "\x03", "\x04", "\x05", "\x07"), array('<', '>', "\n", "\r", "\t", ' '), $t);
}
$s = strpos(" $w", 't') ? "\t" : ' ';
$s = preg_match('`\d`', $w, $m) ? str_repeat($s, $m[0]) : str_repeat($s, ($s == "\t" ? 1 : 2));
$N = preg_match('`[ts]([1-9])`', $w, $m) ? $m[1] : 0;
$a = array('br'=>1);
$b = array('button'=>1, 'input'=>1, 'option'=>1, 'param'=>1);
$c = array('caption'=>1, 'dd'=>1, 'dt'=>1, 'h1'=>1, 'h2'=>1, 'h3'=>1, 'h4'=>1, 'h5'=>1, 'h6'=>1, 'isindex'=>1, 'label'=>1, 'legend'=>1, 'li'=>1, 'object'=>1, 'p'=>1, 'pre'=>1, 'td'=>1, 'textarea'=>1, 'th'=>1, 'math'=>1, 'mn'=>1, 'mo'=>1, 'mn'=>1, 'mrow'=>1, 'msup'=>1, 'semantics'=>1, 'annotation'=>1, 'menclose'=>1, 'merror'=>1, 'mfenced'=>1, 'mfrac'=>1, 'mglyph'=>1, 'mlabeledtr'=>1, 'mmultiscripts'=>1, 'mover'=>1, 'mpadded'=>1, 'mphantom'=>1, 'mroot'=>1, 'mspace'=>1, 'msqrt'=>1, 'mstyle'=>1, 'msub'=>1, 'msubsup'=>1, 'mtable'=>1, 'mtd'=>1, 'mtext'=>1, 'mtr'=>1, 'munder'=>1, 'munderover'=>1);
$d = array('address'=>1, 'blockquote'=>1, 'center'=>1, 'colgroup'=>1, 'dir'=>1, 'div'=>1, 'dl'=>1, 'fieldset'=>1, 'form'=>1, 'hr'=>1, 'iframe'=>1, 'map'=>1, 'menu'=>1, 'noscript'=>1, 'ol'=>1, 'optgroup'=>1, 'rbc'=>1, 'rtc'=>1, 'ruby'=>1, 'script'=>1, 'select'=>1, 'table'=>1, 'tbody'=>1, 'tfoot'=>1, 'thead'=>1, 'tr'=>1, 'ul'=>1, 'math'=>1);
$T = explode('<', $t);
$X = 1;
while($X){
 $n = $N;
 $t = $T;
 ob_start();
 if(isset($d[$p])){echo str_repeat($s, ++$n);}
 echo ltrim(array_shift($t));
 for($i=-1, $j=count($t); ++$i<$j;){
  $r = ''; list($e, $r) = explode('>', $t[$i]);
  $x = $e[0] == '/' ? 0 : (substr($e, -1) == '/' ? 1 : ($e[0] != '!' ? 2 : -1));
  $y = !$x ? ltrim($e, '/') : ($x > 0 ? substr($e, 0, strcspn($e, ' ')) : 0);
  $e = "<$e>"; 
  if(isset($d[$y])){
   if(!$x){
    if($n){echo "\n", str_repeat($s, --$n), "$e\n", str_repeat($s, $n);}
    else{++$N; ob_end_clean(); continue 2;}
   }
   else{echo "\n", str_repeat($s, $n), "$e\n", str_repeat($s, ($x != 1 ? ++$n : $n));}
   echo $r; continue;
  }
  $f = "\n". str_repeat($s, $n);
  if(isset($c[$y])){
   if(!$x){echo $e, $f, $r;}
   else{echo $f, $e, $r;}
  }elseif(isset($b[$y])){echo $f, $e, $r;
  }elseif(isset($a[$y])){echo $e, $f, $r;
  }elseif(!$y){echo $f, $e, $f, $r;
  }else{echo $e, $r;}
 }
 $X = 0;
}
$t = str_replace(array("\n ", " \n"), "\n", preg_replace('`[\n]\s*?[\n]+`', "\n", ob_get_contents()));
ob_end_clean();
if(($l = strpos(" $w", 'r') ? (strpos(" $w", 'n') ? "\r\n" : "\r") : 0)){
 $t = str_replace("\n", $l, $t);
}
return str_replace(array("\x01", "\x02", "\x03", "\x04", "\x05", "\x07"), array('<', '>', "\n", "\r", "\t", ' '), $t);
// eof
}

function hl_version(){
// rel
return '1.1.18';
// eof
}

function kses($t, $h, $p=array('http', 'https', 'ftp', 'news', 'nntp', 'telnet', 'gopher', 'mailto')){
// kses compat
foreach($h as $k=>$v){
 $h[$k]['n']['*'] = 1;
}
$C['cdata'] = $C['comment'] = $C['make_tag_strict'] = $C['no_deprecated_attr'] = $C['unique_ids'] = 0;
$C['keep_bad'] = 1;
$C['elements'] = count($h) ? strtolower(implode(',', array_keys($h))) : '-*';
$C['hook'] = 'kses_hook';
$C['schemes'] = '*:'. implode(',', $p);
return htmLawed($t, $C, $h);
// eof
}

function kses_hook($t, &$C, &$S){
// kses compat
return $t;
// eof
}
