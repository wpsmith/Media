<?php
/**
 * Attachment Display Settings Abstract Class for Images
 *
 * Assists in the creation and management of adding an image setting to the Attachment Display Settings.
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

if ( ! class_exists( 'WPS\Media\ImageDisplaySetting' ) ) {
	/**
	 * Class ImageDisplaySetting
	 *
	 * @package WPS\Media
	 */
	abstract class ImageDisplaySetting extends AttachmentDisplaySetting {

		/**
		 * Attachment settings for a specific type.
		 *
		 * Could be image, video, audio, etc..
		 *
		 * @var string
		 */
		public $type = 'image';

	}
}