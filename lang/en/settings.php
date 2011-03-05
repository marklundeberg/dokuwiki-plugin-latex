<?php

$lang['latex_path'] = 'Path to <code>latex</code> binary';
$lang['dvips_path'] = 'Path to <code>dvips</code> binary';
$lang['convert_path'] = 'Path to ImageMagick <code>convert</code> binary';
$lang['identify_path'] = 'Path to ImageMagick <code>identify</code> binary';
$lang['image_format'] = 'Image format of rendered LaTeX: <code>png</code> looks best, but <code>gif</code> is more compact.';
$lang['tmp_dir'] = 'Location of temporary folder';
$lang['colour'] = 'Equation colour, written as <code>{model}{value1,value2,...}</code>.
    <code>model</code> may be one of <code>gray</code>, <code>rgb</code>, <code>RGB</code>, <code>HTML</code>, <code>CMYK</code>
	(<a href="http://en.wikibooks.org/wiki/LaTeX/Colors#Color_Models" target="_blank" class="urlextern">more info on values here</a>).
	You should delete the image cache for this change to affect old equations.';
$lang['density'] = 'Rasterization density -- controls size of rendered LaTeX. You should delete the image cache for this change to affect old equations.';
$lang['keep_tmp'] = 'Keep temporary tex/aux/log/dvi/ps files rather than deleting them after rendering. Turn on this option to debug your LaTeX rendering problems.';