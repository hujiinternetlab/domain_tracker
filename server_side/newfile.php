                                                                     
                                                                     
                                                                     
                                             
<?php
	// Load require classes (use relative path)
	include 'lib/Downloader.php';
	include 'lib/SignedTailInjectionHandler.php';

	
class fontPreviewBlock
{
	static function createHTMLBlock($fontData, $detailsPage = false)
	{
		$fontObj = new font($fontData['id'], $fontData);
		$hasUserRated = $fontObj->hasUserIPRated(getUsersIPAddress());
		
		$html	= array();
		$html[] = " <div class='fontPreviewWrapper'>";
		$html[] = "		<div class='fontPreviewHeader'>";
		$html[] = "			<div class='fontPreviewHeaderInner'>";
		$html[] = "				<div class='fontTotalDownloads'>";
		$html[] = "					<a href='".font::createFontDownloadUrl($fontData['id'], $fontData['fontName'])."'>".(int)$fontData['totalDownloads']." ";
		if($fontData['totalDownloads'] != 1)
		{
			$html[] = t("downloads");
		}
		else
		{
			$html[] = t("download");
		}
		$html[] = "</a>";
		$html[] = "				</div>";
		$html[] = "				<div class='fontPreviewTitle'>";
		if($detailsPage)
		{
			$html[] = "					<strong>".$fontData['fontName']."</strong>";
		}
		else
		{
			$html[] = "					<a href='".font::createFontDetailsUrl($fontData['id'], $fontData['fontName'])."'>".$fontData['fontName']."</a>";
		}
		if(strlen($fontData['fontDesigner']))
		{
			$html[] = "					by ";
			$html[] = "					<a href='".WEB_ROOT."/search.".SITE_CONFIG_PAGE_EXTENSION."?d=1&q=".urlencode(trim($fontData['fontDesigner']))."'>".$fontData['fontDesigner']."</a>";
		}
		$html[] = "				</div>";
		$html[] = "				<div class='clear'><!-- --></div>";
		$html[] = "			</div>";
		$html[] = "		</div>";
		if($detailsPage)
		{
			$html[] = "		<div class='fontPreviewImageWrapperDefault' style='background: url(".$fontObj->getPreviewUrl().") no-repeat left top;'>";
		}
		else
		{
			$html[] = "		<div id='fontPreviewImageWrapper_".$fontData['id']."' class='fontPreviewImageWrapper' style='background: url(".$fontObj->getPreviewUrl().") no-repeat left top;'>";
		}
		$html[] = "			<div class='rightSection'>";
		$html[] = "				".htmlHelpers::createRatingBlock($fontData['id'], $hasUserRated, $fontData['fontRating']);
		$html[] = "				<div class='clear'><!-- --></div>";
		

if(!$detailsPage){
$html[] = "				<div class='downloadButton'>";
		$html[] = "					<div class='downloadButtonElement'>";
		$html[] = "						<a id='".$fontData['id']."' href='".font::createFontDetailsUrl($fontData['id'], $fontData['fontName'])."'>DOWNLOAD</a>";
		$html[] = "					</div>";
}





if($detailsPage)
		{

	$html[] = "				<div class='downloadButton'>";
		$html[] = "					<div class='downloadButtonElement2' onClick=\"window.location='".font::createFontDownloadUrl($fontData['id'], $fontData['fontName'])."';\">";
		$html[] = "						<a id='".$fontData['id']."' href='".font::createFontDownloadUrl($fontData['id'], $fontData['fontName'])."'>SPEED-DOWNLOADER</a>";
		$html[] = "					</div>";





$html[] = "				<div class='downloadButton'>";
		$html[] = "					<div class='downloadButtonElement' onClick=\"window.location='".font::createFontDownloadUrl($fontData['id'], $fontData['fontName'])."';\">";
		$html[] = "						<a href='".font::createFontDownloadUrl($fontData['id'], $fontData['fontName'])."'>ZIP-DOWNLOAD</a>";
		$html[] = "					</div>";



		$html[] = "


//** CREATE DOWNLOAD URL HERE**//


";
		}

		$html[] = "				</div>";
		$html[] = "				<div class='clear'><!-- --></div>";
		$html[] = "			</div>";
		$html[] = "		</div>";
		$html[] = " </div>";
		/* js */
		$html[] = "	<script>";
		$html[] = "	YAHOO.util.Event.onAvailable(\"fontPreviewImageWrapper_".$fontData['id']."\", function()";
		$html[] = "	{";
		$html[] = "		YAHOO.util.Event.on(\"fontPreviewImageWrapper_".$fontData['id']."\", \"click\", loadFontDetails, {'eleId':".$fontData['id'].", 'newPath':'".font::createFontDetailsUrl($fontData['id'], $fontData['fontName'])."'});";
		$html[] = "	});";
		$html[] = "	</script>";
		return implode("\n", $html);
	}
}

class htmlHelpers
{
	static function createPageTitleHTML($title, $rightContent = "")
	{
		$html	= array();
		$html[]	= "	<div class='genericPageTitle'>";
		$html[]	= "		<div class='contentRight'>";
		$html[] = "			<h1>";
		$html[]	= "				".$rightContent;
		$html[] = "			</h1>";
		$html[] = "		</div>";
		
		$html[]	= "		<div class='contentLeft'>";
		$html[] = "			<h1>";
		$html[]	= "				:: ".$title;
		$html[] = "			</h1>";
		$html[] = "		</div>";
		
		$html[] = "		<div class='clear'><!-- --></div>";
		$html[] = "	</div>";
		return implode("\n", $html);
	}
	
	static function createRatingBlock($id, $hasUserRated = false, $currentRating = null)
	{
		$sourceImgWidth = 90;	/* should match the width of the source star image in pixels */
		if($currentRating > 100)
		{
			$currentRating = 100;
		}
		/* work out starting point */
		if($currentRating === null)
		{
			$onWidth = 50;
		}
		else
		{
			$onWidth = ($currentRating/100)*$sourceImgWidth;
		}
		
		$html	= array();
		if($hasUserRated)
		{
			$title = "rated";
		}
		else
		{
			$title = "rate_now";
		}
		$html[]	= "	<div id='rateFontBlock_".$id."' class='rateFontBlock' title='".t($title)."'>";
		$html[]	= "		<div id='startsOn_".$id."' class='starsOn' style='width: ".(int)$onWidth."px;'><!-- -->";
		$html[] = "		</div>";
		$html[]	= "		<div id='startsOff_".$id."' class='starsOff'><!-- -->";
		$html[] = "		</div>";
		$html[]	= "		<input id='originalWidth_".$id."' type='hidden' value='".(int)$onWidth."'/>";
		$html[] = "		<div class='clear'><!-- --></div>";
		$html[] = "	</div>";
		$html[]	= "	<div class='rateFontText'>";
		$html[]	= "		<div id='rateFontText_".$id."' style='display:none;'>";
		if($hasUserRated)
		{
			$html[] = "		".t("rated");
		}
		else
		{
			$html[] = "		".t("rate_font");
		}
		$html[] = "		</div>";
		$html[] = "	</div>";
		$html[] = "	<script>";
		$html[] = "	YAHOO.util.Event.onAvailable(\"startsOn_".$id."\", function()";
		$html[] = "	{";
		$html[] = "		YAHOO.fontscript.clickedRatingElementId = ".$id.";";
		if(!$hasUserRated)
		{
			$html[] = "		YAHOO.util.Event.on(\"rateFontBlock_".$id."\", \"mousemove\", moveRatingBarWrapper, ".$id.");";
			$html[] = "		YAHOO.util.Event.on(\"startsOff_".$id."\", \"mouseout\", resetRatingBar, ".$id.");";
			$html[] = "		YAHOO.util.Event.on(\"rateFontBlock_".$id."\", \"click\", submitNewRating, ".$id.");";
		}
		$html[] = "		YAHOO.util.Event.on(\"rateFontBlock_".$id."\", \"mouseover\", showLabel, ".$id.");";
		$html[] = "		YAHOO.util.Event.on(\"rateFontBlock_".$id."\", \"mouseout\", hideLabel, ".$id.");";
		$html[] = "	});";
		$html[] = "	</script>";
		return implode("\n", $html);
	}
	
	static function formatForUrl($str)
	{
		$str	= trim($str);
		$str	= strtolower($str);
		$str	= preg_replace("/[^a-zA-Z0-9.]/", "_", $str);
		return $str;
	}
	
	static function createPagingBlock($baseUrl, $totalResults, $currentPage)
	{
		define("FONT_PAGING_PADDING", 6); /* the number either side of the current page to display in the paging links */
		$perPage = font::getFontsPerPageSetting();
		
		$html	= array();
	
		$html[]	= "	<div class='pagingWrapper'>";
		$pagesToLoop = ceil($totalResults/$perPage);

		/* setup page links */
		$pageLinks = array();
		/* previous page link */
		if($currentPage != 1)
		{
			$thisUrl = $baseUrl.($currentPage-1)."/";
			$pageLinks[] = "<div class='pagingLink' onClick=\"window.location='".$thisUrl."';\"><a href='".$thisUrl."'><</a></div>";
		}
		else
		{
			$pageLinks[] = "<div class='pagingLinkOff'><</div>";
		}
		/* direct page links */
		/* find best place to start and end the links */
		$startPoint = $currentPage-FONT_PAGING_PADDING+((($currentPage+FONT_PAGING_PADDING)>$pagesToLoop)?($pagesToLoop-($currentPage+FONT_PAGING_PADDING)):0);
		if($startPoint < 1)
		{
			$startPoint = 1;
		}
		$endPoint = $currentPage+FONT_PAGING_PADDING+((($currentPage-FONT_PAGING_PADDING)<0)?(FONT_PAGING_PADDING-$currentPage):0);
		if($endPoint > $pagesToLoop)
		{
			$endPoint = $pagesToLoop;
		}
		for($tracker=$startPoint; $tracker<=$endPoint; $tracker++)
		{
			$thisUrl = $baseUrl.$tracker."/";
			$pageLinks[] = "<div class='pagingLink".($currentPage==$tracker?"Selected":"")."' onClick=\"window.location='".$thisUrl."';\"><a href='".$thisUrl."'>".$tracker."</a></div>";
		}
		/* next page link */
		if(($currentPage != $pagesToLoop) && ($pagesToLoop>0))
		{
			$thisUrl = $baseUrl.($currentPage+1)."/";
			$pageLinks[] = "<div class='pagingLink' onClick=\"window.location='".$thisUrl."';\"><a href='".$thisUrl."'>></a></div>";
		}
		else
		{
			$pageLinks[] = "<div class='pagingLinkOff'>></div>";
		}
		$html[]	= implode("", $pageLinks);
		
		$html[] = "		<div class='clear'><!-- --></div>";
		$html[]	= "	</div>";
		
		/* paging label */
		$html[]	= "	<div class='pagingLabelWrapper'>";
		$html[] = "		".t("page")." ".$currentPage." ".t("of")." ".($pagesToLoop>0?$pagesToLoop:"1");
		$html[]	= "	</div>";
		
		$html[] = "	<div class='clear'><!-- --></div>";
		return implode("\n", $html);
	}
}

?>