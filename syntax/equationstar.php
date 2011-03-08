<?php

require_once(realpath(dirname(__FILE__).'/../latexinc.php'));

class syntax_plugin_latex_equationstar extends syntax_plugin_latex_common {
   /**
    * return some info
    */
    function getInfo(){
		$a = parent::getInfo();
		$a['name'] = '\\begin{equation*} ... \\end{equation*} syntax';
		return $a;
    }

	function connectTo($mode) {
      $this->Lexer->addEntryPattern('\x5Cbegin\{equation\*\}(?=.*\x5Cend\{equation\*\})',
				    $mode,'plugin_latex_equationstar');
    }
    function postConnect() {
      $this->Lexer->addExitPattern('\x5Cend\{equation\*\}','plugin_latex_equationstar');
    }
 
		function getPType(){return 'stack';}
	
   /**
    * Handle the match
    */
    function handle($match, $state, $pos, &$handler){
	  if($state != DOKU_LEXER_UNMATCHED)
		return array($match,$state,NULL);
	  return array("\\begin{equation*}".$match."\\end{equation*}",$state,'class'=>"latex_displayed", 'title'=>"Equation", NULL);
    }
 }
