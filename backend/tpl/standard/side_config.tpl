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
<input type="hidden" name="lastmodified" value="{LASTMODIFIED}" />
<input type="hidden" name="author" value="{AUTHOR}" />
<input type="hidden" name="created" value="{CREATED}" />
<input type="hidden" name="idcatside" value="{IDCATSIDE}" />
<input type="hidden" name="area" value="con_configside" />


<table class="config" cellspacing="1">
	<tr>
		<td class="head">
			<p>{CON_SIDECONFIG}</p>
		</td>
		<td class="nopadd">

				<table class="sitehack" cellspacing="1">
						<tr>
								<td colspan="2" class="header">
								<!-- Ueberschrift Titel -->
								{SIDE_TITLE_DESC}
								</td>
						</tr>
						<tr>
								<td colspan="2">
										<!-- Eingabe Titel -->
										<input class="w800" type="text" name="title" value="{SIDE_TITLE}" size="30" maxlength="255" />
										<!-- Eingabe Darf Seite sperren -->
										{SELECT_LOCK_SIDE}
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
										<input class="w800" type="text" name="rewrite_url" id="rewrite_url" value="{REWRITE_URL}"	{REWRITE_URL_DISABLED} size="90" maxlength="255"/>{REWRITE_ERROR}
								</td>
						</tr>
						<tr><td	 colspan="2" ><small>{LNG_REWRITE_URL-OF-THIS-PAGE} {REWRITE_CURRENT_URL}</small></td></tr>
<!-- END URL_REWRITE -->

<!-- BEGIN HTTPS -->
    <tr>
    <td class="header" colspan="2">
            <!-- Ueberschrift HTTPS -->
            {LNG_META_TITLE_HTTPS}
    </td>
  </tr>
  <tr>
    <td colspan="2">
            <!-- Feld HTTPS -->
            <input type="checkbox" name="meta_is_https" id="meta_is_https" value="1" {META_IS_HTTPS_CHECKED} /><label for="meta_is_https">{LNG_META_DESC_HTTPS}</label>
    </td>
  </tr>
 <!-- END HTTPS -->

<!-- BEGIN USER_RIGHTS -->
						<tr>
								<td colspan="2">{BACKENDRIGHTS}&nbsp;&nbsp;&nbsp;&nbsp;{FRONTENDRIGHTS}</td>
						</tr>
<!-- END USER_RIGHTS -->	

<!-- BEGIN TIMER_BLOCK -->

						<tr>
								<td class="header" colspan="2">
								<!-- Ueberschrift Sichtbarkeit -->
								{VISBILITY_DESC}
								</td>
						</tr>
						<tr>
								<td colspan="2">
										<!-- Beschreibung online, offline, zeitgesteuert -->
										{LANG_SIDE_IS}:
										<!-- Eingabe online, offline, ... -->
										<input type="radio" name="online" value="0" id="a0" {ONLINE-STATE_C0-CHECKED} onclick="if (document.getElementById('a2').checked==false) $('.timer_input_row').hide();"/>
										<label for="a0">{ONLINE-STATE_C0-LABEL}</label> &nbsp;
										<input type="radio" name="online" value="2" id="a2" {ONLINE-STATE_C2-CHECKED} onclick="$('.timer_input_row').show();"/>
										<label for="a2">{ONLINE-STATE_C2-LABEL}</label> &nbsp;
										<input type="radio" name="online" value="1" id="a1" {ONLINE-STATE_C1-CHECKED} onclick="if (document.getElementById('a2').checked==false) $('.timer_input_row').hide();"/> 
										<label for="a1">{ONLINE-STATE_C1-LABEL}</label> 
								</td>
						</tr>
						<tr class="timer_input_row" {ONLINE-STATE_TIMER-ACTIVE}>
								<td colspan="2">
										<!-- Beschreibung startdatum -->
										<label for="timer_date_start">{LANG_ONLINE}:</label>
										<!-- Eingabe startdatum -->
										<input type="text" name="startdate" id="timer_date_start" onchange="document.getElementById('a2').checked.checked=true;" value="{STARTDATE}" size="10" maxlength="10" style="width: 65px;" class="sfDatepicker sfDatepickerFrom" data-datepicker-options='{"connectedDatepickerId": "timer_date_end"}' />
										{LNG_STARTTIME}:
										<input type="text" name="starttime" value="{STARTTIME}" size="5" maxlength="5"	onchange="document.getElementById('a2').checked.checked=true;" style="width:35px;" />
								</td>
						</tr>
						<tr class="timer_input_row" {ONLINE-STATE_TIMER-ACTIVE}>
								<td colspan="2">
										<!-- Beschreibung enddatum -->
										<label for="timer_date_end">{LANG_OFFLINE}:</label>
										<!-- Eingabe enddatum -->
										<input type="text" name="enddate" id="timer_date_end" value="{ENDDATE}" size="10" maxlength="10"	onchange="document.getElementById('a2').checked=true;" style="width: 65px;" class="sfDatepicker sfDatepickerTo" data-datepicker-options='{"connectedDatepickerId": "timer_date_start"}' />
										{LNG_ENDTIME}:
										<input type="text" name="endtime" value="{ENDTIME}" size="5" maxlength="5"	onchange="document.getElementById('a2').checked=true;" style="width:35px;" />
							</td>
						</tr>



<!-- END TIMER_BLOCK -->

<!-- BEGIN CLONE_AND_NOTICE -->
					<tr>
						<td colspan="2" class="header">
							<table>
								<tr>
									<td class="header" width="325">
										<!-- Beschreibung verschieben/ klonen -->
										{LANG_MOVE_SIDE}:
									</td>
									<td class="header" width="325">
										<!-- Beschreibung Notizen - interne Beschreibung -->
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
									<td width="380">
									  <!-- BEGIN SELECT-SIDEMOVE -->
								    <select name="idcatnew[]" multiple="multiple" style="height:150px;width:380px">
								      <!-- BEGIN SELECT-SIDEMOVE_ENTRY -->
								      <option value="{SELECT-SIDEMOVE_ENTRY-VALUE}" {SELECT-SIDEMOVE_ENTRY-SELECTED}>{SELECT-SIDEMOVE_ENTRY-TITLE}</option>
								      <!-- END SELECT-SIDEMOVE_ENTRY -->
								    </select>
									  <!-- END SELECT-SIDEMOVE -->
									</td>
									<td width="380">
										<!-- Feld Notizen - interne Beschreibung -->
										<textarea name="summary" rows="7" cols="30" style="height:145px;width:380px" class="sans-serif">{SUMMARY}</textarea>
									</td>
								</tr>
						 </table>
						</td>
					</tr>
<!-- END CLONE_AND_NOTICE -->


<!-- BEGIN NOTICE -->
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
										<textarea name="summary" rows="5" cols="30" class="w800">{SUMMARY}</textarea>
									</td>
								</tr>
							</table>
						</td>
					</tr>

<!-- END NOTICE -->

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


<!-- BEGIN META -->
<table class="config" id="siteconfhack1" cellspacing="1">
	<tr>
		<td class="head nowrap" rowspan="8" width="110">
			<p>{LANG_CON_METACONFIG}</p>
		</td>
		<td class="header" colspan="3">
						<!-- Ueberschrift meta description -->
						{LANG_META_DESC}
		</td>
	</tr>
	<tr>
		<td colspan="3">
						<!-- Feld meta description -->
						<textarea class="w800 sans-serif" name="meta_description" rows="2" cols="50">{META_DESC}</textarea>
		</td>
	</tr>
	<tr>
		<td class="header" colspan="3">
						<!-- Ueberschrift meta keywords -->
						{LANG_META_KEYWORDS}
		</td>
	</tr>
	<tr>
		<td colspan="3">
						<!-- Feld meta keywords -->
						<input class="w800" type="text" name="meta_keywords" value="{META_KEYWORDS}" />
		</td>
	</tr>
	<tr>
		<td class="header" style="padding:0;" colspan="3">
			<table>
				<tr>
					<td class="header" width="380">
					{LANG_META_AUTHOR}
					</td>
					<td class="header" style="padding-left:0;" width="380">
					{LANG_META_ROBOTS}
					</td>
				</tr>
			</table>
	 </td>
	 </tr>
	 <tr>
		 <td colspan="3">
			<table>
			<tr>
			<td width="380">
					<!-- Feld Author -->
					<input type="text" name="meta_author" style="width:318px" value="{META_AUTHOR}" />
			</td>
			<td width="380">
						<select name="meta_robots" size="1" style="width:318px">
							<option value="index, follow" {SE-INDICATION_E0-CHECKED}>{SE-INDICATION_E0-TITLE}</option>
							<option value="index, nofollow" {SE-INDICATION_E1-CHECKED}>{SE-INDICATION_E1-TITLE}</option>
							<option value="noindex, follow" {SE-INDICATION_E2-CHECKED}>{SE-INDICATION_E2-TITLE}</option>
							<option value="noindex, nofollow" {SE-INDICATION_E3-CHECKED}>{SE-INDICATION_E3-TITLE}</option>
						</select>
			</td>
			</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td class="header" colspan="3">
						<!-- Ueberschrift Weiterleitung -->
						{LANG_META_REDIRECT}
		</td>
	</tr>
	<tr>
		<td colspan="3">
						<!-- Feld Weiterleitung -->
						<input type="checkbox" name="meta_redirect" value="1" {META_REDIRECT} />
						<input type="text" name="meta_redirect_url" value="{META_REDIRECT_URL}" size="50" maxlength="255" style="width:304px;" />
		</td>
	</tr>
<!-- END META -->


<!-- BEGIN TPL-CONF -->
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
		{TPL-NAME}
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