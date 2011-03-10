<?php
/**
 * Admin for LaTeX plugin.
 * 
 * @license	GPL 2 (http://www.gnu.org/licenses/gpl.html)
 * @author	 Mark Lundeberg <nanite@gmail.com>
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
				'url'	=> 'http://www.dokuwiki.org/plugin:latex'
			);
	}
 
	/**
	 * return sort order for position in admin menu
	 */
	function getMenuSort() {
	  return 999;
	}

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
	//	  atime: age based on fileatime().
	//	  mtime: age based on filemtime().
	//	  all: delete all cached files.
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
			if(is_numeric($days) && $days == 0)
				$mode = 'all';
			if($mode == 'all') {
				// If the admin wants to delete all of the images, probably it's good to print this reminder
				//   since they are likely doing it after changing the colour or something.
				// (I don't know how many hours I spent trying to fix LaTeX heisenbugs that were just cached... grr.)
				$this->output .= '<div class="info">'.$this->getLang('refresh_note').'</div>';
			}
			$numdeleted = 0;
			$numkept = 0;
			$this->output .= "<pre>Purge result ([x] = deleted):\n";
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
				$this->output = "<div class=\"error\">Purger: Bad form inputs. No action taken.</div>".$this->output;
			}
			$this->output .= "Totals: $numdeleted deleted, $numkept kept (kept files not shown).\n";
			if ($numdeleted > 0) {
				touch($config_cascade['main']['local']);
			}
			$this->output .= "</pre>";
	  }
	}

	
	/**
	 * output appropriate html
	 */
	function html() {
		ptln('<h1>LaTeX plugin tasks</h1>');
		ptln('<h2>Cache control</h2>');
		ptln('<p>'.$this->output.'</p>');

		////////////// PURGE FORM
		ptln('<form action="'.wl($ID).'?do=admin&page='.$this->getPluginName().'" method="post">');
		ptln('<fieldset style="float:left;"><legend>'.$this->getLang('legend_purge').'</legend><table class="inline"><tr>');
		ptln('<td rowspan="2"><input type="submit" class="button" name="latexpurge"  value="'.$this->getLang('btn_purge').'" /></td>');
		ptln('<TD>');
		$labtimes = $this->getLang('label_times');
		ptln('(<LABEL><INPUT type="radio" name="purgemode" value="atime" checked/>'.$labtimes['atime'].'</LABEL>');
		ptln(' | <LABEL><INPUT type="radio" name="purgemode" value="mtime"/>'.$labtimes['mtime'].'</LABEL>)');
		echo $this->getLang('label_olderthan');
		echo '<input type="text" name="purgedays" size="3" value="30">';
		echo $this->getLang('label_days');
		ptln('</TD><TR><TD>');
		echo '<LABEL><INPUT type="radio" name="purgemode" value="all"/>'.$this->getLang('label_all').'</LABEL>';
		ptln('</TD></TR></TABLE></fieldset');
		ptln('</form>');
		
		/////////////// DIAGNOSER
		ptln('<h2>Troubleshooter</h2>');
		ptln('<h3>Versions</h3>');
		ptln('<table>');
		foreach(array($this->getConf("latex_path"),$this->getConf("dvips_path"),
				$this->getConf("convert_path"),$this->getConf("identify_path")) as $path) {
			ptln('<tr><td>');
			$parts = explode(' ',$path);
			$cmd = $parts[0]." --version 2>&1";
			echo htmlspecialchars($cmd);
			ptln('</td><td><pre>');
			echo htmlspecialchars(trim(shell_exec($cmd)));
			ptln('</pre></td></tr>');
		}
		ptln('</table>');
		
//		$latex = new syntax_plugin_latex_common();
		
	}
}