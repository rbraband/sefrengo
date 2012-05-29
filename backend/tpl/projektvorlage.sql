INSERT INTO <!--{db_prefix}-->clients VALUES (<!--{idclient}-->, '<!--{projectname}-->', '<!--{projectdesc}-->', <!--{userid}-->, <!--{time}-->, 0);

# CLIENT CONFIG

# PATHES
INSERT INTO <!--{db_prefix}-->values VALUES ('', <!--{idclient}-->, 0, 'cfg_client', 'path_http', '', '', '', '<!--{base_http_path_absolute}-->', 110, 'setuse_path_http', 'setuse_pathes_title', 'txt', NULL, NULL, '1');
INSERT INTO <!--{db_prefix}-->values VALUES ('', <!--{idclient}-->, 0, 'cfg_client', 'path_http_edit', '', '', '', '<!--{base_http_path_absolute}-->', 120, 'setuse_path_http_edit', NULL, 'txt', NULL, NULL, '1');
INSERT INTO <!--{db_prefix}-->values VALUES ('', <!--{idclient}-->, 0, 'cfg_client', 'path_rel', '', '', '', '<!--{client_path_relative}-->', 130, 'setuse_path_rel', NULL, 'txt', NULL, NULL, '1');
INSERT INTO <!--{db_prefix}-->values VALUES ('', <!--{idclient}-->, 0, 'cfg_client', 'contentfile', '', '', '', 'index.php', 140, 'setuse_contentfile', NULL, 'txt', NULL, NULL, '1');
INSERT INTO <!--{db_prefix}-->values VALUES ('', <!--{idclient}-->, 0, 'cfg_client', 'path_fm_rel', '', '', '', 'media/', 150, 'setuse_path_fm_rel', NULL, 'txt', NULL, NULL, '1');
INSERT INTO <!--{db_prefix}-->values VALUES ('', <!--{idclient}-->, 0, 'cfg_client', 'path_css_rel', '', '', '', 'cms/css/', 160, 'setuse_path_css_rel', NULL, 'txt', NULL, NULL, '1');
INSERT INTO <!--{db_prefix}-->values VALUES ('', <!--{idclient}-->, 0, 'cfg_client', 'path_js_rel', '', '', '', 'cms/js/', 170, 'setuse_path_js_rel', NULL, 'txt', NULL, NULL, '1');
INSERT INTO <!--{db_prefix}-->values VALUES ('', <!--{idclient}-->, 0, 'cfg_client', 'page_start', '', '', '', '0', 180, 'setuse_page_start', NULL, 'txt', NULL, NULL, '1');
INSERT INTO <!--{db_prefix}-->values VALUES ('', <!--{idclient}-->, 0, 'cfg_client', 'page_404', '', '', '', '0', 190, 'setuse_page_404', NULL, 'txt', NULL, NULL, '1');
INSERT INTO <!--{db_prefix}-->values VALUES ('', <!--{idclient}-->, 0, 'cfg_client', 'page_timeout', '', '', '0', '', 200, 'setuse_page_timeout', NULL, 'txt', NULL, NULL, '1');
INSERT INTO <!--{db_prefix}-->values VALUES ('', <!--{idclient}-->, 0, 'cfg_client', 'https', '', '', '', '0', 210, 'setuse_https', NULL, 'txt', NULL, NULL, '1');

# MOD_REWRITE
INSERT INTO <!--{db_prefix}-->values VALUES ('', <!--{idclient}-->, 0, 'cfg_client', 'url_rewrite', '', '', '', '2', 310, 'setuse_url_rewrite', 'setuse_mod_rewrite_title', 'txt', NULL, NULL, 1);
INSERT INTO <!--{db_prefix}-->values VALUES ('', <!--{idclient}-->, 0, 'cfg_client', 'url_langid_in_defaultlang', '', '', '', '0', 320, 'setuse_url_langid_in_defaultlang', NULL , 'txt', NULL , NULL , '1');
INSERT INTO <!--{db_prefix}-->values VALUES ('', <!--{idclient}-->, 0, 'cfg_client', 'url_rewrite_suffix', '', '', '', '.html', 330, 'setuse_url_rewrite_suffix', NULL , 'txt', NULL , NULL , '1');

# SESSION
INSERT INTO <!--{db_prefix}-->values VALUES ('', <!--{idclient}-->, 0, 'cfg_client', 'session_enabled', '', '', '', '1', 410, 'setuse_session_enabled', 'setuse_session_title', 'txt', NULL, NULL, '1');
INSERT INTO <!--{db_prefix}-->values VALUES ('', <!--{idclient}-->, 0, 'cfg_client', 'session_lifetime', '', '', '', '15', 420, 'setuse_session_lifetime', NULL, 'txt', NULL, NULL, '1');
INSERT INTO <!--{db_prefix}-->values VALUES ('', <!--{idclient}-->, 0, 'cfg_client', 'session_frontend_domain', '', '', '', '', 430, 'setuse_session_frontend_domain', NULL, 'txt', NULL, NULL, '1');
INSERT INTO <!--{db_prefix}-->values VALUES ('', <!--{idclient}-->, 0, 'cfg_client', 'session_disabled_useragents', '', '', '', 'Googlebot\r\nYahoo\r\nScooter\r\nFAST-WebCrawler\r\nMSNBOT\r\nSeekbot\r\nInktomi\r\nLycos_Spider\r\nUltraseek\r\nOverture\r\nSlurp\r\nSidewinder\r\nMetaspinner\r\nJeeves\r\nWISEnutbot\r\nZealbot\r\nia_archiver\r\nAbachoBOT\r\nFirefly', 440, 'setuse_session_disabled_useragents', NULL, 'txtarea', NULL, NULL, '1');
INSERT INTO <!--{db_prefix}-->values VALUES ('', <!--{idclient}-->, 0, 'cfg_client', 'session_disabled_ips', '', '', '', '127.0.0.98\r\n127.0.0.99', 450, 'setuse_session_disabled_ips', NULL, 'txtarea', NULL, NULL, '1');

# CACHE
INSERT INTO <!--{db_prefix}-->values VALUES ('', <!--{idclient}-->, 0, 'cfg_client', 'cache', '', '', '', '1', 510, 'setuse_cache', 'setuse_cache_title', 'txt', NULL, NULL, 1);

# FILEMANAGER
INSERT INTO <!--{db_prefix}-->values VALUES ('', <!--{idclient}-->, 0, 'cfg_client', 'fm_multi_language_support', '', '', '', '1', 610, 'setuse_fm_multi_language_support', 'setuse_fm_title', 'txt', NULL, NULL, '1');
INSERT INTO <!--{db_prefix}-->values VALUES ('', <!--{idclient}-->, 0, 'cfg_client', 'fm_forbidden_files', '', '', '', 'php,htaccess,htpasswd,htfake,css,js', 620, 'setuse_fm_forbidden_files', NULL, 'txt', NULL, NULL, '1');
INSERT INTO <!--{db_prefix}-->values VALUES ('', <!--{idclient}-->, 0, 'cfg_client', 'fm_forbidden_directories', '', '', '', '', 630, 'setuse_fm_forbidden_directories', NULL, 'txt', NULL, NULL, '1');
INSERT INTO <!--{db_prefix}-->values VALUES ('', <!--{idclient}-->, 0, 'cfg_client', 'thumb_size', '', '', '', '100', 640, 'setuse_thumb_size', '', 'txt', NULL, NULL, '1');
INSERT INTO <!--{db_prefix}-->values VALUES ('', <!--{idclient}-->, 0, 'cfg_client', 'thumb_aspectratio', '', '', '', '1', 650, 'setuse_thumb_aspectratio', '', 'txt', NULL, NULL, '1');
INSERT INTO <!--{db_prefix}-->values VALUES ('', <!--{idclient}-->, 0, 'cfg_client', 'more_thumb_size', '', '', '', '', 660, 'setuse_more_thumb_size', NULL, 'txt', NULL, NULL, '1');
INSERT INTO <!--{db_prefix}-->values VALUES ('', <!--{idclient}-->, 0, 'cfg_client', 'more_thumb_aspect_ratio', '', '', '', '', 670, 'setuse_more_thumb_aspect_ratio', NULL, 'txt', NULL, NULL, '1');
INSERT INTO <!--{db_prefix}-->values VALUES ('', <!--{idclient}-->, 0, 'cfg_client', 'thumbext', '', '', '', '_cms_thumb', 680, 'setuse_thumb_ext', '', 'txt', NULL, NULL, '1');
INSERT INTO <!--{db_prefix}-->values VALUES ('', <!--{idclient}-->, 0, 'cfg_client', 'generate_thumb', '', '', '', 'gif,jpg,jpeg,png', 690, 'setuse_generate_thumb', '', 'txt', '', '', '1');
INSERT INTO <!--{db_prefix}-->values VALUES ('', <!--{idclient}-->, 0, 'cfg_client', 'fm_delete_ignore_404', '', '', '', '1', 700, 'setuse_fm_delete_ignore_404', '', 'txt', NULL, NULL, '1');
INSERT INTO <!--{db_prefix}-->values VALUES ('', <!--{idclient}-->, 0, 'cfg_client', 'fm_remove_files_404', '', '', '', '1', 710, 'setuse_fm_remove_files_404', '', 'txt', '', '', '1');
INSERT INTO <!--{db_prefix}-->values VALUES ('', <!--{idclient}-->, 0, 'cfg_client', 'fm_remove_empty_directories', '', '', '', '0', 720, 'setuse_fm_remove_empty_directories', '', 'txt', '', '', '1');
INSERT INTO <!--{db_prefix}-->values VALUES ('', <!--{idclient}-->, 0, 'cfg_client', 'fm_allow_invalid_dirnames', '', '', '', '2', 730, 'setuse_fm_allow_invalid_dirnames', NULL, 'txt', NULL, NULL, '1');
INSERT INTO <!--{db_prefix}-->values VALUES ('', <!--{idclient}-->, 0, 'cfg_client', 'fm_allow_invalid_filenames', '', '', '', '2', 740, 'setuse_fm_allow_invalid_filenames', NULL, 'txt', NULL, NULL, '1');
INSERT INTO <!--{db_prefix}-->values VALUES ('', <!--{idclient}-->, 0, 'cfg_client', 'fm_allowed_files', '', '', '', '', 750, 'setuse_fm_allowed_files', NULL, 'txt', NULL, NULL, '0');

INSERT INTO <!--{db_prefix}-->values VALUES ('', <!--{idclient}-->, 0, 'cfg_client', 'max_count_scandir', '', '', '', '10', 760, '', '', 'txt', NULL, NULL, 0);
INSERT INTO <!--{db_prefix}-->values VALUES ('', <!--{idclient}-->, 0, 'cfg_client', 'extend_scantime', '', '', '', '60', 770, '', '', 'txt', NULL, NULL, 0);
INSERT INTO <!--{db_prefix}-->values VALUES ('', <!--{idclient}-->, 0, 'cfg_client', 'max_count_scanfile', '', '', '', '2', 780, '', '', 'txt', NULL, NULL, 0);
INSERT INTO <!--{db_prefix}-->values VALUES ('', <!--{idclient}-->, 0, 'cfg_client', 'max_count_scanthumb', '', '', '', '10', 790, '', '', 'txt', NULL, NULL, 0);


# STYLESHEET
INSERT INTO <!--{db_prefix}-->values VALUES ('', <!--{idclient}-->, 0, 'cfg_client', 'css_multi_language_support', '', '', '', '0', 810, 'setuse_css_multi_language_support', 'setuse_css_title', 'txt', NULL, NULL, '0');
INSERT INTO <!--{db_prefix}-->values VALUES ('', <!--{idclient}-->, 0, 'cfg_client', 'css_forbidden_files', '', '', '', 'php,htaccess,htpasswd,htfake', 820, 'setuse_css_forbidden_files', NULL, 'txt', NULL, NULL, '0');
INSERT INTO <!--{db_prefix}-->values VALUES ('', <!--{idclient}-->, 0, 'cfg_client', 'css_forbidden_directories', '', '', '', '', 830, 'setuse_css_forbidden_directories', 'setuse_css_title', 'txt', NULL, NULL, '1');
INSERT INTO <!--{db_prefix}-->values VALUES ('', <!--{idclient}-->, 0, 'cfg_client', 'css_delete_ignore_404', '', '', '', '1', 840, 'setuse_css_delete_ignore_404', '', 'txt', NULL, NULL, '1');
INSERT INTO <!--{db_prefix}-->values VALUES ('', <!--{idclient}-->, 0, 'cfg_client', 'css_remove_files_404', '', '', '', '1', 850, 'setuse_css_remove_files_404', '', 'txt', '', '', '1');
INSERT INTO <!--{db_prefix}-->values VALUES ('', <!--{idclient}-->, 0, 'cfg_client', 'css_remove_empty_directories', '', '', '', '0', 860, 'setuse_css_remove_empty_directories', '', 'txt', '', '', '1');
INSERT INTO <!--{db_prefix}-->values VALUES ('', <!--{idclient}-->, 0, 'cfg_client', 'css_allow_invalid_dirnames', '', '', '', '2', 870, 'setuse_css_allow_invalid_dirnames', NULL, 'txt', NULL, NULL, '1');
INSERT INTO <!--{db_prefix}-->values VALUES ('', <!--{idclient}-->, 0, 'cfg_client', 'css_allow_invalid_filenames', '', '', '', '2', 880, 'setuse_css_allow_invalid_filenames', NULL, 'txt', NULL, NULL, '1');
INSERT INTO <!--{db_prefix}-->values VALUES ('', <!--{idclient}-->, 0, 'cfg_client', 'css_allowed_files', '', '', '', 'css', 890, 'setuse_css_allowed_files', NULL, 'txt', NULL, NULL, '1');

# JAVASCRIPT
INSERT INTO <!--{db_prefix}-->values VALUES ('', <!--{idclient}-->, 0, 'cfg_client', 'js_multi_language_support', '', '', '', '0', 910, 'setuse_js_multi_language_support', 'setuse_js_title', 'txt', NULL, NULL, '0');
INSERT INTO <!--{db_prefix}-->values VALUES ('', <!--{idclient}-->, 0, 'cfg_client', 'js_forbidden_files', '', '', '', 'php,htaccess,htpasswd,htfake', 920, 'setuse_js_forbidden_files', NULL, 'txt', NULL, NULL, '0');
INSERT INTO <!--{db_prefix}-->values VALUES ('', <!--{idclient}-->, 0, 'cfg_client', 'js_forbidden_directories', '', '', '', '', 930, 'setuse_js_forbidden_directories', 'setuse_js_title', 'txt', NULL, NULL, '1');
INSERT INTO <!--{db_prefix}-->values VALUES ('', <!--{idclient}-->, 0, 'cfg_client', 'js_delete_ignore_404', '', '', '', '1', 940, 'setuse_js_delete_ignore_404', '', 'txt', NULL, NULL, '1');
INSERT INTO <!--{db_prefix}-->values VALUES ('', <!--{idclient}-->, 0, 'cfg_client', 'js_remove_files_404', '', '', '', '1', 950, 'setuse_js_remove_files_404', '', 'txt', '', '', '1');
INSERT INTO <!--{db_prefix}-->values VALUES ('', <!--{idclient}-->, 0, 'cfg_client', 'js_remove_empty_directories', '', '', '', '0', 960, 'setuse_js_remove_empty_directories', '', 'txt', '', '', '1');
INSERT INTO <!--{db_prefix}-->values VALUES ('', <!--{idclient}-->, 0, 'cfg_client', 'js_allow_invalid_dirnames', '', '', '', '2', 970, 'setuse_js_allow_invalid_dirnames', NULL, 'txt', NULL, NULL, '1');
INSERT INTO <!--{db_prefix}-->values VALUES ('', <!--{idclient}-->, 0, 'cfg_client', 'js_allow_invalid_filenames', '', '', '', '2', 980, 'setuse_js_allow_invalid_filenames', NULL, 'txt', NULL, NULL, '1');
INSERT INTO <!--{db_prefix}-->values VALUES ('', <!--{idclient}-->, 0, 'cfg_client', 'js_allowed_files', '', '', '', 'js', 990, 'setuse_js_allowed_files', NULL, 'txt', NULL, NULL, '1');

# LOGGING
INSERT INTO <!--{db_prefix}-->values VALUES ('', <!--{idclient}-->, 0, 'cfg_client', 'logs_storage_screen', NULL, NULL, NULL, '', 1010, 'set_logs_storage_screen', 'set_logs_title', 'txt', NULL, NULL, '1');
INSERT INTO <!--{db_prefix}-->values VALUES ('', <!--{idclient}-->, 0, 'cfg_client', 'logs_storage_logfile', NULL, NULL, NULL, 'php[error,fatal];sql[error,fatal];', 1020, 'set_logs_storage_logfile', NULL, 'txt', NULL, NULL, '1');
INSERT INTO <!--{db_prefix}-->values VALUES ('', <!--{idclient}-->, 0, 'cfg_client', 'logs_storage_database', NULL, NULL, NULL, 'user[info,notice,warning,error,fatal];', 1030, 'set_logs_storage_database', NULL, 'txt', NULL, NULL, '1');

# MISC
INSERT INTO <!--{db_prefix}-->values VALUES ('', <!--{idclient}-->, 0, 'cfg_client', 'manipulate_output', '', '', '', 'echo $output;', 1110, 'setuse_manipulate_output', 'setuse_misc_title', 'txtarea', NULL, NULL, '1');

# SEKUNDAER
INSERT INTO <!--{db_prefix}-->values VALUES ('', <!--{idclient}-->, 0, 'cfg_client', 'default_layout', '', '', '', '<html>\r\n<head>\r\n<cms:lay type="head"/>\r\n<title>Sefrengo CMS</title>\r\n</head>\r\n<body>\r\n<cms:lay type="container" id="1" title="Seiten-Content"/>\r\n\r\n<cms:lay type="config"/>\r\n</body>\r\n</html>', 2050, 'setuse_default_layout', NULL, 'txtarea', NULL, NULL, 0);

