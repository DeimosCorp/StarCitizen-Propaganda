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
	//'member' 						=> 'ul#members-data li.member-item',
	'member' 						=> 'li.member-item',
	'member/org-attr-spectrumid' 	=> 'data-org-sid',
	'member/org-attr-name' 			=> 'data-org-name',
	'member/attr-handle' 			=> 'data-member-nickname',
	'member/attr-displayname' 		=> 'data-member-displayname',
	'member/attr-avatar' 			=> 'data-member-avatar',
	'member/attr-mid'	 			=> 'data-member-id', // global org system membership id
	
	'member/roles'	 			=> 'ul.rolelist li.role',
	//'member/roles/name'	 		=> 'ul.rolelist li.role',
	'member/ranking-rank'	 			=> '.rank',
	'member/ranking-stars'	 			=> '.ranking-stars .stars',
);


$crawler = array(
	'currentPage'=>0,
	'resultPerPage'=>255,
	'spectrumId'=>'DEIM',
);
$crawler_notFinished = true;

$MEMBERS = array(
	'list' => array(),
	'listCount' => 0,
	'spectrumId' => $crawler['spectrumId'],
	'webQueryCount' => 0,
	'crawled' => array(),
	'count' => 0,
);

while($crawler_notFinished)
{
	$MEMBERS['webQueryCount']++;
		$crawler['currentPage']++;
	
	/// REQUEST

	/*
	https://robertsspaceindustries.com/api/orgs/getOrgMembers
	?page=1
	&pagesize=255
	&search=
	&symbol=DEIM
	*/
	$url = 'https://robertsspaceindustries.com/api/orgs/getOrgMembers';
	$postdata = http_build_query(
	    array(
			'page' => $crawler['currentPage'],
			'pagesize' => $crawler['resultPerPage'],
			'search' => "",
			'symbol' => $crawler['spectrumId'],
	    )
	);

	$opts = array('http' =>
	    array(
	        'method'  => 'POST',
	        'Content-Type'  => 'application/x-www-form-urlencoded',
	        'content' => $postdata
	    )
	);

	$RESPONSE = file_get_contents($url, false, stream_context_create($opts));
	//$RESPONSE = file_get_contents('sample_members.json'); //DEBUG
	$JSON = json_decode($RESPONSE);
	$MARKUP = $JSON->data->html;

	////////////////////

	$MEMBERS['crawled'][] = array('url'=>$url,'POST'=>$postdata);
	
	if($JSON->success)
	{

		$doc = phpQuery::newDocument($MARKUP);
		phpQuery::selectDocument($doc);
		$containers = phpQuery::pq($selectorList['member']);

		if($MEMBERS['count'] === 0)
			$MEMBERS['count'] = ((int)$JSON->data->totalrows);

		foreach ($containers as $container)
		{
			$item = phpQuery::pq($container);


			
			$roles = array();
			foreach ($item[$selectorList['member/roles']] as $roleitem)
			{
				$roleitem_ = phpQuery::pq($roleitem);
				$role = trim(''.$roleitem_->html());
				if($role=='' || $role=='None')
					$role = null;
				else
					$role = ''.trim(substr(''.$role, 1));
				$roles[] = $role;
			}
			
			$ranking_stars_matches = array();
			preg_match_all("/width\:\ ?(?<percent>[0-9]{1,3})\%/i", ''.$item[$selectorList['member/ranking-stars']], $ranking_stars_matches);
			$ranking = array(
				'stars' => (int)(''.$ranking_stars_matches['percent'][0]),
				'rank' => trim(''.$item[$selectorList['member/ranking-rank']]->html()),
			);

			$MEMBRE = array(
				'id' => trim(''.$item->attr($selectorList['member/attr-mid'])),
				'handle' => trim(''.$item->attr($selectorList['member/attr-handle'])),
				'displayName' => trim(''.$item->attr($selectorList['member/attr-displayname'])),
				'avatar' => trim(''.$item->attr($selectorList['member/attr-avatar'])),
				'org'=>array(
						'spectrumId' => trim(''.$item->attr($selectorList['member/org-attr-spectrumid'])),
						'name'=> trim(''.$item->attr($selectorList['member/org-attr-name'])),
						'roles'=> $roles,
						'ranking'=> $ranking,
				),
			);

			$MEMBERS['list'][$MEMBRE['id']] = $MEMBRE;
		}

		if( count($MEMBERS['list']) >= $MEMBERS['count'] )
			$crawler_notFinished = false;
	}
	else
	{
		$crawler_notFinished = false;
		echo 'ERROR : JSON SUCCESS = false';
	}

}

$MEMBERS['listCount'] = count($MEMBERS['list']);
$RESULT = json_encode($MEMBERS);



?>
<textarea style="width:100%;height:100%;"><?php echo($RESULT);?></textarea>
