{LDAP action="search"}
{if isset($results)}
	{if $results|@count > 0}
		{if $results|@count < 2}
			<ul>
				{foreach from=$results item=entry}
					<li>{$entry|var_dump}</li>
				{/foreach}
			</ul>
		{else}
			<ul>
				{foreach from=$results item=entry}
					<li>
						<a href="{LDAP action="url_for" maction="default" cn=$entry.cn}">Details for {$entry.cn}</a>
					</li>
				{/foreach}
			</ul>
		{/if}
	{else}
		<p>No result found</p>
	{/if}
{/if}