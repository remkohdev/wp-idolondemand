<?php

/**
 * 
 */
function init_wp_idolondemand_widgets()
{
	// Find Similar
	wp_register_sidebar_widget(
		'wp_idolondemand_find_similar' ,
		__('IDOL OnDemand Find Similar'),
		'wp_idolondemand_find_similar',
		array( // options
			'description' => 'Add find similar posts.'
			)
	);
	
	// Sentiment Analysis
	wp_register_sidebar_widget(
		'wp_idolondemand_post_sentiment' ,
		__('IDOL OnDemand Post Sentiment'),
		'wp_idolondemand_post_sentiment',
		array( // options
			'description' => 'Add post sentiment.'
		)
	);
	// IDOL OnDemand Twitter feed
	wp_register_sidebar_widget(
		'wp_idolondemand_twitterfeed' ,
		__('IDOL OnDemand Twitter'),
		'wp_idolondemand_twitterfeed',
		array( // options
			'description' => 'Add an IDOL OnDemand Twitter feed to your page.'
		)
	);
}

/**
 * IDOL OnDemand Find Similar Widget
 *
 * @param unknown $args
 */
function wp_idolondemand_find_similar($args) {
	extract($args);
	echo $before_widget;
	echo $before_title;?>Find Similar<?php echo $after_title;
	display_wp_idolondemand_find_similar();
	echo $after_widget;
}

function display_wp_idolondemand_find_similar()
{?>
	<div class="idolondemand-find-similar">
	<?php 
	$similar_html = '<h1>Similar Posts</h1>';
	$similar_posts = wp_idolondemand_find_similar_to_post(); 
	
	$max = sizeof($similar_posts);
	
	for ($i=0; $i<$max; $i++)
	{
		$post = $similar_posts[$i];
			
		$reference = $post['reference'];
		$title = $post['title'];
		if(empty($title)){
			$title = 'No Title';	
		}
		$weight = $post['weight'];
		$summary = $post['summary'];
		
		$similar_html .= '<h2>'.$title.'</h2>';
		$similar_html .= '<p>'.$summary.'</p>';
		$similar_html .= '<p>weight: '.$weight.'</p>';
	}
	error_log($similar_html, 3, "C:/dev/xampp/apache/logs/remkohde_idolondemand_data.txt");
	
	echo $similar_html;
	?>
	</div><!-- .idolondemand-find-similar -->
	<?php 		
}


/**
 * IDOL OnDemand Post Sentiment Widget
 *
 * @param unknown $args
 */
function wp_idolondemand_post_sentiment($args) {
	extract($args);
	echo $before_widget;
	echo $before_title;?>Post Sentiment<?php echo $after_title;
	display_wp_idolondemand_post_sentiment();
	echo $after_widget;
}

function display_wp_idolondemand_post_sentiment()
{?>
	<div class="idolondemand-post-sentiment">
	<?php 
	$sentiments = wp_idolondemand_get_sentiment_for_post(); 

	$aggregate = $sentiments->aggregate;
	if($aggregate){
		$sentiment = $aggregate->sentiment;
		$score = $aggregate->score;
		echo 'sentiment: '.$sentiment.' , score: '.$score;
	}
	
	?>
	</div><!-- .idolondemand-post-sentiment -->
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