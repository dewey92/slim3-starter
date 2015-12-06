<?php

function crawl_page($url) {
	if (! isset($url)) {
		return;
	}

	// $url = 'http://dwaspada.tumblr.com/post/126930532699/welcome-to-the-web-dev-world';
	$dom = new DOMDocument();
	@$dom->loadHTMLFile($url);

	// Get meta
	$allMetas = array();
	$metas = $dom->getElementsByTagName('meta');
	for ($i = 0; $i < $metas->length; $i++) {
		$meta = $metas->item($i);

		if (preg_match('/^og:/', $meta->getAttribute('property')))
			$allMetas[$meta->getAttribute('property')] = $meta->getAttribute('content');
	}

	// Get title
	$title = $dom->getElementsByTagName('title')->item(0);
}