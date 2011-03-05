<?php

require_once(realpath(dirname(__FILE__).'/../latexinc.php'));

class syntax_plugin_latex_dollar extends syntax_plugin_latex_common {
   /**
    * return some info
    */
    function getInfo(){
		$a = parent::getInfo();
		$a['name'] = '$...$ syntax for inline LaTeX-math';
		return $a;
    }

    function connectTo($mode) {
      $this->Lexer->addEntryPattern('\$(?=.*\$)',$mode,'plugin_latex_dollar');
    }
    function postConnect() { $this->Lexer->addExitPattern('\$','plugin_latex_dollar'); }
 
   /**
    * Handle the match
    */
    function handle($match, $state, $pos, &$handler){
	  if($state != DOKU_LEXER_UNMATCHED)
		return array($match,$state,NULL);
	  return array("$".$match."$",$state,'class'=>"latex_inline", 'title'=>"Math", NULL);
    }
 }
