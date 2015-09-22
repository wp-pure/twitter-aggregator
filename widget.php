<?php

/*
Plugin Name: Twitter Aggregator Widget
Plugin URI: 
Description: A widget to pull multiple twitter feeds into a local cache and provide a widget that displays them.
Author: James Pederson
Version: 0.0.1
*/

include( "twitter-aggregator.php" );


class twitter_aggregator_widget extends WP_Widget {
	function twitter_aggregator_widget() {
		// Instantiate parent object
		parent::__construct(
			'twitter_aggregator_widget',
			'Twitter Aggregator Widget',
			array( 'description' => 'Widget to display tweets from multiple twitter accounts')
		);
	}

	function widget( $args, $instance ) {

		// Widget output
		extract( $args );

		echo $before_widget;
		echo $before_title;
		echo $instance['title'];
		echo $after_title;

		twitter_aggregator_widget( $instance );
		
		echo $after_widget;
		
	}

	function update( $new_instance, $old_instance ) {

		// Save widget options
		$new_instance = (array) $new_instance;

		$instance = array( 'styles' => 0 );
		foreach ( $instance as $field => $val ) {
			if ( isset( $new_instance[$field] ) ) $instance[$field] = 1; // loop through booleans
		}
		$instance['consumer_key'] = htmlspecialchars($new_instance['consumer_key']);
		$instance['consumer_secret'] = htmlspecialchars($new_instance['consumer_secret']);
		$instance['oauth_access_token'] = htmlspecialchars($new_instance['oauth_access_token']);
		$instance['oauth_access_token_secret'] = htmlspecialchars($new_instance['oauth_access_token_secret']);
		$instance['title'] = htmlspecialchars($new_instance['title']);
		$instance['usernames'] = htmlspecialchars($new_instance['usernames']);
		$instance['limit'] = intval($new_instance['limit']);

		// Return
		return $instance;

	}

	function form( $instance ) {
		// Output admin widget options form
		?>
	<p>
	<label for="<?php echo $this->get_field_id('consumer_key'); ?>">Consumer Key: </label><br />
	<input type="text" class="widefat" id="<?php echo $this->get_field_id('consumer_key'); ?>" name="<?php echo $this->get_field_name('consumer_key'); ?>" value="<?php echo $instance['consumer_key']; ?>" />
	</p>
	<p>
	<label for="<?php echo $this->get_field_id('consumer_secret'); ?>">Consumer Secret: </label><br />
	<input type="text" class="widefat" id="<?php echo $this->get_field_id('consumer_secret'); ?>" name="<?php echo $this->get_field_name('consumer_secret'); ?>" value="<?php echo $instance['consumer_secret']; ?>" />
	</p>
	<p>
	<label for="<?php echo $this->get_field_id('oauth_access_token'); ?>">Access Token: </label><br />
	<input type="text" class="widefat" id="<?php echo $this->get_field_id('oauth_access_token'); ?>" name="<?php echo $this->get_field_name('oauth_access_token'); ?>" value="<?php echo $instance['oauth_access_token']; ?>" />
	</p>
	<p>
	<label for="<?php echo $this->get_field_id('oauth_access_token_secret'); ?>">Access Token Secret: </label><br />
	<input type="text" class="widefat" id="<?php echo $this->get_field_id('oauth_access_token_secret'); ?>" name="<?php echo $this->get_field_name('oauth_access_token_secret'); ?>" value="<?php echo $instance['oauth_access_token_secret']; ?>" />
	</p>
	<p>
	<label for="<?php echo $this->get_field_id('title'); ?>">Widget Title: </label><br />
	<input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $instance['title']; ?>" />
	</p>
	<p>	
	<label for="<?php echo $this->get_field_id('users'); ?>">Usernames: </label><br />
	<input type="text" class="widefat" id="<?php echo $this->get_field_id('usernames'); ?>" name="<?php echo $this->get_field_name('usernames'); ?>" value="<?php echo $instance['usernames']; ?>" /><br />
	<small><em>enter accounts separated with a comma</em></small>
	</p>
	<p>
	<label for="<?php echo $this->get_field_id('user_limit'); ?>">Number of Tweets: </label>
	<input type="text" class="short" id="<?php echo $this->get_field_id('limit'); ?>" name="<?php echo $this->get_field_name('limit'); ?>" value="<?php echo $instance['limit']; ?>" /><br />
        <small><em>Limits number of total results.</em></small>
	</p>
	<p>
	<label for="<?php echo $this->get_field_id('styles'); ?>">Apply Styles?</label>
	<input type="checkbox" name="<?php echo $this->get_field_name('styles'); ?>" id="<?php echo $this->get_field_id('styles'); ?>" <?php if ($instance['styles']) echo 'checked="checked"'; ?> />
	</p>
<?php
	}
}



function twitter_aggregator_init() {
	return register_widget( "twitter_aggregator_widget" );
}
add_action("widgets_init", "twitter_aggregator_init");



?>