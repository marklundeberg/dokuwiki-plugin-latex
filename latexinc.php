<?php



if(!defined('DOKU_INC')) die();
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'syntax.php');

require_once(dirname(__FILE__).'/class.latexrender.php');

/**
 * All DokuWiki plugins to extend the parser/rendering mechanism
 * need to inherit from this class
 */
class syntax_plugin_latex_common extends DokuWiki_Syntax_Plugin {
   /**
    * return some info
    */
     function getInfo(){
        if(method_exists(DokuWiki_Syntax_Plugin,"getInfo"))
             return parent::getInfo(); /// this will grab the data from the plugin.info.txt

        // Otherwise return some hardcoded data for old dokuwikis
        return array(
            'author' => 'Alexander Kraus, Michael Boyle, and Mark Lundeberg)',
            'email'  => '.',
            'date'   => '???',
            'name'   => 'LaTeX plugin',
            'desc'   => 'LaTeX rendering plugin; requires LaTeX, dvips, ImageMagick.',
            'url'    => 'http://www.dokuwiki.org/plugin:latex'
        );
    }
	
	/* common constructor -- get config settings */
	function syntax_plugin_latex_common()
	{
		global $conf;
        if ( !is_dir($conf['mediadir'] . '/latex') ) {
          mkdir($conf['mediadir'] . '/latex', 0777-$conf['dmask']);
        }
		$latex = new LatexRender($conf['mediadir'] . '/latex/',
						DOKU_BASE.'lib/exe/fetch.php?media=latex:',
						$this->getConf("tmp_dir"));
		$latex->_latex_path = $this->getConf("latex_path");
		$latex->_dvips_path = $this->getConf("dvips_path");
		$latex->_convert_path = $this->getConf("convert_path");
		$latex->_identify_path = $this->getConf("identify_path");
		$latex->_keep_tmp = $this->getConf("keep_tmp");
		$latex->_image_format = $this->getConf("image_format");
		$latex->_colour = $this->getConf("colour");
		$latex->_xsize_limit = $this->getConf("xsize_limit");
		$latex->_ysize_limit = $this->getConf("ysize_limit");
		$latex->_string_length__limit = $this->getConf("string_length_limit");
		
		$this->_latex = $latex;
	}

    function getType(){return 'protected'; }

    function getSort(){return 405; }
	
    function render($mode, &$renderer, $data) {
//      global $conf;
	  if($data[1] != DOKU_LEXER_UNMATCHED) return true; // ignore entry/exit states
	  
      if($mode == 'xhtml') {
		  $url = $this->_latex->getFormulaURL($data[0]);
		  $title = $data['title'];
		  
		  if(!$url){
			// some kinda error.
			$url = DOKU_BASE.'lib/plugins/latex/images/renderfail.png';
			switch($this->_latex->_errorcode) {
				case 1: $title = 'Fail: formula too long (current limit is '.
						$this->_latex->_string_length_limit.' characters)';
				break;
				case 2: $title = 'Fail: triggered security filter; contains blacklisted LaTeX tags.';
				break;
				case 4: $title = 'Fail: LaTeX compilation failed.';
				break;
				case 5: $title = 'Fail: image too big (max '.
						$this->_latex->_xsize_limit.'x'.$this->_latex->_ysize_limit.' px) '.
						$this->_latex->_errorextra;
				break;
				case 6: $title = 'Fail: unknown processing error.';
				break;
				default: $title = 'Fail: unknown error.';
				break;
			}
		  }
		  if($data['class'] == "latex_displayed")
			$renderer->doc .= "\n<br/>";
		  $renderer->doc .= '<img src="'.$url.'" class="'.$data['class'].'" alt="'.htmlspecialchars($data[0]).'" title="'.$title.'"/>';		    
		  if($data['class'] == "latex_displayed")
			$renderer->doc .= "<br/>\n";
		  $fname = $this->_latex->_filename;
		  dbg('$renderer->_odtAddImage("'.$fname.'");');
		  return true;
	  } elseif ($mode == 'metadata') {
	      // nothing to meta.
		  return true;
      } elseif ($mode == 'odt') {
		  $url = $this->_latex->getFormulaURL($data[0]);
		  $fname = dirname(__FILE__).'/images/renderfail.png';
		  if($url) {
			$fname = $this->_latex->_filename;
		  }
		  //dbg("$renderer->_odtAddImage('".$fname."');");
		  $renderer->_odtAddImage($fname);
		  return true;
      } elseif ($mode == 'latex') {
		  $renderer->doc .= $data[0]."\n";
		  return true;
	  }
	  $renderer->doc .= htmlspecialchars($data[0]); /// unknown render mode, just fart out the latex code.
      return false;
    }
    
}
