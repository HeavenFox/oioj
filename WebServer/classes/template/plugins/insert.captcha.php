<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     insert.captcha.php
 * Type:     insert
 * Name:     captcha
 * Purpose:  Inserts a CAPTCHA
 * -------------------------------------------------------------
 */
function smarty_insert_captcha($params, Smarty_Internal_Template $template)
{
    require_once LIB_DIR . 'recaptchalib.php';
    return recaptcha_get_html(Config::$CAPTCHA_Public);
}
?>
