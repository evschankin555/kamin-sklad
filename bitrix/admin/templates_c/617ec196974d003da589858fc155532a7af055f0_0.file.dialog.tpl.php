<?php
/* Smarty version 4.3.1, created on 2024-06-21 13:11:19
  from '/home/httpd/vhosts/kamin-sklad.ru/httpdocs/bitrix/modules/thebrainstech.copyiblock/templates/dialog.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.3.1',
  'unifunc' => 'content_667551c7cac303_82458175',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '617ec196974d003da589858fc155532a7af055f0' => 
    array (
      0 => '/home/httpd/vhosts/kamin-sklad.ru/httpdocs/bitrix/modules/thebrainstech.copyiblock/templates/dialog.tpl',
      1 => 1712820545,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:content.tpl' => 1,
  ),
),false)) {
function content_667551c7cac303_82458175 (Smarty_Internal_Template $_smarty_tpl) {
ob_start();
$_smarty_tpl->_subTemplateRender('file:content.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
$_smarty_tpl->assign('content', ob_get_clean());
?>
javascript:(
    new BX.CDialog({
    content_url: "<?php echo $_smarty_tpl->tpl_vars['params']->value;?>
",
    width: 500,
    head: "",
    height: 260,
    resizable: false,
    draggable: true,
    content: "<?php echo strtr((string)$_smarty_tpl->tpl_vars['content']->value, array("\\" => "\\\\", "'" => "\\'", "\"" => "\\\"", "\r" => "\\r", 
                       "\n" => "\\n", "</" => "<\/", "<!--" => "<\!--", "<s" => "<\s", "<S" => "<\S",
                       "`" => "\\`", "\${" => "\\\$\{"));?>
",
    buttons: [BX.CDialog.btnSave, BX.CDialog.btnCancel]})
).Show();
<?php }
}
