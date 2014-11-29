<?php

/*
Extension Name: Markdown+Itex
Extension Url: http://www.math.ntnu.no/~stacey/HowDidIDoThat/Vanilla/itex
Description: A text-and-itex-to-XHTML+MathML conversion tool. 
Version: 0.0.2
Author: Andrew Stacey (itex additions), Michel Fortin (PHP Version), John Gruber (Original Markdown)
Author Url: http://www.math.ntnu.no/~stacey
*/

/*
Copyright (C) 2009 Andrew Stacey

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

if (!defined ('APPLICATION')) exit();

define( 'MARKDOWNITEX_VERSION', "0.2" ); # Thu 25 Feb 2010

# Include markdown if we don't already have it

#if (!defined('MARKDOWN_VERSION')) include('markdown.php');
require_once(PATH_LIBRARY.'/vendors/markdown/markdown.php');
# location of public server and cache should be configuration options

if (@include('itextomml.php'))
  {
    define('LOCAL_ITEX', true );
  }
else
  {
    define('LOCAL_ITEX', false );
  }

function MarkdownItex($text) {

#
# Initialize the parser and return the result of its transform method.
#

# Need to access the itex options

  static $itexcfg;
  if (!isset($itexcfg))
    $itexcfg = new Itex_Parser();

	# Setup static parser variable.
static $parser;
if (!isset($parser))
	  $parser = new MarkdownExtraItex_Parser();

	# Transform text using parser.
	return $parser->transform($text);
}

#
# Itex and Wikilink extras
#

class Itex_Parser {

  static $itexfilter;
  var $Options = array();
  var $markdown;
  var $outputType;

# Constructor

  function Itex_Parser()
  {
    if (LOCAL_ITEX && !isset($this->itexfilter))
      {
$this->itexfilter = new itextomml();
}

    // Set up the options

    // Defaults
    $this->Options =
      array(
	    'ITEX_WIKILINKS_STRING' => json_encode(array(
					    'none' => 'http://ncatlab.org/nlab/show/%s',
					    'default' => 'http://ncatlab.org/%t/show/%s',
					    'google' => 'http://www.google.com/search?q=%s',
					    'wikipedia' => 'http://en.wikipedia.org/wiki/Special:Search?search=%s'
							))
	    ) ;

    // Now convert wikilinks option to an array

    $this->Options['ITEX_WIKILINKS_ARRAY'] = json_decode($this->Options['ITEX_WIKILINKS_STRING'], true);

    $this->outputType = 'mathml';

    return;
  }

# Associate variables with Markdown object

  function associateMarkdownVars (&$markdown)
  {
    $this->markdown =& $markdown;
  }

  function hashWikiLinks($text) {
    $text = preg_replace_callback(
				  '/(?<!\\\\)\[\[\s*([^\]\s][^\]]*?)\s*\]\]/',
				  array(&$this, '_hashWikiLinks_callback'),
				  $text);
    
    return $text;
    
  }

  function _hashWikiLinks_callback($wikilinks) {
    $link = $wikilinks[1];
    $itexOptions = $this->Options;
    $wikilinks = $itexOptions['ITEX_WIKILINKS_ARRAY'];

    $typeRegex = '#^([A-Za-z]*):(.*)#';
    $textRegex = '#(.*)\|(.*)$#';
    
    if (preg_match($typeRegex,$link,$matches)) {
      $link = $matches[2];
      $type = strtolower($matches[1]);
    } else {
      $type = 'none';
    }
    
    if (preg_match($textRegex,$link,$matches)) {
      $link = $matches[1];
      $text = $matches[2];
    } else {
      $text = $link;
      if ($type != 'none')
	$text .= " (" . $type. ")";
    }

    if (array_key_exists($type,$wikilinks)) {
      $url = $wikilinks[$type];
    } else {
      if (array_key_exists('default',$wikilinks)) {

	$url = preg_replace('#%t#', $type,$wikilinks['default']);
      } else {
	$url = './';
      }
    }

    $url = preg_replace('#%s#', $link,$url);

    return $this->markdown->hashPart('<a href="' . $url . '">' . $text . '</a>');
  }

  function hashInlineItexEqns($text) {
    $text = preg_replace_callback(
				  '/(?<!\\\\)\${1}((?:[^\$]|\\\$)+)\$/',
				  array(&$this, '_hashItexEqnsInline_callback'),
				  $text);

    return $text;
    
  }

  function hashBlockItexEqns($text) {
    $text = preg_replace_callback(
				  '/^[ ]{0,3}(?:\$\$|\\\\\[|\\\\begin\{equation\})(.*?)(?:\$\$|\\\]|\\\\end\{equation\})\s*$/sm',
				  array(&$this, '_hashItexEqnsBlock_callback'),
				  $text);

    return $text;
    
  }

  function _hashItexEqnsInline_callback($itexstr)
  {
    return $this->markdown->hashPart($this->processItexEqns(false,$itexstr));
  }

  function _hashItexEqnsBlock_callback($itexstr)
  {
    return $this->markdown->hashBlock($this->processItexEqns(true,$itexstr));
  }

  function processItexEqns($block,$itexstr) {
    $itex = $itexstr[1];

    if (!LOCAL_ITEX) {
      if ($block) {
	$result = '$$' . $itex . '$$';
      } else {
	$result = '$' . $itex . '$';
      }
    } else {
      if ($block)
	{
	  $result = $this->itexfilter->block_filter($itex);
	}
      else
	{
	  $result = $this->itexfilter->inline_filter($itex);
	}
    }
//	$mathml = preg_replace('/<.?semantics>/','',$mathml);
//	$mathml = preg_replace('/<annotation.*<\/annotation>/','',$mathml);
    return $result;
  }


}

#
# Markdown+Itex Parser Class
#

class MarkdownItex_Parser extends Markdown_Parser {

  var $itexparser;
  var $main_escape_chars_re;
  var $math_escape_chars_re;

  function MarkdownItex_Parser ($Object)
  {
    $this->itexparser = new Itex_Parser($Object);
    $this->itexparser->associateMarkdownVars($this);
    $this->span_gamut += array(
			       "doInlineMath" => -20,
			       "parseSpanAgain" => -10,
			       "doWikiLinks" => 25
			       );
    $this->block_gamut += array(
				"doBlockMath" => 48
				);
    $this->escape_chars .= '$';
    parent::Markdown_Parser();
    $this->main_escape_chars_re = $this->escape_chars_re;
    $this->math_escape_chars_re =  '[' . preg_quote('$') . ']';
  }

  function setup()
  {
    $this->escape_chars_re = $this->math_escape_chars_re;
    parent::setup();
  }

  function parseSpanAgain($text)
  {
    $this->escape_chars_re = $this->main_escape_chars_re;
    $return = $this->parseSpan($text);
    $this->escape_chars_re = $this->math_escape_chars_re;
    return $return;
  }

  function doInlineMath($text)
  {
    return $this->itexparser->hashInlineItexEqns($text);
  }

  function doBlockMath($text)
  {
    return $this->itexparser->hashBlockItexEqns($text);
  }

  function doWikiLinks($text)
  {
    return $this->itexparser->hashWikiLinks($text);
  }

}

class MarkdownExtraItex_Parser extends MarkdownExtra_Parser {

  var $itexparser;
  var $main_escape_chars_re;
  var $math_escape_chars_re;

  function MarkdownExtraItex_Parser ()
  {
    $this->itexparser = new Itex_Parser();
    $this->itexparser->associateMarkdownVars($this);
    $this->span_gamut += array(
			       "doInlineMath" => -20,
			       "parseSpanAgain" => -10,
			       "doWikiLinks" => 25
			       );
    $this->block_gamut += array(
				"doBlockMath" => 48
				);
    $this->escape_chars .= '$';
    parent::MarkdownExtra_Parser();
    $this->main_escape_chars_re = $this->escape_chars_re;
    $this->math_escape_chars_re =  '[' . preg_quote('$') . ']';
  }

  function setup()
  {
    $this->escape_chars_re = $this->math_escape_chars_re;
    parent::setup();
  }

  function parseSpanAgain($text)
  {
    $this->escape_chars_re = $this->main_escape_chars_re;
    $return = $this->parseSpan($text);
    $this->escape_chars_re = $this->math_escape_chars_re;
    return $return;
  }

  function doInlineMath($text)
  {
    return $this->itexparser->hashInlineItexEqns($text);
  }

  function doBlockMath($text)
  {
    return $this->itexparser->hashBlockItexEqns($text);
  }

  function doWikiLinks($text)
  {
    return $this->itexparser->hashWikiLinks($text);
  }

}


?>
