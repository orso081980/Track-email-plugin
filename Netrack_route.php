<?php if(is_user_logged_in()): ?>
	<div id="table"></div>

	<ul class='pagination' id="pagination">
		<?php if(!empty($total_pages)):
			for($i=1; $i<=$total_pages; $i++):  
				if($i == 1):
					?>
					<li class='active' id="<?php echo $i;?>">
						<a href='<?php echo $i;?>'><?php echo $i;?></a>
					</li> 
					<?php 
				else: 
					?>
					<li id="<?php echo $i;?>">
						<a href='<?php echo $i;?>'><?php echo $i;?></a>
					</li>
					<?php 
				endif;
				?>			
				<?php 
			endfor;
		endif;?>  
	</ul>
	<?php 
else: 
	?>
	<p>You can view the entries only if you're sign in</p>
	<?php
endif;
?>