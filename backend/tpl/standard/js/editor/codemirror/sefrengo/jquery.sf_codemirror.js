/**
 * Scans the document for class '.codemirror' and replace it with the CodeMirror editor
 * Note: Needs jQuery v1.6+
 */
$jqsf(document).ready( function() {
	var editor = null;
	
	// Fullscreen editing isn't working correctly:
	// # If more than on CM is used on one page always the last instance is used. 
	// # On closing the fullscreen mode the length of the "small" editor is greater (than before using fullscreen mode)
	/*var toggleFullscreenEditing = function (editor)
	{
		var editorDiv = $jqsf('.CodeMirror-scroll');
		if (!editorDiv.hasClass('fullscreen')) {
			$jqsf('#main').css({'position': 'static'});
			toggleFullscreenEditing.beforeFullscreen = { height: editorDiv.height(), width: editorDiv.width() }
			editorDiv.addClass('fullscreen');
			editorDiv.height('100%');
			editorDiv.width('100%');
			editor.refresh();
		}
		else {
			$jqsf('#main').css({'position': null});
			editorDiv.removeClass('fullscreen');
			editorDiv.height(null);//toggleFullscreenEditing.beforeFullscreen.height);
			editorDiv.width(null);//toggleFullscreenEditing.beforeFullscreen.width);
			editor.refresh();
		}
	}*/
	
	$jqsf('.codemirror').each(function() {
		var $this = $jqsf(this);
		var options = {};
		
		if(typeof($this.data('options')) !== 'undefined')
		{
			options = $this.data('options');
		}
		options.id = $this.attr('id');
		
		// auto-close/complete html/xml tags in "text/html" (based on htmlmixed.js or xml.js) and "xmlpure" modes
		options.extraKeys = {
			"'>'": function(cm) { cm.closeTag(cm, '>'); },
			"'/'": function(cm) { cm.closeTag(cm, '/'); }
		};
		
		// highlight current line
		options.onCursorActivity = function() {
			editor.setLineClass(hlLine, null, null);
			hlLine = editor.setLineClass(editor.getCursor().line, null, "activeline");
			
			// highlight matches of selected text on select
			editor.matchHighlight("CodeMirror-matchhighlight");
		};
		
		// fullscreen editing
		/*options.onKeyEvent = function(i, e) {
		  // Hook into F11
		  if ((e.keyCode == 122 || e.keyCode == 27) && e.type == 'keydown') {
			e.stop();
			return toggleFullscreenEditing(editor);
		  }
		}*/
		
		// replace editor with textarea
		editor = CodeMirror.fromTextArea(document.getElementById(options.id), options);
		var hlLine = editor.setLineClass(0, "activeline");
		
		// search and replace
		/*var lastPos = null, lastQuery = null, marked = [];

		$jqsf('.'+options.id+'_search_btn').click(function() {
			for (var i = 0; i < marked.length; ++i) marked[i]();
			marked.length = 0;
			
			var text = $jqsf('.'+options.id+'_query_txt').attr('value');
			if (!text) return;
			for (var cursor = editor.getSearchCursor(text); cursor.findNext();)
				marked.push(editor.markText(cursor.from(), cursor.to(), "searched"));
			
			if (lastQuery != text) lastPos = null;
			var cursor = editor.getSearchCursor(text, lastPos || editor.getCursor());
			if (!cursor.findNext()) {
				cursor = editor.getSearchCursor(text);
				if (!cursor.findNext()) return;
			}
			editor.setSelection(cursor.from(), cursor.to());
			lastQuery = text; lastPos = cursor.to();
		});
		
		$jqsf('.'+options.id+'_replace_btn').click(function() {
			for (var i = 0; i < marked.length; ++i) marked[i]();
			marked.length = 0;
			
			var text = $jqsf('.'+options.id+'_query_txt').attr('value'),
				replace = $jqsf('.'+options.id+'_replace_txt').attr('value');
				
			if (!text) return;
			
			for (var cursor = editor.getSearchCursor(text); cursor.findNext();)
				cursor.replace(replace);
		});*/
		
		// add textarea resize to resize CodeMirror
		$jqsf('div.CodeMirror-scroll:not(.processed)').TextAreaResizer();
	});
});


/* 
	jQuery TextAreaResizer plugin
	Created on 17th January 2008 by Ryan O'Dell 
	Version 1.0.4
	
	Converted from Drupal -> textarea.js
	Found source: http://plugins.jquery.com/misc/textarea.js
	$Id: textarea.js,v 1.11.2.1 2007/04/18 02:41:19 drumm Exp $

	1.0.1 Updates to missing global 'var', added extra global variables, fixed multiple instances, improved iFrame support
	1.0.2 Updates according to textarea.focus
	1.0.3 Further updates including removing the textarea.focus and moving private variables to top
	1.0.4 Re-instated the blur/focus events, according to information supplied by dec

	
*/
(function($) {
	/* private variable "oHover" used to determine if you're still hovering over the same element */
	var textarea, staticOffset;  // added the var declaration for 'staticOffset' thanks to issue logged by dec.
	var iLastMousePos = 0;
	var iMin = 32;
	var grip;
	/* TextAreaResizer plugin */
	$.fn.TextAreaResizer = function() {
		return this.each(function() {
			textarea = $(this).addClass('processed'), staticOffset = null;

			// 18-01-08 jQuery bind to pass data element rather than direct mousedown - Ryan O'Dell
			// When wrapping the text area, work around an IE margin bug.  See:
			// http://jaspan.com/ie-inherited-margin-bug-form-elements-and-haslayout
			$(this).wrap('<div class="resizable-textarea"><span></span></div>')
			  .parent().append($('<div class="grippie"></div>').bind("mousedown",{el: this} , startDrag));

			var grippie = $('div.grippie', $(this).parent())[0];
			grippie.style.marginRight = (grippie.offsetWidth - $(this)[0].offsetWidth) +'px';

		});
	};
	/* private functions */
	function startDrag(e) {
		textarea = $(e.data.el);
		textarea.blur();
		iLastMousePos = mousePosition(e).y;
		staticOffset = textarea.height() - iLastMousePos;
		textarea.css('opacity', 0.25);
		$(document).mousemove(performDrag).mouseup(endDrag);
		return false;
	}

	function performDrag(e) {
		var iThisMousePos = mousePosition(e).y;
		var iMousePos = staticOffset + iThisMousePos;
		if (iLastMousePos >= (iThisMousePos)) {
			iMousePos -= 5;
		}
		iLastMousePos = iThisMousePos;
		iMousePos = Math.max(iMin, iMousePos);
		textarea.height(iMousePos + 'px');
		if (iMousePos < iMin) {
			endDrag(e);
		}
		return false;
	}

	function endDrag(e) {
		$(document).unbind('mousemove', performDrag).unbind('mouseup', endDrag);
		textarea.css('opacity', 1);
		textarea.focus();
		textarea = null;
		staticOffset = null;
		iLastMousePos = 0;
	}

	function mousePosition(e) {
		return { x: e.clientX + document.documentElement.scrollLeft, y: e.clientY + document.documentElement.scrollTop };
	};
})(jQuery);