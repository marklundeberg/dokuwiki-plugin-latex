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
        return array(
            'author' => 'Alexander Kraus, Michael Boyle, and Mark Lundeberg)',
            'email'  => '.',
            'date'   => '2011-03-04',
            'name'   => 'LaTeX plugin',
            'desc'   => 'LaTeX rendering plugin; requires LaTeX, dvips, ImageMagick.',
            'url'    => 'http://www.dokuwiki.org/plugin:latex'
        );
    }
	
	/* common constructor -- get config settings */
	function syntax_plugin_latex_common()
	{
	  $dir = $this->getConf("tmp_dir");
      $latex = new LatexRender($dir,$dir,$dir);
	  $latex->_latex_path = $this->getConf("latex_path");
	  $latex->_dvips_path = $this->getConf("dvips_path");
	  $latex->_convert_path = $this->getConf("convert_path");
	  $latex->_identify_path = $this->getConf("identify_path");
      $latex->_image_format = $this->getConf("image_format");
      $latex->_colour = $this->getConf("colour");
      $latex->_formula_density = $this->getConf("density");
	  $this->_latex = $latex;
	}

    function getType(){return 'protected'; }

    function getSort(){return 405; }
	
    function render($mode, &$renderer, $data) {
      global $conf;
	  //dbg('function render($mode, &$renderer, $data)-->'.' mode = '.$mode.' data = '.serialize($data).'\n');
	  if($data[1] != DOKU_LEXER_UNMATCHED) return true; // ignore entry/exit states
      if($mode == 'xhtml') {
//		  dbg('xhtml render: '.$data[0].', '.$data[1]);
		  
		  $url = $this->getImage($data[0]);
		  if($data['class'] == "latex_displayed")
			$renderer->doc .= "\n<br/>";
		  $renderer->doc .= '<img src="'.$url.'" class="'.$data['class'].'" alt="'.htmlspecialchars($data[0]).'" title="'.$data['title'].'"/>';		    
		  if($data['class'] == "latex_displayed")
			$renderer->doc .= "<br/>\n";
      } elseif ($mode == 'latex') {
		  $renderer->doc .= $data[0]."\n";
		  return true;
	  }
      return $data[0];
    }    
  
    function getImage(&$data) {
		global $conf;
		
        if ( !is_dir($conf['mediadir'] . '/latex') ) {
          mkdir($conf['mediadir'] . '/latex', 0777-$conf['dmask']);
        }
        $hash = md5($data);
        $filename = $conf['mediadir'] . '/latex/'.$hash.'.'.$this->getConf("image_format");
        $url = DOKU_BASE.'lib/exe/fetch.php?cache='.$cache.'&amp;media='.urlencode('latex:'.$hash.'.'.$this->getConf("image_format"));
		
//		dbg("getImage('".$data."'); ==> ".$filename);
		if(is_readable($filename))
			return $url;
		
		if($this->createImage($filename, $data))
			return $url;
		else
			return  DOKU_BASE.'lib/plugins/latex/images/renderfail.png';
//			return false;
    }

    function createImage($filename, &$data) {
      global $conf;
      if ($url = $this->_latex->getFormulaURL($data)) {
        rename($url,$filename);
		return true;
      }
      return false;
    }

    
}
