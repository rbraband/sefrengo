<!-- Anfang goup_config.tpl -->
<div id="main">
<div class="forms">
		{BACK}
</div>
    <h5>{AREA}</h5>
    <!-- BEGIN ERROR -->
    <p class="errormsg">{ERRORMESSAGE}</p>
    <!-- END ERROR -->
		<form name="perms" method="post" action="{FORM_URL}">
		    <input type="hidden" name="area" value="group_config" />
		    <input type="hidden" name="action" value="save" />
		    <input type="hidden" name="idgroup" value="{IDGROUP}" />
		    <input type="hidden" name="idclient" value="{IDCLIENT}" />
		    <input type="hidden" name="idlang" value="{IDLANG}" />
		    <input type="hidden" name="order" value="{ORDER}" />
		    <input type="hidden" name="ascdesc" value="{ASCDESC}" />
			<table class="uber sfSelectTableRow">
			<!-- BEGIN MAIN_RIGHTS -->
		      <tr>
		        <th colspan="3" align="left">{PERM_AREA}
          </th>
				{PERM_AREA_ON}
				{PERM_AREA_OFF}
			  </tr>
			<!-- BEGIN MAIN_RIGHTS_ROW -->
		      <tr class="tblrbgcolors2">
				<td width="14"></td>
				{PERM_BUTTON}
		        <td class="group"><label class="groupconf" for="{PERM_LABEL_FOR}">{PERM_LABEL}</label></td>
				<td width="14"></td>
				<td width="14"></td>
			  </tr>
			<!-- END MAIN_RIGHTS_ROW --><!-- END MAIN_RIGHTS -->
			<!-- BEGIN EMPTY -->
		      <tr>
		        <td class="entry nowrap">{LANG_NOGROUPS}<td>
		      </tr>
			<!-- END EMPTY -->
		      <tr>
					<td class="content7" colspan="5" style="text-align:right">
					<input name="sf_safe" type="submit" value="{BUTTON_SUBMIT_VALUE}" title="{BUTTON_SUBMIT_TEXT}" class="sf_buttonAction" />
					<input name="sf_apply" type="submit" value="{BUTTON_APPLY_VALUE}" title="{BUTTON_APPLY_TEXT}" class="sf_buttonAction" />
					<input name="sf_cancel" type="button" value="{BUTTON_CANCEL_VALUE}" title="{BUTTON_CANCEL_TEXT}" class="sf_buttonActionCancel" onclick="window.location='{BUTTON_CANCEL_URL}'" />
					</td>
			  </tr>

	    	</table>

<div id="rightsmenu" name="rightsmenu" onmouseover="clearhideRP();highlightRP(event,'on')" onmouseout="highlightRP(event,'off');dynamichideRP(event)"></div>
<script type="text/javascript" src="tpl/standard/js/popupmenu.js"></script>
<script type="text/javascript" src="tpl/standard/js/userrights.js"></script>
<script type="text/javascript">
	cms_rm.formname = "perms";
	cms_rm.adjustposition = false;
</script>
</div>
{FOOTER}
