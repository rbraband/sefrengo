<!-- Anfang side_config.tpl -->
<script type="text/javascript">
<!--
function timemanagement() {
	document.editform.online[2].selected = true;
}
//-->
</script>



<div id="main" class="siteconf">
<h5>{AREA_TITLE}</h5>
<!-- BEGIN ERROR_BLOCK -->
<p class="errormsg">{ERR_MSG}</p>
<!-- END ERROR_BLOCK -->
<form name="editform" method="post" action="{FORM_ACTION}">
<input type="hidden" name="action" value="save" />
<input type="hidden" name="idtplconf" value="{IDTPLCONF}" />
<input type="hidden" name="idcat" value="{IDCAT}" />
<input type="hidden" name="parent" value="{PARENT}" />


<table class="config" cellspacing="1">
	<tr>
		<td class="head">
			<p>{CON_CATCONFIG}</p>
		</td>
		<td class="nopadd">

				<table class="sitehack" cellspacing="1">
						<tr>
								<td colspan="2" class="header">
								<!-- Ueberschrift Titel -->
								{LNG_CAT-TITLE}
								</td>
						</tr>
						<tr>
								<td colspan="2">
										<!-- Eingabe Titel -->
										<input class="w800" type="text" name="name" value="{CAT_TITLE}" size="30" maxlength="255" />
								</td>
						</tr>
 <!-- BEGIN URL_REWRITE -->
						<tr><td class="header" colspan="2">{LNG_REWRITE_PAGE-URL}</td></tr>
						<tr>
								<td colspan="2">
									<input type="checkbox" name="rewrite_use_automatic" value="1" id="rewrite_use_automatic" {REWRITE_USE_AUTOMATIC_CHECKED}/>
									<label for="rewrite_use_automatic">{LNG_REWRITE_AUTO-URL}</label>
								</td>
						</tr>
						<tr>
								<td	 colspan="2">
										<input class="w800" type="text" name="rewrite_alias" id="rewrite_alias" value="{REWRITE_ALIAS}"	{REWRITE_URL_DISABLED} size="90" maxlength="255"/>{REWRITE_ERROR}
								</td>
						</tr>
						<tr><td	 colspan="2" ><small>{LNG_REWRITE_URL-OF-THIS-PAGE} {REWRITE_CURRENT_URL}</small></td></tr>
<!-- END URL_REWRITE -->

<!-- BEGIN USER_RIGHTS -->
						<tr>
								<td colspan="2">{BACKENDRIGHTS}&nbsp;&nbsp;&nbsp;&nbsp;{FRONTENDRIGHTS}</td>
						</tr>
<!-- END USER_RIGHTS -->	

<!-- BEGIN DESCRIPTION -->
					<tr>
						<td class="header" colspan="2">
							<table>
								<tr>
									<td">
										<!-- Beschreibung Notizen -->
										{LANG_NOTICES}:
									</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<table>
								<tr>
									<td">
										<!-- Feld Notizen - interne Beschreibung -->
										<textarea name="description" rows="3" cols="30" class="w800 sans-serif">{NOTICES}</textarea>
									</td>
								</tr>
							</table>
						</td>
					</tr>

<!-- END DESCRIPTION -->

				</table>
			</tr>
		</td>
	</table>
	
	
<!-- BEGIN BUTTONS -->
<table class="config" id="siteconfhack2" cellspacing="1">
	<tr>
		<td class='content7' style='text-align:right' colspan='2'>
			<input type='submit' name='sf_save' title='{BUTTON-SAVE_TITLE}' value='{BUTTON-SAVE_VALUE}' class="sf_buttonAction" />
			<input type='submit' name='sf_apply' title='{BUTTON-APPLY_TITLE}' value='{BUTTON-APPLY_VALUE}' class="sf_buttonAction" />
			<input type='button' name='sf_cancel' title='{BUTTON-CANCEL_TITLE}' value='{BUTTON-CANCEL_VALUE}' class="sf_buttonActionCancel" onclick="window.location='{BUTTON-CANCEL_ONCLICK-LOCATION}'" />
		</td>	
	</tr>
</table>
<!-- END BUTTONS -->



<!-- BEGIN TPL-CONF -->
<table class="config" id="siteconfhack2" cellspacing="1">
<tr>
	<td class="head nowrap">{LNG_TEMPLATE}</td>
	<td colspan="3">
		<!-- BEGIN TPL-CONF_SELECT -->
		<select name="idtpl" size="1" onchange="document.editform.action.value='changetpl';document.editform.submit();">
			<!-- BEGIN TPL-CONF_SELECT_ENTRY -->
			<option value="{ENTRY-VALUE}" {ENTRY-SELECTED}>{ENTRY-TITLE}</option>
			<!-- END TPL-CONF_SELECT_ENTRY -->
		</select>
		<!-- END TPL-CONF_SELECT -->
	</td>
</tr>
</table>
<!-- END TPL-CONF -->

{TPL-MOD_CONF}

<!-- BEGIN HIDDEN_FIELDS -->
	<!-- BEGIN HIDDEN-FIELDS_FIELD -->		
	<input type="hidden" name="{FIELD-NAME}" value="{FIELD-VALUE}" />
	<!-- END HIDDEN-FIELDS_FIELD -->
<!-- END HIDDEN_FIELDS -->

{BUTTONS-BOTTOM}

</form>
</div>
{FOOTER}