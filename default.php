<?php if (!defined('APPLICATION')) exit();

//
// Original Markdown file
// Copyright 2010 Jeff Verkoeyen
//
// Licensed under the Apache License, Version 2.0 (the "License");
// you may not use this file except in compliance with the License.
// You may obtain a copy of the License at
//
//    http://www.apache.org/licenses/LICENSE-2.0
//
// Unless required by applicable law or agreed to in writing, software
// distributed under the License is distributed on an "AS IS" BASIS,
// WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
// See the License for the specific language governing permissions and
// limitations under the License.
//
// Modifications for iTeX
// Copyright 2014 Andrew Stacey
//
// Licensed under the Apache License as above.

$PluginInfo['MarkdownItex'] = array(
   'Description' => 'Adds Markdown+iTeX syntax support to the discussions and comments.',
   'Version' => '1.0',
   'RequiredApplications' => NULL, 
   'RequiredTheme' => FALSE, 
   'RequiredPlugins' => FALSE,
   'HasLocale' => FALSE,
   'Author' => "Andrew Stacey",
   'AuthorEmail' => 'loopspace@mathforge.org',
   'AuthorUrl' => 'http://loopspace.mathforge.org'
);
require_once('vendors'.DS.'markdown'.DS.'markdownitex.php');
if (LOCAL_ITEX) {
  $Configuration['EnabledPlugins']['HtmLawed'] = FALSE;
  require_once('vendors'.DS.'Sanitiser'.DS.'sanitiser.php');
}

Gdn::FactoryInstall('HtmlFormatter', 'MarkdownItexHTMLPlugin', __FILE__, Gdn::FactorySingleton);

if (LOCAL_ITEX) {

  class MarkdownItexHTMLPlugin extends Gdn_Plugin {
    public function Format($Html) {
      return Validate($Html);
    }
  }
}

class MarkdownItexPlugin implements Gdn_IPlugin {
  
  // Standard rendering of comments.
  // See applications/vanilla/views/discussion/helper_functions.php
  // Look for BeforeCommentBody.
  public function DiscussionController_BeforeCommentBody_Handler($Sender) {
    if (isset($Sender->CurrentComment)) {
      $Comment = $Sender->CurrentComment;
      $Comment->Body = MarkdownItex($Comment->Body);
    } elseif (isset($Sender->Discussion)) {
      $Discussion = $Sender->Discussion;
      $Discussion->Body = MarkdownItex($Discussion->Body);
    }
  }

  // Add MathJaX to header
  public function Base_Render_Before($Sender) {
    if (LOCAL_ITEX) {
      $Sender->Head->AddScript('http://cdn.mathjax.org/mathjax/latest/MathJax.js?config=MML_HTMLorMML');
    } else {
      $Sender->Head->AddString(
			       <<<EOF
	        <script>
			MathJax.Hub.Config({
				tex2jax: {
			            inlineMath: [['$','$'], ['\\(','\\)']],
				    displayMath: [['$$','$$'], ['\\[','\\]']],
				}
			});
		</script>
EOF
			       );
      $Sender->Head->AddScript('http://cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-AMS-MML_HTMLorMML');
    }
  }
  // AJAX posting of comments
  public function PostController_BeforeCommentBody_Handler($Sender) {
    $this->DiscussionController_BeforeCommentBody_Handler($Sender);
  }

  // AJAX preview of new discussions.
  public function PostController_BeforeDiscussionRender_Handler($Sender) {
    if ($Sender->View == 'preview') {
      $Sender->Comment->Body = MarkdownItex($Sender->Comment->Body);
    }
  }

  // AJAX preview of new comments.
  public function PostController_BeforeCommentRender_Handler($Sender) {
    if ($Sender->View == 'preview') {
      $Sender->Comment->Body = MarkdownItex($Sender->Comment->Body);
    }
  }

  public function Setup() {
  }
}
