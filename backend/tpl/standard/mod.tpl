
<!-- Anfang mod.tpl -->
<div id="main">
		<div class="forms">
  {NEW_MOD}{IMPORT_MOD}{BACK}
  </div>
 <h5>{AREA}</h5>
	<!-- BEGIN ERROR -->
	<p class="errormsg">{ERRORMESSAGE}</p>
	<!-- END ERROR -->
	<!-- BEGIN WARN -->
	<p class="warning">{WARNMESSAGE}</p>
	<!-- END WARN -->
	<!-- BEGIN OK -->
	<p class="ok">{OKMESSAGE}</p>
	<!-- END OK -->
    <table class="uber">
      <tr>
        <th align="left">{MOD_MODULECAT}</th>
        <th align="left">{MOD_MODULENAME}</th>
        <th align="left">{MOD_MODULEVERSION}</th>
        <th width="100%">{MOD_DESCRIPTION}</th>
        <th class="center">{MOD_ACTION}</th>
      </tr>
<!-- BEGIN ENTRY -->
      <tr onmouseover="this.style['background']='{OVERENTRY_BGCOLOR}';" onmouseout="this.style['background']='{ENTRY_BGCOLOR}';" bgcolor="{ENTRY_BGCOLOR}">
        <td class="entry nowrap">{ENTRY_ICON} {ENTRY_CAT}</td>
        <td class="entry nowrap">{ENTRY_NAME}</td>
        <td class="entry">{ENTRY_VERSION}</td>
        <td class="entry">{ENTRY_DESCRIPTION}</td>
        <td class="entry buttons">{ENTRY_INFO} {ENTRY_ANAME} {ENTRY_EDIT} {ENTRY_DUPLICATE} {ENTRY_CONFIG} {ENTRY_SQL} {ENTRY_IMPORT} {ENTRY_EXPORT} {ENTRY_DOWNLOAD} {ENTRY_UPDATE} {ENTRY_DELBUT}</td>
      </tr>
<!-- END ENTRY -->
<!-- BEGIN NOENTRY -->
      <tr class="tblrbgcolors2">
        <td class="content7" colspan="4">{MOD_NOMODULES}<td>
      </tr>
<!-- END NOENTRY -->
<!-- BEGIN FILEUPLOAD -->
	<form action="main.php" method="post" enctype="multipart/form-data">
		<input type="hidden" name="{FILEUPLOAD_SESSIONNAME}" value="{FILEUPLOAD_SESSIONID}" />
		<input type="hidden" name="area" value="{FILEUPLOAD_AREA}" />
		<input type="hidden" name="action" value="{FILEUPLOAD_ACTION}" />
		<input type="hidden" name="idclient" value="{FILEUPLOAD_CLIENT}" />
		<tr>
			<td class="content7" align="right" colspan="3">{FILEUPLOAD_TEXT}</td>
			<td class="content7" align="center"><input class="uplinput" name="{FILEUPLOAD_NAME}" type="file" size="16" /></td>
			<td class="content7" align="right"><input type="image" src="{FILEUPLOAD_PICT}" align="right" alt="{FILEUPLOAD_HINT}" title="{FILEUPLOAD_HINT}" /></td>
		</tr>
	</form>
<!-- END FILEUPLOAD -->
<!-- BEGIN CLOSELINE -->
	    <tr>
			<td class="content7" align="right" colspan="5">{NOVALUE}</td>
        </tr>
<!-- END CLOSELINE -->

    </table>
</div>
{FOOTER}
