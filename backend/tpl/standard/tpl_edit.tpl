<!-- Anfang tpl_edit.tpl -->
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
<input type="hidden" name="anchor" value="" />


<table class="config" cellspacing="1">
	<tr>
		<td class="head">
			<p>{LNG_TPL-EDIT}</p>
		</td>
		<td class="nopadd">

				<table class="sitehack" cellspacing="1">
						<tr>
								<td colspan="2" class="header">
								<!-- Ueberschrift Titel -->
								{LNG_TPL-NAME}
								</td>
						</tr>
						<tr>
								<td colspan="2">
										<!-- Eingabe Titel -->
										<input class="w800" type="text" name="tplname" value="{TPL-NAME}" size="30" maxlength="255" />
								</td>
						</tr>

<!-- BEGIN USER_RIGHTS -->
						<tr>
								<td colspan="2">{RIGHTS}</td>
						</tr>
<!-- END USER_RIGHTS -->	

<!-- BEGIN DESCRIPTION -->
					<tr>
						<td class="header" colspan="2">
							<table>
								<tr>
									<td">
										<!-- Beschreibung Notizen -->
										{LNG_NOTICES}:
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
	

<!-- BEGIN TPL-CONF -->
<table class="config" id="siteconfhack2" cellspacing="1">
<tr>
	<td class="head nowrap">{LNG_LAYOUT}</td>
	<td >
		<!-- BEGIN TPL-CONF_SELECT -->
		<select name="idlay" size="1" onchange="document.editform.action.value='change';document.editform.submit();">
			<!-- BEGIN TPL-CONF_SELECT_ENTRY -->
			<option value="{ENTRY-VALUE}" {ENTRY-SELECTED}>{ENTRY-TITLE}</option>
			<!-- END TPL-CONF_SELECT_ENTRY -->
		</select>
		<!-- END TPL-CONF_SELECT -->
	</td>
</tr>
<!-- BEGIN TPL-CONF_DESCRIPTION -->
<tr>
	<td class="head nowrap">{LNG_LAYOUT-DESCRIPTION}</td>
	<td>{LAYOUT-DESCRIPTION}</td>
</tr>
<!-- END TPL-CONF_DESCRIPTION -->
</table>
<!-- END TPL-CONF -->



<!-- BEGIN HIDDEN_FIELDS -->
	<!-- BEGIN HIDDEN-FIELDS_FIELD -->		
	<input type="hidden" name="{FIELD-NAME}" value="{FIELD-VALUE}" />
	<!-- END HIDDEN-FIELDS_FIELD -->
<!-- END HIDDEN_FIELDS -->

{TPL-MOD_CONF}
	
<!-- BEGIN BUTTONS -->
<table class="config" id="siteconfhack2" cellspacing="1">
	<!-- BEGIN BUTTONS_OPTION-ADVANCED -->
	<tr>
		<td class=head>{LNG_ADVANCED}</td>
		<td><input type="checkbox" name="tpl_overwrite_all" value="1" id="touchme" /> <label for="touchme">{LNG_OVERWRITE-ALL}</label></td>
	</tr>
	<!-- END BUTTONS_OPTION-ADVANCED -->
	<tr>
		<td class='content7' style='text-align:right' colspan='2'>
			<input type='submit' name='sf_save' title='{BUTTON-SAVE_TITLE}' value='{BUTTON-SAVE_VALUE}' class="sf_buttonAction" />
			<input type='submit' name='sf_apply' title='{BUTTON-APPLY_TITLE}' value='{BUTTON-APPLY_VALUE}' class="sf_buttonAction" />
			<input type='button' name='sf_cancel' title='{BUTTON-CANCEL_TITLE}' value='{BUTTON-CANCEL_VALUE}' class="sf_buttonActionCancel" onclick="window.location='{BUTTON-CANCEL_ONCLICK-LOCATION}'" />
		</td>	
	</tr>
</table>
<!-- END BUTTONS -->


</form>
</div>
{FOOTER}
