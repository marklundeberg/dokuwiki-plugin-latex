<?php

$lang['adminlink'] = 'If you want this change to affect old LaTeX renders, you should delete the image cache from the <a href="'.
  wl($ID).'?do=admin&page=latex'.
  '" target="_blank" class=""wikilink1">admin page</a> and refresh your browser\'s cache.';

$lang['latex_path'] = 'Path to <code>latex</code> program and options';
$lang['dvips_path'] = 'Path to <code>dvips</code> program and options';
$lang['convert_path'] = 'Path to ImageMagick <code>convert</code> program and options;
   <code>-density &lt;number&gt;</code> controls rendering size, and <code>-transparent &lt;colour&gt;</code>
   converts colour to transparency. '.$lang['adminlink'];
$lang['identify_path'] = 'Path to ImageMagick <code>identify</code> program';
$lang['xsize_limit'] = 'Maximum width (in pixels) of rendered LaTeX images';
$lang['ysize_limit'] = 'Maximum height (in pixels) of rendered LaTeX images';
$lang['string_length_limit'] = 'Maximum length (in characters) of LaTeX code (not including pre/post-amble)';
$lang['image_format'] = 'Image format of rendered LaTeX; <code>png</code> looks best, but <code>gif</code> is more compact.';
$lang['tmp_dir'] = 'Location of temporary folder';
$lang['keep_tmp'] = 'Keep temporary tex/aux/log/dvi/ps files? Turn on this option to debug your LaTeX rendering problems.';

$lang['preamble'] = 'LaTeX compilation preamble;
here you can include <a href="http://en.wikibooks.org/wiki/LaTeX/Colors#Color_Models" target="_blank" class="urlextern">colours</a>,
new packages, and more. '.$lang['adminlink'];
$lang['postamble'] = 'LaTeX compilation postamble';
