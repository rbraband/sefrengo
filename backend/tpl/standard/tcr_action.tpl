<!-- BEGIN ICON_LINK -->
	<a href="{ICON_LINK_URL}" title="{ICON_LINK_TITLE}" {ICON_LINK_ATTRIBUTES}><img src="{SKIN_IMG_PATH}{ICON_LINK_IMGSRC}" width="16" height="16" alt="" class="icon" /></a>
<!-- END ICON_LINK -->

<!-- BEGIN TEXT_LINK -->
	<a href="{TEXT_LINK_URL}" title="{TEXT_LINK_TITLE}" {TEXT_LINK_ATTRIBUTES}>{TEXT_LINK_TEXT}</a>
<!-- END TEXT_LINK -->

<!-- BEGIN ICON_NOLINK -->
	<img src="{SKIN_IMG_PATH}{ICON_NOLINK_IMGSRC}" width="16" height="16" alt="" class="icon" />
<!-- END ICON_NOLINK -->

<!-- BEGIN TEXT_NOLINK -->
	{TEXT_NOLINK_TEXT}
<!-- END TEXT_NOLINK -->

<!-- BEGIN TOOLINFO -->
	<span class="toolinfo">
		<table cellpadding="0" cellspacing="0" border="0" class="{TOOLINFO_TABLE_CLASS}">
			<tbody>
				<!-- BEGIN TOOLINFO_HEAD -->
					<tr class="headline">
						<th>{TOOLINFO_HEAD_NAME}</th>
						<th align="right"><small>{TOOLINFO_HEAD_IDNAME}</small> {TOOLINFO_HEAD_ID}</th>
					</tr>
				<!-- END TOOLINFO_HEAD -->
				<!-- BEGIN TOOLINFO_ROW -->
					<tr {TOOLINFO_ROW_ATTRIBUTES}>
						<td><strong>{TOOLINFO_ROW_NAME}</strong></td>
						<td align="right"><span>{TOOLINFO_ROW_VALUE}<span></td>
					</tr>
				<!-- END TOOLINFO_ROW -->
			</tbody>
		</table>
	</span>
<!-- END TOOLINFO -->