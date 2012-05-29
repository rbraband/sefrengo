<table class="uber sfSelectTableRow" {TABLE_ATTRIBUTES}>
	<!-- BEGIN PAGER_TOP -->
	<tr>
		<td colspan="{PAGER_TOP_COLSPAN}" class="entryuser">
			<!-- BEGIN PAGER -->
				<p class="seite">
					{LANG_PAGE}
					<input type="text" name="changepage1" value="{CHANGEPAGE_CURRENT}" />
					{LANG_FROM} {CHANGEPAGE_MAX}
				</p>
				<p class="zahl">{PAGER_LINKS}</p>
			<!-- END PAGER -->
			<!-- BEGIN FILTER_RESET -->
				<p style="display:block; text-align:center; margin:5px 15% 5px 12%;">
					{LANG_FILTER_SHOW}
					<strong>{SET_FILTERS}</strong> |
					<a href="{RESET_URL}" class="ajaxdeeplink action">{LANG_FILTER_RESET}</a>
				</p>
			<!-- END FILTER_RESET -->
		</td>
	</tr>
	<!-- END PAGER_TOP -->
	
	<!-- BEGIN TABLE_HEAD -->
	<tr>
		<!-- BEGIN TABLE_HEAD_COLUMN -->
		<th {TABLE_HEAD_ATTRIBUTES}>
			<!-- BEGIN TABLE_HEAD_LINK -->
				<a href="{TABLE_HEAD_URL}" class="ajaxdeeplink sort {TABLE_HEAD_ORDER}">{TABLE_HEAD_TITLE}</a>{TABLE_HEAD_SEPARATOR}
			<!-- END TABLE_HEAD_LINK -->
			<!-- BEGIN TABLE_HEAD_NAME -->
				{TABLE_HEAD_TITLE}{TABLE_HEAD_SEPARATOR}
			<!-- END TABLE_HEAD_NAME -->
		</th>
		<!-- END TABLE_HEAD_COLUMN -->
	</tr>
	<!-- END TABLE_HEAD -->
	
	<!-- BEGIN ENTRIES -->
		<!-- BEGIN ENTRY -->
		<tr valign="top" {TR_ATTRIBUTES}>
			<!-- BEGIN TABLE_BODY_COLUMN -->
				<td {TABLE_BODY_ATTRIBUTES}>
					{TABLE_BODY_CONTENT}
				</td>
			<!-- END TABLE_BODY_COLUMN -->
		</tr>
		<!-- END ENTRY -->
		
		<!--
			Note:
			The detail row is specific for this index template and has to be modified
			if you copy this template file
		-->
		<!-- BEGIN ENTRY_DETAIL -->
		<tr valign="top" {DETAIL_TR_ATTRIBUTES}>
			<td colspan="{DETAIL_COLSPAN}" class="detail">
				<div class="wrapper clearfix">
					<div class="thumbnail">
						{DETAIL_THUMBNAIL}
					</div>
					<div class="content">
						<p class="title"><span>{DETAIL_LANG_TITLE}:</span> {DETAIL_TITLE}</p>
						<p class="description"><span>{DETAIL_LANG_DESCRIPTION}:</span> {DETAIL_DESCRIPTION}</p>
						<p class="filename">{DETAIL_FILENAME}</p>
						<p class="size">{DETAIL_FILESIZE} / {DETAIL_IMAGE_DIMENSION}<br />
						{DETAIL_CREATED} - {DETAIL_LASTMODIFIED}</p>
					</div>
				</div>
			</td>
		</tr>
		<!-- END ENTRY_DETAIL -->
	<!-- END ENTRIES -->
	
	<!-- BEGIN EMPTY -->
	<tr style="background-color: #ffffff">
		<td colspan="{COLSPAN}" class="entry">{LANG_NODATA}</td>
	</tr>
	<!-- END EMPTY -->
	
	<!-- BEGIN FOOTER_SELECT_MULTIPLE -->
	<tr style="background-color: #F0F8FF;">
		<td colspan="{COLSPAN}" class="entry">
			{LANG_SELECT}
			<a href="#" class="chk_select_all action" rel="{SELECT_ALL}">{LANG_SELECT_ALL}</a> |
			<a href="#" class="chk_select_none action" rel="{SELECT_NONE}">{LANG_SELECT_NONE}</a>
		</td>
		<td {TABLE_BODY_ATTRIBUTES}>{ACTIONS}</td>
	</tr>
	<!-- END FOOTER_SELECT_MULTIPLE -->
	
	
	<!-- BEGIN PAGER_BOTTOM -->
	<tr>
		<td colspan="{PAGER_BOTTOM_COLSPAN}" class="entryuser">
			<!-- BEGIN PAGER -->
				<p class="seite">
					{LANG_PAGE}
					<input type="text" name="changepage2" value="{CHANGEPAGE_CURRENT}" />
					{LANG_FROM} {CHANGEPAGE_MAX}
				</p>
				<p class="zahl">{PAGER_LINKS}</p>
			<!-- END PAGER -->
		</td>
	</tr>
	<!-- END PAGER_BOTTOM -->
</table>