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


	// // Purgers.
	// function vio_atime($fname) {
		// if(time() - fileatime($fname) - $this->_timelimit > 0)
		// {
			// //unlink($fname);
			// return true;
		// }
		// return false;
	// }
	// function vio_mtime($fname) {
		// if(time() - filemtime($fname) - $this->_timelimit > 0)
		// {
			// //unlink($fname);
			// return true;
		// }
		// return false;
	// }
	// function vio_all($fname) {
		// //unlink($fname);
		// return false;
	// }
 
 
	// purge all files older than $timelimit (in seconds)
	function latexpurge($mode, $timelimit)
	{
	    global $conf, $config_cascade;
		$images = glob($conf['mediadir'].'/latex/img*');
		$this->_timelimit = $timelimit;
		switch($mode) {
			case 'atime':
				$vio = array_map($this->vio_atime,$images);
				break;
			case 'mtime':
				$vio = array_map($this->vio_mtime,$images);
				break;
			case 'all':
				$vio = array_map($this->vio_all,$images);
				break;
			default:
				return false;
		}
		touch($config_cascade['main']['local']);
		return array_combine($images,$vio);
	}
 
    /**
     * handle user request
     */
    function handle() {
	  global $conf, $config_cascade;
	  $this->output = "";
	  if(isset($_POST['latexpurge']))
	  {
		$mode = $_POST['purgemode'];
		$days = $_POST['purgetime'];
		if($mode == 'all' || is_numeric($days))
			$res = $this->latexpurge($mode, $timelimit*86400);
			$this->output .= "Purge result ([x] = deleted):<br/><pre>";
			foreach($res as $img => $vio){
				if($vio)
					echo '[x] ';
				else
					echo '[ ] ';
				$this->output .= $img . "\n";
			}
			$this->output .= "</pre>";
		} else {
			$this->output .= "Purger: Bad input (non-numeric?). No action taken.";
		}
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
	  ptln('<fieldset style="width:500px;"><legend>'.$this->getLang('legend_purge').'</legend><table class="inline"><tr>');
	  ptln('<td rowspan="2"><input type="submit" class="button" name="latexpurge"  value="'.$this->getLang('btn_purge').'" /></td>');
	  ptln('<TD>');
	  $labtimes = $this->getLang('label_times');
	  ptln('<LABEL><INPUT type="radio" name="purgemode" value="atime" checked/>'.$labtimes['atime'].'</LABEL>');
	  ptln('<LABEL><INPUT type="radio" name="purgemode" value="mtime"/>'.$labtimes['mtime'].'</LABEL>');
	  echo $this->getLang('label_olderthan');
	  echo '<input type="text" name="purgedays" size="3" value="100">';
	  echo $this->getLang('label_days');
	  ptln('</TD><TR><TD>');
	  echo '<LABEL><INPUT type="radio" name="purgemode" value="all"/>'.$this->getLang('label_all').'</LABEL>';
	  ptln('</TD></TR></TABLE></fieldset');
      ptln('</form>');
    }
 
}