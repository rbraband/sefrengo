<!--
	All fields are used inside a tab container.
	E.g. <div class="clearfix tab-pane-vertical"></div>
	
	Note: Use this template with the toolbar.tpl!
-->

<!-- BEGIN HIDDEN --> 
	<input type="hidden" name="{HIDDEN_NAME}" value="{HIDDEN_VAL}" />
<!-- END HIDDEN --> 

<!-- BEGIN TEXT -->
<div id="tp_{TEXT_ID}">
	<h2 class="tab">{TEXT_TITLE} <span>{NOSELECTION}</span></h2>
	<label for="{TEXT_ID}" class="visuallyhidden">{TEXT_TITLE}</label>
	<input type="text" name="{TEXT_NAME}" id="{TEXT_ID}" value="{TEXT_VAL}" {TEXT_ATTRIBUTES} />
</div>
<!-- END TEXT -->

<!-- BEGIN TEXTAREA --> 
<div id="tp_{TEXTAREA_ID}">
	<h2 class="tab">{TEXTAREA_TITLE} <span>{NOSELECTION}</span></h2>
	<label for="{TEXTAREA_ID}" class="visuallyhidden">{TEXTAREA_TITLE}</label>
	<input type="text" name="{TEXTAREA_NAME}" id="{TEXTAREA_ID}" value="{TEXTAREA_VAL}" {TEXTAREA_ATTRIBUTES} />
</div>
<!-- END TEXTAREA --> 

<!-- BEGIN CHECKBOX --> 
<div id="tp_{CHECKBOX_ID}">
	<h2 class="tab">{CHECKBOX_TITLE} <span>{NOSELECTION}</span></h2>
	<!-- BEGIN CHECKBOX_FIELDS -->
		<input type="checkbox" name="{CHECKBOX_FIELDS_NAME}" id="{CHECKBOX_FIELDS_ID}" value="{CHECKBOX_FIELDS_VAL}" {CHECKBOX_FIELDS_ATTRIBUTES} {CHECKBOX_FIELDS_CHECKED} />
		<label for="{CHECKBOX_FIELDS_ID}">{CHECKBOX_FIELDS_LABEL}</label><br />
	<!-- END CHECKBOX_FIELDS -->
</div>
<!-- END CHECKBOX --> 

<!-- BEGIN RADIO --> 
<div id="tp_{RADIO_ID}">
	<h2 class="tab">{RADIO_TITLE} <span>{NOSELECTION}</span></h2>
	<!-- BEGIN RADIO_FIELDS -->
		<input type="radio" name="{RADIO_FIELDS_NAME}" id="{RADIO_FIELDS_ID}" value="{RADIO_FIELDS_VAL}" {RADIO_FIELDS_ATTRIBUTES} {RADIO_FIELDS_CHECKED} />
		<label for="{RADIO_FIELDS_ID}">{RADIO_FIELDS_LABEL}</label><br />
	<!-- END RADIO_FIELDS -->
</div>
<!-- END RADIO -->

<!-- BEGIN SELECT --> 
<div id="tp_{SELECT_ID}">
	<h2 class="tab">{SELECT_TITLE} <span>{NOSELECTION}</span></h2>
	<label for="{SELECT_ID}" class="visuallyhidden">{SELECT_TITLE}</label>
	<select name="{SELECT_NAME}" id="{SELECT_ID}" {SELECT_ATTRIBUTES}>
	<!-- BEGIN SELECT_OPTIONS -->
		<option value="{SELECT_OPTIONS_VAL}" {SELECT_OPTIONS_SELECTED}>{SELECT_OPTIONS_TITLE}</option>
	<!-- END SELECT_OPTIONS -->
	</select>
	<!--
		<p class="sfSelection">
			<a class="sfHint" title="Mehrfachauswahl mit gedrückter STRG-Taste möglich.">Hinweis</a>
			Auswählen: <a href="#" class="sfSelectAll action">Alle</a> | <a href="#" class="sfSelectNone action">Keine</a>
		</p>
	-->
</div>
<!-- END SELECT -->

<!-- BEGIN DATEPICKER -->
<div id="tp_{DATEPICKER_ID}">
	<h2 class="tab">{DATEPICKER_TITLE} <span>{NOSELECTION}</span></h2>
	<!-- BEGIN DATEPICKER_FIELDS -->
	<div class="sfDatepickerContainer">
		<label for="{DATEPICKER_FIELDS_ID}">{DATEPICKER_FIELDS_LABEL}</label>
		<input type="text" name="{DATEPICKER_FIELDS_NAME}" id="{DATEPICKER_FIELDS_ID}" value="{DATEPICKER_FIELDS_VAL}" {DATEPICKER_FIELDS_ATTRIBUTES} class="sfDatepicker" />
	</div>
	<!-- END DATEPICKER_FIELDS -->
</div>
<!-- END DATEPICKER -->
