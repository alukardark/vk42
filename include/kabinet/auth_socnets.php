<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
?>
<div class="row auth-socnets">
    <div class="">
        <button class="bg-vk" onclick="OAuth.auth(this, event, 'VK')" title="Вход через VK"><? \Axi::GSVG("vk_1") ?></button>
        <button class="bg-fb" onclick="OAuth.auth(this, event, 'FB')" title="Вход через FaceBook"><? \Axi::GSVG("fb_1") ?></button>
        <button class="bg-ok" onclick="OAuth.auth(this, event, 'OK')" title="Вход через Одноклассники"><? \Axi::GSVG("ok_1") ?></button>
    </div>
</div>