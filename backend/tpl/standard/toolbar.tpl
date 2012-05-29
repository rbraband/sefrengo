<div class="toolbar clearfix">
	<!-- BEGIN FORM_START -->
		<!--<form action="{FORM_ACTION}" method="get" name="toolbar" class="clearfix">-->
	<!-- END FORM_START -->
	
	<!-- BEGIN ACTION -->
		<!-- BEGIN SEARCHFIELD -->
			<div class="toolbarElement toolbarSearch">
				<form action="{FORM_ACTION}" method="get">
					<button type="submit" value="submit" class="submit" title="{SEARCH}"><span>{SEARCH}</span></button>
					<input type="text" value="{VALUE}" name="{NAME}" {ATTRIBUTES}  placeholder="{SEARCHTERMS}" />
				</form>
			</div>
		<!-- END SEARCHFIELD -->
		
		<!-- BEGIN ADV_SEARCHFIELD -->
			<div class="toolbarElement toolbarSearch toolbarAdvancedSearch">
				<form action="{FORM_ACTION}" method="get">
					<button type="submit" value="submit" class="submit" title="{SEARCH}"><span>{SEARCH}</span></button>
					<input type="text" value="{VALUE}" name="{NAME}" {ATTRIBUTES}  placeholder="{SEARCHTERMS}" />
					<a href="#" class="sfOpenLink ir" title="{ADVANCED_SEARCH}">&#9660;</a>
					<div class="flyout">
						<div class="header">
							<a href="#" class="sfCloseLink">&#215;</a>
							<h5>{ADVANCED_SEARCH}</h5>
						</div>
						<!-- The form elements are located in form_elements_advsearch.tpl -->
						<div class="clearfix tab-pane-vertical">{TABS}</div>
						<div class="buttons">
							<input type="submit" value="{SEARCH}" class="sf_buttonAction"/>
							<!--<input type="button" value="Zurücksetzen" class="sf_buttonActionCancel" />-->
						</div>
					</div>
				</form>
			</div>
		<!-- END ADV_SEARCHFIELD -->
		
		<!-- BEGIN ACTIONBOX -->
			<div class="toolbarElement actionBox">
				<form action="{FORM_ACTION}" method="get">
					<select data-onchange-confirm="{ONCHANGE_CONFIRM}" size="1" name="{NAME}" {ATTRIBUTES}>
						<!-- BEGIN ACTIONBOX_OPTION -->
							<option value="{VALUE}">{TEXT}</option>
						<!-- END ACTIONBOX_OPTION -->
					</select>
				</form>
			</div>
		<!-- END ACTIONBOX -->
			
		<!-- BEGIN HIDDENFIELD -->
			<input type="hidden" value="{VALUE}" name="{NAME}" {ATTRIBUTES} />
		<!-- END HIDDENFIELD -->
		
		<!-- BEGIN ICON_NOLINK -->
			<div class="toolbarElement onlyIcon">
				<img class="icon" src="{IMGPATH}{SRC}" alt="{TEXT}" title="{TEXT}" {ATTRIBUTES} />
			</div>
		<!-- END ICON_NOLINK -->
		
		<!-- BEGIN ICON_LINKED -->
			<div class="toolbarElement linkWithIcon">
				<a href="{URL}" {ATTRIBUTES}><img class="icon" src="{IMGPATH}{SRC}" alt="{TEXT}" title="{TEXT}" /></a>
			</div>
		<!-- END ICON_LINKED -->
	 	
		<!-- BEGIN LINK -->
			<div class="toolbarElement linkWithText">
				<a href="{URL}" {ATTRIBUTES}>{TEXT}</a>
			</div>
		<!-- END LINK -->
	 	
		<!-- BEGIN LINK_ICON -->
			<div class="toolbarElement linkWithIconAndText">
				<a href="{URL}" {ATTRIBUTES}><img class="icon" src="{IMGPATH}{SRC}" alt="{TEXT}" /> <span>{TEXT}</span></a>
			</div>
		<!-- END LINK_ICON -->
	 	
		<!-- BEGIN TEXT -->
			<div class="toolbarElement textOnly">{TEXT}</div>
		<!-- END TEXT -->
		
		<!-- BEGIN TEXT_ICON -->
			<div class="toolbarElement onlyIconAndText">
				<img class="icon" src="{IMGPATH}{SRC}" alt="{TEXT}" {ATTRIBUTES} /> <span>{TEXT}</span>
			</div>
		<!-- END TEXT_ICON -->
	
	 	<!-- BEGIN DELIMITER -->
	 		<div class="toolbarElement toolbarDelimiter">
				<span {ATTRIBUTES}>|</span>
			</div>
		<!-- END DELIMITER -->
	 	
	<!-- END ACTION -->
	
	<!-- BEGIN FORM_END -->
		<!-- </form>{DUMMY} -->
	<!-- END FORM_END -->
</div>