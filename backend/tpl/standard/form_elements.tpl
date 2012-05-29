<!-- Anfang form_elements.tpl -->

<!-- BEGIN FORM_START --> 
	<form {FORM_START_ATTRIBUTES}>
    
	<table class="config" cellspacing="1">
<!-- END FORM_START --> 

<!-- BEGIN HIDDEN --> 
	<input type="hidden" name="{HIDDEN_NAME}" value="{HIDDEN_VAL}" />
<!-- END HIDDEN --> 


<!-- BEGIN HEADLINE --> 
	<tr>
		<td class="header" colspan=2>{HEADLINE_TITLE}</td>
	</tr>
<!-- END HEADLINE --> 

<!-- BEGIN INFO --> 
	<tr>
		<td class="head"><p>{INFO_TITLE}</p></td>
		<td width="100%">{INFO_VAL}</td>
	</tr>
<!-- END INFO --> 

<!-- BEGIN TEXT --> 
	<tr>
		<td class="head"><p><label for="{TEXT_ID}">{TEXT_TITLE}</label></p></td>
		<td width="100%"><input type="text" name="{TEXT_NAME}" id="{TEXT_ID}" value="{TEXT_VAL}" {TEXT_ATTRIBUTES} /></td>
	</tr>
<!-- END TEXT --> 

<!-- BEGIN TEXTAREA --> 
	<tr>
		<td class="head"><p><label for="{TEXTAREA_ID}">{TEXTAREA_TITLE}</label></p></td>
		<td width="100%"><textarea name="{TEXTAREA_NAME}" id="{TEXTAREA_ID}" {TEXTAREA_ATTRIBUTES}>{TEXTAREA_VAL}</textarea></td>
	</tr>
<!-- END TEXTAREA --> 

<!-- BEGIN CHECKBOX --> 
	<tr>
		<td class="head"><p>{CHECKBOX_TITLE}</p></td>
		<td width="100%">
			<!-- BEGIN CHECKBOX_FIELDS -->
			<input type="checkbox" name="{CHECKBOX_FIELDS_NAME}" id="{CHECKBOX_FIELDS_ID}" value="{CHECKBOX_FIELDS_VAL}" {CHECKBOX_FIELDS_ATTRIBUTES} {CHECKBOX_FIELDS_CHECKED} />
			<label for="{CHECKBOX_FIELDS_ID}">{CHECKBOX_FIELDS_LABEL}</label><br />
			<!-- END CHECKBOX_FIELDS -->
		</td>
	</tr>
<!-- END CHECKBOX --> 

<!-- BEGIN RADIO --> 
	<tr>
		<td class="head"><p>{RADIO_TITLE}</p></td>
		<td width="100%">
			<!-- BEGIN RADIO_FIELDS -->
				<input type="radio" name="{RADIO_FIELDS_NAME}" id="{RADIO_FIELDS_ID}" value="{RADIO_FIELDS_VAL}" {RADIO_FIELDS_ATTRIBUTES} {RADIO_FIELDS_CHECKED} />
				<label for="{RADIO_FIELDS_ID}">{RADIO_FIELDS_LABEL}</label><br />
			<!-- END RADIO_FIELDS -->
		</td>
	</tr>
<!-- END RADIO -->

<!-- BEGIN SELECT --> 
	<tr>
		<td class="head"><p><label for="{SELECT_ID}">{SELECT_TITLE}</label></p></td>
		<td><select name="{SELECT_NAME}" id="{SELECT_ID}" {SELECT_ATTRIBUTES}>
			<!-- BEGIN SELECT_OPTIONS -->
				<option value="{SELECT_OPTIONS_VAL}" {SELECT_OPTIONS_SELECTED}>{SELECT_OPTIONS_TITLE}</option>
			<!-- END SELECT_OPTIONS -->
			</select></td>
	</tr>
<!-- END SELECT -->

<!-- BEGIN CMSLINK --> 
	<tr>
		<td class="head"><p>{CMSLINK_TITLE}</p></td>
		<td width="100%">
		    <script type="text/javascript">
			function sf_getLink(input_id, name, value) 
			{
				document.getElementById(input_id).value= value;
				document.getElementById(input_id + '_intern').value= name;
			}
			</script>
			<input type="hidden" id="{CMSLINK_NAME}" name="{CMSLINK_NAME}" value="{CMSLINK_VAL}" />
			<input type="text" id="{CMSLINK_NAME}_intern" name="{CMSLINK_NAME}_intern" value="{CMSLINK_VAL_INTERN}" {CMSLINK_ATTRIBUTES} />
			<input type='button' value='DEL' onclick="sf_getLink('{CMSLINK_NAME}', '', '')" />
			&nbsp;
			<input type='button' value='&hellip;' onclick="SF.Utils.popup('{CMSLINK_RB_URL}', 'rb', '', screen.width * 0.7, screen.height * 0.7, 'true')" />
		</td>
	</tr>
<!-- END CMSLINK -->  

<!-- BEGIN DIRECTORYCHOOSER -->
	<tr>
		<td class="head"><p>{DIRECTORYCHOOSER_TITLE}</p></td>
		<td width="100%">
			<div {DIRECTORYCHOOSER_ATTRIBUTES}>{DIRECTORYCHOOSER_TREE}</div>
			<input type="hidden" name="{DIRECTORYCHOOSER_NAME}" id="{DIRECTORYCHOOSER_NAME}" value="{DIRECTORYCHOOSER_VAL}" />
		</td>
	</tr>
<!-- END DIRECTORYCHOOSER --> 

<!-- BEGIN RIGHTSPANEL -->
	<tr>
		<td class="head"><p>{RIGHTSPANEL_TITLE}</p></td>
		<td width="100%">
			<div {RIGHTSPANEL_ATTRIBUTES}>{RIGHTSPANEL_PANEL}</div>
		</td>
	</tr>
<!-- END RIGHTSPANEL --> 

<!-- BEGIN EDITOR -->
	<tr>
		<td class="head"><p>{EDITOR_TITLE}</p></td>
		<td width="100%">
			<div {EDITOR_ATTRIBUTES}>{EDITOR_EDITOR}</div>
		</td>
	</tr>
<!-- END EDITOR -->

<!-- BEGIN BUTTON_TRIPPLE -->	 
 	<tr>
		<td class="content7" colspan="2" style="text-align:right">
			<input name="save" type="submit" value="{BUTTON_TRIPPLE_SUBMIT_VALUE}" title="{BUTTON_TRIPPLE_SUBMIT_TEXT}" class="sf_buttonAction" />
			<input name="apply" type="submit" value="{BUTTON_TRIPPLE_APPLY_VALUE}" title="{BUTTON_TRIPPLE_APPLY_TEXT}" class="sf_buttonAction" />
			<input name="cancel" type="button" value="{BUTTON_TRIPPLE_CANCEL_VALUE}" title="{BUTTON_TRIPPLE_CANCEL_TEXT}" class="sf_buttonActionCancel" onclick="window.location='{BUTTON_CANCEL_URL}'" />
		</td>
	</tr>
<!-- END BUTTON_TRIPPLE --> 

<!-- BEGIN ACTIONBUTTONS -->	 
 	<tr>
		<td class="content7" colspan="2" style="text-align:right">
			<div {ACTIONBUTTONS_ATTRIBUTES}>
				<!-- BEGIN SUBMIT_BUTTON -->
					<input name="{SUBMIT_BUTTON_NAME}" type="submit" value="{SUBMIT_BUTTON_VAL}" title="{SUBMIT_BUTTON_TEXT}" {SUBMIT_BUTTON_ATTRIBUTES} />
				<!-- END SUBMIT_BUTTON -->
				<!-- BEGIN RESET_BUTTON -->
					<input name="{RESET_BUTTON_NAME}" type="reset" value="{RESET_BUTTON_VAL}" title="{RESET_BUTTON_TEXT}" {RESET_BUTTON_ATTRIBUTES} />
				<!-- END RESET_BUTTON -->
				<!-- BEGIN CUSTOM_BUTTON -->
					<input name="{CUSTOM_BUTTON_NAME}" type="button" value="{CUSTOM_BUTTON_VAL}" title="{CUSTOM_BUTTON_TEXT}" {CUSTOM_BUTTON_ATTRIBUTES} />
				<!-- END CUSTOM_BUTTON -->
			</div>
		</td>
	</tr>
<!-- END ACTIONBUTTONS --> 

<!-- BEGIN FORM_END --> 
    </table>
    </form>
<!-- END FORM_END --> 