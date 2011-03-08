<?php

require_once(realpath(dirname(__FILE__).'/../latexinc.php'));
 
class syntax_plugin_latex_eqnarraystar extends syntax_plugin_latex_common {
   /**
    * return some info
    */
    function getInfo(){
		$a = parent::getInfo();
		$a['name'] = '\\begin{eqnarray*} ... \\end{eqnarray*} syntax';
		return $a;
    }
    function connectTo($mode) {
      $this->Lexer->addEntryPattern('\x5Cbegin\{eqnarray\*\}(?=.*\x5Cend\{eqnarray\*\})',
				    $mode,'plugin_latex_eqnarraystar');
    }
    function postConnect() {
      $this->Lexer->addExitPattern('\x5Cend\{eqnarray\*\}','plugin_latex_eqnarraystar');
    }
 
		function getPType(){return 'stack';}
	
   /**
    * Handle the match
    */
    function handle($match, $state, $pos, &$handler){
	  if($state != DOKU_LEXER_UNMATCHED)
		return array($match,$state,NULL);
	  return array("\\begin{eqnarray*}".$match."\\end{eqnarray*}",$state,'class'=>"latex_displayed", 'title'=>"Equations", NULL);
    }
 
 }
