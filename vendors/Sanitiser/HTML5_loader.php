<?php

$path=PATH_PLUGINS . DS . 'Masterminds' . DS;

require_once($path.'HTML5.php');
$path .= 'HTML5' . DS;
require_once($path.'Elements.php');
require_once($path.'Entities.php');
require_once($path.'Exception.php');
require_once($path.'InstructionProcessor.php');
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
require_once($path.'HTML5Entities.php');
require_once($path.'RulesInterface.php');
require_once($path.'OutputRules.php');
require_once($path.'Traverser.php');

?>
