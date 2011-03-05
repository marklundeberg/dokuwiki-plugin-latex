<?php

$conf['latex_path'] = '/usr/bin/latex --interaction=nonstopmode';
$conf['dvips_path'] = '/usr/bin/dvips -E';
$conf['convert_path'] = '/usr/bin/convert -density 120 -trim -transparent "#FFFFFF"';
$conf['identify_path'] = '/usr/bin/identify';
$conf['image_format'] = 'png';
$conf['tmp_dir'] = '/tmp';
$conf['keep_tmp'] = false;
$conf['colour'] = '{rgb}{0.50,0.00,0.00}';

$conf['xsize_limit'] = 1000;
$conf['ysize_limit'] = 500;
$conf['string_length_limit'] = 2000;

$conf['preamble'] = 'Asdf\njkhgl';
