<?php

require('phpQuery.php');

// INITIALIZE IT
// phpQuery::newDocumentHTML($markup);
// phpQuery::newDocumentXML();
// phpQuery::newDocumentFileXHTML('test.html');
// phpQuery::newDocumentFilePHP('test.php');
// phpQuery::newDocument('test.xml', 'application/rss+xml');
// this one defaults to text/html in utf8

$selectorList = array(
	'filter/lang' => '.org-cell',
	'org' => '.org-cell',
	'org/count' => '.totalrows',
	'org/spectrumid' => '.symbol',
	'org/name' => '.name',
	'org/logo' => '.thumb img',
	'org/infoitem' => '.infocontainer .infoitem',
	'org/infoitem/label' => '.label',
	'org/infoitem/value' => '.value',
);

$crawler = array(
	'currentPage'=>1,
	'orgPerPage'=>255,
);

//$MARKUP = file_get_contents('https://robertsspaceindustries.com/community/orgs/listing?sort=size_desc&search=&language[]=fr&pagesize='.$crawler['orgPerPage'].'&page='.$crawler['currentPage'].'&');
//$MARKUP = file_get_contents('sample.html');

$doc = phpQuery::newDocument($MARKUP);
phpQuery::selectDocument($doc);
$containers = phpQuery::pq($selectorList['org']);

$ORGANIZATIONS = array(
	'count' => 0,
	'list' => array(),
);
$ORGANIZATIONS['count'] = ((int)trim(''.reset(phpQuery::pq($selectorList['org/count'])->getStrings())));

foreach ($containers as $container)
{
	$item = phpQuery::pq($container);
	$ORG = array(
		'spectrumId' => trim(''.reset($item[$selectorList['org/spectrumid']]->getStrings())),
		'name' => trim(''.reset($item[$selectorList['org/name']]->getStrings())),
		'logo' => trim(''.$item[$selectorList['org/logo']]->attr('src')),
		'isDefaultLogo' => 0,
	);

	foreach ($item[$selectorList['org/infoitem']] as $infoitem)
	{
		$infoitem_ = phpQuery::pq($infoitem);
		$label = '_'.substr(trim(''.$infoitem_[$selectorList['org/infoitem/label']]->html()), 0, -1);
		$ORG[$label] = substr(trim(''.$infoitem_[$selectorList['org/infoitem/value']]->html()), 0);
	}


	$matches = array();
	preg_match("/organization\/defaults\/logo/", $ORG['logo'], $matches);
	if(count($matches)>0)
		$ORG['isDefaultLogo'] = 1;


	$ORGANIZATIONS['list'][$ORG['spectrumId']] = $ORG;
}




	//die('<pre>'.print_r($ORGANIZATIONS, true).'</pre>');
	$RESULT = json_encode($ORGANIZATIONS);
?>
<textarea style="width:100%;height:100%;">
	<?php echo($RESULT);?>
</textarea>
