<?php

$conf['latex_path'] = 'latex --interaction=nonstopmode';
$conf['dvips_path'] = 'dvips -E';
$conf['convert_path'] = 'convert -density 120 -trim -transparent "#FFFFFF"';
$conf['identify_path'] = 'identify';
$conf['image_format'] = 'png';
$conf['tmp_dir'] = '/tmp';
$conf['keep_tmp'] = false;
$conf['colour'] = '{rgb}{0.408,0.094,0.059}';

$conf['xsize_limit'] = 1000;
$conf['ysize_limit'] = 500;
$conf['string_length_limit'] = 2000;
