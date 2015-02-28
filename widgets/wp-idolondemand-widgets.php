<?php

/**
 * 
 */
function init_wp_idolondemand_widgets()
{
	// ===== SEARCH =====
	
	// Find Related Concepts
	wp_register_sidebar_widget(
		'wp_idolondemand_find_related_concepts' ,
		__('HPHaven Find Related Concepts'),
		'wp_idolondemand_find_related_concepts',
		array( // options
			'description' => 'Add find related concepts for post search results.'
		)
	);
		
	// Find Similar
	wp_register_sidebar_widget(
		'wp_idolondemand_find_similar' ,
		__('HPHaven Find Similar'),
		'wp_idolondemand_find_similar',
		array( // options
			'description' => 'Add Find Similar.'
		)
	);
	
	// Get Content
	// TBD
	
	// Get Parametric Values
	wp_register_sidebar_widget(
		'wp_idolondemand_get_parametric_values' ,
		__('HPHaven Get Parametric Values'),
		'wp_idolondemand_get_parametric_values',
		array( // options
			'description' => 'Add Get Parametric Values.'
		)
	);
	
	// Query Text Index
	wp_register_sidebar_widget(
		'wp_idolondemand_query_text_index' ,
		__('HPHaven Query Text Index'),
		'wp_idolondemand_query_text_index',
		array( // options
			'description' => 'Add Query Text Index.'
		)
	);
	
	// Retrieve Index Fields
	wp_register_sidebar_widget(
		'wp_idolondemand_retrieve_index_fields' ,
		__('HPHaven Retrieve Index Fields'),
		'wp_idolondemand_retrieve_index_fields',
		array( // options
			'description' => 'Add Retrieve Index Fields.'
		)
	);
	
	// ===== TEXT ANALYSIS =====
	
	// Concept Extraction
	wp_register_sidebar_widget(
		'wp_idolondemand_concept_extraction' ,
		__('HPHaven Concept Extraction'),
		'wp_idolondemand_concept_extraction',
		array( // options
			'description' => 'Add concept extraction.'
		)
	);
	
	// Sentiment Analysis
	wp_register_sidebar_widget(
		'wp_idolondemand_post_sentiment' ,
		__('HPHaven Post Sentiment'),
		'wp_idolondemand_post_sentiment',
		array( // options
			'description' => 'Add Sentiment Analysis.'
		)
	);
	
	// ===== TWITTER =====
	
	// IDOL OnDemand Twitter feed
	wp_register_sidebar_widget(
		'wp_idolondemand_twitterfeed' ,
		__('HPHaven Twitter'),
		'wp_idolondemand_twitterfeed',
		array( // options
			'description' => 'Add an IDOL OnDemand Twitter feed to your page.'
		)
	);
}

/**
 * IDOL OnDemand Find Related Concepts Widget
 *
 * @param unknown $args
 */
function wp_idolondemand_find_related_concepts($args) {
	extract($args);
	echo $before_widget;
	echo $before_title;?>Find Related Concepts<?php echo $after_title;
	display_wp_idolondemand_find_related_concepts();
	echo $after_widget;
}

function display_wp_idolondemand_find_related_concepts()
{?>
	<div class="idolondemand-find-related-concepts">
	<?php 
	$page_html = '<h1>Related Concepts</h1>';
	
	?>
	</div><!-- .idolondemand-find-related-concepts -->
	<?php 		
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
	//error_log($similar_html, 3, "C:/dev/xampp/apache/logs/remkohde_idolondemand_data.txt");
	
	echo $similar_html;
	?>
	</div><!-- .idolondemand-find-similar -->
	<?php 		
}

/**
 * IDOL OnDemand Get Parametric Values Widget
 *
 * @param unknown $args
 */
function wp_idolondemand_get_parametric_values($args) {
	extract($args);
	echo $before_widget;
	echo $before_title;?>get_parametric_values<?php echo $after_title;
	display_wp_idolondemand_get_parametric_values();
	echo $after_widget;
}

function display_wp_idolondemand_get_parametric_values()
{?>
	<div class="idolondemand-get-parametric-values">
	<?php 
	$page_html = '<h1>Get Parametric Values</h1>';
	
	$posts = wp_idolondemand_fn_get_parametric_values();	
	$max = sizeof($posts);
	for ($i=0; $i<$max; $i++)
	{
		$post = $posts[$i];
	}
	
	echo $page_html;
	?>
	</div><!-- .idolondemand-get-parametric-values -->
	<?php 		
}

/**
 * IDOL OnDemand Query Text Index Widget
 *
 * @param unknown $args
 */
function wp_idolondemand_query_text_index($args) {
	extract($args);
	echo $before_widget;
	echo $before_title;?>query_text_index<?php echo $after_title;
	display_wp_idolondemand_query_text_index();
	echo $after_widget;
}

function display_wp_idolondemand_query_text_index()
{?>
	<div class="idolondemand-query-text-index">
	<?php 
	$page_html = '<h1>Query Text Index</h1>';
	
	?>
	</div><!-- .idolondemand-query-text-index -->
	<?php 		
}

/**
 * IDOL OnDemand Retrieve Index Fields Widget
 *
 * @param unknown $args
 */
function wp_idolondemand_retrieve_index_fields($args) {
	extract($args);
	echo $before_widget;
	echo $before_title;?>retrieve_index_fields<?php echo $after_title;
	display_wp_idolondemand_retrieve_index_fields();
	echo $after_widget;
}

function display_wp_idolondemand_retrieve_index_fields()
{?>
	<div class="idolondemand-retrieve-index-fields">
	<?php 
	$page_html = '<h1>Retrieve Index Fields</h1>';
	
	?>
	</div><!-- .idolondemand-retrieve-index-fields -->
	<?php 		
}


/**
 * IDOL OnDemand concept_extraction Widget
 *
 * @param unknown $args
 */
function wp_idolondemand_concept_extraction($args) {
	extract($args);
	echo $before_widget;
	echo $before_title;?>Concept Extraction<?php echo $after_title;
	display_wp_idolondemand_concept_extraction();
	echo $after_widget;
}

function display_wp_idolondemand_concept_extraction()
{?>
	<div class="idolondemand-concept_extraction">
	<?php 
	$page_html = '<h1>Concepts</h1>';
	
	$concepts = wp_idolondemand_fn_concept_extraction();
	$max = sizeof($concepts);
	for ($i=0; $i<$max; $i++)
	{
		$conceptItem = $concepts[$i];
		
		$concept = $conceptItem["concept"];
		$occurrences = $conceptItem["occurrences"];
		
		$page_html .= $concept . '['.$occurrences.'] ';
	}
	
	echo $page_html;
	?>
	</div><!-- .idolondemand-concept_extraction -->
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
	$page_html = '<h1>Post Sentiment</h1>';
	
	$sentiments = wp_idolondemand_get_sentiment_for_post(); 

	$aggregate = $sentiments->aggregate;
	if($aggregate){
		$sentiment = $aggregate->sentiment;
		$score = $aggregate->score;
		$page_html .= 'sentiment: '.$sentiment.' [score: '.$score.']';
	}
	
	echo $page_html;
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