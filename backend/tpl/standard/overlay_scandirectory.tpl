<h5>{TITLE}</h5>
<div class="overlay_scandirectory_settings clearfix">
	<form method="POST" action="{FORM_URL}" id="scan_settings_form">
		<input type="hidden" name="close_url" id="close_url" value="{CLOSE_URL}" />
		<!-- BEGIN SETTINGS_THUMBS -->
		<input type="checkbox" name="updatethumbs" id="updatethumbs" value="1" />
		<label for="updatethumbs">{LANG_UPDATETHUMBS}</label><br />
		<!-- END SETTINGS_THUMBS -->
		<input type="checkbox" name="nosubdirscan" id="nosubdirscan" value="1" />
		<label for="nosubdirscan">{LANG_NOSUBDIRSCAN}</label>
		<div class="buttons">
			<input type="submit" name="start" class="sf_buttonAction" value="{LANG_START}">
			<input type="button" name="close_settings" class="sf_buttonActionCancel" value="{LANG_CANCEL}">
		</div>
	</form>
</div>
<div class="overlay_scandirectory_progress clearfix">
	<div class="progressbar_wrap">
		<div class="progressbar_value" style="width: 0%;">
			<div class="progressbar_text">0%</div>
		</div>
	</div>
	
	<table class="stats">
		<thead>
			<tr>
				<th>&nbsp;</th>
				<th>{LANG_TOTAL}</th>
				<th>{LANG_DONE}</th>
				<th>{LANG_OPEN}</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<th scope="row">{LANG_DIRECTORIES}</th>
				<td id="directories_total">0</td>
				<td id="directories_done">0</td>
				<td id="directories_open">0</td>
			</tr>
			<tr>
				<th scope="row">{LANG_FILES}</th>
				<td id="files_total">0</td>
				<td id="files_done">0</td>
				<td id="files_open">0</td>
			</tr>
			<!-- BEGIN STATS_THUMBS -->
			<tr>
				<th scope="row">{LANG_THUMBNAILS}</th>
				<td id="thumbs_total">0</td>
				<td id="thumbs_done">0</td>
				<td id="thumbs_open">0</td>
			</tr>
			<!-- END STATS_THUMBS -->
		</tbody>
	</table>
	
	<div class="buttons">
		<input type="button" name="cancel_progress" id="cancel_progress" class="sf_buttonActionCancel" value="{LANG_CANCEL}">
		<input type="button" name="close_progress" id="close_progress" class="sf_buttonAction" value="{LANG_CLOSE}">
	</div>
</div>