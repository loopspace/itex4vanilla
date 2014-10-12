<?php

include('itex2MML.php');

class itextomml extends itex2MML
{
  function html_filter($string)
  {
    itex2MML_html_filter($string, strlen($string));
    return itex2MML_output();
  }

  function filter($string)
  {
    itex2MML_filter($string, strlen($string));
    return itex2MML_output();
  }

  function inline_filter($string)
  {
    itex2MML_filter("\$$string\$", strlen($string)+2);
    return itex2MML_output();
  }

  function block_filter($string)
  {
    itex2MML_filter("\$\$$string\$\$", strlen($string)+4);
    return itex2MML_output();
  }
}

if (basename(__FILE__) == $argv[0])
  {

    $itex = new itextomml();

    $htmloutput=$itex->html_filter('HTML Filter: $x^2 + y^2 \to z^2$');
    print $htmloutput;
    print "\n";
    $filteroutput = $itex->filter('Filter: $x^2 + y^2 \to z^2$');
    print $filteroutput;
    print "\n";
    $inlineoutput = $itex->inline_filter('x^2 + y^2 \to z^2');
    print $inlineoutput;
    print "\n";
    $blockoutput = $itex->block_filter('x^2 + y^2 \to z^2');
    print $blockoutput;
    print "\n";
  }

?>
