<?php
/**
 * Plugin Skeleton: Displays "Hello World!"
 * 
 * @license    GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author     Christopher Smith <chris@jalakai.co.uk>
 */

if(!defined('DOKU_INC')) die();
if(!defined('DOKU_PLUGIN')) define('DOKU_PLUGIN',DOKU_INC.'lib/plugins/');
require_once(DOKU_PLUGIN.'admin.php');

require_once(dirname(__FILE__).'/latexinc.php');

/**
 * All DokuWiki plugins to extend the admin function
 * need to inherit from this class
 */
class admin_plugin_latex extends DokuWiki_Admin_Plugin {
	var $output;
    /**
     * return some info
     */
     function getInfo(){
		$a = '';
        if(method_exists(DokuWiki_Admin_Plugin,"getInfo"))
             $a = parent::getInfo(); /// this will grab the data from the plugin.info.txt
		else
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
 
    /**
     * return sort order for position in admin menu
     */
    function getMenuSort() {
      return 999;
    }
    
    /**
     *  return a menu prompt for the admin menu
     *  NOT REQUIRED - its better to place $lang['menu'] string in localised string file
     *  only use this function when you need to vary the string returned
     */
//    function getMenuText() {
//      return 'a menu prompt';
//    }
 
    /**
     * handle user request
     */
    function handle() {
	  global $conf, $config_cascade;
	  $this->output = "";
	  if(isset($_POST['latexpurge']))
	  {
		$this->output .= "Want to purge:<br/><pre>";
		foreach(glob($conf['mediadir'].'/latex/img*') as $fname) {
			$base = basename($fname);
			$this->output .= $fname."\n";
		}
		$this->output .= "</pre>";
		touch($config_cascade['main']['local']); // touch config settings to force re-rendering.
	  }
    }
 
    /**
     * output appropriate html
     */
    function html() {
	  dbg('$_REQUEST = '.print_r($_REQUEST,true));
	  dbg('$_POST = '.print_r($_POST,true));
      ptln('<p>'.$this->output.'</p>');
      
      ptln('<form action="'.wl($ID).'?do=admin&page='.$this->getPluginName().'" method="post">');
	  echo $this->getLang('btn_latexpurge');
	  $labtimes = $this->getLang('label_times')
	  foreach(array('mtime','atime') as $which) {
		echo '<LABEL><INPUT type="radio" name="purgetime" value="'.$which.'"/>';
		echo $labtimes[$which];
		echo "</LABEL>\n";
	  }
	  ptln('  <input type="submit" class="button" name="latexpurge"  value="'.$this->getLang('btn_purge').'" />');
      ptln('</form>');
    }
 
}