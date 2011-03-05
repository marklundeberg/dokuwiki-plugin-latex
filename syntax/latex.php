<?php

require_once(realpath(dirname(__FILE__).'/../latexinc.php'));
 
class syntax_plugin_latex_latex extends syntax_plugin_latex_common {
   /**
    * return some info
    */
    function getInfo(){
		$a = parent::getInfo();
		$a['name'] = '<latex>...</latex> syntax for inline LaTeX (non-math-mode)';
		return $a;
    }
	
	// Sort in at high priority.
    function getSort(){
      return 100;
    }

    /**
    * Connect pattern to lexer
    */
    function connectTo($mode) {
      $this->Lexer->addEntryPattern('\x3Clatex\x3E(?=.*\x3C/latex\x3E)',$mode,'plugin_latex_latex');
    }
	
    function postConnect() {
      $this->Lexer->addExitPattern('\x3C/latex\x3E','plugin_latex_latex');
    }

    /**
     * Handle the match
    */
    function handle($match, $state, $pos, &$handler){
	  if($state != DOKU_LEXER_UNMATCHED)
		return array($match,$state,NULL);
	  return array($match,$state,'class'=>"latex_inline", 'title'=>"LaTeX", NULL);
    }
 }

 
 