<!-- Anfang con_main.tpl -->
<div id="main">
	<div class="forms" id="formpadd">
		
		<!-- BEGIN FORM_SELECT_ACTIONS -->
	  <form name="actionform" method="post" action="{FORM_URL_ACTIONS}">
	    <input type="hidden" name="area" value="con" />
	    <select name="change_show_tree" size="1" onchange="actionform.submit()">
	      <option value="">{LANG_SELECT_ACTIONS}</option>
	      <!-- BEGIN SELECT_ACTIONS -->
	      <option value="{ACTIONS_VALUE}" {ACTIONS_SELECTED}>{ACTIONS_ENTRY}</option>
	      <!-- END SELECT_ACTIONS -->
	    </select>
	  </form>
	  <!-- END FORM_SELECT_ACTIONS -->
	  
	  <!-- BEGIN FORM_SELECT_VIEW -->
	  <form name="treeform" method="post" action="{FORM_URL_VIEW}">
	    <input type="hidden" name="area" value="con" />
	    <select name="change_show_tree" size="1" onchange="treeform.submit()">
	      <option value="">{LANG_SELECT_VIEW}</option>
	      <!-- BEGIN SELECT_FOLDERLIST -->
	      <option value="{FOLDERLIST_VALUE}" {FOLDERLIST_SELECTED}>{FOLDERLIST_ENTRY}</option>
	      <!-- END SELECT_FOLDERLIST -->
	    </select>
	  </form>
	  <!-- END FORM_SELECT_VIEW -->
	  
	  <!-- BEGIN FORM_CHANGE_TO -->
	  <form name="changetoform" method="post" action="{FORM_URL_CHANGE_TO}">
	    <input type="hidden" name="area" value="con" />
	    <select name="sort" size="1" onchange="changetoform.submit()">
	      <option value="">{LANG_CHANGE_TO}</option>
	      <!-- BEGIN SELECT_CHANGE_TO -->
	      <option value="{CHANGE_TO_VALUE}" {CHANGE_TO_SELECTED}>{CHANGE_TO_ENTRY}</option>
	      <!-- END SELECT_CHANGE_TO -->
	    </select>
	  </form>
		<!-- END FORM_CHANGE_TO -->
		
	</div>
	<h5>{AREA}</h5>
	<!-- BEGIN ERRORMESSAGE -->
	<p class="errormsg">{ERRORMESSAGE} </p>
	<!-- END ERRORMESSAGE -->

	<!-- BEGIN TREE-HEAD -->
	<table class="uber" style="margin-bottom:0">
		<tr>
		  <th class="nobordr">{LANG_STRUCTURE_AND_SIDE}
		  <a style="text-decoration:none;" href="main.php?area=con&amp;action=expand&amp;expanded=3&amp;idcat={CAT}"  title="{LINK-TITLE-EXPAND}">
				<img src="tpl/{CMS-SKIN}/img/but_plus_small.gif" width="16" height="11"  alt="" />
			</a>
			<a style="text-decoration:none;" href="main.php?area=con&amp;action=expand&amp;expanded=2&amp;idcat={CAT}" title="{LINK-TITLE-COLLAPSE}">
				<img src="tpl/{CMS-SKIN}/img/but_minus_small.gif" width="16" height="11"  alt="" />
			</a>
			</th>
		  <th width="140" class="center nobordl" style="text-align:right;">{LANG_ACTIONS}</th>
		</tr>
	</table>
	<!-- END TREE-HEAD -->

  <!-- BEGIN MAIN-FOLDER -->
  <table class="uber" style="margin:0">
  <!-- BEGIN FOLDER -->
    <tr rel="{TABLE_OVERCOLOR}" class="{TABLE_COLOR}" <!-- BEGIN FOLDER_EXPAND-ANCHOR -->id="catanchor" name="catanchor"<!-- END FOLDER_EXPAND-ANCHOR -->>
      <td class="entry nobordr" style="padding-left:{FOLDER-PADDING-LEFT}px">

	      <!-- BEGIN FOLDER_BUTTON-EXPAND -->
	      	<!-- BEGIN FOLDER_BUTTON-EXPAND_EXPAND -->
					<a href="{LINK-HREF}#catanchor" title="{LINK-TITLE}"><img src="tpl/{CMS-SKIN}/img/but_plus.gif" width="16" height="16" alt="" /></a>
	      	<!-- END FOLDER_BUTTON-EXPAND_EXPAND -->
	      	<!-- BEGIN FOLDER_BUTTON-EXPAND_COLLAPSE -->
					<a href="{LINK-HREF}#catanchor" title="{LINK-TITLE}"><img src="tpl/{CMS-SKIN}/img/but_minus.gif" width="16" height="16" alt="" /></a>
	      	<!-- END FOLDER_BUTTON-EXPAND_COLLAPSE -->
	      <!-- END FOLDER_BUTTON-EXPAND -->

	      <!-- BEGIN FOLDER_BUTTON-CONFIG -->
		      <!-- BEGIN FOLDER_BUTTON-CONFIG_ON -->
					<a href="{LINK-HREF}" class="toolinfo"><img src="tpl/{CMS-SKIN}/img/but_folder_info.gif" width="16" height="16" alt=""/></a>
		      <!-- END FOLDER_BUTTON-CONFIG_ON -->
		      <!-- BEGIN FOLDER_BUTTON-CONFIG_NON -->
					<a href="{LINK-HREF}" class="toolinfo"><img src="tpl/{CMS-SKIN}/img/but_folder_off.gif" width="16" height="16" alt=""/></a>
		      <!-- END FOLDER_BUTTON-CONFIG_NON -->		 
		      <!-- BEGIN FOLDER_BUTTON-CONFIG_OFF -->
					<a class="toolinfo"><img src="tpl/{CMS-SKIN}/img/but_folder_info.gif" width="16" height="16" alt=""/></a>
		      <!-- END FOLDER_BUTTON-CONFIG_OFF -->
		      <!-- BEGIN FOLDER_BUTTON-CONFIG_NON-OFF -->
					<a class="toolinfo"><img src="tpl/{CMS-SKIN}/img/but_folder_off.gif" width="16" height="16" alt=""/></a>
		      <!-- END FOLDER_BUTTON-CONFIG_NON-OFF -->		 
		      <!-- BEGIN FOLDER_BUTTON-CONFIG_INFO -->		 
					<span class="toolinfo"><!-- {TOOLINFO-TITLE}<br/> -->
						<table class="toolinfotablefolder" cellspacing="0" cellpadding="0" border="0">
							<tr class="headline">
								<th>{LNG_FOLDERINFO}</th>
								<th align="right"><small>idcat</small> {IDCAT}</th>
							</tr>
							<tr>
								<td class="first"><strong>{LNG_TPL}</strong></td>
								<td class="first" align="right">{TPL-NAME}</td>
							</tr>
							<!-- <tr>
								<td><strong>{LNG_EDITOR}</strong></td>
								<td align="right">{EDITOR}</td>
							</tr> -->
						</table>
					</span>
		      <!-- END FOLDER_BUTTON-CONFIG_INFO -->		 
	      <!-- END FOLDER_BUTTON-CONFIG -->	 
			{CAT_NAME}
			</td>
			<td width="1%" class="nobordl">     	  
				<!-- BEGIN FOLDER_ACTION-SELECT -->
		    <select size="1" onchange="if(this.options[this.selectedIndex].value != ''){window.location.href = this.options[this.selectedIndex].value}" style="width:180px;">
		      <!-- BEGIN FOLDER_ACTION-SELECT_HEADLINE-ENTRY-SORT -->
		      <option value="" style="color:#144282;margin:3px 0;">{LNG_ENTRY-HEADLINE}</option>
		      <!-- END FOLDER_ACTION-SELECT_HEADLINE-ENTRY-SORT -->
		      <!-- BEGIN FOLDER_ACTION-SELECT_ENTRY-SORT -->
		      <option value="{FIELD-VALUE}" {FIELD-SELECTED} style="color:#222;font-size:10px !important;">{FIELD-TITLE}</option>
		      <!-- END FOLDER_ACTION-SELECT_ENTRY-SORT -->
		      <!-- BEGIN FOLDER_ACTION-SELECT_HEADLINE-ENTRY-MOVE -->
		      <option value="" style="color:#144282;margin:3px 0;">{LNG_ENTRY-HEADLINE}</option>
		      <!-- END FOLDER_ACTION-SELECT_HEADLINE-ENTRY-MOVE -->
		      <!-- BEGIN FOLDER_ACTION-SELECT_ENTRY-MOVE -->
		      <option value="{FIELD-VALUE}" {FIELD-SELECTED} style="color:#222;font-size:10px !important;">{FIELD-TITLE}</option>
		      <!-- END FOLDER_ACTION-SELECT_ENTRY-MOVE -->
		      </select>
			  <!-- END FOLDER_ACTION-SELECT -->
			</td>
      <td width="1%" class="entry buttons" >

	      <!-- BEGIN FOLDER_BUTTON-NEWSIDE -->
	      	<!-- BEGIN FOLDER_BUTTON-NEWSIDE_ON -->
					<a href="{LINK-HREF}" title="{LINK-TITLE}"><img src="tpl/{CMS-SKIN}/img/but_newside.gif" width="16" height="16" alt=""/></a>
	      	<!-- END FOLDER_BUTTON-NEWSIDE_ON -->
	      	<!-- BEGIN FOLDER_BUTTON-NEWSIDE_OFF -->
					<img src="tpl/{CMS-SKIN}/img/space.gif" width="16" height="16" alt=""/>
	      	<!-- END FOLDER_BUTTON-NEWSIDE_OFF -->
	      <!-- END FOLDER_BUTTON-NEWSIDE -->
	      
	      <!-- BEGIN FOLDER_BUTTON-NEWCAT -->
		      <!-- BEGIN FOLDER_BUTTON-NEWCAT_ON -->
					<a href="{LINK-HREF}" title="{LINK-TITLE}"><img src="tpl/{CMS-SKIN}/img/but_newcat.gif" width="16" height="16" alt=""/></a>
	      	<!-- END FOLDER_BUTTON-NEWCAT_ON -->
	      	<!-- BEGIN FOLDER_BUTTON-NEWCAT_OFF -->
					<img src="tpl/{CMS-SKIN}/img/space.gif" width="16" height="16" alt=""/>
	      	<!-- END FOLDER_BUTTON-NEWCAT_OFF -->
	      <!-- END FOLDER_BUTTON-NEWCAT -->
	      
	      <!-- BEGIN FOLDER_BUTTON-COPYCAT -->
		      <!-- BEGIN FOLDER_BUTTON-COPYCAT_ON -->
					<a href="{LINK-HREF}" title="{LINK-TITLE}"><img src="tpl/{CMS-SKIN}/img/but_copy_cat.gif" width="16" height="16" alt=""/></a>
		      <!-- END FOLDER_BUTTON-COPYCAT_ON -->
		      <!-- BEGIN FOLDER_BUTTON-COPYCAT_OFF -->
					<img src="tpl/{CMS-SKIN}/img/space.gif" width="16" height="16" alt=""/>
		      <!-- END FOLDER_BUTTON-COPYCAT_OFF -->
	      <!-- END FOLDER_BUTTON-COPYCAT -->
	      
	      <!-- BEGIN FOLDER_BUTTON-LOCK -->
		      <!-- BEGIN FOLDER_BUTTON-LOCK_LOCK -->
					<a href="{LINK-HREF}#sideanchor" title="{LINK-TITLE}"><img src="tpl/{CMS-SKIN}/img/but_unlock.gif" width="16" height="16" alt=""/></a>
		      <!-- END FOLDER_BUTTON-LOCK_LOCK -->
		      <!-- BEGIN FOLDER_BUTTON-LOCK_UNLOCK -->
					<a href="{LINK-HREF}#sideanchor" title="{LINK-TITLE}"><img src="tpl/{CMS-SKIN}/img/but_lock.gif" width="16" height="16" alt=""/></a>
		      <!-- END FOLDER_BUTTON-LOCK_UNLOCK -->
		      <!-- BEGIN FOLDER_BUTTON-LOCK_OFF -->
					<img src="tpl/{CMS-SKIN}/img/space.gif" width="16" height="16" alt=""/>
		      <!-- END FOLDER_BUTTON-LOCK_OFF -->
	      <!-- END FOLDER_BUTTON-LOCK -->
	      
	      <!-- BEGIN FOLDER_BUTTON-PUBLISH -->
		      <!-- BEGIN FOLDER_BUTTON-PUBLISH_ONLINE -->
					<a href="{LINK-HREF}" title="{LINK-TITLE}"><img src="tpl/{CMS-SKIN}/img/but_online.gif" width="16" height="16" alt=""/></a>
		      <!-- END FOLDER_BUTTON-PUBLISH_ONLINE -->
		      <!-- BEGIN FOLDER_BUTTON-PUBLISH_OFFLINE -->
					<a href="{LINK-HREF}" title="{LINK-TITLE}"><img src="tpl/{CMS-SKIN}/img/but_offline.gif" width="16" height="16" alt=""/></a>
		      <!-- END FOLDER_BUTTON-PUBLISH_OFFLINE -->
		      <!-- BEGIN FOLDER_BUTTON-PUBLISH_ON-PUBLISH -->
					<a href="{LINK-HREF}" title="{LINK-TITLE}"><img src="tpl/{CMS-SKIN}/img/but_onpublish.gif" width="16" height="16" alt=""/></a>
		      <!-- END FOLDER_BUTTON-PUBLISH_ON-PUBLISH -->
		      <!-- BEGIN FOLDER_BUTTON-PUBLISH_OFF-PUBLISH -->
					<a href="{LINK-HREF}" title="{LINK-TITLE}"><img src="tpl/{CMS-SKIN}/img/but_offpublish.gif" width="16" height="16" alt=""/></a>
		      <!-- END FOLDER_BUTTON-PUBLISH_OFF-PUBLISH -->
		      <!-- BEGIN FOLDER_BUTTON-PUBLISH_OFF -->
					<img src="tpl/{CMS-SKIN}/img/space.gif" width="16" height="16" alt=""/>
		      <!-- END FOLDER_BUTTON-PUBLISH_OFF -->
	      <!-- END FOLDER_BUTTON-PUBLISH -->	      

	      <!-- BEGIN FOLDER_BUTTON-DELETE -->
		      <!-- BEGIN FOLDER_BUTTON-DELETE_ON -->
					<a href="{LINK-HREF}" onclick="return delete_confirm()" title="{LINK-TITLE}"><img src="tpl/{CMS-SKIN}/img/but_deleteside.gif" width="16" height="16" alt=""/></a>
		      <!-- END FOLDER_BUTTON-DELETE_ON -->
		      <!-- BEGIN FOLDER_BUTTON-DELETE_OFF -->
					<img src="tpl/{CMS-SKIN}/img/space.gif" width="16" height="16" alt=""/>
		      <!-- END FOLDER_BUTTON-DELETE_OFF -->
	      <!-- END FOLDER_BUTTON-DELETE -->

	      <!-- BEGIN FOLDER_BUTTON-PREVIEW -->
				<a href="{LINK-HREF}" target="_blank" title="{LINK-TITLE}"><img src="tpl/{CMS-SKIN}/img/but_preview.gif" width="16" height="16" alt=""/></a>
	      <!-- END FOLDER_BUTTON-PREVIEW -->
      
	      <!-- BEGIN FOLDER_BUTTON-SORT -->
		      <!-- BEGIN FOLDER_BUTTON-SORT_TOP -->
					<a href="{LINK-HREF}#catanchor" title="{LINK-TITLE}"><img src="tpl/{CMS-SKIN}/img/but_foldertop.gif" width="16" height="16" alt=""/></a>
		      <!-- END FOLDER_BUTTON-SORT_TOP -->
		      <!-- BEGIN FOLDER_BUTTON-SORT_TOP-OFF -->
					<img src="tpl/{CMS-SKIN}/img/space.gif" width="16" height="16" alt=""/>
		      <!-- END FOLDER_BUTTON-SORT_TOP-OFF -->
		      <!-- BEGIN FOLDER_BUTTON-SORT_UP -->
					<a href="{LINK-HREF}#catanchor" title="{LINK-TITLE}"><img src="tpl/{CMS-SKIN}/img/but_folderup.gif" width="16" height="16" alt=""/></a>
		      <!-- END FOLDER_BUTTON-SORT_UP -->
		      <!-- BEGIN FOLDER_BUTTON-SORT_UP-OFF -->
					<img src="tpl/{CMS-SKIN}/img/space.gif" width="16" height="16" alt=""/>
		      <!-- END FOLDER_BUTTON-SORT_UP-OFF -->
		      <!-- BEGIN FOLDER_BUTTON-SORT_DOWN -->
					<a href="{LINK-HREF}#catanchor" title="{LINK-TITLE}"><img src="tpl/{CMS-SKIN}/img/but_folderdown.gif" width="16" height="16" alt=""/></a>
		      <!-- END FOLDER_BUTTON-SORT_DOWN -->
		      <!-- BEGIN FOLDER_BUTTON-SORT_DOWN-OFF -->
					<img src="tpl/{CMS-SKIN}/img/space.gif" width="16" height="16" alt=""/>
		      <!-- END FOLDER_BUTTON-SORT_DOWN-OFF -->
		      <!-- BEGIN FOLDER_BUTTON-SORT_BOTTOM -->
					<a href="{LINK-HREF}#catanchor" title="{LINK-TITLE}"><img src="tpl/{CMS-SKIN}/img/but_folderbottom.gif" width="16" height="16" alt=""/></a>
		      <!-- END FOLDER_BUTTON-SORT_BOTTOM -->
		      <!-- BEGIN FOLDER_BUTTON-SORT_BOTTOM-OFF -->
					<img src="tpl/{CMS-SKIN}/img/space.gif" width="16" height="16" alt=""/>
		      <!-- END FOLDER_BUTTON-SORT_BOTTOM-OFF -->
	      <!-- END FOLDER_BUTTON-SORT -->

		</td>
    </tr>
    <!-- BEGIN SIDES -->
    <tr rel="{TABLE_OVERCOLOR}" class="{TABLE_COLOR}" <!-- BEGIN SIDE_ANCHOR -->name="sideanchor" id="sideanchor"<!-- END SIDE_ANCHOR -->>
      <td class="entry nobordr" style="padding-left:{SIDE-PADDING-LEFT}px">
      	 <!-- BEGIN SIDE_BUTTON-CONFIG -->
		      <!-- BEGIN SIDE_BUTTON-CONFIG_ON -->
					<a href="{LINK-HREF}" class="toolinfo"><img src="tpl/{CMS-SKIN}/img/but_sideinfo.gif" width="16" height="16" alt=""/></a>
		      <!-- END SIDE_BUTTON-CONFIG_ON -->
		      <!-- BEGIN SIDE_BUTTON-CONFIG_CLONE -->
					<a href="{LINK-HREF}" class="toolinfo"><img src="tpl/{CMS-SKIN}/img/but_sideinfo_doublet.gif" width="16" height="16" alt=""/></a>
		      <!-- END SIDE_BUTTON-CONFIG_CLONE -->
		      <!-- BEGIN SIDE_BUTTON-CONFIG_OFF -->
					<a class="toolinfo"><img src="tpl/{CMS-SKIN}/img/but_sideinfo.gif" width="16" height="16" alt=""/></a>
					<!-- END SIDE_BUTTON-CONFIG_OFF -->
		      <!-- BEGIN SIDE_BUTTON-CONFIG_CLONE-OFF -->
					<a class="toolinfo"><img src="tpl/{CMS-SKIN}/img/but_sideinfo_doublet.gif" width="16" height="16" alt=""/></a>
		      <!-- END SIDE_BUTTON-CONFIG_CLONE-OFF -->

		      <!-- BEGIN SIDE_BUTTON-CONFIG_INFO -->		 
					<span class="toolinfo"><!-- {TOOLINFO-TITLE}<br/> -->
						<table class="toolinfotableside" cellspacing="0" cellpadding="0" border="0">
							<tr class="headline">
								<th>{LNG_SIDEINFO}</th>
								<th align="right"><small>idcatside</small> {IDCATSIDE}</th>
							</tr>
							<tr>
								<td class="first"><strong>{LNG_CREATED}</strong></td>
								<td class="first" align="right">{CREATED}</td>
							</tr>
							<tr>
								<td><strong>{LNG_MODIFIED}</strong></td>
								<td align="right">{MODIFIED}</td>
							</tr>
							<tr>
								<td><strong>{LNG_TPL}</strong></td>
								<td align="right"><span {TPL-CLASS}>{TPL-NAME}</span></td>
							</tr>
							<tr>
								<td><strong>{LNG_EDITOR}</strong></td>
								<td align="right">{EDITOR}</td>
							</tr>
						  <!-- BEGIN SIDE_BUTTON-CONFIG_INFO_SUMMARY -->		 
							<tr>
								<td colspan="2"><strong>{LNG_SUMMARY}</strong><br/>{SUMMARY}</td>
							</tr>
				      <!-- END SIDE_BUTTON-CONFIG_INFO_SUMMARY -->		 
				      <!-- BEGIN SIDE_BUTTON-CONFIG_INFO_REDIRECT -->		 
							<tr>
								<td><strong>{LNG_REDIRECT}</strong></td>
								<td align="right">{REDIRECT}</td>
							</tr>
				      <!-- END SIDE_BUTTON-CONFIG_INFO_REDIRECT -->	
						</table>
					</span>
		      <!-- END SIDE_BUTTON-CONFIG_INFO -->		 
	      <!-- END SIDE_BUTTON-CONFIG -->
	      
				<!-- BEGIN SIDE_NAME -->
	    		<a href="{LINK-HREF}" class="action" title="{LINK-TITLE}">{LINK-NAME}</a>
	      <!-- END SIDE_NAME -->
			</td>
      <td width="1%" class="nobordl">&nbsp;</td>
      <td width="1%" class="entry buttons">

	      <!-- BEGIN SIDE_BUTTON-STARTPAGE -->
	      	<!-- BEGIN SIDE_BUTTON-STARTPAGE_IS -->
					<a title="{LINK-TITLE}"><img src="tpl/{CMS-SKIN}/img/but_start_yes.gif" width="16" height="16" alt=""/></a>
	      	<!-- END SIDE_BUTTON-STARTPAGE_IS -->
	      	<!-- BEGIN SIDE_BUTTON-STARTPAGE_IS-NOT -->
					<a href="{LINK-HREF}" title="{LINK-TITLE}"><img src="tpl/{CMS-SKIN}/img/but_start_no.gif" width="16" height="16" alt=""/></a>
	      	<!-- END SIDE_BUTTON-STARTPAGE_IS-NOT -->
	      	<!-- BEGIN SIDE_BUTTON-STARTPAGE_OFF -->
					<img src="tpl/{CMS-SKIN}/img/space.gif" width="16" height="16" alt=""/>
	      	<!-- END SIDE_BUTTON-STARTPAGE_OFF -->
	      <!-- END SIDE_BUTTON-STARTPAGE -->
	      
	      <!-- BEGIN SIDE_BUTTON-EDIT -->
		      <!-- BEGIN SIDE_BUTTON-EDIT_ON -->
					<a href="{LINK-HREF}" title="{LINK-TITLE}"><img src="tpl/{CMS-SKIN}/img/but_edit.gif" width="16" height="16" alt=""/></a>
	      	<!-- END SIDE_BUTTON-EDIT_ON -->
	      	<!-- BEGIN SIDE_BUTTON-EDIT_OFF -->
					<img src="tpl/{CMS-SKIN}/img/space.gif" width="16" height="16" alt=""/>
	      	<!-- END SIDE_BUTTON-EDIT_OFF -->
	      <!-- END SIDE_BUTTON-EDIT -->
	      
	      <!-- BEGIN SIDE_BUTTON-COPY -->
		      <!-- BEGIN SIDE_BUTTON-COPY_ON -->
					<a href="{LINK-HREF}" title="{LINK-TITLE}"><img src="tpl/{CMS-SKIN}/img/but_duplicate.gif" width="16" height="16" alt=""/></a>
		      <!-- END SIDE_BUTTON-COPY_ON -->
		      <!-- BEGIN SIDE_BUTTON-COPY_OFF -->
					<img src="tpl/{CMS-SKIN}/img/space.gif" width="16" height="16" alt=""/>
		      <!-- END SIDE_BUTTON-COPY_OFF -->
	      <!-- END SIDE_BUTTON-COPY -->
	      
	      <!-- BEGIN SIDE_BUTTON-LOCK -->
		      <!-- BEGIN SIDE_BUTTON-LOCK_LOCK -->
					<a href="{LINK-HREF}#sideanchor" title="{LINK-TITLE}"><img src="tpl/{CMS-SKIN}/img/but_unlock.gif" width="16" height="16" alt=""/></a>
		      <!-- END SIDE_BUTTON-LOCK_LOCK -->
		      <!-- BEGIN SIDE_BUTTON-LOCK_UNLOCK -->
					<a href="{LINK-HREF}#sideanchor" title="{LINK-TITLE}"><img src="tpl/{CMS-SKIN}/img/but_lock.gif" width="16" height="16" alt=""/></a>
		      <!-- END SIDE_BUTTON-LOCK_UNLOCK -->
		      <!-- BEGIN SIDE_BUTTON-LOCK_OFF -->
					<img src="tpl/{CMS-SKIN}/img/space.gif" width="16" height="16" alt=""/>
		      <!-- END SIDE_BUTTON-LOCK_OFF -->
	      <!-- END SIDE_BUTTON-LOCK -->
	      
	      <!-- BEGIN SIDE_BUTTON-PUBLISH -->
		      <!-- BEGIN SIDE_BUTTON-PUBLISH_ONLINE -->
					<a href="{LINK-HREF}#sideanchor" title="{LINK-TITLE}"><img src="tpl/{CMS-SKIN}/img/but_online.gif" width="16" height="16" alt=""/></a>
		      <!-- END SIDE_BUTTON-PUBLISH_ONLINE -->
		      <!-- BEGIN SIDE_BUTTON-PUBLISH_ONOFFLINE -->
					<a href="{LINK-HREF}#sideanchor" title="{LINK-TITLE}"><img src="tpl/{CMS-SKIN}/img/but_onoffline.gif" width="16" height="16" alt=""/></a>
		      <!-- END SIDE_BUTTON-PUBLISH_ONOFFLINE -->
		      <!-- BEGIN SIDE_BUTTON-PUBLISH_OFFLINE -->
					<a href="{LINK-HREF}#sideanchor" title="{LINK-TITLE}"><img src="tpl/{CMS-SKIN}/img/but_offline.gif" width="16" height="16" alt=""/></a>
		      <!-- END SIDE_BUTTON-PUBLISH_OFFLINE -->
		      <!-- BEGIN SIDE_BUTTON-PUBLISH_ON-PUBLISH -->
					<a href="{LINK-HREF}#sideanchor" title="{LINK-TITLE}"><img src="tpl/{CMS-SKIN}/img/but_onpublish.gif" width="16" height="16" alt=""/></a>
		      <!-- END SIDE_BUTTON-PUBLISH_ON-PUBLISH -->
		      <!-- BEGIN SIDE_BUTTON-PUBLISH_OFF-PUBLISH -->
					<a href="{LINK-HREF}#sideanchor" title="{LINK-TITLE}"><img src="tpl/{CMS-SKIN}/img/but_offpublish.gif" width="16" height="16" alt=""/></a>
		      <!-- END SIDE_BUTTON-PUBLISH_OFF-PUBLISH -->
		      <!-- BEGIN SIDE_BUTTON-PUBLISH_TIME -->
					<a href="{LINK-HREF}#sideanchor" title="{LINK-TITLE}"><img src="tpl/{CMS-SKIN}/img/but_time.gif" width="16" height="16" alt=""/></a>
		      <!-- END SIDE_BUTTON-PUBLISH_TIME -->
		      <!-- BEGIN SIDE_BUTTON-PUBLISH_OFF -->
					<img src="tpl/{CMS-SKIN}/img/space.gif" width="16" height="16" alt=""/>
		      <!-- END SIDE_BUTTON-PUBLISH_OFF -->
	      <!-- END SIDE_BUTTON-PUBLISH -->	      

	      <!-- BEGIN SIDE_BUTTON-DELETE -->
		      <!-- BEGIN SIDE_BUTTON-DELETE_ON -->
					<a href="{LINK-HREF}#sideanchor" onclick="return delete_confirm()" title="{LINK-TITLE}"><img src="tpl/{CMS-SKIN}/img/but_deleteside.gif" width="16" height="16" alt=""/></a>
		      <!-- END SIDE_BUTTON-DELETE_ON -->
		      <!-- BEGIN SIDE_BUTTON-DELETE_ON-CLONE -->
					<a href="{LINK-HREF}#sideanchor" onclick="return confirm('Achtung! Diese Seite existiert als Kopie/ Klone in mehreren Kategorien! Wirklich löschen?');" title="{LINK-TITLE}"><img src="tpl/{CMS-SKIN}/img/but_deleteside.gif" width="16" height="16" alt=""/></a>
		      <!-- END SIDE_BUTTON-DELETE_ON-CLONE -->
		      <!-- BEGIN SIDE_BUTTON-DELETE_OFF -->
					<img src="tpl/{CMS-SKIN}/img/space.gif" width="16" height="16" alt=""/>
		      <!-- END SIDE_BUTTON-DELETE_OFF -->
	      <!-- END SIDE_BUTTON-DELETE -->

	      <!-- BEGIN SIDE_BUTTON-PREVIEW -->
					<a href="{LINK-HREF}" target="_blank" title="{LINK-TITLE}"><img src="tpl/{CMS-SKIN}/img/but_preview.gif" width="16" height="16" alt=""/></a>
	      <!-- END SIDE_BUTTON-PREVIEW -->
            
	      <!-- BEGIN SIDE_BUTTON-SORT -->
		      <!-- BEGIN SIDE_BUTTON-SORT_TOP -->
					<a href="{LINK-HREF}#sideanchor" title="{LINK-TITLE}"><img src="tpl/{CMS-SKIN}/img/but_sidetop.gif" width="16" height="16" alt=""/></a>
		      <!-- END SIDE_BUTTON-SORT_TOP -->
		      <!-- BEGIN SIDE_BUTTON-SORT_TOP-OFF -->
					<img src="tpl/{CMS-SKIN}/img/space.gif" width="16" height="16" alt=""/>
		      <!-- END SIDE_BUTTON-SORT_TOP-OFF -->
		      <!-- BEGIN SIDE_BUTTON-SORT_UP -->
					<a href="{LINK-HREF}#sideanchor" title="{LINK-TITLE}"><img src="tpl/{CMS-SKIN}/img/but_sideup.gif" width="16" height="16" alt=""/></a>
		      <!-- END SIDE_BUTTON-SORT_UP -->
		      <!-- BEGIN SIDE_BUTTON-SORT_UP-OFF -->
					<img src="tpl/{CMS-SKIN}/img/space.gif" width="16" height="16" alt=""/>
		      <!-- END SIDE_BUTTON-SORT_UP-OFF -->
		      <!-- BEGIN SIDE_BUTTON-SORT_DOWN -->
					<a href="{LINK-HREF}#sideanchor" title="{LINK-TITLE}"><img src="tpl/{CMS-SKIN}/img/but_sidedown.gif" width="16" height="16" alt=""/></a>
		      <!-- END SIDE_BUTTON-SORT_DOWN -->
		      <!-- BEGIN SIDE_BUTTON-SORT_DOWN-OFF -->
					<img src="tpl/{CMS-SKIN}/img/space.gif" width="16" height="16" alt=""/>
		      <!-- END SIDE_BUTTON-SORT_DOWN-OFF -->
		      <!-- BEGIN SIDE_BUTTON-SORT_BOTTOM -->
					<a href="{LINK-HREF}#sideanchor" title="{LINK-TITLE}"><img src="tpl/{CMS-SKIN}/img/but_sidebottom.gif" width="16" height="16" alt=""/></a>
		      <!-- END SIDE_BUTTON-SORT_BOTTOM -->
		      <!-- BEGIN SIDE_BUTTON-SORT_BOTTOM-OFF -->
					<img src="tpl/{CMS-SKIN}/img/space.gif" width="16" height="16" alt=""/>
		      <!-- END SIDE_BUTTON-SORT_BOTTOM-OFF -->
	      <!-- END SIDE_BUTTON-SORT -->

      </td>
		</tr>
    <!-- END SIDES -->
    <!-- END FOLDER -->
  </table>
  <!-- END MAIN-FOLDER -->

  <!-- BEGIN EMPTY -->
  <table class="uber" style="margin:0">  
	  <tr class="tblrbgcolors2">
	    <td class="entry">{LANG_NOCATS}</td>
	  </tr>
  </table>
  <!-- END EMPTY -->

</div>

{FOOTER}
