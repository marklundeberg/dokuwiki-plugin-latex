<?php
/**
 * LaTeX Rendering Class
 * Copyright (C) 2003  Benjamin Zeiss <zeiss@math.uni-goettingen.de>
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 * --------------------------------------------------------------------
 * @author Benjamin Zeiss <zeiss@math.uni-goettingen.de>
 * @version v0.8
 * @package latexrender
 *
 */

class LatexRender {

    // ====================================================================================
    // Variable Definitions
    // ====================================================================================
    var $_picture_path = "";
    var $_picture_path_httpd = "";
    // i was too lazy to write mutator functions for every single program used
    // just access it outside the class or change it here if nescessary

	/////////////////////////////////////////
    ////// NOTE - DO NOT CHANGE THESE SETTINGS. USE THE CONFIG MANAGER
    //////    IN DOKUWIKI INSTEAD; THESE ARE OVERWRITTEN.
    var $_tmp_dir = "";
    var $_keep_tmp = false;   // keep temporary files? (good for debug)
    var $_latex_path = "latex";
    var $_dvips_path = "dvips";
    var $_convert_path = "convert";
    var $_identify_path = "identify";
    var $_xsize_limit = 1000;
    var $_ysize_limit = 500;
    var $_string_length_limit = 2000;
    var $_image_format = "png"; //change to png if you prefer
	////////////////////////////////////

		var $_font_size = 10;
		var $_latexclass = "article"; //install extarticle class if you wish to have smaller font sizes
    var $_tmp_filename;
    // this most certainly needs to be extended. in the long term it is planned to use
    // a positive list for more security. this is hopefully enough for now. i'd be glad
    // to receive more bad tags !
    var $_latex_tags_blacklist = array(
	   "include","def","command","loop","repeat","open","toks","output","input",
	   "catcode","name","^^",
	   "\\every","\\errhelp","\\errorstopmode","\\scrollmode","\\nonstopmode","\\batchmode",
	   "\\read","\\write","csname","\\newhelp","\\uppercase", "\\lowercase","\\relax","\\aftergroup",
	   "\\afterassignment","\\expandafter","\\noexpand","\\special"
	    );
    var $_errorcode = 0;
		var $_errorextra = "";
		var $_filename;


    // ====================================================================================
    // constructor
    // ====================================================================================

    /**
	* Initializes the class
	*
	* @param string path where the rendered pictures should be stored
	* @param string same path, but from the httpd chroot
	*/
    function LatexRender($picture_path,$picture_path_httpd,$tmp_dir) {
	   $this->_picture_path = $picture_path;
	   $this->_picture_path_httpd = $picture_path_httpd;
	   $this->_tmp_dir = $tmp_dir;
    }

    // ====================================================================================
    // public functions
    // ====================================================================================

    /**
	* Picture path Mutator function
	*
	* @param string sets the current picture path to a new location
	*/
    function setPicturePath($name) {
	   $this->_picture_path = $name;
    }

    /**
	* Picture path Mutator function
	*
	* @returns the current picture path
	*/
    function getPicturePath() {
	   return $this->_picture_path;
    }

    /**
	* Picture path HTTPD Mutator function
	*
	* @param string sets the current httpd picture path to a new location
	*/
    function setPicturePathHTTPD($name) {
	   $this->_picture_path_httpd = $name;
    }

    /**
	* Picture path HTTPD Mutator function
	*
	* @returns the current picture path
	*/
    function getPicturePathHTTPD() {
	   return $this->_picture_path_httpd;
    }

    /**
	* Tries to match the LaTeX Formula given as argument against the
	* formula cache. If the picture has not been rendered before, it'll
	* try to render the formula and drop it in the picture cache directory.
	*
	* @param string formula in LaTeX format
	* @returns the webserver based URL to a picture which contains the
	* requested LaTeX formula. If anything fails, the resultvalue is false.
	*/
    function getFormulaURL($latex_formula) {
	   // circumvent certain security functions of web-software which
	   // is pretty pointless right here
	   $latex_formula = preg_replace("/&gt;/i", ">", $latex_formula);
	   $latex_formula = preg_replace("/&lt;/i", "<", $latex_formula);
		
		 $this->latex_document = $this->_preamble."\n".trim($latex_formula)."\n".$this->_postamble;

	   $formula_hash = md5($latex_formula);

	   $filename = "img".$formula_hash.'.'.$this->_image_format;
	   $full_path_filename = $this->getPicturePath()."/".$filename;
		 $this->_filename = $full_path_filename;
		
	   if (is_readable($full_path_filename)) {
		  return $this->getPicturePathHTTPD()."/".$filename;
	   } else {
		  // security filter: reject too-long formulas
		  if (strlen($latex_formula) > $this->_string_length_limit) {
		  	$this->_errorcode = 1;
							$this->_errorextra = ': '.strlen($latex_formula);
		    return false;
		  }

		  // security filter: try to match against LaTeX-Tags Blacklist
		  for ($i=0;$i<sizeof($this->_latex_tags_blacklist);$i++) {
			 if (stristr($latex_formula,$this->_latex_tags_blacklist[$i])) {
			 	$this->_errorcode = 2;
			   return false;
			 }
		  }

		  // security checks assume correct formula, let's render it
		  if ($this->renderLatex($this->latex_document,$full_path_filename)) {
			 return $this->getPicturePathHTTPD().$filename;
		  } else {
			 // uncomment if required
			 // $this->_errorcode = 3;
			 return false;
		  }
	   }
    }

    // ====================================================================================
    // private functions
    // ====================================================================================

    /**
	* returns the dimensions of a picture file using 'identify' of the
	* imagemagick tools. The resulting array can be adressed with either
	* $dim[0] / $dim[1] or $dim["x"] / $dim["y"]
	*
	* @param string path to a picture
	* @returns array containing the picture dimensions
	*/
    function getDimensions($filename) {
	   $output=$this->myexec($this->_identify_path." ".$filename, $status);
	   $result=explode(" ",$output);
	   $dim=explode("x",$result[2]);
	   $dim["x"] = $dim[0];
	   $dim["y"] = $dim[1];

	   return $dim;
    }

    /**
	* Renders a LaTeX formula by the using the following method:
	*  - write the formula into a wrapped tex-file in a temporary directory
	*    and change to it
	*  - Create a DVI file using latex (tetex)
	*  - Convert DVI file to Postscript (PS) using dvips (tetex)
	*  - convert, trim and add transparancy by using 'convert' from the
	*    imagemagick package.
	*  - Save the resulting image to the picture cache directory using an
	*    md5 hash as filename. Already rendered formulas can be found directly
	*    this way.
	*
	* @param string LaTeX formula
	* @returns true if the picture has been successfully saved to the picture
	*		cache directory
	*/
    function renderLatex($latex_document,$destination) {

	   $current_dir = getcwd();

	   chdir($this->_tmp_dir);
		
	   $this->_tmp_filename = md5(rand().$destination);

		 $this->_cmdout = " >> ".$this->_tmp_filename.".cmd 2>&1";
	   // create temporary latex file
	   $fp = fopen($this->_tmp_dir."/".$this->_tmp_filename.".tex","w");
	   fputs($fp,$latex_document);
	   fclose($fp);

	   // create temporary dvi file
	   $command = $this->_latex_path." --interaction=nonstopmode ".$this->_tmp_filename.".tex";
	   $this->myexec($command,$status_latex);

		// LaTeXing only fails if DVI doesn't exist. - let's ignore some minor errors.
	  if (!file_exists($this->_tmp_filename.".dvi"))
		{
			if( ! $this->_keep_tmp)
				$this->cleanTemporaryDirectory();
			chdir($current_dir);
			$this->_errorcode = 4; /// Error 4: latexing failed
			return false;
		}

	   // convert dvi file to postscript using dvips
	   $command = $this->_dvips_path." -E ".$this->_tmp_filename.".dvi"." -o ".$this->_tmp_filename.".ps";
	   $this->myexec($command,$status_dvips);


	   // imagemagick convert ps to image and trim picture
	   $command = $this->_convert_path." ".$this->_tmp_filename.".ps ".
				$this->_tmp_filename.".".$this->_image_format;
	   $this->myexec($command,$status_convert);

		 
		 if ($status_dvips || $status_convert) {
			if( ! $this->_keep_tmp)
				$this->cleanTemporaryDirectory();
			chdir($current_dir);
			$this->_errorcode = 6;
			return false;
		}

	   // test picture for correct dimensions
	   $dim = $this->getDimensions($this->_tmp_filename.".".$this->_image_format);

	   if ( ($dim["x"] > $this->_xsize_limit) or ($dim["y"] > $this->_ysize_limit)) {
		  if( ! $this->_keep_tmp)
				$this->cleanTemporaryDirectory();
			chdir($current_dir);
		  $this->_errorcode = 5; // image too big.
		  $this->_errorextra = ": " . $dim["x"] . "x" . $dim["y"];
		  return false;
	   }

	   // copy temporary formula file to cahed formula directory
	   $status_code = copy($this->_tmp_filename.".".$this->_image_format,$destination);
		 chdir($current_dir);
		 
	   if( ! $this->_keep_tmp)
				$this->cleanTemporaryDirectory();
		
	   if (!$status_code) { $this->_errorcode = 7; return false; }

	   return true;
    }
		
		//// Run command and append it to _cmdoutput if that variable exists. (for debug).
		function myexec($cmd,&$status) {
			$cmd = "$cmd 2>&1";
			$lastline = exec($cmd,$output,$status);
			
//			//strip trailing empty lines from output
//			for($i = count($output)-1 ; $i > 0 ; $i -= 1) 
//				if($output[$i]) break;
//			$lastline = $output[$i];
			
			if(isset($this->_cmdoutput))
				$this->_cmdoutput .= "\n>>>>> $cmd\n".trim(implode(PHP_EOL,$output)).PHP_EOL."  --- exit status ".$status;
			
			return $lastline;
		}

   /**
		* Cleans the temporary directory
		*/
    function cleanTemporaryDirectory() {
//	   $current_dir = getcwd();
//	   chdir($this->_tmp_dir);

			@unlink($this->_tmp_dir."/".$this->_tmp_filename.".tex");
			@unlink($this->_tmp_dir."/".$this->_tmp_filename.".aux");
			@unlink($this->_tmp_dir."/".$this->_tmp_filename.".log");
			@unlink($this->_tmp_dir."/".$this->_tmp_filename.".dvi");
			@unlink($this->_tmp_dir."/".$this->_tmp_filename.".ps");
			@unlink($this->_tmp_dir."/".$this->_tmp_filename.".".$this->_image_format);

//	   chdir($current_dir);
    }

}
