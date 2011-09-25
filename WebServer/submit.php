<?php
require_once 'init.php';

import('OIOJ');

OIOJ::InitTemplate();

OIOJ::$template->display('submit.tpl');
?>