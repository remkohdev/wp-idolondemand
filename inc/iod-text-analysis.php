<?php

/**
 * IDOL OnDemand - Concept Extraction for current post
 */
function wp_idolondemand_fn_concept_extraction(){
	
	global $post;
	if($post){
	
		$post_id = $post->ID;
		$post_title = $post->post_title;
		$post_content = $post->post_content;
	
		$iod_url_sentiment_analysis = "https://api.idolondemand.com/1/api/sync/extractconcepts/v1?%s";
		$apikey = wp_idolondemand_get_setting('apikey');
	
		$params = array();
		$params['apikey'] = $apikey;
		$url1 = urlencode(get_permalink($post_id));
	
		$params['text'] = urlencode("<h1>".$post_title."</h1><br>".$post_content);
		$query = "";
		$i = count($params);
		foreach ($params as $key => $value){
			$prefix ="";
			if(!empty($query)){
				$prefix = "&";
			}
			$query .= sprintf("%s%s=%s", $prefix, $key, $value);
		}
		$iod_url = sprintf($iod_url_sentiment_analysis, $query);
			
		$response = file_get_contents($iod_url);
		$json = json_decode($response);
		
		$filtered_concepts = wp_idolondemand_filter_concepts($json);
		
		return $filtered_concepts;
	}
}

function wp_idolondemand_filter_concepts($json) {
	
	$filtered_concepts = array();
	// remove common wordpress and site specific noise in occurrences
	$filter = array('tags','categories','date','Notify','Comment Cancel','weight',
			'email','Next','post','Previous','required','title','Comment Cancel reply',
			'b blockquote cite','style','http','jpg','left','margin',
			'content','align','br img src'
	);
	$concepts = $json->concepts;
	
	if($concepts){
		$max = (sizeof($concepts));// reset index to zero
		for ($i=0; $i<$max; $i++) {
			$conceptItem = $concepts[$i];
			$concept = $conceptItem->concept;
			$occurrences = $conceptItem->occurrences;
			
			if(! in_array( $concept, $filter)) {
				$conceptArr = array(
						"concept"=>$concept, 
						"occurrences"=>$occurrences
				);
				array_push($filtered_concepts, $conceptArr);
			}
		}
	}
	
	return $filtered_concepts;
}

/**
 * IDOL OnDemand - get Sentiment for current post
 */
function wp_idolondemand_get_sentiment_for_post( )
{
	$sentiment = "";
	
	global $post;	
	if($post){

		$post_id = $post->ID;
		$post_title = $post->post_title;
		$post_content = $post->post_content;

		$iod_url_sentiment_analysis = "https://api.idolondemand.com/1/api/sync/analyzesentiment/v1?%s";
		$apikey = wp_idolondemand_get_setting('apikey');

		$params = array();
		$params['apikey'] = $apikey;
		$url1 = urlencode(get_permalink($post_id));

		$params['text'] = urlencode("<h1>".$post_title."</h1><br>".$post_content);
		$query = "";
		$i = count($params);
		foreach ($params as $key => $value){
			$prefix ="";
			if(!empty($query)){
				$prefix = "&";
			}
			$query .= sprintf("%s%s=%s", $prefix, $key, $value);
		}
		$iod_url = sprintf($iod_url_sentiment_analysis, $query);
			
		$response = file_get_contents($iod_url);
		$sentiments = json_decode($response);

		return $sentiments;
	}
}


function wp_idolondemand_parse_sentiments($sentiments){

	$msg = "";

	$aggregate = $sentiments->aggregate;
	if($aggregate){
		$sentiment = $aggregate->sentiment;
		$score = $aggregate->score;
	}

	$positive = $sentiments->positive;
	if($positive){
		$max = (sizeof($positive))-1;
		for ($i=0; $i<=$max; $i++) {

			$sentiment = 		$positive[$i]->sentiment;
			$topic = 			$positive[$i]->topic;
			$score = 			$positive[$i]->score;
			$original_text = 	$positive[$i]->original_text;
			$original_length = 	$positive[$i]->original_length;
			$normalized_text = 	$positive[$i]->normalized_text;
			$normalized_length = $positive[$i]->normalized_length;

			$msg += "<br>";
		}
	}
	$negative = $sentiments->negative;
	if($negative){

		$sentiment = 		$negative[$i]->sentiment;
		$topic = 			$negative[$i]->topic;
		$score = 			$negative[$i]->score;
		$original_text = 	$negative[$i]->original_text;
		$original_length = 	$negative[$i]->original_length;
		$normalized_text = 	$negative[$i]->normalized_text;
		$normalized_length = $negative[$i]->normalized_length;
			
		$msg += "<br>";
	}
}

?>