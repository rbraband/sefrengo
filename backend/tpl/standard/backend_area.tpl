{HEADER}

<div id="main" {MAIN_ATTRIBUTES}>
	<!-- BEGIN LEFTPANE -->
		<div class="leftpane">
			<div class="leftpane_spacer">
				{LEFTPANE}
			</div>
		</div>
	<!-- END LEFTPANE -->
	
	<!-- BEGIN RIGHTPANE -->
		<div class="rightpane">
			<div class="rightpane_spacer">
				{TOOLBAR}
				<!-- BEGIN TITLE --><h5>{TITLE}</h5><!-- END TITLE -->
				{MESSAGE}
				{BREADCRUMB}
				{RIGHTPANE}
			</div>
		</div>
	<!-- END RIGHTPANE -->
</div>

<div id="sf_loading"><span>{UPDATE_VIEW}</span></div>

{OVERLAY}
{FOOTER}