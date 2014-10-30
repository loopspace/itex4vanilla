<?php

$bpath=PATH_PLUGINS . DS . 'MarkdownItex'.DS.'vendors'.DS.'Masterminds'.DS.'src'.DS;

require_once($bpath.'HTML5.php');
$bpath .= 'HTML5' . DS;
require_once($bpath.'Elements.php');
require_once($bpath.'Entities.php');
require_once($bpath.'Exception.php');
require_once($bpath.'InstructionProcessor.php');
$path = $bpath . 'Parser' . DS;
require_once($path.'CharacterReference.php');
require_once($path.'EventHandler.php');
require_once($path.'DOMTreeBuilder.php');
require_once($path.'InputStream.php');
require_once($path.'StringInputStream.php');
require_once($path.'FileInputStream.php');
require_once($path.'ParseError.php');
require_once($path.'Scanner.php');
require_once($path.'Tokenizer.php');
require_once($path.'TreeBuildingRules.php');
require_once($path.'UTF8Utils.php');
$path = $bpath . 'Serializer' . DS;
require_once($path.'HTML5Entities.php');
require_once($path.'RulesInterface.php');
require_once($path.'OutputRules.php');
require_once($path.'Traverser.php');

?>
