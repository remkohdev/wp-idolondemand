<?php

/**
 * IDOL OnDemand - find similar to current post
 */
function wp_idolondemand_find_similar_to_post( )
{
	global $post;

	if($post){

		$iod_url_find_similar = "https://api.idolondemand.com/1/api/sync/findsimilar/v1?%s";
		
		$post_id = $post->ID;
		$post_title = $post->post_title;
		$post_content = $post->post_content;
		
		$find_similar_text = urlencode("<h1>".$post_title."</h1><br>".$post_content);
		$find_similar_indexes = 'wp-idolondemand-remkohde'; 
		$summary = 'quick';
		$apikey = wp_idolondemand_get_setting('apikey');

		$params = array();
		$params['summary'] = $summary;
		$params['text'] = $find_similar_text;
		$params['indexes'] = $find_similar_indexes;
		
		$apiname = 'findsimilar';
		$sync_or_async = 'sync';
		
		$response = send_multipart_post_message($apiname, $sync_or_async, $params);
		 
		$similar = wp_idolondemand_parse_find_similar($response);
		
		return $similar;
	}
}


function wp_idolondemand_parse_find_similar($response){
	
	$similar_docs = array();
	
	$json = json_decode($response);
	
	$documents = $json->documents;
	if($documents){
		
		$max = sizeof($documents);
		
		for ($i=0; $i<$max; $i++) {

			$document = $documents[$i];
			
			$reference = $document->reference;
			$weight = $document->weight;
			$links = $document->links;
			$index = $document->index;
			$title = $document->title;
			$summary = $document->summary;
			
			$doc =  array(
					'reference' => $reference,
					'weight' => $weight,
					'title' => $title,
					'summary' => $summary
			);
			array_push($similar_docs, $doc);
		}
	}
	
	return $similar_docs;
}

?>