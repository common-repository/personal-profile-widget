<?php

class Personal_Profile_Widget extends WP_Widget {

        function __construct( $id_base = false, $name = false, $widget_options = array(), $control_options = array() ) {
                $id_base = ( $id_base ) ? $id_base : 'personalprofile';
                $name = ( $name ) ? $name : __( 'Personal Profile Widget', 'personal-profile-widget' );

                $widget_options = wp_parse_args( $widget_options, array(
                        'classname'   => 'widget_personalprofile',
                        'description' => __( 'Display an image', 'personal-profile-widget' ),
                ) );

                $control_options = wp_parse_args( $control_options, array(
                        'width' => 300
                ) );

                parent::__construct( $id_base, $name, $widget_options, $control_options );

                add_action( 'save_post', array( $this, 'flush_group_cache' ) );
                add_action( 'delete_attachment', array( $this, 'flush_group_cache' ) );
                add_action( 'switch_theme', array( $this, 'flush_group_cache' ) );
        }

        function widget( $args, $instance ) {
                $cache = (array) wp_cache_get( 'personal-profile-widget', 'widget' );

                if ( isset( $cache[ $this->id ] ) ) {
                        echo $cache[ $this->id ];
                        return;
                }

                $instance['name_raw'] = $instance['name'];
                $instance['name'] = apply_filters( 'widget_name', empty( $instance['name'] ) ? '' : $instance['name'], $instance, $this->id_base );

                $instance['text_raw'] = $instance['text'];
                $instance['text'] = apply_filters( 'widget_text', empty( $instance['text'] ) ? '' : $instance['text'], $instance, $this->id_base );

                $output = '';

                if ( ! empty( $instance['image_id'] ) ) {
                        $image = get_post( $instance['image_id'] );
                        if ( ! $image || 'attachment' != get_post_type( $image ) ) {
                                $output = '<!-- Image Widget Error: Invalid Attachment ID -->';
                        }
                }

                if ( empty( $output ) ) {
                        $output = $this->render( $args, $instance );
                }

                echo $output;

                $cache[ $this->id ] = $output;
                wp_cache_set( 'personal_profile_widget', array_filter( $cache ), 'widget' );
        }

        function render( $args, $instance ) {

				$output = $args['before_widget'];

                        if ( $inside = apply_filters( 'personal_profile_widget_output', '', $args, $instance, $this->id_base ) ) {
                                $output .= $inside;
                        } else {
                                if ( ! empty( $instance['image_id'] ) ) {
                                        $image_size = ( ! empty( $instance['image_size'] ) ) ? $instance['image_size'] : apply_filters( 'personal_profile_widget_output_default_size', 'medium', $this->id_base );

                                        $output .= sprintf( '<p class="personal-profile">%s%s%s</p>',
                                                $instance['link_open'],
                                                wp_get_attachment_image( $instance['image_id'], $image_size ),
                                                $instance['link_close']
                                        );
                                }
								
								if ( ! empty( $instance['name'] ) ) {
                                        $output .= '<h3>' . apply_filters( 'the_title', $instance['name'] . '</h3>' );
                                }
								
                                if ( ! empty( $instance['text'] ) ) {
                                        $output .= apply_filters( 'the_content', $instance['text'] );
                                }
								
								if ( ! empty( $instance['linktitle'] ) ) {
                                        $output .= '<h3>' . apply_filters( 'the_title', $instance['linktitle'] . '</h3>');
                                }
								
								if ( ! empty( $instance['linkone'] ) ) {
                                        $output .= '<a href="' . $instance['linkone'] . '">' . $instance['linkonetitle'] . '</a><HR />';
                                }
								
								if ( ! empty( $instance['linktwo'] ) ) {
                                        $output .= '<a href="' . $instance['linktwo'] . '">' . $instance['linktwotitle'] . '</a><HR />';
                                }
								
								if ( ! empty( $instance['linkthree'] ) ) {
                                        $output .= '<a href="' . $instance['linkthree'] . '">' . $instance['linkthreetitle'] . '</a><HR />';
                                }
								
								if ( ! empty( $instance['socialiconstitle'] ) ) {
                                        $output .= '<h3>' . apply_filters( 'the_title', $instance['socialiconstitle'] . '</h3>' );
                                }
								if( ! empty( $instance['facebook'] ) ) {
										$output .= '<div class="facebook"><a href="' . $instance['facebook'] . '" target="_blank" class="social">Facebook</a></div>';
								}
								if( ! empty( $instance['twitter'] ) ) {
										$output .= '<div class="twitter"><a href="' . $instance['twitter'] . '" target="_blank" class="social">Twitter</a></div>';
								}
								if( ! empty( $instance['gplus'] ) ) {
										$output .= '<div class="google"><a href="' . $instance['gplus'] . '" target="_blank" class="social">Google +</a></div>';
								}
								if( ! empty( $instance['instagram'] ) ) {
									    $output .= '<div class="instagram"><a href="'. $instance['instagram'] .'" target="_blank" class="social">Instagram</a></div>';
								}   
								if( ! empty( $instance['pinterest'] ) ) {
										$output .= '<div class="pinterest"><a href="'. $instance['pinterest'] .'" target="_blank" class="social">Pinterest</a></div>';
								}   
								if( ! empty( $instance['linkedin'] ) ) {
										$output .= '<div class="linkedin"><a href="'. $instance['linkedin'] .'" target="_blank" class="social">LinkedIn</a></div>';
								}
								if( ! empty( $instance['youtube'] ) ) {
										$output .= '<div class="youtube"><a href="'. $instance['youtube'] .'" target="_blank" class="social">YouTube</a></div>';
								} 
                        }

                $output .= $args['after_widget'];

                return $output;
        }
		
        function form( $instance ) {
                $instance = wp_parse_args( (array) $instance, array(
                        'image_id'   => '',
                        'image_size' => 'full',
                        'link'       => '',
                        'link_text'  => '',
                        'new_window' => '',
                        'name'      => '',
                        'text'       => '',
                ) );

                $instance['image_id'] = absint( $instance['image_id'] );
                $instance['name'] = wp_strip_all_tags( $instance['name'] );

                $button_class = array( 'button', 'button-hero', 'personal-profile-widget-control-choose' );
                $image_id = $instance['image_id'];

                $fields = (array) apply_filters( 'personal_profile_widget_fields', $this->form_fields(), $this->id_base );
                ?>

                <div class="personal-profile-widget-form">

                        <?php do_action( 'personal_profile_widget_form_before', $instance, $this->id_base ); ?>

                        <?php if ( ! is_personal_profile_widget_legacy() ) : ?>
                                <p class="personal-profile-widget-control<?php echo ( $image_id ) ? ' has-image' : ''; ?>"
                                        data-name="<?php esc_attr_e( 'Choose an Image for the Widget', 'personal-profile-widget' ); ?>"
                                        data-update-text="<?php esc_attr_e( 'Update Image', 'personal-profile-widget' ); ?>"
                                        data-target=".image-id">
                                        <?php
                                        if ( $image_id ) {
                                                echo wp_get_attachment_image( $image_id, 'medium', false );
                                                unset( $button_class[ array_search( 'button-hero', $button_class ) ] );
                                        }
                                        ?>
                                        <input type="hidden" name="<?php echo $this->get_field_name( 'image_id' ); ?>" id="<?php echo $this->get_field_id( 'image_id' ); ?>" value="<?php echo $image_id; ?>" class="image-id personal-profile-widget-control-target">
                                        <a href="#" class="<?php echo join( ' ', $button_class ); ?>"><?php _e( 'Choose an Image', 'personal-profile-widget' ); ?></a>
                                </p>
                        <?php endif; ?>

                        <?php if ( is_personal_profile_widget_legacy() || ! empty( $instance['image'] ) ) : ?>
                                <div class="personal-profile-widget-legacy-fields">
                                        <?php if ( ! is_personal_profile_widget_legacy() ) : ?>
                                        <?php endif; ?>

                                        <p>
                                                <label for="<?php echo $this->get_field_id( 'image' ); ?>"><?php _e( 'Image URL:', 'personal-profile-widget' ); ?></label>
                                                <input type="text" name="<?php echo $this->get_field_name( 'image' ); ?>" id="<?php echo $this->get_field_id( 'image' ); ?>" value="<?php echo esc_url( $instance['image'] ); ?>" class="widefat">
                                        </p>
                                        <p>
                                                <label for="<?php echo $this->get_field_id( 'alt' ); ?>"><?php _e( 'Alternate Text:', 'personal-profile-widget' ); ?></label>
                                                <input type="text" name="<?php echo $this->get_field_name( 'alt' ); ?>" id="<?php echo $this->get_field_id( 'alt' ); ?>" value="<?php echo esc_attr( $instance['alt'] ); ?>" class="widefat">
                                        </p>
                                </div>
                        <?php endif; ?>
						
						<p>
<div align="center"><a href="http://hptonline.co.uk/personal-profile-widget/instructions/" target="_blank">Instructions on adding image</a></div>
</p>

                        <?php
                        if ( ! empty( $fields ) ) {
                                foreach ( $fields as $field ) {
                                        switch ( $field ) {
                                                case 'image_size' :
                                                        $sizes = $this->get_image_sizes( $image_id );
                                                        ?>
                                                        <p>
                                                                <label for="<?php echo $this->get_field_id( 'image_size' ); ?>"><?php _e( 'Size:', 'personal-profile-widget' ); ?></label>
                                                                <select name="<?php echo $this->get_field_name( 'image_size' ); ?>" id="<?php echo $this->get_field_id( 'image_size' ); ?>" class="widefat image-size"<?php echo ( sizeof( $sizes ) < 2 ) ? ' disabled="disabled"' : ''; ?>>
                                                                        <?php
                                                                        foreach ( $sizes as $id => $label ) {
                                                                                printf( '<option value="%s"%s>%s</option>',
                                                                                        esc_attr( $id ),
                                                                                        selected( $instance['image_size'], $id, false ),
                                                                                        esc_html( $label )
                                                                                );
                                                                        }
                                                                        ?>
                                                                </select>
                                                        </p>														
																				                        <p>
                                <label for="<?php echo $this->get_field_id( 'name' ); ?>"><?php _e( 'Name:', 'personal-profile-widget' ); ?></label>
                                <input type="text" name="<?php echo $this->get_field_name( 'name' ); ?>" id="<?php echo $this->get_field_id( 'name' ); ?>" value="<?php echo esc_attr( $instance['name'] ); ?>" class="widefat">
                        </p>
						
                                                        <?php
                                                        break;

                                                case 'text' :
                                                        ?>
                                                        <p>
                                                                <label for="<?php echo $this->get_field_id( 'text' ); ?>"><?php _e( 'Bio:', 'personal-profile-widget' ); ?></label>
                                                                <textarea name="<?php echo $this->get_field_name( 'text' ); ?>" id="<?php echo $this->get_field_id( 'text' ); ?>" rows="4" class="widefat"><?php echo esc_textarea( $instance['text'] ); ?></textarea>
                                                        </p>
                                                        <?php
                                                        break;

                                                default :
                                                        do_action( 'personal_profile_widget_field-' . sanitize_key( $field ), $instance, $this );
                                        }
                                }
                        }

                        do_action( 'personal_profile_widget_form_after', $instance, $this->id_base );
                        ?>
						
						<p>
                                <label for="<?php echo $this->get_field_id( 'linktitle' ); ?>"><?php _e( 'Link Title:', 'personal-profile-widget' ); ?></label>
                                <input type="text" name="<?php echo $this->get_field_name( 'linktitle' ); ?>" id="<?php echo $this->get_field_id( 'linktitle' ); ?>" value="<?php echo esc_attr( $instance['linktitle'] ); ?>" class="widefat">
                        </p>
						<hr>
						<p>
                                <label for="<?php echo $this->get_field_id( 'linkonetitle' ); ?>"><?php _e( '1st Link Title:', 'personal-profile-widget' ); ?></label>
                                <input type="text" name="<?php echo $this->get_field_name( 'linkonetitle' ); ?>" id="<?php echo $this->get_field_id( 'linkonetitle' ); ?>" value="<?php echo esc_attr( $instance['linkonetitle'] ); ?>" class="widefat">
                        </p>
						
						<p>
                                <label for="<?php echo $this->get_field_id( 'linkone' ); ?>"><?php _e( '1st Link:', 'personal-profile-widget' ); ?></label>
                                <input type="text" name="<?php echo $this->get_field_name( 'linkone' ); ?>" id="<?php echo $this->get_field_id( 'linkone' ); ?>" value="<?php echo esc_attr( $instance['linkone'] ); ?>" class="widefat">
                        </p>
						<hr>
						<p>
                                <label for="<?php echo $this->get_field_id( 'linktwotitle' ); ?>"><?php _e( '2nd Link Title:', 'personal-profile-widget' ); ?></label>
                                <input type="text" name="<?php echo $this->get_field_name( 'linktwotitle' ); ?>" id="<?php echo $this->get_field_id( 'linktwotitle' ); ?>" value="<?php echo esc_attr( $instance['linktwotitle'] ); ?>" class="widefat">
                        </p>
						
						<p>
                                <label for="<?php echo $this->get_field_id( 'linktwo' ); ?>"><?php _e( '2nd Link:', 'personal-profile-widget' ); ?></label>
                                <input type="text" name="<?php echo $this->get_field_name( 'linktwo' ); ?>" id="<?php echo $this->get_field_id( 'linktwo' ); ?>" value="<?php echo esc_attr( $instance['linktwo'] ); ?>" class="widefat">
                        </p>
						<hr>
						<p>
                                <label for="<?php echo $this->get_field_id( 'linkthreetitle' ); ?>"><?php _e( '3rd Link Title:', 'personal-profile-widget' ); ?></label>
                                <input type="text" name="<?php echo $this->get_field_name( 'linkthreetitle' ); ?>" id="<?php echo $this->get_field_id( 'linkthreetitle' ); ?>" value="<?php echo esc_attr( $instance['linkthreetitle'] ); ?>" class="widefat">
                        </p>
						
						<p>
                                <label for="<?php echo $this->get_field_id( 'linkthree' ); ?>"><?php _e( '3rd Link:', 'personal-profile-widget' ); ?></label>
                                <input type="text" name="<?php echo $this->get_field_name( 'linkthree' ); ?>" id="<?php echo $this->get_field_id( 'linkthree' ); ?>" value="<?php echo esc_attr( $instance['linkthree'] ); ?>" class="widefat">
                        </p>
						<hr>
						<p>
								<label for="<?php echo $this->get_field_id('socialiconstitle'); ?>"><?php _e('Social Icons Title (e.g. Connect with Us):', 'personal-profile-widget'); ?></label>
								<input class="widefat" id="<?php echo $this->get_field_id('socialiconstitle'); ?>" name="<?php echo $this->get_field_name('socialiconstitle'); ?>" type="text" value="<?php echo esc_attr( $instance['socialiconstitle'] ); ?>" />
						</p>

						<p>
						<label for="<?php echo $this->get_field_id('facebook'); ?>"><?php _e('Facebook URL:', 'personal-profile-widget'); ?></label>
						<input class="widefat" id="<?php echo $this->get_field_id('facebook'); ?>" name="<?php echo $this->get_field_name('facebook'); ?>" type="text" value="<?php echo esc_attr( $instance['facebook'] ); ?>" />
						</p>

						<p>
						<label for="<?php echo $this->get_field_id('twitter'); ?>"><?php _e('Twitter URL:', 'personal-profile-widget'); ?></label>
						<input class="widefat" id="<?php echo $this->get_field_id('twitter'); ?>" name="<?php echo $this->get_field_name('twitter'); ?>" type="text" value="<?php echo esc_attr( $instance['twitter'] ); ?>" />
						</p>

						<p>
						<label for="<?php echo $this->get_field_id('gplus'); ?>"><?php _e('Google Plus URL:', 'personal-profile-widget'); ?></label>
						<input class="widefat" id="<?php echo $this->get_field_id('gplus'); ?>" name="<?php echo $this->get_field_name('gplus'); ?>" type="text" value="<?php echo esc_attr( $instance['gplus'] ); ?>" />
						</p>

						<p>
						<label for="<?php echo $this->get_field_id('linkedin'); ?>"><?php _e('Linked In URL:', 'personal-profile-widget'); ?></label>
						<input class="widefat" id="<?php echo $this->get_field_id('linkedin'); ?>" name="<?php echo $this->get_field_name('linkedin'); ?>" type="text" value="<?php echo esc_attr( $instance['linkedin'] ); ?>" />
						</p>

						<p>
						<label for="<?php echo $this->get_field_id('youtube'); ?>"><?php _e('YouTube URL:', 'personal-profile-widget'); ?></label>
						<input class="widefat" id="<?php echo $this->get_field_id('youtube'); ?>" name="<?php echo $this->get_field_name('youtube'); ?>" type="text" value="<?php echo esc_attr( $instance['youtube'] ); ?>" />
						</p>

						<p>
						<label for="<?php echo $this->get_field_id('pinterest'); ?>"><?php _e('Pinterest URL:', 'personal-profile-widget'); ?></label>
						<input class="widefat" id="<?php echo $this->get_field_id('pinterest'); ?>" name="<?php echo $this->get_field_name('pinterest'); ?>" type="text" value="<?php echo esc_attr( $instance['pinterest']) ; ?>" />
						</p>

						<p>
						<label for="<?php echo $this->get_field_id('instagram'); ?>"><?php _e('Instagram URL:', 'personal-profile-widget'); ?></label>
						<input class="widefat" id="<?php echo $this->get_field_id('instagram'); ?>" name="<?php echo $this->get_field_name('instagram'); ?>" type="text" value="<?php echo esc_attr( $instance['instagram'] ); ?>" />
						</p>
						
						<p>
						<br />
						<div align="center">
						<a href="http://demo.homepage-technologies.co.uk/contact-display-widget/plugin-suggestion/" target="_blank">Got a suggestion? Get in touch!</a>
						</div>
						</p>
					</div>
                <?php
        }

        function form_fields() {
                $fields = array( 'link', 'link_text', 'text' );

                // Don't show the image size field for users with older WordPress versions.
                if ( ! is_personal_profile_widget_legacy() ) {
                        array_unshift( $fields, 'image_size' );
                }

                return $fields;
        }

        function get_image_sizes( $image_id ) {
                $sizes = array( 'full' => __( 'Full Size', 'personal-profile-widget' ) );

                $imagedata = wp_get_attachment_metadata( $image_id );
                if ( isset( $imagedata['sizes'] ) ) {
                        $size_names = Personal_Profile_Widget_Loader::get_image_size_names();

                        $sizes['full'] .= ( isset( $imagedata['width'] ) && isset( $imagedata['height'] ) ) ? sprintf( ' (%d&times;%d)', $imagedata['width'], $imagedata['height'] ) : '';

                        foreach( $imagedata['sizes'] as $_size => $data ) {
                                $label  = ( isset( $size_names[ $_size ] ) ) ? $size_names[ $_size ] : ucwords( $_size );
                                $label .= sprintf( ' (%d&times;%d)', $data['width'], $data['height'] );

                                $sizes[ $_size ] = $label;
                        }
                }

                return $sizes;
        }
		
        function flush_widget_cache() {
                $cache = (array) wp_cache_get( 'personal_profile_widget', 'widget' );

                if ( isset( $cache[ $this->id ] ) ) {
                        unset( $cache[ $this->id ] );
                }

                wp_cache_set( 'personal_profile_widget', array_filter( $cache ), 'widget' );
        }

        function flush_group_cache( $post_id = null ) {
                if ( 'save_post' == current_filter() && 'attachment' != get_post_type( $post_id ) ) {
                        return;
                }

                wp_cache_delete( 'personal_profile_widget', 'widget' );
        }
}

add_action('init', 'register_style');

function register_style(){

    wp_register_style( 'personal_profile_widget', plugins_url('/css/personalprofilewidget.css', __FILE__));
	wp_register_style( 'entypo', plugins_url('/css/entypo.css', __FILE__));
}

add_action('wp_enqueue_scripts', 'enqueue_style');
function enqueue_style(){

    wp_enqueue_style( 'personal_profile_widget' );
	wp_enqueue_style( 'entypo' );
} 