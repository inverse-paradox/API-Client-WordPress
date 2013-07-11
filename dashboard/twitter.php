<?php
$twitter = ip_api_get_config('twitter');
$twitter_feed = json_decode($twitter->feed);
?>
<ul>
	<?php foreach($twitter_feed as $post): ?>
		<li>
			<h4><a href="http://twitter.com/<?php echo $twitter->screen_name;?>" target="_blank">@<?php echo $twitter->screen_name;?></a> - <?php echo date("F j, Y", strtotime($post->created_at));?></h4>
			<p><?php echo $post->text;?></p>
		</li>
	<?php endforeach;?>
</ul>