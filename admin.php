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
		ptln('<p>'.$this->output.'</p>');
		ptln('<h1>LaTeX plugin administrator tasks</h1>');
		ptln('<h2>'.$this->getLang('legend_purge').'</h2>');
		ptln('<div class="level2">');

		////////////// PURGE FORM
		ptln('<form action="'.wl($ID).'?do=admin&page='.$this->getPluginName().'" method="post">');
		ptln('<table class="inline"><tr>');
		ptln('<td rowspan="2"><input type="submit" class="button" name="latexpurge"  value="'.$this->getLang('btn_purge').'" /></td>');
		ptln('<TD>');
		$labtimes = $this->getLang('label_times');
		ptln('(<LABEL><INPUT type="radio" name="purgemode" value="atime" checked />'.$labtimes['atime'].'</LABEL>');
		ptln(' | <LABEL><INPUT type="radio" name="purgemode" value="mtime" />'.$labtimes['mtime'].'</LABEL>)');
		echo $this->getLang('label_olderthan');
		echo '<input type="text" name="purgedays" size="3" value="30">';
		echo $this->getLang('label_days');
		ptln('</TD><TR><TD>');
		echo '<LABEL><INPUT type="radio" name="purgemode" value="all" />'.$this->getLang('label_all').'</LABEL>';
		ptln('</TD></TR></TABLE>');
		ptln('</form>');
		
		ptln('</div>');

		/////////////// DIAGNOSER
		ptln('<h2>Troubleshooter</h2>');
		$troubleshoot = 1;
		if($troubleshoot) {
			ptln('<h3>Versions</h3>');
			ptln('<div class="level3">');
			ptln('This is a test of the acessibility of your programs and their versions.');
			ptln('<table class="inline">');
			ptln('<tr><th>command</th><th>output</th></tr>');
			foreach(array($this->getConf("latex_path"),$this->getConf("dvips_path"),
					$this->getConf("convert_path"),$this->getConf("identify_path")) as $path) {
				ptln('<tr><td><pre>');
				$parts = explode(' ',$path);
				$cmd = $parts[0]." --version 2>&1";
				echo htmlspecialchars($cmd);
				ptln('</pre></td><td>');
				unset($execout);
				exec($cmd,$execout,$statuscode);
				if($statuscode == 0)
					echo '<pre>';
				else
					echo '<pre style="background-color:#FCC;">'; //pink for error status
				echo htmlspecialchars(implode(PHP_EOL,$execout));
				ptln('</pre></td></tr>');
			}
			ptln('</table>');
			ptln('</div>');
			
			ptln('<h3>Test run</h3>');
			$plug = new syntax_plugin_latex_common();
			
			/// Directory sanity checks
			if(is_writable($plug->_latex->getPicturePath()) && is_dir($plug->_latex->getPicturePath()))
				ptln('<div class="success">Media directory is writable: '.$plug->_latex->getPicturePath().'</div>');
			else
				ptln('<div class="error">Media directory not writable or nonexistant! '.$plug->_latex->getPicturePath().'
						<br />Recommendation: This media namespace must be writable on the file system.</div>');
			if(is_writable($plug->_latex->_tmp_dir) && is_dir($plug->_latex->_tmp_dir))
				ptln('<div class="success">Temporary directory is writable: '.$plug->_latex->_tmp_dir.'</div>');
			else
				ptln('<div class="error">Temporary directory not writable or nonexistant! '.$plug->_latex->_tmp_dir.'
						<br />Recommendation: Choose a new temporary directory.</div>');

			// simulate a call to the syntax plugin; force render, keep temp files.				
			$testformula = '$a+b=c$';
			$md5 = md5($testformula);
			$outname = $plug->_latex->getPicturePath()."/img".$md5.'.'.$plug->_latex->_image_format;
			if(file_exists($outname)) {
				unlink($outname);
				ptln('<div class="info">Removed cache file for test: '.$outname.'</div>');
			}
			ptln('<div class="info">Attempting to render: <tt>'.$testformula.'</tt> => '.$outname.'</div>');
			$plug->_latex->_keep_tmp = true;
			$data = array($testformula,DOKU_LEXER_UNMATCHED,'class'=>"latex_inline", 'title'=>"Math", NULL);
			$this->doc = '';
			$plug->render('xhtml', $this, $data);
			$tmpf = $plug->_latex->_tmp_dir."/".$plug->_latex->_tmp_filename;
			$tmpext = array('tex','log','aux','dvi','ps',$plug->_latex->_image_format);
			foreach($tmpext as $ext) {
				$fname = $tmpf.'.'.$ext;
				if(is_file($fname))
					ptln('<div class="success">File created: '.$fname.'</div>');
				else
					ptln('<div class="error">File missing! '.$fname.'</div>');
			}
			ptln('<div class="info">These files '.$tmpf.'/* will be deleted at the end of this script.</div>');
			ptln('<div class="level3">');
			ptln('<table class="inline"><tr><th>Input LaTeX file</th><th>Final result</th></tr>');
			ptln('<tr><td><pre>');
			if(is_readable($tmpf.'.tex') && is_file($tmpf.'.tex'))
				echo htmlspecialchars(file_get_contents($tmpf.'.tex'));
			else
				echo 'MISSING';
			ptln('</pre></td><td>');
//			ptln(htmlspecialchars($plug->_url));
//			ptln('<br /><br />');
			ptln('<center>');
			ptln($this->doc);
			ptln('</center></td></tr>');
			ptln('</table>');
			
			ptln('Command log '.$tmpf.'.cmd (does not include <tt>identify</tt> output):');
			echo '<pre>';
			echo htmlspecialchars(file_get_contents($tmpf.'.cmd'));
			echo '</pre>';
			
			ptln('Contents of '.$tmpf.'.log:');
			echo '<pre>';
			echo htmlspecialchars(file_get_contents($tmpf.'.log'));
			echo '</pre>';
			
			$plug->_latex->cleanTemporaryDirectory();
			ptln('</div>');
		}
	}
}