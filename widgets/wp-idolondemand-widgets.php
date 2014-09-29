<?php

/**
 * 
 */
function init_wp_idolondemand_widgets()
{
	wp_register_sidebar_widget(
	'wp_idolondemand_post_analytics' ,
	__('IDOL OnDemand Post Sentiment'),
	'wp_idolondemand_post_analytics',
	array(                            // options
	'description' => 'Add IDOL OnDemand post sentiment.'
			)
	);
	// IDOL OnDemand Twitter feed
	wp_register_sidebar_widget(
		'wp_idolondemand_twitterfeed' ,
		__('IDOL OnDemand Twitter'),
		'wp_idolondemand_twitterfeed',
		array(                            // options
			'description' => 'Add an IDOL OnDemand Twitter feed to your page.'
		)
	);
}

/**
 * IDOL OnDemand Post Analytics Widget
 *
 * @param unknown $args
 */
function wp_idolondemand_post_analytics($args) {
	extract($args);
	echo $before_widget;
	echo $before_title;?>IDOL OnDemand Post Analytics<?php echo $after_title;
	display_wp_idolondemand_post_analytics();
	echo $after_widget;
}
function display_wp_idolondemand_post_analytics()
{?>
	<div class="idolondemand-post-analytics">
	<?php 
	$sentiments = wp_idolondemand_get_sentiment_for_post(); 

	$aggregate = $sentiments->aggregate;
	if($aggregate){
		$sentiment = $aggregate->sentiment;
		$score = $aggregate->score;
		echo 'sentiment: '.$sentiment.' , score: '.$score;
	}
	
	?>
	</div><!-- .idolondemand-post-analytics -->
	<?php 		
}

/**
 * IDOL OnDemand Twitter feed
 * 
 * @param unknown $args
 */
function wp_idolondemand_twitterfeed($args) {
	extract($args);	
	echo $before_widget;
	echo $before_title;?>twitter.com/idolondemand<?php echo $after_title;
	display_wp_idolondemand_twitter_feed();
	echo $after_widget;
}
function display_wp_idolondemand_twitter_feed()
{?>
	<div class="idolondemand-twitter-feed">
		<a class="twitter-timeline" href="https://twitter.com/IDOLOnDemand" data-widget-id="491415597947699201">Tweets by @IDOLOnDemand</a>
		<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
	</div><!-- .idolondemand-twitter-feed -->
	<?php 		
}



?>