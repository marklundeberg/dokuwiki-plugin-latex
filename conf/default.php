<?php

$conf['latex_path'] = 'latex';
$conf['dvips_path'] = 'dvips';
$conf['convert_path'] = 'convert';
$conf['convert_options'] = '-density 120 -trim -transparent "#FFFFFF"';
$conf['identify_path'] = 'identify';
$conf['image_format'] = 'png';
$conf['latex_namespace'] = 'wiki:latex';

$conf['xsize_limit'] = 1000;
$conf['ysize_limit'] = 500;
$conf['string_length_limit'] = 2000;

$conf['preamble'] = '\\documentclass[10pt]{article}
\\usepackage[utf8]{inputenc}
\\usepackage{amsmath}
\\usepackage{amsfonts}
\\usepackage{amssymb}
\\usepackage{color}
\\pagestyle{empty}
\\begin{document}
\\definecolor{MyColour}{rgb}{0.50,0.00,0.00}
{\\color{MyColour}';

$conf['postamble'] = '}\end{document}';

// bugfix: force all newlines to be \r\n. Web browsers submit textarea data with \r\n, whereas
// this default.php might be stored with either \r\n or \n  (probably \n).
// config manager demands an exact match to default value here or it will regard the textarea as changed.
// so, we should make the default value match what the web browser hands back to us.
$conf['preamble'] = str_replace("\n","\r\n",str_replace("\r\n","\n",$conf['preamble']));
$conf['postamble'] = str_replace("\n","\r\n",str_replace("\r\n","\n",$conf['postamble']));

