<?PHP
$mod_tpl_start='
<table class="config" cellspacing="1">
	<tr>
		<td class="head nowrap" rowspan="2"><p>{CONTAINER-TITLE}</p></td>
		<td class="header">
			{IDLAY}
			{VAL5}
			<div class="forms">
			{MOD-SELECT}
				&nbsp;
				<select name="cview{MOD-KEY}" size="1" onchange="if (this.value==-1) $(\'.modconfig{MOD-KEY} td\').hide(); else $(\'.modconfig{MOD-KEY} td\').show();">
					<option value="0" {ACTIVE-SELECT_SELECTED-TRUE}>{ACTIVE-SELECT_TITLE-TRUE}</option>
					<option value="-1" {ACTIVE-SELECT_SELECTED-FALSE}>{ACTIVE-SELECT_TITLE-FALSE}</option>
				</select>
				&nbsp;
				<select name="cedit{MOD-KEY}" size="1">
					<option value="0" {EDIT-SELECT_SELECTED-TRUE}>{EDIT-SELECT_TITLE-TRUE}</option>
					<option value="-1" {EDIT-SELECT_SELECTED-FALSE}>{EDIT-SELECT_TITLE-FALSE}</option>
				</select>
				&nbsp;
			</div>
			<img src="tpl/{SKIN}/img/about.gif" alt="{MOD-TITLE}" class="toolinfo" width="16" height="16" />
			<span class="toolinfo">
				<table class="toolinfomod" cellspacing="0" cellpadding="0" border="0">
					<tr class="headline">
						<th>{LNG_MOD-INFO}</th>
						<th align="right"><small>idmod</small> {IDMOD}</th>
					</tr>
					<tr class="{MODCAT-ROW_DISABLED}">
						<td class="first"><strong>{LNG_MODCAT}</strong></td>
						<td class="first" align="right">{MODCAT}</td>
					</tr>
					<tr class="{MODVERB-ROW_DISABLED}">
						<td><strong>{LNG_MODVERB}</strong></td>
						<td align="right">{MODVERB}</td>
					</tr>
					<tr class="{MODORIG-ROW_DISABLED}">
						<td><strong>{LNG_MODORIG}</strong></td>
						<td align="right">{MODORIG}</td>
					</tr>
					<tr class="{MODVERS-ROW_DISABLED}">
						<td><strong>{LNG_MODVERS}</strong></td>
						<td align="right">{MODVERS}</td>
					</tr>				
				</table>
			</span>

		{MOD-NAME}
		</td>
	</tr>
	<tr class="modconfig{MOD-KEY}">
		<td class="content nopadd" {MOD_NOTACTIVE}>
';

$mod_tpl_end='
		</td>
	</tr>
</table>
';

$mod_tpl_start_empty='
<table class="config" cellspacing="1">
	<tr>
		<td class="head nowrap" rowspan="2"><p>{CONTAINER-TITLE}</p></td>
		<td class="content">
			<div class="forms">	
				{MOD-SELECT}
			</div>
';


$mod_tpl_selectmod='
		<select name="c{MOD-KEY}" size="1" onchange="document.editform.action.value=\'change\';document.editform.changed.value=\'{MOD-KEY}\';document.editform.anchor.value=\'{CONTAINER-NAME}_{MOD-KEY}\';document.editform.submit();">
{SELECT-ENTRIES}
		</select>
';

$mod_tpl_selectmod_entry='
			<option value="{ENTRY-VALUE}" {ENTRY-SELECTED}>{ENTRY-TITLE}</option>
';

?>