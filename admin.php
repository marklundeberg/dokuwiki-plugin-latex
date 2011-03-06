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
        if(method_exists(DokuWiki_Admin_Plugin,"getInfo")) {
             $a = parent::getInfo(); /// this will grab the data from the plugin.info.txt
			 $a['name'] = 'LaTeX plugin administration';
			 return $a;
		} else
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


	// Purgers.
	function vio_atime($fname) {
		if(time() - fileatime($fname) - $this->_timelimit > 0)
		{
			unlink($fname);
			return $this->_timelimit;
		}
		return false;
	}
	function vio_mtime($fname) {
		if(time() - filemtime($fname) - $this->_timelimit > 0)
		{
			unlink($fname);
			return true;
		}
		return false;
	}
	function vio_all($fname) {
		unlink($fname);
		return true;
	}
 
 
	// purge all files older than $timelimit (in seconds)
	// $mode = 
	//      atime: age based on fileatime().
	//      mtime: age based on filemtime().
	//      all: delete all cached files.
	function latexpurge($mode, $timelimit)
	{
	    global $conf, $config_cascade;
		$images = glob($conf['mediadir'].'/latex/img*');
		$this->_timelimit = $timelimit;
		switch($mode) {
			case 'atime':
				$vio = array_map(array($this,'vio_atime'),$images);
				break;
			case 'mtime':
				$vio = array_map(array($this,'vio_mtime'),$images);
				break;
			case 'all':
				$vio = array_map(array($this,'vio_all'),$images);
				break;
			default:
				return false;
		}
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
		$days = $_POST['purgedays'];
		$this->output .= "<pre>Purge result ([x] = deleted):\n";
		$numdeleted = 0;
		$numkept = 0;
		if($mode == 'all' || (is_numeric($days) && $days >= 0)) {
			$res = $this->latexpurge($mode, $days*86400);
			
			foreach($res as $img => $vio){
				if($vio) {
					$this->output .= '[x] '.$img . "\n";
					$numdeleted += 1;
				} else {
					// $this->output .= '[ ] '.$img . "\n";
					$numkept += 1;
				}
			}
				
		} else {
			$this->output .= "Purger: Bad input (non-numeric?). No action taken.\n";
		}
		$this->output .= "Totals: $numdeleted deleted, $numkept kept (kept files not shown).\n";
		if ($numdeleted > 0) {
			touch($config_cascade['main']['local']);
			$this->output .= "** If you have modified rendering settings (such as colour), ".
			   "** refresh your browser's cache to download the new images.";
		}
		$this->output .= "</pre>";
	  }
    }

	
    /**
     * output appropriate html
     */
    function html() {
      ptln('<p>'.$this->output.'</p>');
      
      ptln('<form action="'.wl($ID).'?do=admin&page='.$this->getPluginName().'" method="post">');
	  ptln('<fieldset style="width:500px;"><legend>'.$this->getLang('legend_purge').'</legend><table class="inline"><tr>');
	  ptln('<td rowspan="2"><input type="submit" class="button" name="latexpurge"  value="'.$this->getLang('btn_purge').'" /></td>');
	  ptln('<TD>');
	  $labtimes = $this->getLang('label_times');
	  ptln('(<LABEL><INPUT type="radio" name="purgemode" value="atime" checked/>'.$labtimes['atime'].'</LABEL>');
	  ptln('|<LABEL><INPUT type="radio" name="purgemode" value="mtime"/>'.$labtimes['mtime'].'</LABEL>)');
	  echo $this->getLang('label_olderthan');
	  echo '<input type="text" name="purgedays" size="3" value="30">';
	  echo $this->getLang('label_days');
	  ptln('</TD><TR><TD>');
	  echo '<LABEL><INPUT type="radio" name="purgemode" value="all"/>'.$this->getLang('label_all').'</LABEL>';
	  ptln('</TD></TR></TABLE></fieldset');
      ptln('</form>');
    }
 
}