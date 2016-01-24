<link rel="stylesheet" type="text/css" href="mods/advanced_search/style.css">
<div class=block-header>Advanced Search Options</div>
<form id=advsearch action="?a=search&p=adv_search&act=go" method=post>
	<table class="kb-table">
		<tr class=kb-table-row-odd>
			<td class=searchlabel>Victim's name¹:</td>
			<td>
				<input class=searchfield type="text" name=victimname>
			</td>
			<td class=searchlabel>Involved ship:</td>
			<td>
				<input class=searchfield type="text" name=invship>
			</td>
		</tr>
		<tr class=kb-table-row-even>
			<td class=searchlabel>Victim's corp¹:</td>
			<td>
				<input class=searchfield type="text" name=victimcorp>
			</td>
			<td class=searchlabel>Involved shiptype:</td>
			<td>
				<select style="width: 100%;" name=invshipclass>
					<option value=all>All</option>
					{foreach from=$kbShipClasses key=key item=l}<option value={$key}>{$l}</option>
					{/foreach}
					
				</select>
			</td>
		</tr>
		<tr class=kb-table-row-odd>
			<td class=searchlabel>Victim's alliance¹:</td>
			<td>
				<input class=searchfield type="text" name=victimally>
			</td>
			<td class=searchlabel>Involved weapon:</td>
			<td>
				<input class=searchfield type="text" name=invweapon>
			</td>
		</tr>
		<tr class=kb-table-row-even>
			<td class=searchlabel>Destroyed ship¹:</td>
			<td>
				<input class=searchfield type="text" name=destroyedship>
			</td>
			<td class=searchlabel>Involved pilot:</td>
			<td>
				<input class=searchfield type="text" name=invpilot>
			</td>
		</tr>
		<tr class=kb-table-row-odd>
			<td class=searchlabel>Destroyed type:</td>
			<td>
				<select style="width: 100%;" name=destroyedshipclass>
					<option value=all>All</option>
					{foreach from=$kbShipClasses key=key item=l}<option value={$key}>{$l}</option>
					{/foreach}
					
				</select>
			</td>
			<td class=searchlabel>Involved corporation:</td>
			<td>
				<input class=searchfield type="text" name=invcorp>
			</td>
		</tr>
		<tr class=kb-table-row-even>
			<td class=searchlabel>Region¹:</td>
			<td>
				<input class=searchfield type="text" name=region>
			</td>
			<td class=searchlabel>Involved alliance:</td>
			<td>
				<input class=searchfield type="text" name=invally>
			</td>
		</tr>
		<tr class=kb-table-row-odd>
			<td class=searchlabel>Constellation¹:</td>
			<td>
				<input class=searchfield type="text" name=const>
			</td>
			<td class=searchlabel>Dropped item:</td>
			<td>
				<input class=searchfield type="text" name=itemdropped>
			</td>
		</tr>
		<tr class=kb-table-row-even>
			<td class=searchlabel>System¹:</td>
			<td>
				<input class=searchfield type="text" name=system>
			</td>
			<td class=searchlabel>Destroyed item:</td>
			<td>
				<input class=searchfield type="text" name=itemdestroyed>
			</td>
		</tr>
		<tr class=kb-table-row-odd>
			<td class=searchlabel>System range²:</td>
			<td>
				<select style="width: 100%;" name=sysrange>
					<option value=0>0</option>
					<option value=1>1</option>
					<option value=2>2</option>
					<option value=3>3</option>
					<option value=4>4</option>
					<option value=5>5</option>
				</select>
			</td>
			<td class=searchlabel>Involved count³:</td>
			<td>
				<input class=searchfield type="text" name=invcount>
			</td>
		</tr>
		<tr class=kb-table-row-even>
			<td class=searchlabel>Date range⁴:</td>
			<td>
				<input class=searchfield type="text" name=daterange>
			</td>
			<td class=searchlabel>Comment count³:</td>
			<td>
				<input class=searchfield type="text" name=commcnt>
			</td>
		</tr>
		<tr class="kb-table-row-odd">
			<td class=searchlabel>Kills per page:</td>
			<td>
				<select style="width: 100%;" name=killperpage>
					<option value=5>5</option>
					<option value=10>10</option>
					<option value=15>15</option>
					<option SELECTED value=25>25</option>
					<option value=50>50</option>
					<option value=100>100</option>
					<option value=150>150</option>
					<option value=250>250</option>
					<option value=400>400</option>
				</select>
			</td>
			<td class=searchbutton colspan=2>
				<input type=reset name=reset value=Clear>&nbsp;<input type=submit name=submit value=Search>
			</td>
		</tr>
	</table>
</form>
<div class=search-info>
	&nbsp;&nbsp;&nbsp;&nbsp;Filter phrases apply to kill's victim information unless explicitly defined otherwise. Only results matching full string will be searched for. Wildcard '*' (asterisk) is allowed within the filter phrase. Partial matches should be done by using asterisk symbol. Empty field means all values. Only the results matching <b>all</b> of the defined filter fields will be displayed. Multiple options must be separated by <b>comma</b> and multiple obligatory filters must be separated by the <b>&amp;</b> (ampersand) symbol. Optional values mean that only of of them must be satisfied, while obligatory values must be satisfied all for the field to be included in results.<br>
	<br>
	<b>Notes:</b><br>
	&nbsp;&nbsp;¹ Does not allow multiple obligatory values. All values in this field will be treated as optional.<br>
	&nbsp;&nbsp;² Defines how far (in jumps) from the original system (defined by the <i>System</i> field) to look for neighbouring systems to include in the search. If the <i>System</i> field is empty, this value is ignored.<br>
	&nbsp;&nbsp;³ Accepts numbers, number sets and ranges (<i>f. ex.: 1, 4, 5-8</i>).<br>
	&nbsp;&nbsp;⁴ Similar to the above, accepts date sets and ranges (dates are in format 'Y.m.d' - 4-digit year, followed by two-digit month and then by two-digit day, separated by periods), ex.: 2012.03.01-2012.06.30.
</div>