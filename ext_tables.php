<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');
	// get extension configuration 
$confArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['tt_news']);

$TCA['tt_news'] = Array (
	'ctrl' => Array (
		'title' => 'LLL:EXT:tt_news/locallang_tca.php:tt_news',
		'label' => $confArr['label'],
		'label_alt' => $confArr['label_alt'].($confArr['label_alt2']?','.$confArr['label_alt2']:''),
		'label_alt_force' => $confArr['label_alt_force'],
		'default_sortby' => 'ORDER BY datetime DESC',
		'prependAtCopy' => 'LLL:EXT:lang/locallang_general.php:LGL.prependAtCopy',
		'versioning' => TRUE,
		'versioning_followPages' => TRUE,
		'dividers2tabs' => $confArr['noTabDividers']?FALSE:TRUE,
		'useColumnsForDefaultValues' => 'type',
		'transOrigPointerField' => 'l18n_parent',
		'transOrigDiffSourceField' => 'l18n_diffsource',
		'languageField' => 'sys_language_uid',
		'crdate' => 'crdate',
		'tstamp' => 'tstamp',
		'delete' => 'deleted',
		'type' => 'type',
		'cruser_id' => 'cruser_id',
		'editlock' => 'editlock',
		'enablecolumns' => Array (
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
			'fe_group' => 'fe_group',
		),
		'typeicon_column' => 'type',
		'typeicons' => Array (
			'1' => t3lib_extMgm::extRelPath($_EXTKEY).'res/tt_news_article.gif',
			'2' => t3lib_extMgm::extRelPath($_EXTKEY).'res/tt_news_exturl.gif',
		),
		'thumbnail' => 'image',
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'ext_icon.gif',
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php'
	)
);

$TCA['tt_news_cat'] = Array (
	'ctrl' => Array (
		'title' => 'LLL:EXT:tt_news/locallang_tca.php:tt_news_cat',
		'label' => 'title',
		'tstamp' => 'tstamp',
		'delete' => 'deleted',
		'sortby' => 'sorting',
		'treeParentField' => 'parent_category',
		'enablecolumns' => Array (
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
			'fe_group' => 'fe_group',
		),
		'prependAtCopy' => 'LLL:EXT:lang/locallang_general.php:LGL.prependAtCopy',
		'crdate' => 'crdate',
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'res/tt_news_cat.gif',
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php'
	)
);

	// load tt_content to $TCA array
t3lib_div::loadTCA('tt_content');
	// remove some fields from the tt_content content element
$TCA['tt_content']['types']['list']['subtypes_excludelist'][9]='layout,select_key,pages,recursive';
	// add FlexForm field to tt_content
$TCA['tt_content']['types']['list']['subtypes_addlist'][9]='pi_flexform';
	// add tt_news to the "insert plugin" content element (list_type = 9)
t3lib_extMgm::addPlugin(Array('LLL:EXT:tt_news/locallang_tca.php:tt_news', '9'));

	// initialize static extension templates
t3lib_extMgm::addStaticFile($_EXTKEY,'static/ts_new/','CSS-based tmpl');	
t3lib_extMgm::addStaticFile($_EXTKEY,'static/css/','default CSS-styles');	
t3lib_extMgm::addStaticFile($_EXTKEY,'static/ts_old/','table-based tmpl');	
t3lib_extMgm::addStaticFile($_EXTKEY,'static/rss_feed/','News-feed (RSS,RDF,Atom03)');

	// allow news and news-category records on normal pages
t3lib_extMgm::allowTableOnStandardPages('tt_news_cat');
t3lib_extMgm::allowTableOnStandardPages('tt_news');
	// add the tt_news record to the insert records content element
t3lib_extMgm::addToInsertRecords('tt_news');


// switch the XML files for the FlexForm depending on if "use StoragePid"(general record Storage Page) is set or not.
if ($confArr['useStoragePid']) {
	t3lib_extMgm::addPiFlexFormValue(9, 'FILE:EXT:tt_news/flexform_ds.xml');
} else {
	t3lib_extMgm::addPiFlexFormValue(9, 'FILE:EXT:tt_news/flexform_ds_no_sPID.xml');
}

	// sets the transformation mode for the RTE to "ts_css" if the extension css_styled_content is installed (default is: "ts")
if (t3lib_extMgm::isLoaded('css_styled_content')) {
t3lib_extMgm::addPageTSConfig('
# RTE mode in table "tt_news"
RTE.config.tt_news.bodytext.proc.overruleMode=ts_css');
}

	// initalize "context sensitive help" (csh)
t3lib_extMgm::addLLrefForTCAdescr('tt_news','EXT:tt_news/locallang_csh_ttnews.php');
t3lib_extMgm::addLLrefForTCAdescr('tt_news_cat','EXT:tt_news/locallang_csh_ttnewscat.php');
t3lib_extMgm::addLLrefForTCAdescr('xEXT_tt_news','EXT:tt_news/locallang_csh_manual.xml');

	
if (TYPO3_MODE=='BE')	{
		// Adds a tt_news wizard icon to the content element wizard.
	$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_ttnews_wizicon'] = t3lib_extMgm::extPath($_EXTKEY).'pi/class.tx_ttnews_wizicon.php';
		// add folder icon
	$ICON_TYPES['news'] = array('icon' => t3lib_extMgm::extRelPath($_EXTKEY).'ext_icon_ttnews_folder.gif');
	
		// adds processing for extra "codes" that have been added to the "what to display" selector in the content element by other extensions
	include_once(t3lib_extMgm::extPath($_EXTKEY).'class.tx_ttnews_itemsProcFunc.php');
		// class for displaying the category tree in BE forms
	include_once(t3lib_extMgm::extPath($_EXTKEY).'class.tx_ttnews_treeview.php');
		// class that uses hooks in class.t3lib_tcemain.php (processDatamapClass and processCmdmapClass) to process allowed "commands" for a certain BE usergroup
	include_once(t3lib_extMgm::extPath($_EXTKEY).'class.tx_ttnews_tcemain.php');
}

?>