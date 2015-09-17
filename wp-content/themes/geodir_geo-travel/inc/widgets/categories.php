<?php
class TWcategoryWidget extends WP_Widget {
    function TWcategoryWidget() {
        $widget_ops = array('classname' => 'TWcategoryWidget', 'description' => 'designinvento category.');
        parent::WP_Widget(false, 'designinvento category ', $widget_ops);
    }
    function widget($args, $instance) {
        global $post;
		$title = apply_filters('widget_title', $instance['title']);
		
				?>
				<div class="custom-cat-widget">
				<div class="cat-widget custom-widget">
					<?php if (isset($before_widget))
            echo $before_widget;
        if ($title != '')
            echo $args['before_title'] . $title . $args['after_title']; 
			?>
		    		<div class="cat-widget-content">

		    			<ul> 
						<?php
				$categories = get_terms(
				'category', 
				array('parent' => 0,'order'=> 'DESC')	
				);
		    		$current = -1;
					//print_r($categories);		      
					foreach ($categories as $category) {
							
							$tag = $category->term_id;
							
											$tag_extra_fields = get_option(MY_CATEGORY_FIELDS);
											if (isset($tag_extra_fields[$tag])) {
												$category_icon_code = $tag_extra_fields[$tag]['category_icon_code'];
												$category_icon_color = $tag_extra_fields[$tag]['category_icon_color'];
											}



										?>
							
							<li>
									<?php
									if(!empty($category_icon_code)) {

									?>

						        	<div class="category-icon-box"><?php $category_icon = stripslashes($category_icon_code); echo $category_icon; ?></div>

						        	<?php } ?>
						  			<a href="<?php echo get_category_link( $category->term_id )?>" title="View posts in <?php echo $category->name?>"><?php echo $category->name ?></a>

						  			<span class="category-counter"><?php echo $category->count ?></span>

						  		</li>
								<?php
							}
						?>
						</ul>

		    		</div>

		    	</div>
		    	</div>
				<?php
    }

    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        /* Strip tags (if needed) and update the widget settings. */
        $instance['title'] = strip_tags($new_instance['title']);
       
        return $instance;
    }

    function form($instance) {
	extract($instance);
       ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e("Title:", "designinvento");?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $title; ?>"  />
        </p>
        <?php
    }
}
add_action('widgets_init', create_function('', 'return register_widget("TWcategoryWidget");'));
 ?>