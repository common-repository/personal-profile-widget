<?php
/*
Plugin Name: Personal Profile Widget
Plugin URI: http://demo.homepage-technologies.co.uk/personal-profile-widget/
Description: Display your personal profile in an easy to use widget. Add links, images and social media.
Version: 1.0.2
Author: HPTOnline (Ian Norris, James White)
License: GPL2+
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Original code based on https://github.com/blazersix/simple-image-widget
*/

require_once( plugin_dir_path( __FILE__ ) . 'class-personal-profile-widget.php' );

class Personal_Profile_Widget_Loader {

        public static function load() {
                self::load_textdomain();
                add_action( 'widgets_init', array( __CLASS__, 'register_widget' ) );
                add_action( 'init', array( __CLASS__, 'init' ) );
                add_action( 'admin_enqueue_scripts', array( __CLASS__, 'admin_scripts' ) );
                add_action( 'admin_head-widgets.php', array( __CLASS__, 'admin_head_widgets' ) );
                add_action( 'admin_footer-widgets.php', array( __CLASS__, 'admin_footer_widgets' ) );
        }

        public static function load_textdomain() {
                $locale = apply_filters( 'plugin_locale', get_locale(), 'personal-profile-widget' );
                load_textdomain( 'personal-profile-widget', WP_LANG_DIR . '/personal-profile-widget/' . $locale . '.mo' );
                load_plugin_textdomain( 'personal-profile-widget', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
        }

        public static function init() {
                wp_register_script( 'personal-profile-widget', plugin_dir_url( __FILE__ ) . 'js/personal-profile-widget.js', array( 'media-upload', 'media-views' ) );

                wp_localize_script( 'personal-profile-widget', 'PersonalProfileWidget', array(
                        'frameTitle'      => __( 'Choose an Attachment', 'personal-profile-widget' ),
                        'frameUpdateText' => __( 'Update Attachment', 'personal-profile-widget' ),
                        'fullSizeLabel'   => __( 'Full Size', 'personal-profile-widget' ),
                        'imageSizeNames'  => self::get_image_size_names(),
                ) );
        }

        public static function register_widget() {
                register_widget( 'Personal_Profile_Widget' );
        }

        public static function admin_scripts( $hook_suffix ) {
                if ( 'widgets.php' == $hook_suffix ) {
                        wp_enqueue_media();
                        wp_enqueue_script( 'personal-profile-widget' );
                }
        }

        public static function admin_head_widgets() {
                ?>
                <style type="text/css">
                .widget .widget-inside .personal-profile-widget-form .personal-profile-widget-control { padding: 20px 0; text-align: center; border: 1px dashed #aaa;}
                .widget .widget-inside .personal-profile-widget-form .personal-profile-widget-control.has-image { padding: 10px; text-align: left; border: 1px dashed #aaa;}
                .widget .widget-inside .personal-profile-widget-form .personal-profile-widget-control img { display: block; margin-bottom: 10px; max-width: 100%; height: auto;}
                </style>
                <?php
        }

        public static function admin_footer_widgets() {
                ?>
                <script type="text/javascript">
                jQuery(function($) {
                        $('#wpbody').on('selectionChange.personalprofilewidget', '.personal-profile-widget-control', function( e, selection ) {
                                var $control = $( e.target ),
                                        $sizeField = $control.closest('.personal-profile-widget-form').find('select.image-size'),
                                        model = selection.first(),
                                        sizes = model.get('sizes'),
                                        size, image;

                                if ( sizes ) {.
                                        size = sizes['post-thumbnail'] || sizes.medium;
                                }

                                if ( $sizeField.length ) {
                                        PeronsalProfileWidget.updateSizeDropdownOptions( $sizeField, sizes );
                                }

                                size = size || model.toJSON();

                                image = $( '<img />', { src: size.url, width: size.width } );

                                $control.find('img').remove().end()
                                        .prepend( image )
                                        .addClass('has-image')
                                        .find('a.personal-profile-widget-control-choose').removeClass('button-hero');
                        });
                });
                </script>
                <?php
        }

        public static function get_image_size_names() {
                return apply_filters( 'image_size_names_choose', array(
                        'thumbnail' => __( 'Thumbnail', 'personal-profile-widget' ),
                        'medium'    => __( 'Medium', 'personal-profile-widget' ),
                        'large'     => __( 'Large', 'personal-profile-widget' ),
                        'full'      => __( 'Full Size', 'personal-profile-widget' ),
                ) );
        }
}
add_action( 'plugins_loaded', array( 'Personal_Profile_Widget_Loader', 'load' ) );

function is_personal_profile_widget_legacy() {
        return version_compare( get_bloginfo( 'version' ), '3.4.2', '<=' );
}