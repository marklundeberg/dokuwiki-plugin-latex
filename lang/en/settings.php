<?php

$lang['adminlink'] = 'If you want this change to affect old LaTeX renders, you should delete the image cache from the <a href="'.
  wl($ID).'?do=admin&page=latex'.
  '" target="_blank" class=""wikilink1">admin page</a> and refresh your browser\'s cache.';

$lang['latex_path'] = 'Path to <code>latex</code> program';
$lang['dvips_path'] = 'Path to <code>dvips</code> program';
$lang['convert_path'] = 'Path to ImageMagick <code>convert</code> program';
$lang['convert_options'] = 'Options for <code>convert</code> program; e.g.
   <code>-density &lt;number&gt;</code> controls rendering size, and <code>-transparent &lt;colour&gt;</code>
   converts colour to transparency. '.$lang['adminlink'];
$lang['identify_path'] = 'Path to ImageMagick <code>identify</code> program';
$lang['xsize_limit'] = 'Maximum width (in pixels) of rendered LaTeX images';
$lang['ysize_limit'] = 'Maximum height (in pixels) of rendered LaTeX images';
$lang['string_length_limit'] = 'Maximum length (in characters) of LaTeX code (not including pre/post-amble)';
$lang['image_format'] = 'Image format of rendered LaTeX; <code>png</code> looks best, but <code>gif</code> is more compact, especially if you add <code>+antialias</code> to the <code>convert</code> options below.';
$lang['latex_namespace'] = 'Media namespace in which to store cached image files; temporary files used during compilation will be stored in the <code>tmp</code> sub-namespace.';

$lang['preamble'] = 'LaTeX compilation preamble;
here you can include <a href="http://en.wikibooks.org/wiki/LaTeX/Colors#Color_Models" target="_blank" class="urlextern">colours</a>,
new packages, and more. '.$lang['adminlink'];
$lang['postamble'] = 'LaTeX compilation postamble';
