<?php
$blog = ip_api_get_config('blog');
$feed = ip_api_fetch_feed($blog->url, $blog->num);
?>
<ul>
	<?php foreach($feed as $article): ?>
		<li>
			<h4><a href="<?php echo $article->link;?>" target="_blank"><?php echo $article->title;?></a></h4>
			<address>By <?php echo $article->dc->creator;?> on <?php echo date("F j, Y", strtotime($article->pubdate));?></address>
			<p><?php echo $article->description;?></p>
		</li>
	<?php endforeach;?>
</ul>