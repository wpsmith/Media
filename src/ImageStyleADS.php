<?php
/**
 * Attachment Display Settings Abstract Class for Videos
 *
 * Assists in the creation and management of adding a video setting to the Attachment Display Settings.
 *
 * You may copy, distribute and modify the software as long as you track changes/dates in source files.
 * Any modifications to or software including (via compiler) GPL-licensed code must also be made
 * available under the GPL along with build & install instructions.
 *
 * @package    WPS\Media
 * @author     Travis Smith <t@wpsmith.net>
 * @copyright  2015-2019 Travis Smith
 * @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License v2
 * @link       https://github.com/wpsmith/WPS
 * @version    1.0.0
 * @since      0.1.0
 */

namespace WPS\WP\Media;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( __NAMESPACE__ . '\ImageStyleADS' ) ) {
	/**
	 * Class ImageStyleADS
	 *
	 * @package WPS\Media
	 */
	class ImageStyleADS extends ImageDisplaySetting {


		/**
		 * Setting slug.
		 *
		 * @var string
		 */
		public $setting = 'style';

		/**
		 * Default value
		 *
		 * @var mixed
		 */
		public $default_value = 'nocaption';

		/**
		 * AttachmentDisplayOption constructor.
		 */
		public function __construct() {
			add_action( 'wp_enqueue_media', array( $this, 'wp_enqueue_media' ) );
			add_filter( 'wp_prepare_attachment_for_js', array( $this, '_wp_prepare_attachment_for_js' ), 10, 3 );
			add_filter( 'media_send_to_editor', array( $this, 'media_send_to_editor' ), 10, 3 );
		}

		/**
		 * Wherever the Media Modal is deployed, also deploy our overrides.
		 */
		public function wp_enqueue_media() {
			add_action( 'admin_print_footer_scripts', array( $this, 'media_template' ), 11 );
		}

		/**
		 * Template Overrides to inject our setting & override the AttachmentDisplay method.
		 */
		public function media_template() {
			?>
            <script type="text/html" id="tmpl-attachment-display-settings_twc">
                <h2><?php _e( 'Attachment Display Settings' ); ?></h2>

                <# if ( 'image' === data.type ) { #>
                <label class="setting">
                    <span><?php _e( 'Alignment' ); ?></span>
                    <select class="alignment"
                            data-setting="align"
                    <# if ( data.userSettings ) { #>
                    data-user-setting="align"
                    <# } #>>

                    <option value="left">
						<?php esc_html_e( 'Left' ); ?>
                    </option>
                    <option value="center">
						<?php esc_html_e( 'Center' ); ?>
                    </option>
                    <option value="right">
						<?php esc_html_e( 'Right' ); ?>
                    </option>
                    <option value="none" selected>
						<?php esc_html_e( 'None' ); ?>
                    </option>
                    </select>
                </label>
                <# } console.log(data); #>

                <div class="setting">
                    <label>
                        <# if ( data.model.canEmbed ) { #>
                        <span><?php _e( 'Embed or Link' ); ?></span>
                        <# } else { #>
                        <span><?php _e( 'Link To' ); ?></span>
                        <# } #>

                        <select class="link-to"
                                data-setting="link"
                        <# if ( data.userSettings && ! data.model.canEmbed ) { #>
                        data-user-setting="urlbutton"
                        <# } #>>

                        <# if ( data.model.canEmbed ) { #>
                        <option value="embed" selected>
							<?php esc_html_e( 'Embed Media Player' ); ?>
                        </option>
                        <option value="file">
                            <# } else { #>
                        <option value="none" selected>
							<?php esc_html_e( 'None' ); ?>
                        </option>
                        <option value="file">
                            <# } #>
                            <# if ( data.model.canEmbed ) { #>
							<?php esc_html_e( 'Link to Media File' ); ?>
                            <# } else { #>
							<?php esc_html_e( 'Media File' ); ?>
                            <# } #>
                        </option>
                        <option value="post">
                            <# if ( data.model.canEmbed ) { #>
							<?php esc_html_e( 'Link to Attachment Page' ); ?>
                            <# } else { #>
							<?php esc_html_e( 'Attachment Page' ); ?>
                            <# } #>
                        </option>
                        <# if ( 'image' === data.type ) { #>
                        <option value="custom">
							<?php esc_html_e( 'Custom URL' ); ?>
                        </option>
                        <# } #>
                        </select>
                    </label>
                    <input type="text" class="link-to-custom" data-setting="linkUrl"/>
                </div>

                <# if ( data.type == 'image' && 'undefined' !== typeof data.sizes ) { #>
                <label class="setting">
                    <span><?php _e( 'Size' ); ?></span>
                    <select class="size" name="size"
                            data-setting="size"
                    <# if ( data.userSettings ) { #>
                    data-user-setting="imgsize"
                    <# } #>>
					<?php
					/** This filter is documented in wp-admin/includes/media.php */
					$sizes = apply_filters( 'image_size_names_choose', array(
						'thumbnail' => __( 'Thumbnail' ),
						'medium'    => __( 'Medium' ),
						'large'     => __( 'Large' ),
						'full'      => __( 'Full Size' ),
					) );

					foreach ( $sizes as $value => $name ) : ?>
                        <#
                        var size = data.sizes['<?php echo esc_js( $value ); ?>'];
                        if ( size ) { #>
                        <option value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, 'full' ); ?>>
							<?php echo esc_html( $name ); ?> &ndash; {{ size.width }} &times; {{ size.height }}
                        </option>
                        <# } #>
					<?php endforeach; ?>
                    </select>
                </label>
                <# } #>


                <# if( data.type == '<?php echo $this->type ?>' ) { #>
                <label class="setting">
					<?php $this->setting(); ?>
                </label>
                <# } #>
            </script>
            <script>
                $.ajaxPrefilter(function (options, originalOptions, jqXHR) {
                    if (originalOptions.type !== 'POST' || options.type !== 'POST') {
                        return;
                    }

                    if (originalOptions.data && "send-attachment-to-editor" === originalOptions.data.action) {
                        options.data = originalOptions.data;
                        options.data.attachment = $.extend(originalOptions.data.attachment, {
                            '<?php echo $this->setting ?>': getUserSetting('<?php echo $this->setting ?>', '<?php echo $this->default_value ?>')
                        });
                        options.data = $.param(options.data);
                    }
                });
            </script>

			<?php
			$this->override_attachment_display();
		}

		// Abstract

		/**
		 * Output the backbone template for our setting.
		 *
		 * @todo Abstract this.
		 */
		protected function setting() {
			?>
            <span><?php _e( 'Image Style', 'wps' ); ?></span>
            <select class="<?php echo $this->type . '-' . $this->setting ?>"
                    data-setting="<?php echo $this->setting ?>"
                    name="<?php echo $this->setting ?>"
            <# if ( data.userSettings ) { #>
            data-user-setting="<?php echo $this->setting ?>"
            <# } #>>

            <option value="full-width-dark">
				<?php esc_html_e( 'Full Width Dark' ); ?>
            </option>
            <option value="full-width-light">
				<?php esc_html_e( 'Full Width Light' ); ?>
            </option>
            <option value="small">
				<?php esc_html_e( 'Small' ); ?>
            </option>
            <option value="nocaption" selected>
				<?php esc_html_e( 'No Caption' ); ?>
            </option>
            </select>
			<?php
		}

		/**
		 * Actually overrides the backbone AttachmentDisplay with ours.
		 */
		private function override_attachment_display() {
			?>
            <script>
                (function (media) {
                    var attDisplayOld = wp.media.view.Settings.AttachmentDisplay;
                    wp.media.view.Settings.AttachmentDisplay = attDisplayOld.extend({
                        template: wp.template('attachment-display-settings_twc')
                    });

                })(wp.media);
            </script>
			<?php
		}

		/**
		 * Adds our setting to the response array to prevent PHP notices.
		 *
		 * @param array $response Array of prepared attachment data.
		 * @param int|object $attachment Attachment ID or object.
		 * @param array $meta Array of attachment meta data.
		 *
		 * @return array
		 */
		public function _wp_prepare_attachment_for_js( $response, $attachment, $meta ) {
			// only for image
			if ( $response['type'] != $this->type ) {
				return $response;
			}

			return $this->wp_prepare_attachment_for_js( $response, $attachment, $meta );
		}

		/**
		 * Adds our setting to the response array to prevent PHP notices.
		 *
		 * @todo Abstract this
		 *
		 * @param array $response Array of prepared attachment data.
		 * @param int|object $attachment Attachment ID or object.
		 * @param array $meta Array of attachment meta data.
		 *
		 * @return array
		 */
		protected function wp_prepare_attachment_for_js( $response, $attachment, $meta ) {

			$response[ $this->setting ] = isset( $response[ $this->setting ] ) ? $response[ $this->setting ] : $this->default_value;

			return $response;

		}

		/**
		 * Modify the HTML response to be sent to the editor.
		 *
		 * @todo Abstract this
		 *
		 * @param string $html HTML markup for a media item sent to the editor.
		 * @param int $send_id The first key from the $_POST['send'] data.
		 * @param array $attachment Array of attachment metadata.
		 *
		 * @return string
		 */
		public function media_send_to_editor( $html, $send_id, $attachment ) {

			if ( false !== strpos( $html, '[caption' ) ) {
				$html = str_replace( '[caption', sprintf( '[caption class="%s"', $attachment[ $this->setting ] ), $html );
			} else {
				$html = sprintf( '<div class="%s">%s</div>', $attachment[ $this->setting ], $html );
			}

			return $html;
		}
	}
}