<?php

$lang['encoding']   = 'utf-8';
$lang['direction']  = 'ltr';

$lang['menu'] = 'LaTeX plugin tasks (clean image cache, diagnose problems)'; 

$lang['legend_purge'] = "Purge cached LaTeX render files";
$lang['label_times'] = array('mtime'=>'modification time', 'atime'=>'access time');
$lang['label_olderthan'] = 'older than ';
$lang['label_days'] = ' days';
$lang['label_all'] = 'all ';
$lang['btn_purge'] = 'Delete!';

$lang['legend_preamble'] = "LaTeX preamble settings";

$lang['refresh_note'] = "If you have modified rendering settings (such as colour or image size), force-refresh (CTRL-F5) your browser's cache on each page (or clear your cache fully) to download the new images.";

/// title tags (ie. tooltips) for failed renders.
$lang['fail1'] = 'Fail: formula too long (in characters)';
$lang['fail2'] = 'Fail: triggered security filter; contains blacklisted LaTeX tags.';
//$lang['fail3'] = ''; // there is no fail #3;
$lang['fail4'] = 'Fail: LaTeX compilation failed.';
$lang['fail5'] = 'Fail: image too wide or too tall: ';
$lang['fail6'] = 'Fail: error during graphic processing.';
$lang['fail7'] = 'Fail: error saving to cache.';
$lang['failX'] = 'Fail: unknown error.';
$lang['failmax'] =', maximum allowed:';
