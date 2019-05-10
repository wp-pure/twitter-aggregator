<?php


// include twitter api php class
require_once( 'vendor/j7mbo/twitter-api-php/TwitterAPIExchange.php' );



// settings for twitter api interaction
$settings = array(
    'consumer_key' => "UFBxe5cHwmGbDxHf3H9jDAGar",
    'consumer_secret' => "HSozmjgxMvNa74D8Sz5RL6Nav56uK0LKLvIvUu6FAgjNH7uClt",
    'oauth_access_token' => "29196496-q1Wllv60i94w1Wlpt6Ztzimfu5IvQOxOcxt8uwEN1",
    'oauth_access_token_secret' => "SziLDM5qOVAqGrPMvqTKEEWQ7Z4qgmA66aLJh1uOeOfVT",
    'usernames' => "jamespederson",
    'limit' => "10",
    'timeout' => 10
);



function make_clickable( $string ) {
	$url = '/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/';   
	$string = preg_replace($url, '<a href="$0" target="_blank" title="$0">$0</a>', $string);
	return $string;
}


// get twitter timelines and return them
function twitter_aggregator_get_timeline( $instance_settings ) {

	// include global settings
	global $settings;

	// merge instance settings with global settings, overriding global if passed here
	$all_settings = array_merge( $settings, $instance_settings );

	if ( function_exists( 'wp_upload_dir' ) ) {
		// get upload directory info
		$upload_info = wp_upload_dir();

		// set up some variables to store cache urls
		$cache_dir = $upload_info['basedir'] . '/cache';
	} else {
		$cache_dir = 'cache';

		if ( !file_exists( $cache_dir ) ) {
			mkdir( $cache_dir );
		}
	}

	// cache file url for this set of usernames
	$cache_file = $cache_dir . '/twitter-' . md5( $all_settings['usernames'] ) . '.txt';

	// if cache folder doesn't exist, make it.
	if ( !file_exists( $cache_dir ) ) {
		if ( !mkdir( $cache_dir, '775', 1 ) ) {
			return array(
				'error' => 1,
				'error_message' => "Couldn't create cache directories for twitter."
			);
		}
	}

	// check if we have a cached version
	if ( file_exists( $cache_file ) ) {
		if ( filemtime( $cache_file ) < ( time() - ( $all_settings['timeout'] * 60 ) ) ) {
			$cached = false;
		} else {
			$cached = true;
		}
	} else {
		$cached = false;
	}
	

	// if we don't have the timeline cached, grab it again.
	if ( $cached === false ) {

		// split apart usernames
		$usernames = explode( ",", $all_settings['usernames'] );

		// empty array to place results into
		$response_final = array();

		// loop through usernames
		if ( !empty( $usernames ) ) {
			foreach ( $usernames as $username ) {

				// pull some statuses
				$url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';

				// put together an array of query string arguments
				$query_args = array(
					'screen_name' => trim( $username ),
					'skip_status' => 1,
					'exclude_replies' => 1,
					'count' => $all_settings['limit']
				);

				// build the query string
				$query = '?' . http_build_query( $query_args );

				// use the get method
				$method = 'GET';

				// open up a twitter API object for us
				$twitter = new TwitterAPIExchange( $all_settings );

				// execute response
				$response = $twitter->setGetfield( $query )->buildOauth( $url, $method )->performRequest();

				// convert the response from json to an array
				$response_array = json_decode( $response );

				// loop through response, and set up the array by date
				if ( isset( $response_array->errors ) ) {
					return array(
						'error' => 1,
						'error_message' => $response_array->errors[0]->message
					);
				} else {
					foreach ( $response_array as $result ) {
						$date = strtotime( $result->created_at );
						$response_final[ $date ] = $result;
					}
				}
			}

			// sort response array in reverse order
			krsort( $response_final );

			// write the cache file
			if ( !file_put_contents( $cache_file, serialize( $response_final ) ) ) {
				return array(
					'error' => 1,
					'error_message' => "Could not write cache file."
				);
			}

		} else {
			return array(
				'error' => 1,
				'error_message' => "No usernames specified."
			);
		}

	} else {

		// grab cache file if we have it
		$response_final = unserialize( file_get_contents( $cache_file ) );

	}

	// return the tweets
	return $response_final;
}



// output widget with settings parameter
function twitter_aggregator_widget( $instance_settings ) {

	// get the timelines
	$tweets = twitter_aggregator_get_timeline( $instance_settings );

	// output timeline
	if ( isset( $tweets['error'] ) ) {
		print $tweets['error_message'];
	} else {
		$tweet_count = 0;
		foreach ( $tweets as $tweet ) {
			if ( $tweet_count <= $instance_settings['limit'] ) {
			?>
		<div class="twitter-aggregator-tweet">
			<div class="twitter-aggregator-tweet-profile-pic"><img src="<?php print str_replace( 'http://', 'https://', $tweet->user->profile_image_url ); ?>" alt="Tweet by @<?php print $tweet->user->screen_name ?>"></div>
			<h3 class="twitter-aggregator-tweet-profile-name"><a href="https://twitter.com/<?php print $tweet->user->screen_name ?>"><?php print $tweet->user->screen_name ?></a></h3>
			<div class="twitter-aggregator-tweet-time"><?php print ago( $tweet->created_at ); ?> ago</div>
			<div class="twitter-aggregator-tweet-text"><?php print make_clickable( $tweet->text ); ?></div>
		</div>
			<?php
			}
			$tweet_count++;
		}		
	}

}



// time ago function
if ( !function_exists( 'ago' ) ) {
	function ago( $tm, $rcs = 0 ) {
		if ( is_string( $tm ) ) $tm = strtotime( $tm );

		$cur_tm = time();
		$dif = $cur_tm - $tm;
		$pds = array( 'second', 'minute', 'hour', 'day', 'week', 'month', 'year', 'decade' );
		$lngh = array( 1, 60, 3600, 86400, 604800, 2630880, 31570560, 315705600 );
		for($v = sizeof($lngh)-1; ($v >= 0)&&(($no = $dif/$lngh[$v])<=1); $v--); if($v < 0) $v = 0; $_tm = $cur_tm-($dif%$lngh[$v]);

		$no = floor( $no ); if( $no <> 1 ) $pds[$v] .='s';
		$x=sprintf("%d %s ",$no,$pds[$v]);
		if ( ( $rcs == 1 ) && ( $v >= 1 ) && ( ( $cur_tm - $_tm ) > 0 ) ) $x .= time_ago($_tm);
		return $x;
	}
}



?>