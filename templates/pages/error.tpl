{extends file="layout.tpl"}

{block name="content"}
<div class="error-page">
    <h1>404</h1>
    <p>{$message|escape}</p>
    <a href="/" class="btn">На главную</a>
</div>
{/block}
