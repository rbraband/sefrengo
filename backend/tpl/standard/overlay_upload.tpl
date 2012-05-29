<h5>{TITLE}</h5>
<div class="overlay_upload clearfix" data-options='{METADATA}'>
	<form action="{UPLOAD_FORM_ACTION}" method="post" name="upload_html" enctype="multipart/form-data">
		
		<div id="upload_file_queue"><span class="empty_queue">{LANG_NOFILESSELECTED}</span></div>
		
		<div class="upload_mode_flash">
			<div class="buttons uploadify_buttons clearfix">
				<div class="totalsize">{LANG_TOTALSIZE}: <span></span> / {TOTALSIZE}</div>
				<a href="#" class="upload_mode action" rel="html" title="{LANG_UPLOAD_MODE_HTML_TOOLTIP}">{LANG_UPLOAD_MODE_HTML}</a>
				<div class="buttontripple">
					<input type="file" name="uploadify" id="uploadify" />
					<input type="button" name="browse" class="sf_buttonAction" value="{LANG_BROWSE}">
					<input type="button" name="clear" class="sf_buttonActionCancel" value="{LANG_CLEARQUEUE}">
				</div>
			</div>
		</div>
		
		<div class="upload_mode_html buttons">
			<a href="#" class="upload_mode action" rel="flash">{LANG_UPLOAD_MODE_FLASH}</a>
			<input type="hidden" name="upload_mode" value="html" />
			<input type="file" name="sf_upload[]" id="sf_upload">
		</div>
		
		<div class="directorychooser">{DIRECTORYCHOOSER_TREE}</div>
		<input type="hidden" name="destination" id="destination" value="{DIRECTORYCHOOSER_VAL}" />
		
		<div class="extract_files">
			<input type="checkbox" name="extractfiles" id="extractfiles" value="1" />
			<label for="extractfiles">{LANG_UPLOAD_EXTRACT_FILES}</label>
		</div>
		<div class="buttontripple buttons">
			<input type="button" name="upload" class="sf_buttonAction" value="{LANG_UPLOAD}">
			<input type="button" name="cancel" class="sf_buttonActionCancel" value="{LANG_CANCEL}">
		</div>
	</form>
</div>
<div class="overlay_upload_finish clearfix">
	<ul class="filter">
		<li>{LANG_SHOW_MESSAGES}</li>
		<li><a href="#" class="action active" rel="all">{LANG_MESSAGES_ALL}</a></li>
		<li><a href="#" class="action" rel="errormsg">{LANG_MESSAGES_ERROR}</a></li>
		<li><a href="#" class="action" rel="warning">{LANG_MESSAGES_WARNING}</a></li>
		<li><a href="#" class="action" rel="ok">{LANG_MESSAGES_OK}</a></li>
	</ul>
	<div class="messages">
		<p class="dummy"><span class="message"></span><span class="filename"></span></p>
	</div>
	<div class="buttons">
		<input type="button" name="close" class="sf_buttonAction" value="{LANG_CLOSE}">
	</div>
</div>