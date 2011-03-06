<?php

$lang['latex_path'] = 'Path to <code>latex</code> binary and options.';
$lang['dvips_path'] = 'Path to <code>dvips</code> binary and options.';
$lang['convert_path'] = 'Path to ImageMagick <code>convert</code> binary and options.
   <code>-density &lt;number&gt;</code> controls rendering size, and <code>-transparent &lt;colour&gt;</code>
   converts colour to transparency.';
$lang['identify_path'] = 'Path to ImageMagick <code>identify</code> binary.';
$lang['xsize_limit'] = 'Maximum width (in pixels) of rendered LaTeX images';
$lang['ysize_limit'] = 'Maximum height (in pixels) of rendered LaTeX images';
$lang['string_length_limit'] = 'Maximum length of LaTeX code';
$lang['image_format'] = 'Image format of rendered LaTeX: <code>png</code> looks best, but <code>gif</code> is more compact.';
$lang['tmp_dir'] = 'Location of temporary folder';
$lang['density'] = 'Rasterization density -- controls size of rendered LaTeX. You should delete the image cache for this change to affect old equations.';
$lang['keep_tmp'] = 'Keep temporary tex/aux/log/dvi/ps files, rather than deleting them after rendering? Turn on this option to debug your LaTeX rendering problems.';

$lang['preamble'] = 'LaTeX compilation preamble.
Include <a href="http://en.wikibooks.org/wiki/LaTeX/Colors#Color_Models" target="_blank" class="urlextern">colours</a>,
new packages, and more. You should delete the image cache for this change to affect old equations.';
$lang['postamble'] = 'LaTeX compilation postamble.';
