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
    
      if (!isset($_REQUEST['cmd'])) return;   // first time - nothing to do

      $this->output = 'invalid';

      if (!is_array($_REQUEST['cmd'])) return;
      
      // verify valid values
      switch (key($_REQUEST['cmd'])) {
        case 'hello' : $this->output = 'again'; break;
        case 'goodbye' : $this->output = 'goodbye'; break;
      }      
    }
 
    /**
     * output appropriate html
     */
    function html() {
	  dbg(print_r($_REQUEST,true));
      ptln('<p>'.htmlspecialchars($this->getLang($this->output)).'</p>');
      
      ptln('<form action="'.wl($ID).'" method="get">');
	  
      // output hidden values to ensure dokuwiki will return back to this plugin
      ptln('  <input type="hidden" name="do"   value="admin" />');
      ptln('  <input type="hidden" name="page" value="'.$this->getPluginName().'" />');
      
      ptln('  <input type="submit" class="button" name="cmd[hello]"  value="'.$this->getLang('btn_hello').'" />');
      ptln('  <input type="submit" name="cmd[goodbye]"  value="'.$this->getLang('btn_goodbye').'" />');
      ptln('</form>');
    }
 
}