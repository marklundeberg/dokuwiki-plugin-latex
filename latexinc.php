<?php

///// Generic plugin for all the latex syntax plugins.
//// Handles the rendering bits, so the syntax plugins just need to match syntax.


if(!defined('DOKU_INC')) die();
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'syntax.php');

require_once(dirname(__FILE__).'/class.latexrender.php');

/**
 * All DokuWiki plugins to extend the parser/rendering mechanism
 * need to inherit from this class
 */
class syntax_plugin_latex_common extends DokuWiki_Syntax_Plugin {
	var $_latex;
	
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
		$meddir = $conf['mediadir'] . '/' . strtr($this->getConf('latex_namespace'),':','/');
		$this->_mkpath($meddir,$conf['dmode']);
		$this->_mkpath($meddir.'/tmp',$conf['dmode']);
		$latex = new LatexRender($meddir,
						DOKU_BASE.'lib/exe/fetch.php?media='.$this->getConf('latex_namespace').':',
						$meddir.'/tmp');
		$latex->_latex_path = $this->getConf("latex_path");
		$latex->_dvips_path = $this->getConf("dvips_path");
		$latex->_convert_path = $this->getConf("convert_path").' '.$this->getConf("convert_options");
		$latex->_identify_path = $this->getConf("identify_path");
		$latex->_keep_tmp = false;
		$latex->_image_format = $this->getConf("image_format");
		$latex->_colour = $this->getConf("colour");
		$latex->_xsize_limit = $this->getConf("xsize_limit");
		$latex->_ysize_limit = $this->getConf("ysize_limit");
		$latex->_string_length_limit = $this->getConf("string_length_limit");
		$latex->_preamble = $this->getConf("preamble");
		$latex->_postamble = $this->getConf("postamble");
		
		$this->_latex = $latex;
	}

	function getType(){return 'protected'; }

	function getSort(){return 405; }
	
	function render($mode, &$renderer, $data) {
//	  global $conf;
	  if($data[1] != DOKU_LEXER_UNMATCHED) return true; // ignore entry/exit states
	  
	  if($mode == 'xhtml') {
			////////////////////////////////////
			// XHTML                          //
			////////////////////////////////////
		  $url = $this->_latex->getFormulaURL($data[0]);
			$this->_url = $url;
		  $title = $data['title'];
		  
		  if(!$url){
				// some kinda error.
				$url = DOKU_BASE.'lib/plugins/latex/images/renderfail.png';
				switch($this->_latex->_errorcode) {
					case 1: $title = $this->getLang('fail1').$this->_latex->_errorextra.
							$this->getLang('failmax').$this->_latex->_string_length_limit;
						break;
					case 2: $title = $this->getLang('fail2');
						break;
					case 4: $title = $this->getLang('fail4');
						break;
					case 5: $title = $this->getLang('fail5').$this->_latex->_errorextra.
							$this->getLang('failmax').$this->_latex->_xsize_limit.'x'.$this->_latex->_ysize_limit.'px';
						break;
					case 6: $title = $this->getLang('fail6');
						break;
					case 7: $title = $this->getLang('fail7');
						break;
					default: $title = $this->getLang('failX');
						break;
				}
		  }
				
		  $renderer->doc .= '<img src="'.$url.'" class="'.$data['class'].'" alt="'.htmlspecialchars($data[0]).'" title="'.$title.'" />';
			
		  $fname = $this->_latex->_filename;
		  return true;
      
      
	  } elseif ($mode == 'metadata') {
		  // nothing to do in metadata mode.
		  return true;
      
      
	  } elseif ($mode == 'odt') {
			////////////////////////////////////
			// ODT                            //
			////////////////////////////////////
		  $url = $this->_latex->getFormulaURL($data[0]);
		  $fname = dirname(__FILE__).'/images/renderfail.png';
		  if($url) {
				$fname = $this->_latex->_filename;
		  }
		  $info  = getimagesize($fname);
		  // expand images sizes 20% larger than those in renderer->_odtGetImageSize .
		  $width = ($info[0] * 0.03175)."cm";
		  $height = ($info[1] * 0.03175)."cm";
		  
		  if($data['class'] == "latex_displayed")
				// displayed math: 5 spaces seems to look okay.
				$renderer->doc .= '<text:s text:c="5"/>';
		  
		  $renderer->_odtAddImage($fname,$width,$height);
		  
		  return true;
      
      
	  } elseif ($mode == 'latex') {
			////////////////////////////////////
			// LATEX                          //
			////////////////////////////////////
		  if($data['class'] == "latex_displayed")
				$renderer->doc .= "\n".$data[0]."\n";
		  else
				$renderer->doc .= $data[0];
		  return true;
	  }
	  $renderer->doc .= htmlspecialchars($data[0]); /// unknown render mode, just fart out the latex code.
	  return false;
	}
  
	function _mkpath($path,$dmask=0777)
	{
		if(@mkdir($path,$dmask) or file_exists($path)) return true;
		return ($this->_mkpath(dirname($path),$dmask) and mkdir($path,$dmask));
	}

}
