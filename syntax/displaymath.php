<?php

require_once(realpath(dirname(__FILE__).'/../latexinc.php'));

class syntax_plugin_latex_displaymath extends syntax_plugin_latex_common {
   /**
    * return some info
    */
    function getInfo(){
		$a = parent::getInfo();
		$a['name'] = '\\begin{displaymath} ... \\end{displaymath} syntax';
		return $a;
    }

	function connectTo($mode) {
      $this->Lexer->addEntryPattern('\x5Cbegin\{displaymath\}(?=.*\x5Cend\{displaymath\})',
				    $mode,'plugin_latex_displaymath');
    }
    function postConnect() {
      $this->Lexer->addExitPattern('\x5Cend\{displaymath\}','plugin_latex_displaymath');
    }
 
		function getPType(){return 'stack';}
	
   /**
    * Handle the match
    */
    function handle($match, $state, $pos, &$handler){
	  if($state != DOKU_LEXER_UNMATCHED)
		return array($match,$state,NULL);
	  return array("\\begin{displaymath}".$match."\\end{displaymath}",$state,'class'=>"latex_displayed", 'title'=>"Equation", NULL);
    }
 }
