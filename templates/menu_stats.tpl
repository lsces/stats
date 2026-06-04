{strip}
{if $packageMenuTitle}<a class="dropdown-toggle" data-toggle="dropdown" href="#"> {tr}{$packageMenuTitle}{/tr} <b class="caret"></b></a>{/if}
<ul class="dropdown-menu">
	{if $gBitUser->hasPermission( 'p_stats_view' )}
		<li><a class="item" href="{$smarty.const.STATS_PKG_URL}index.php">{biticon ipackage="icons" iname="user-home" iexplain="Site Stats"}</a></li>
		{if $gBitSystem->mDb->mType eq 'postgres'}
			<li><a class="item" href="{$smarty.const.STATS_PKG_URL}users.php">{biticon ipackage="icons" iname="user-desktop" iexplain="User Stats"}</a></li>
		{/if}
	{/if}
	{if $gBitSystem->isFeatureActive( 'stats_referers' ) && $gBitUser->hasPermission( 'p_stats_view_referer' )}
		<li><a class="item" href="{$smarty.const.STATS_PKG_URL}referrers.php">{biticon ipackage="icons" iname="dialog-information" iexplain="Referer Stats"}</a></li>
	{/if}
</ul>
{/strip}
