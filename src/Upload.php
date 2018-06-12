<?php
/**
 * Media Upload Class
 *
 * Allows for other mime types.
 *
 * You may copy, distribute and modify the software as long as you track changes/dates in source files.
 * Any modifications to or software including (via compiler) GPL-licensed code must also be made
 * available under the GPL along with build & install instructions.
 *
 * @package    WPS\Media
 * @author     Travis Smith <t@wpsmith.net>
 * @copyright  2015-2018 Travis Smith
 * @license    http://opensource.org/licenses/gpl-2.0.php GNU Public License v2
 * @link       https://github.com/wpsmith/WPS
 * @version    1.0.0
 * @since      0.1.0
 */

namespace WPS\Media;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPS\Media\Upload' ) ) {
	/**
	 * Class Upload
	 *
	 * @package WPS\Media
	 */
	class Upload extends \WPS\Core\Singleton {

		/**
		 * @var array
		 */
		private $mime_types = array(
			'svg' => 'image/svg+xml',
		);

		/**
		 * Upload constructor.
		 *
		 * @param array $mime_types Mime types to be supported.
		 */
		protected function __construct( $mime_types = array() ) {
			$this->mime_types = !empty( $mime_types ) ? $mime_types : $this->mime_types;

			add_filter( 'upload_mimes', array( $this, 'upload_mimes' ), 1, 2 );
		}

		/**
		 * Adds mime type from array of types to be added.
		 *
		 * @param string $ext Extension.
		 * @param string $mime_type Mime type.
		 *
		 * @return bool Whether mime type was added or not.
		 */
		public function add_type( $ext, $mime_type ) {
			if ( ! isset( $this->mime_types[$ext] ) ) {
				$this->mime_types[$ext] = $mime_type;
				return true;
			}
			return false;
		}


		/**
		 * Removes mime type from array of types to be added.
		 *
		 * @param string $ext Extension.
		 *
		 * @return bool Whether mime type was removed or not.
		 */
		public function remove_type( $ext ) {
			if ( isset( $this->mime_types[$ext] ) ) {
				unset( $this->mime_types[$ext] );
				return true;
			}
			return false;
		}

		/**
		 * Filters list of allowed mime types and file extensions.
		 *
		 * @param array            $t    Mime types keyed by the file extension regex corresponding to
		 *                               those types. 'swf' and 'exe' removed from full list. 'htm|html' also
		 *                               removed depending on '$user' capabilities.
		 * @param int|WP_User|null $user User ID, User object or null if not provided (indicates current user).
		 */
		protected function upload_mimes( $mime_types, $user ) {

			foreach( $this->mime_types as $ext => $mime_type ) {
				if ( ! isset( $mime_types[$ext] ) ) {
					$mime_types[$ext] = $mime_type;
				}
			}

			return $mime_types;
		}
	}
}