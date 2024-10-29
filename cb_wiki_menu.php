<?php
/*
Plugin Name: Wiki Menus
Plugin URI: http://www.couldbestudios.com
Description: Generates a wiki-style menu for all of the headings in a page
Version: 2.0
Author: Matt Beck
Author URI: http://www.couldbestudios.com
*/


function cb_wiki_check($content){
	if(is_page())
		{
		add_filter('the_content','cb_wiki_menu');
		}
	return $content;
	}

function cb_wiki_menu($content)
	{
	if(!stristr($content, "[wikimenu]") === FALSE)
		{
		preg_match_all("/(<(h[\w]+)[^>]*>)(.*?)(<\/\\2>)/", $content, $matches, PREG_SET_ORDER);
		$headers=array("h1","h2","h3","h4","h5");
		$c=0;
		foreach($matches as $match)
			{
			if(in_array(trim($match[2]), $headers))
				{
				$theIDs[$c]['id']=str_replace(" ", "-", $match[3]);
				$theIDs[$c]['tag']=$match[2];
				$theIDs[$c]['name']=$match[3];
				$temp = str_replace($match[1], $match[1]."<span id=\"".str_replace(" ", "-", $match[3])."\">", $match[0]);
				$temp = str_replace($match[4], "</span>".$match[4], $temp);
				$content = str_replace($match[0], $temp, $content);
				}
			$c++;
			}

		if(isset($theIDs[0]['id']))
			{
			$wikiblock = '
				<div id="wikinav">
					<strong>Contents</strong>
					<a href="#wikinav" id="wikinav-hide" onclick="return wikiToggle(\'hide\')">[hide]</a>
					<a href="#wikinav" id="wikinav-show" onclick="return wikiToggle(\'show\')">[show]</a>
					<ul id="wikinav-ul">';
			for($i=0; $i<sizeof($theIDs); $i++)
				{
				$wikiblock .= '<li class="'.$theIDs[$i]['tag'].'"><a href="#'.$theIDs[$i]['id'].'">'.$theIDs[$i]['name'].'</a></li>';
				}
			$wikiblock .= '</ul></div>';
			$content = $wikiblock.$content.'
				<div id="wikiback"><a href="#wikinav">back to top</a></div>
				<script type="text/javascript">
				function wikiToggle(clicker){
					wikiNavUL = document.getElementById("wikinav-ul");
					wikiNavHide = document.getElementById("wikinav-hide");
					wikiNavShow = document.getElementById("wikinav-show");
					if(clicker==="hide"){
						wikiNavUL.style.display = "none";
						wikiNavHide.style.display = "none";
						wikiNavShow.style.display = "inline";
						}
						else{
							wikiNavUL.style.display = "block";
							wikiNavHide.style.display = "inline";
							wikiNavShow.style.display = "none";
							}
					return false;
					}
				</script>
				';
			}
		}
	return $content;
	}

function cb_wiki_head()
	{
	if(!is_admin()){
		wp_register_style('cb_wiki_style', plugins_url('cb_wiki_style.css', __FILE__));
		wp_enqueue_style('cb_wiki_style');
		}
	}

function cb_wiki_code()
	{
	return null;
	}

add_action('loop_start','cb_wiki_check');
add_action('wp_head', 'cb_wiki_head');
add_shortcode('wikimenu', 'cb_wiki_code');
?>
