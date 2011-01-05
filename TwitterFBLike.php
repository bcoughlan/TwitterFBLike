<?php
/*
 * Parser that inserts Twitter and Facebook "Like" buttons on a page
 *
 * For more info see http://mediawiki.org/wiki/Extension:TwitterFBLike
 *
 * @package MediaWiki
 * @subpackage Extensions
 * @author Barry Coughlan
 * @copyright © 2010 Barry Coughlan
 * @licence GNU General Public Licence 2.0 or later
 */

$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'name' => 'TwitterFBLike', 
	'author' => 'Barry Coughlan', 
	'url' => 'http://mediawiki.org/wiki/Extension:TwitterFBLike',
	'description' => 'Template that inserts Twitter and Facebook "Like" buttons on a page',
);

$wgHooks['ParserFirstCallInit'][] = 'twitterFBLikeParserFunction_Setup';
$wgHooks['LanguageGetMagic'][]       = 'twitterFBLikeParserFunction_Magic';
$wgHooks['BeforePageDisplay'][] = 'twitterFBLikeParserFeedHead'; # Setup function

function twitterFBLikeParserFunction_Setup( &$parser ) {
	# Set a function hook associating the "twitterFBLike_parser" magic word with our function
	$parser->setFunctionHook( 'twitterFBLike', 'twitterFBLikeParserFunction_Render' );
	return true;
}
 
function twitterFBLikeParserFunction_Magic( &$magicWords, $langCode ) {
        //Set first parameter to 1 to make it case sensitive
		$magicWords['twitterFBLike'] = array( 0, 'TwitterFBLike' );
        return true;
}

function twitterFBLikeParserFeedHead(&$out, &$sk) {
	global $wgScriptPath;
	$out->addHeadItem('twitterFBLike.css','<link rel="stylesheet" type="text/css" href="'.$wgScriptPath.'/extensions/TwitterFeed/TwitterFeed.css"/>');
	return $out;
}

 
function twitterFBLikeParserFunction_Render( &$parser, $param1 = '', $param2 = '' ) {
		global $wgSitename;
	
		if ($param1 === "left" || $param1 === "right") {
			$float = $param1;
		} else {
			$float = "none";
		}
		
		if ($param2 === "small") {
			$linebreak = "";
			$layout = "button_count";
			$height = "20";
		} else {
			$layout = "box_count";
			$linebreak = "<br />";
			$height = "65";
		}
		
		//Get page title and URL
		$title = $parser->getTitle();
		$urltitle = $title->getPartialURL(); //e.g. "Main_Page"
		if (!$title) return "";
		$url = $title->getFullURL();
		if (!$url ) return "";
		
		$text = str_replace("+", "%20", $wgSitename . ":%20" . urlencode($title->getFullText()));

		//FB Like Button
		$output = '<div class="twitterFBLike twitterFBLike_'.$urltitle.'" style="float: '.$float.'">
				   <iframe src="http://www.facebook.com/plugins/like.php?href='.$url.'&layout='.$layout.
				  '&show_faces=true&width=450&action=recommend&colorscheme=light&height=65"
				   scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:100px; height:'.$height.'px;"
				   allowTransparency="true"></iframe>
				   <script src="http://platform.twitter.com/widgets.js" type="text/javascript"></script>'.$linebreak.'
				   <a href="http://twitter.com/share?url='.urlencode($url).'&text=' .
				   $text .'" class="twitter-share-button">Tweet</a>
				   </div>';
				  //var_dump($output);
		return $parser->insertStripItem($output, $parser->mStripState);;
}