{extends file="two-column.tpl"}
{block name="column-left"}
<h2>{$c->title}</h2>
<div id="description">{$c->description}</div>
<div id="my-status">
{nocache}
{/nocache}
</div>
{/block}
{block name="column-right"}
<div id="sidebar-box">
<h2>At a Glance</h2>
<ul>
<li>Reg Begin:</li>
<li>Reg Deadline:</li>
<li>Contest Begin</li>
<li>Contest End:</li>
<li>Duration:</li>
</ul>
</div>
{/block}