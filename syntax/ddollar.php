<?php

require_once(realpath(dirname(__FILE__).'/../latexinc.php'));

class syntax_plugin_latex_ddollar extends syntax_plugin_latex_common {
   /**
    * return some info
    */
    function getInfo(){
		$a = parent::getInfo();
		$a['name'] = '$$...$$ syntax for displayed LaTeX-math';
		return $a;
    }
	
	// Sort in at medium priority.
	function getSort(){return 300; }

    function connectTo($mode) {
      $this->Lexer->addEntryPattern('\$\$(?=.*\$\$)',$mode,'plugin_latex_ddollar');
    }
    function postConnect() { $this->Lexer->addExitPattern('\$\$','plugin_latex_ddollar'); }
 
		function getPType(){return 'stack';}
	
   /**
    * Handle the match
    */
    function handle($match, $state, $pos, &$handler){
	  if($state != DOKU_LEXER_UNMATCHED)
		return array($match,$state,NULL);
	  return array("$$".$match."$$",$state,'class'=>"latex_displayed", 'title'=>"Equation", NULL);
    }
 }
