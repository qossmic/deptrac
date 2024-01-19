<?php

namespace DEPTRAC_202401;

use DEPTRAC_202401\JetBrains\PhpStorm\ArrayShape;
use DEPTRAC_202401\JetBrains\PhpStorm\Deprecated;
use DEPTRAC_202401\JetBrains\PhpStorm\Internal\LanguageLevelTypeAware;
use DEPTRAC_202401\JetBrains\PhpStorm\Internal\PhpStormStubsElementAvailable;
use DEPTRAC_202401\JetBrains\PhpStorm\Pure;
/**
 * Retrieve information about the currently installed GD library
 * @link https://php.net/manual/en/function.gd-info.php
 * @return array an associative array.
 * <p>
 * <table>
 * Elements of array returned by <b>gd_info</b>
 * <tr valign="top">
 * <td>Attribute</td>
 * <td>Meaning</td>
 * </tr>
 * <tr valign="top">
 * <td>GD Version</td>
 * <td>string value describing the installed
 * libgd version.</td>
 * </tr>
 * <tr valign="top">
 * <td>FreeType Support</td>
 * <td>boolean value. <b>TRUE</b>
 * if FreeType Support is installed.</td>
 * </tr>
 * <tr valign="top">
 * <td>FreeType Linkage</td>
 * <td>string value describing the way in which
 * FreeType was linked. Expected values are: 'with freetype',
 * 'with TTF library', and 'with unknown library'. This element will
 * only be defined if FreeType Support evaluated to
 * <b>TRUE</b>.</td>
 * </tr>
 * <tr valign="top">
 * <td>T1Lib Support</td>
 * <td>boolean value. <b>TRUE</b>
 * if T1Lib support is included.</td>
 * </tr>
 * <tr valign="top">
 * <td>GIF Read Support</td>
 * <td>boolean value. <b>TRUE</b>
 * if support for reading GIF
 * images is included.</td>
 * </tr>
 * <tr valign="top">
 * <td>GIF Create Support</td>
 * <td>boolean value. <b>TRUE</b>
 * if support for creating GIF
 * images is included.</td>
 * </tr>
 * <tr valign="top">
 * <td>JPEG Support</td>
 * <td>boolean value. <b>TRUE</b>
 * if JPEG support is included.</td>
 * </tr>
 * <tr valign="top">
 * <td>PNG Support</td>
 * <td>boolean value. <b>TRUE</b>
 * if PNG support is included.</td>
 * </tr>
 * <tr valign="top">
 * <td>WBMP Support</td>
 * <td>boolean value. <b>TRUE</b>
 * if WBMP support is included.</td>
 * </tr>
 * <tr valign="top">
 * <td>XBM Support</td>
 * <td>boolean value. <b>TRUE</b>
 * if XBM support is included.</td>
 * </tr>
 * <tr valign="top">
 * <td>WebP Support</td>
 * <td>boolean value. <b>TRUE</b>
 * if WebP support is included.</td>
 * </tr>
 * </table>
 * </p>
 * <p>
 * Previous to PHP 5.3.0, the JPEG Support attribute was named
 * JPG Support.
 * </p>
 */
#[Pure]
#[ArrayShape(["GD Version" => "string", "FreeType Support" => "bool", "GIF Read Support" => "bool", "GIF Create Support" => "bool", "JPEG Support" => "bool", "PNG Support" => "bool", "WBMP Support" => "bool", "XPM Support" => "bool", "XBM Support" => "bool", "WebP Support" => "bool", "BMP Support" => "bool", "TGA Read Support" => "bool", "AVIF Support" => "bool", "JIS-mapped Japanese Font Support" => "bool"])]
function gd_info() : array
{
}
/**
 * Draws an arc
 * @link https://php.net/manual/en/function.imagearc.php
 * @param resource|GdImage $image
 * @param int $center_x <p>
 * x-coordinate of the center.
 * </p>
 * @param int $center_y <p>
 * y-coordinate of the center.
 * </p>
 * @param int $width <p>
 * The arc width.
 * </p>
 * @param int $height <p>
 * The arc height.
 * </p>
 * @param int $start_angle <p>
 * The arc start angle, in degrees.
 * </p>
 * @param int $end_angle <p>
 * The arc end angle, in degrees.
 * 0° is located at the three-o'clock position, and the arc is drawn
 * clockwise.
 * </p>
 * @param int $color <p>
 * A color identifier created with
 * <b>imagecolorallocate</b>.
 * </p>
 * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
 */
function imagearc(\GdImage $image, int $center_x, int $center_y, int $width, int $height, int $start_angle, int $end_angle, int $color) : bool
{
}
/**
 * Draw an ellipse
 * @link https://php.net/manual/en/function.imageellipse.php
 * @param resource|GdImage $image
 * @param int $center_x <p>
 * x-coordinate of the center.
 * </p>
 * @param int $center_y <p>
 * y-coordinate of the center.
 * </p>
 * @param int $width <p>
 * The ellipse width.
 * </p>
 * @param int $height <p>
 * The ellipse height.
 * </p>
 * @param int $color <p>
 * The color of the ellipse. A color identifier created with
 * <b>imagecolorallocate</b>.
 * </p>
 * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
 */
function imageellipse(\GdImage $image, int $center_x, int $center_y, int $width, int $height, int $color) : bool
{
}
/**
 * Draw a character horizontally
 * @link https://php.net/manual/en/function.imagechar.php
 * @param resource|GdImage $image
 * @param int $font
 * @param int $x <p>
 * x-coordinate of the start.
 * </p>
 * @param int $y <p>
 * y-coordinate of the start.
 * </p>
 * @param string $char <p>
 * The character to draw.
 * </p>
 * @param int $color <p>
 * A color identifier created with
 * <b>imagecolorallocate</b>.
 * </p>
 * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
 */
function imagechar(\GdImage $image, #[LanguageLevelTypeAware(['8.1' => 'GdFont|int'], default: 'int')] $font, int $x, int $y, string $char, int $color) : bool
{
}
/**
 * Draw a character vertically
 * @link https://php.net/manual/en/function.imagecharup.php
 * @param resource|GdImage $image
 * @param int $font
 * @param int $x <p>
 * x-coordinate of the start.
 * </p>
 * @param int $y <p>
 * y-coordinate of the start.
 * </p>
 * @param string $char <p>
 * The character to draw.
 * </p>
 * @param int $color <p>
 * A color identifier created with
 * <b>imagecolorallocate</b>.
 * </p>
 * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
 */
function imagecharup(\GdImage $image, #[LanguageLevelTypeAware(['8.1' => 'GdFont|int'], default: 'int')] $font, int $x, int $y, string $char, int $color) : bool
{
}
/**
 * Get the index of the color of a pixel
 * @link https://php.net/manual/en/function.imagecolorat.php
 * @param resource|GdImage $image
 * @param int $x <p>
 * x-coordinate of the point.
 * </p>
 * @param int $y <p>
 * y-coordinate of the point.
 * </p>
 * @return int|false the index of the color or <b>FALSE</b> on failure
 */
#[Pure]
function imagecolorat(\GdImage $image, int $x, int $y) : int|false
{
}
/**
 * Allocate a color for an image
 * @link https://php.net/manual/en/function.imagecolorallocate.php
 * @param resource|GdImage $image
 * @param int $red <p>Value of red component.</p>
 * @param int $green <p>Value of green component.</p>
 * @param int $blue <p>Value of blue component.</p>
 * @return int|false A color identifier or <b>FALSE</b> if the allocation failed.
 */
function imagecolorallocate(\GdImage $image, int $red, int $green, int $blue) : int|false
{
}
/**
 * Copy the palette from one image to another
 * @link https://php.net/manual/en/function.imagepalettecopy.php
 * @param resource|GdImage $dst <p>
 * The destination image resource.
 * </p>
 * @param resource|GdImage $src <p>
 * The source image resource.
 * </p>
 * @return void No value is returned.
 */
function imagepalettecopy(\GdImage $dst, \GdImage $src) : void
{
}
/**
 * Create a new image from the image stream in the string
 * @link https://php.net/manual/en/function.imagecreatefromstring.php
 * @param string $data <p>
 * A string containing the image data.
 * </p>
 * @return resource|GdImage|false An image resource will be returned on success. <b>FALSE</b> is returned if
 * the image type is unsupported, the data is not in a recognised format,
 * or the image is corrupt and cannot be loaded.
 */
#[Pure]
function imagecreatefromstring(string $data) : \GdImage|false
{
}
/**
 * Get the index of the closest color to the specified color
 * @link https://php.net/manual/en/function.imagecolorclosest.php
 * @param resource|GdImage $image
 * @param int $red <p>Value of red component.</p>
 * @param int $green <p>Value of green component.</p>
 * @param int $blue <p>Value of blue component.</p>
 * @return int|false the index of the closest color, in the palette of the image, to
 * the specified one or <b>FALSE</b> on failure
 */
#[Pure]
function imagecolorclosest(\GdImage $image, int $red, int $green, int $blue) : int
{
}
/**
 * Get the index of the color which has the hue, white and blackness
 * @link https://php.net/manual/en/function.imagecolorclosesthwb.php
 * @param resource|GdImage $image
 * @param int $red <p>Value of red component.</p>
 * @param int $green <p>Value of green component.</p>
 * @param int $blue <p>Value of blue component.</p>
 * @return int|false an integer with the index of the color which has
 * the hue, white and blackness nearest the given color or <b>FALSE</b> on failure
 */
#[Pure]
function imagecolorclosesthwb(\GdImage $image, int $red, int $green, int $blue) : int
{
}
/**
 * De-allocate a color for an image
 * @link https://php.net/manual/en/function.imagecolordeallocate.php
 * @param resource|GdImage $image
 * @param int $color <p>
 * The color identifier.
 * </p>
 * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
 */
function imagecolordeallocate(\GdImage $image, int $color) : bool
{
}
/**
 * Get the index of the specified color or its closest possible alternative
 * @link https://php.net/manual/en/function.imagecolorresolve.php
 * @param resource|GdImage $image
 * @param int $red <p>Value of red component.</p>
 * @param int $green <p>Value of green component.</p>
 * @param int $blue <p>Value of blue component.</p>
 * @return int|false a color index or <b>FALSE</b> on failure
 */
#[Pure]
function imagecolorresolve(\GdImage $image, int $red, int $green, int $blue) : int
{
}
/**
 * Get the index of the specified color
 * @link https://php.net/manual/en/function.imagecolorexact.php
 * @param resource|GdImage $image
 * @param int $red <p>Value of red component.</p>
 * @param int $green <p>Value of green component.</p>
 * @param int $blue <p>Value of blue component.</p>
 * @return int|false the index of the specified color in the palette, -1 if the
 * color does not exist, or <b>FALSE</b> on failure
 */
#[Pure]
function imagecolorexact(\GdImage $image, int $red, int $green, int $blue) : int
{
}
/**
 * Set the color for the specified palette index
 * @link https://php.net/manual/en/function.imagecolorset.php
 * @param resource|GdImage $image
 * @param int $color <p>
 * An index in the palette.
 * </p>
 * @param int $red <p>Value of red component.</p>
 * @param int $green <p>Value of green component.</p>
 * @param int $blue <p>Value of blue component.</p>
 * @param int $alpha [optional] <p>
 * Value of alpha component.
 * </p>
 * @return bool|null
 */
#[LanguageLevelTypeAware(['8.2' => 'null|false'], default: 'null|bool')]
function imagecolorset(\GdImage $image, int $color, int $red, int $green, int $blue, int $alpha = 0) : ?bool
{
}
/**
 * Define a color as transparent
 * @link https://php.net/manual/en/function.imagecolortransparent.php
 * @param resource|GdImage $image
 * @param int $color [optional] <p>
 * A color identifier created with
 * <b>imagecolorallocate</b>.
 * </p>
 * @return int The identifier of the new (or current, if none is specified)
 * transparent color is returned. If <i>color</i>
 * is not specified, and the image has no transparent color, the
 * returned identifier will be -1.
 */
function imagecolortransparent(\GdImage $image, ?int $color = null) : int
{
}
/**
 * Find out the number of colors in an image's palette
 * @link https://php.net/manual/en/function.imagecolorstotal.php
 * @param resource|GdImage $image <p>
 * An image resource, returned by one of the image creation functions, such
 * as <b>imagecreatefromgif</b>.
 * </p>
 * @return int|false the number of colors in the specified image's palette, 0 for
 * truecolor images, or <b>FALSE</b> on failure
 */
#[Pure]
function imagecolorstotal(\GdImage $image) : int
{
}
/**
 * Get the colors for an index
 * @link https://php.net/manual/en/function.imagecolorsforindex.php
 * @param resource|GdImage $image
 * @param int $color <p>
 * The color index.
 * </p>
 * @return array|false an associative array with red, green, blue and alpha keys that
 * contain the appropriate values for the specified color index or <b>FALSE</b> on failure
 */
#[Pure]
#[LanguageLevelTypeAware(['8.0' => 'array'], default: 'array|false')]
#[ArrayShape(["red" => "int", "green" => "int", "blue" => "int", "alpha" => "int"])]
function imagecolorsforindex(\GdImage $image, int $color)
{
}
/**
 * Copy part of an image
 * @link https://php.net/manual/en/function.imagecopy.php
 * @param resource|GdImage $dst_image <p>
 * Destination image link resource.
 * </p>
 * @param resource|GdImage $src_image <p>
 * Source image link resource.
 * </p>
 * @param int $dst_x <p>
 * x-coordinate of destination point.
 * </p>
 * @param int $dst_y <p>
 * y-coordinate of destination point.
 * </p>
 * @param int $src_x <p>
 * x-coordinate of source point.
 * </p>
 * @param int $src_y <p>
 * y-coordinate of source point.
 * </p>
 * @param int $src_width <p>
 * Source width.
 * </p>
 * @param int $src_height <p>
 * Source height.
 * </p>
 * @return bool true on success or false on failure.
 */
function imagecopy(\GdImage $dst_image, \GdImage $src_image, int $dst_x, int $dst_y, int $src_x, int $src_y, int $src_width, int $src_height) : bool
{
}
/**
 * Copy and merge part of an image
 * @link https://php.net/manual/en/function.imagecopymerge.php
 * @param resource|GdImage $dst_image <p>
 * Destination image link resource.
 * </p>
 * @param resource|GdImage $src_image <p>
 * Source image link resource.
 * </p>
 * @param int $dst_x <p>
 * x-coordinate of destination point.
 * </p>
 * @param int $dst_y <p>
 * y-coordinate of destination point.
 * </p>
 * @param int $src_x <p>
 * x-coordinate of source point.
 * </p>
 * @param int $src_y <p>
 * y-coordinate of source point.
 * </p>
 * @param int $src_width <p>
 * Source width.
 * </p>
 * @param int $src_height <p>
 * Source height.
 * </p>
 * @param int $pct <p>
 * The two images will be merged according to pct
 * which can range from 0 to 100. When pct = 0,
 * no action is taken, when 100 this function behaves identically
 * to imagecopy for pallete images, while it
 * implements alpha transparency for true colour images.
 * </p>
 * @return bool true on success or false on failure.
 */
function imagecopymerge(\GdImage $dst_image, \GdImage $src_image, int $dst_x, int $dst_y, int $src_x, int $src_y, int $src_width, int $src_height, int $pct) : bool
{
}
/**
 * Copy and merge part of an image with gray scale
 * @link https://php.net/manual/en/function.imagecopymergegray.php
 * @param resource|GdImage $dst_image <p>
 * Destination image link resource.
 * </p>
 * @param resource|GdImage $src_image <p>
 * Source image link resource.
 * </p>
 * @param int $dst_x <p>
 * x-coordinate of destination point.
 * </p>
 * @param int $dst_y <p>
 * y-coordinate of destination point.
 * </p>
 * @param int $src_x <p>
 * x-coordinate of source point.
 * </p>
 * @param int $src_y <p>
 * y-coordinate of source point.
 * </p>
 * @param int $src_width <p>
 * Source width.
 * </p>
 * @param int $src_height <p>
 * Source height.
 * </p>
 * @param int $pct <p>
 * The src_im will be changed to grayscale according
 * to pct where 0 is fully grayscale and 100 is
 * unchanged. When pct = 100 this function behaves
 * identically to imagecopy for pallete images, while
 * it implements alpha transparency for true colour images.
 * </p>
 * @return bool true on success or false on failure.
 */
function imagecopymergegray(\GdImage $dst_image, \GdImage $src_image, int $dst_x, int $dst_y, int $src_x, int $src_y, int $src_width, int $src_height, int $pct) : bool
{
}
/**
 * Copy and resize part of an image
 * @link https://php.net/manual/en/function.imagecopyresized.php
 * @param resource|GdImage $dst_image
 * @param resource|GdImage $src_image
 * @param int $dst_x <p>
 * x-coordinate of destination point.
 * </p>
 * @param int $dst_y <p>
 * y-coordinate of destination point.
 * </p>
 * @param int $src_x <p>
 * x-coordinate of source point.
 * </p>
 * @param int $src_y <p>
 * y-coordinate of source point.
 * </p>
 * @param int $dst_width <p>
 * Destination width.
 * </p>
 * @param int $dst_height <p>
 * Destination height.
 * </p>
 * @param int $src_width <p>
 * Source width.
 * </p>
 * @param int $src_height <p>
 * Source height.
 * </p>
 * @return bool true on success or false on failure.
 */
function imagecopyresized(\GdImage $dst_image, \GdImage $src_image, int $dst_x, int $dst_y, int $src_x, int $src_y, int $dst_width, int $dst_height, int $src_width, int $src_height) : bool
{
}
/**
 * Create a new palette based image
 * @link https://php.net/manual/en/function.imagecreate.php
 * @param int $width <p>
 * The image width.
 * </p>
 * @param int $height <p>
 * The image height.
 * </p>
 * @return resource|GdImage|false an image resource identifier on success, false on errors.
 */
#[Pure]
function imagecreate(int $width, int $height) : \GdImage|false
{
}
/**
 * Create a new true color image
 * @link https://php.net/manual/en/function.imagecreatetruecolor.php
 * @param int $width <p>
 * Image width.
 * </p>
 * @param int $height <p>
 * Image height.
 * </p>
 * @return resource|GdImage|false an image resource identifier on success, false on errors.
 */
#[Pure]
function imagecreatetruecolor(int $width, int $height) : \GdImage|false
{
}
/**
 * Finds whether an image is a truecolor image
 * @link https://php.net/manual/en/function.imageistruecolor.php
 * @param resource|GdImage $image
 * @return bool true if the image is truecolor, false
 * otherwise.
 */
#[Pure]
function imageistruecolor(\GdImage $image) : bool
{
}
/**
 * Convert a true color image to a palette image
 * @link https://php.net/manual/en/function.imagetruecolortopalette.php
 * @param resource|GdImage $image
 * @param bool $dither <p>
 * Indicates if the image should be dithered - if it is true then
 * dithering will be used which will result in a more speckled image but
 * with better color approximation.
 * </p>
 * @param int $num_colors <p>
 * Sets the maximum number of colors that should be retained in the palette.
 * </p>
 * @return bool true on success or false on failure.
 */
function imagetruecolortopalette(\GdImage $image, bool $dither, int $num_colors) : bool
{
}
/**
 * Set the thickness for line drawing
 * @link https://php.net/manual/en/function.imagesetthickness.php
 * @param resource|GdImage $image
 * @param int $thickness <p>
 * Thickness, in pixels.
 * </p>
 * @return bool true on success or false on failure.
 */
function imagesetthickness(\GdImage $image, int $thickness) : bool
{
}
/**
 * Draw a partial arc and fill it
 * @link https://php.net/manual/en/function.imagefilledarc.php
 * @param resource|GdImage $image
 * @param int $center_x <p>
 * x-coordinate of the center.
 * </p>
 * @param int $center_y <p>
 * y-coordinate of the center.
 * </p>
 * @param int $width <p>
 * The arc width.
 * </p>
 * @param int $height <p>
 * The arc height.
 * </p>
 * @param int $start_angle <p>
 * The arc start angle, in degrees.
 * </p>
 * @param int $end_angle <p>
 * The arc end angle, in degrees.
 * 0&deg; is located at the three-o'clock position, and the arc is drawn
 * clockwise.
 * </p>
 * @param int $color <p>
 * A color identifier created with
 * imagecolorallocate.
 * </p>
 * @param int $style <p>
 * A bitwise OR of the following possibilities:
 * IMG_ARC_PIE</p>
 * @return bool true on success or false on failure.
 */
function imagefilledarc(\GdImage $image, int $center_x, int $center_y, int $width, int $height, int $start_angle, int $end_angle, int $color, int $style) : bool
{
}
/**
 * Draw a filled ellipse
 * @link https://php.net/manual/en/function.imagefilledellipse.php
 * @param resource|GdImage $image
 * @param int $center_x <p>
 * x-coordinate of the center.
 * </p>
 * @param int $center_y <p>
 * y-coordinate of the center.
 * </p>
 * @param int $width <p>
 * The ellipse width.
 * </p>
 * @param int $height <p>
 * The ellipse height.
 * </p>
 * @param int $color <p>
 * The fill color. A color identifier created with
 * imagecolorallocate.
 * </p>
 * @return bool true on success or false on failure.
 */
function imagefilledellipse(\GdImage $image, int $center_x, int $center_y, int $width, int $height, int $color) : bool
{
}
/**
 * Set the blending mode for an image
 * @link https://php.net/manual/en/function.imagealphablending.php
 * @param resource|GdImage $image
 * @param bool $enable <p>
 * Whether to enable the blending mode or not. On true color images
 * the default value is true otherwise the default value is false
 * </p>
 * @return bool true on success or false on failure.
 */
function imagealphablending(\GdImage $image, bool $enable) : bool
{
}
/**
 * Set the flag to save full alpha channel information (as opposed to single-color transparency) when saving PNG images
 * @link https://php.net/manual/en/function.imagesavealpha.php
 * @param resource|GdImage $image
 * @param bool $enable <p>
 * Whether to save the alpha channel or not. Default to false.
 * </p>
 * @return bool true on success or false on failure.
 */
function imagesavealpha(\GdImage $image, bool $enable) : bool
{
}
/**
 * Allocate a color for an image
 * @link https://php.net/manual/en/function.imagecolorallocatealpha.php
 * @param resource|GdImage $image
 * @param int $red <p>
 * Value of red component.
 * </p>
 * @param int $green <p>
 * Value of green component.
 * </p>
 * @param int $blue <p>
 * Value of blue component.
 * </p>
 * @param int $alpha <p>
 * A value between 0 and 127.
 * 0 indicates completely opaque while
 * 127 indicates completely transparent.
 * </p>
 * @return int|false A color identifier or false if the allocation failed.
 */
function imagecolorallocatealpha(\GdImage $image, int $red, int $green, int $blue, int $alpha) : int|false
{
}
/**
 * Get the index of the specified color + alpha or its closest possible alternative
 * @link https://php.net/manual/en/function.imagecolorresolvealpha.php
 * @param resource|GdImage $image
 * @param int $red <p>
 * Value of red component.
 * </p>
 * @param int $green <p>
 * Value of green component.
 * </p>
 * @param int $blue <p>
 * Value of blue component.
 * </p>
 * @param int $alpha <p>
 * A value between 0 and 127.
 * 0 indicates completely opaque while
 * 127 indicates completely transparent.
 * </p>
 * @return int|false a color index or <b>FALSE</b> on failure
 */
#[Pure]
function imagecolorresolvealpha(\GdImage $image, int $red, int $green, int $blue, int $alpha) : int
{
}
/**
 * Get the index of the closest color to the specified color + alpha
 * @link https://php.net/manual/en/function.imagecolorclosestalpha.php
 * @param resource|GdImage $image
 * @param int $red <p>
 * Value of red component.
 * </p>
 * @param int $green <p>
 * Value of green component.
 * </p>
 * @param int $blue <p>
 * Value of blue component.
 * </p>
 * @param int $alpha <p>
 * A value between 0 and 127.
 * 0 indicates completely opaque while
 * 127 indicates completely transparent.
 * </p>
 * @return int|false the index of the closest color in the palette or
 * <b>FALSE</b> on failure
 */
#[Pure]
function imagecolorclosestalpha(\GdImage $image, int $red, int $green, int $blue, int $alpha) : int
{
}
/**
 * Get the index of the specified color + alpha
 * @link https://php.net/manual/en/function.imagecolorexactalpha.php
 * @param resource|GdImage $image
 * @param int $red <p>
 * Value of red component.
 * </p>
 * @param int $green <p>
 * Value of green component.
 * </p>
 * @param int $blue <p>
 * Value of blue component.
 * </p>
 * @param int $alpha <p>
 * A value between 0 and 127.
 * 0 indicates completely opaque while
 * 127 indicates completely transparent.
 * </p>
 * @return int|false the index of the specified color+alpha in the palette of the
 * image, -1 if the color does not exist in the image's palette, or <b>FALSE</b>
 * on failure
 */
#[Pure]
#[LanguageLevelTypeAware(['8.0' => 'int'], default: 'int|false')]
function imagecolorexactalpha(\GdImage $image, int $red, int $green, int $blue, int $alpha)
{
}
/**
 * Copy and resize part of an image with resampling
 * @link https://php.net/manual/en/function.imagecopyresampled.php
 * @param resource|GdImage $dst_image
 * @param resource|GdImage $src_image
 * @param int $dst_x <p>
 * x-coordinate of destination point.
 * </p>
 * @param int $dst_y <p>
 * y-coordinate of destination point.
 * </p>
 * @param int $src_x <p>
 * x-coordinate of source point.
 * </p>
 * @param int $src_y <p>
 * y-coordinate of source point.
 * </p>
 * @param int $dst_width <p>
 * Destination width.
 * </p>
 * @param int $dst_height <p>
 * Destination height.
 * </p>
 * @param int $src_width <p>
 * Source width.
 * </p>
 * @param int $src_height <p>
 * Source height.
 * </p>
 * @return bool true on success or false on failure.
 */
function imagecopyresampled(\GdImage $dst_image, \GdImage $src_image, int $dst_x, int $dst_y, int $src_x, int $src_y, int $dst_width, int $dst_height, int $src_width, int $src_height) : bool
{
}
/**
 * Rotate an image with a given angle
 * @link https://php.net/manual/en/function.imagerotate.php
 * @param resource|GdImage $image
 * @param float $angle <p>
 * Rotation angle, in degrees.
 * </p>
 * @param int $background_color <p>
 * Specifies the color of the uncovered zone after the rotation
 * </p>
 * @param bool $ignore_transparent [optional] <p>
 * If set and non-zero, transparent colors are ignored (otherwise kept).
 * </p>
 * @return resource|GdImage|false the rotated image or <b>FALSE</b> on failure
 */
function imagerotate(\GdImage $image, float $angle, int $background_color, bool $ignore_transparent = \false) : \GdImage|false
{
}
/**
 * Should antialias functions be used or not. <br/>
 * Before 7.2.0 it's only available if PHP iscompiled with the bundled version of the GD library.
 * @link https://php.net/manual/en/function.imageantialias.php
 * @param resource|GdImage $image
 * @param bool $enable <p>
 * Whether to enable antialiasing or not.
 * </p>
 * @return bool true on success or false on failure.
 */
function imageantialias(\GdImage $image, bool $enable) : bool
{
}
/**
 * Set the tile image for filling
 * @link https://php.net/manual/en/function.imagesettile.php
 * @param resource|GdImage $image
 * @param resource|GdImage $tile <p>
 * The image resource to be used as a tile.
 * </p>
 * @return bool true on success or false on failure.
 */
function imagesettile(\GdImage $image, \GdImage $tile) : bool
{
}
/**
 * Set the brush image for line drawing
 * @link https://php.net/manual/en/function.imagesetbrush.php
 * @param resource|GdImage $image
 * @param resource|GdImage $brush <p>
 * An image resource.
 * </p>
 * @return bool true on success or false on failure.
 */
function imagesetbrush(\GdImage $image, \GdImage $brush) : bool
{
}
/**
 * Set the style for line drawing
 * @link https://php.net/manual/en/function.imagesetstyle.php
 * @param resource|GdImage $image
 * @param int[] $style <p>
 * An array of pixel colors. You can use the
 * IMG_COLOR_TRANSPARENT constant to add a
 * transparent pixel.
 * </p>
 * @return bool true on success or false on failure.
 */
function imagesetstyle(\GdImage $image, array $style) : bool
{
}
/**
 * Create a new image from file or URL
 * @link https://php.net/manual/en/function.imagecreatefrompng.php
 * @param string $filename <p>
 * Path to the PNG image.
 * </p>
 * @return resource|GdImage|false an image resource identifier on success, false on errors.
 */
function imagecreatefrompng(string $filename) : \GdImage|false
{
}
/**
 * Create a new image from file or URL
 * @link https://www.php.net/manual/function.imagecreatefromavif.php
 * @param string $filename Path to the AVIF raster image.
 * @return GdImage|false returns an image object representing the image obtained from the given filename
 * @since 8.1
 */
function imagecreatefromavif(string $filename) : \GdImage|false
{
}
/**
 * Create a new image from file or URL
 * @link https://php.net/manual/en/function.imagecreatefromgif.php
 * @param string $filename <p>
 * Path to the GIF image.
 * </p>
 * @return resource|GdImage|false an image resource identifier on success, false on errors.
 */
function imagecreatefromgif(string $filename) : \GdImage|false
{
}
/**
 * Create a new image from file or URL
 * @link https://php.net/manual/en/function.imagecreatefromjpeg.php
 * @param string $filename <p>
 * Path to the JPEG image.
 * </p>
 * @return resource|GdImage|false an image resource identifier on success, false on errors.
 */
function imagecreatefromjpeg(string $filename) : \GdImage|false
{
}
/**
 * Create a new image from file or URL
 * @link https://php.net/manual/en/function.imagecreatefromwbmp.php
 * @param string $filename <p>
 * Path to the WBMP image.
 * </p>
 * @return resource|GdImage|false an image resource identifier on success, false on errors.
 */
function imagecreatefromwbmp(string $filename) : \GdImage|false
{
}
/**
 * Create a new image from file or URL
 * @link https://php.net/manual/en/function.imagecreatefromwebp.php
 * @param string $filename <p>
 * Path to the WebP image.
 * </p>
 * @return resource|GdImage|false an image resource identifier on success, false on errors.
 * @since 5.4
 */
function imagecreatefromwebp(string $filename) : \GdImage|false
{
}
/**
 * Create a new image from file or URL
 * @link https://php.net/manual/en/function.imagecreatefromxbm.php
 * @param string $filename <p>
 * Path to the XBM image.
 * </p>
 * @return resource|GdImage|false an image resource identifier on success, false on errors.
 */
function imagecreatefromxbm(string $filename) : \GdImage|false
{
}
/**
 * Create a new image from file or URL
 * @link https://php.net/manual/en/function.imagecreatefromxpm.php
 * @param string $filename <p>
 * Path to the XPM image.
 * </p>
 * @return resource|GdImage|false an image resource identifier on success, false on errors.
 */
function imagecreatefromxpm(string $filename) : \GdImage|false
{
}
/**
 * Create a new image from GD file or URL
 * @link https://php.net/manual/en/function.imagecreatefromgd.php
 * @param string $filename <p>
 * Path to the GD file.
 * </p>
 * @return resource|GdImage|false an image resource identifier on success, false on errors.
 */
function imagecreatefromgd(string $filename) : \GdImage|false
{
}
/**
 * Create a new image from GD2 file or URL
 * @link https://php.net/manual/en/function.imagecreatefromgd2.php
 * @param string $filename <p>
 * Path to the GD2 image.
 * </p>
 * @return resource|GdImage|false an image resource identifier on success, false on errors.
 */
function imagecreatefromgd2(string $filename) : \GdImage|false
{
}
/**
 * Create a new image from a given part of GD2 file or URL
 * @link https://php.net/manual/en/function.imagecreatefromgd2part.php
 * @param string $filename <p>
 * Path to the GD2 image.
 * </p>
 * @param int $x <p>
 * x-coordinate of source point.
 * </p>
 * @param int $y <p>
 * y-coordinate of source point.
 * </p>
 * @param int $width <p>
 * Source width.
 * </p>
 * @param int $height <p>
 * Source height.
 * </p>
 * @return resource|GdImage|false an image resource identifier on success, false on errors.
 */
function imagecreatefromgd2part(string $filename, int $x, int $y, int $width, int $height) : \GdImage|false
{
}
/**
 * Output a PNG image to either the browser or a file
 * @link https://php.net/manual/en/function.imagepng.php
 * @param resource|GdImage $image
 * @param string $file [optional] <p>
 * The path to save the file to. If not set or null, the raw image stream
 * will be outputted directly.
 * </p>
 * <p>
 * null is invalid if the quality and
 * filters arguments are not used.
 * </p>
 * @param int $quality [optional] <p>
 * Compression level: from 0 (no compression) to 9.
 * </p>
 * @param int $filters [optional] <p>
 * Allows reducing the PNG file size. It is a bitmask field which may be
 * set to any combination of the PNG_FILTER_XXX
 * constants. PNG_NO_FILTER or
 * PNG_ALL_FILTERS may also be used to respectively
 * disable or activate all filters.
 * </p>
 * @return bool true on success or false on failure.
 */
function imagepng(\GdImage $image, $file = null, int $quality = -1, int $filters = -1) : bool
{
}
/**
 * Output a WebP image to browser or file
 * @link https://php.net/manual/en/function.imagewebp.php
 * @param resource|GdImage $image
 * @param string $to [optional] <p>
 * The path to save the file to. If not set or null, the raw image stream
 * will be outputted directly.
 * </p>
 * @param int $quality [optional] <p>
 * quality ranges from 0 (worst quality, smaller file) to 100 (best quality, biggest file).
 * </p>
 * @return bool true on success or false on failure.
 * @since 5.4
 */
function imagewebp($image, $to = null, $quality = 80) : bool
{
}
/**
 * Output image to browser or file
 * @link https://php.net/manual/en/function.imagegif.php
 * @param resource|GdImage $image
 * @param string $file [optional] <p>
 * The path to save the file to. If not set or null, the raw image stream
 * will be outputted directly.
 * </p>
 * @return bool true on success or false on failure.
 */
function imagegif(\GdImage $image, $file = null) : bool
{
}
/**
 * Output image to browser or file
 * @link https://php.net/manual/en/function.imagejpeg.php
 * @param resource|GdImage $image
 * @param string $filename [optional] <p>
 * The path to save the file to. If not set or null, the raw image stream
 * will be outputted directly.
 * </p>
 * <p>
 * To skip this argument in order to provide the
 * quality parameter, use null.
 * </p>
 * @param int $quality [optional] <p>
 * quality is optional, and ranges from 0 (worst
 * quality, smaller file) to 100 (best quality, biggest file). The
 * default is the default IJG quality value (about 75).
 * </p>
 * @return bool true on success or false on failure.
 */
function imagejpeg($image, $filename = null, $quality = null) : bool
{
}
/**
 * Output image to browser or file
 * @link https://php.net/manual/en/function.imagewbmp.php
 * @param resource|GdImage $image
 * @param string $file [optional] <p>
 * The path to save the file to. If not set or null, the raw image stream
 * will be outputted directly.
 * </p>
 * @param int $foreground_color [optional] <p>
 * You can set the foreground color with this parameter by setting an
 * identifier obtained from imagecolorallocate.
 * The default foreground color is black.
 * </p>
 * @return bool true on success or false on failure.
 */
function imagewbmp(\GdImage $image, $file = null, ?int $foreground_color = null) : bool
{
}
/**
 * Output GD image to browser or file. <br/>
 * Since 7.2.0 allows to output truecolor images.
 * @link https://php.net/manual/en/function.imagegd.php
 * @param resource|GdImage $image
 * @param string|null $file [optional] <p>
 * The path to save the file to. If not set or null, the raw image stream
 * will be outputted directly.
 * </p>
 * @return bool true on success or false on failure.
 */
function imagegd(\GdImage $image, ?string $file = null) : bool
{
}
/**
 * Output GD2 image to browser or file
 * @link https://php.net/manual/en/function.imagegd2.php
 * @param resource|GdImage $image
 * @param string|null $file [optional] <p>
 * The path to save the file to. If not set or null, the raw image stream
 * will be outputted directly.
 * </p>
 * @param int $chunk_size [optional] <p>
 * Chunk size.
 * </p>
 * @param int $mode [optional] <p>
 * Either IMG_GD2_RAW or
 * IMG_GD2_COMPRESSED. Default is
 * IMG_GD2_RAW.
 * </p>
 * @return bool true on success or false on failure.
 */
function imagegd2(\GdImage $image, ?string $file = null, int $chunk_size = null, int $mode = null) : bool
{
}
/**
 * Destroy an image
 * @link https://php.net/manual/en/function.imagedestroy.php
 * @param resource|GdImage $image
 * @return bool true on success or false on failure.
 */
function imagedestroy(\GdImage $image) : bool
{
}
/**
 * Apply a gamma correction to a GD image
 * @link https://php.net/manual/en/function.imagegammacorrect.php
 * @param resource|GdImage $image
 * @param float $input_gamma <p>
 * The input gamma.
 * </p>
 * @param float $output_gamma <p>
 * The output gamma.
 * </p>
 * @return bool true on success or false on failure.
 */
function imagegammacorrect(\GdImage $image, float $input_gamma, float $output_gamma) : bool
{
}
/**
 * Flood fill
 * @link https://php.net/manual/en/function.imagefill.php
 * @param resource|GdImage $image
 * @param int $x <p>
 * x-coordinate of start point.
 * </p>
 * @param int $y <p>
 * y-coordinate of start point.
 * </p>
 * @param int $color <p>
 * The fill color. A color identifier created with
 * imagecolorallocate.
 * </p>
 * @return bool true on success or false on failure.
 */
function imagefill(\GdImage $image, int $x, int $y, int $color) : bool
{
}
/**
 * Draw a filled polygon
 * @link https://php.net/manual/en/function.imagefilledpolygon.php
 * @param resource|GdImage $image
 * @param int[] $points <p>
 * An array containing the x and y
 * coordinates of the polygons vertices consecutively.
 * </p>
 * @param int $num_points_or_color <p>
 * Total number of vertices, which must be at least 3.
 * </p>
 * @param int|null $color <p>
 * A color identifier created with
 * imagecolorallocate.
 * </p>
 * @return bool true on success or false on failure.
 */
function imagefilledpolygon(\GdImage $image, array $points, #[Deprecated(since: "8.1")] int $num_points_or_color, #[PhpStormStubsElementAvailable(from: '5.3', to: '7.4')] ?int $color, #[PhpStormStubsElementAvailable(from: '8.0')] ?int $color = null) : bool
{
}
/**
 * Draw a filled rectangle
 * @link https://php.net/manual/en/function.imagefilledrectangle.php
 * @param resource|GdImage $image
 * @param int $x1 <p>
 * x-coordinate for point 1.
 * </p>
 * @param int $y1 <p>
 * y-coordinate for point 1.
 * </p>
 * @param int $x2 <p>
 * x-coordinate for point 2.
 * </p>
 * @param int $y2 <p>
 * y-coordinate for point 2.
 * </p>
 * @param int $color <p>
 * The fill color. A color identifier created with
 * imagecolorallocate.
 * </p>
 * @return bool true on success or false on failure.
 */
function imagefilledrectangle(\GdImage $image, int $x1, int $y1, int $x2, int $y2, int $color) : bool
{
}
/**
 * Flood fill to specific color
 * @link https://php.net/manual/en/function.imagefilltoborder.php
 * @param resource|GdImage $image
 * @param int $x <p>
 * x-coordinate of start.
 * </p>
 * @param int $y <p>
 * y-coordinate of start.
 * </p>
 * @param int $border_color <p>
 * The border color. A color identifier created with
 * imagecolorallocate.
 * </p>
 * @param int $color <p>
 * The fill color. A color identifier created with
 * imagecolorallocate.
 * </p>
 * @return bool true on success or false on failure.
 */
function imagefilltoborder(\GdImage $image, int $x, int $y, int $border_color, int $color) : bool
{
}
/**
 * Get font width
 * @link https://php.net/manual/en/function.imagefontwidth.php
 * @param int $font
 * @return int the width of the pixel
 */
#[Pure]
function imagefontwidth(#[LanguageLevelTypeAware(['8.1' => 'GdFont|int'], default: 'int')] $font) : int
{
}
/**
 * Get font height
 * @link https://php.net/manual/en/function.imagefontheight.php
 * @param int $font
 * @return int the height of the pixel.
 */
#[Pure]
function imagefontheight(#[LanguageLevelTypeAware(['8.1' => 'GdFont|int'], default: 'int')] $font) : int
{
}
/**
 * Enable or disable interlace
 * @link https://php.net/manual/en/function.imageinterlace.php
 * @param resource|GdImage $image
 * @param bool|null $enable [optional] <p>
 * If non-zero, the image will be interlaced, else the interlace bit is
 * turned off.
 * </p>
 * @return bool 1 if the interlace bit is set for the image,
 * 0 if it is not
 */
function imageinterlace(\GdImage $image, ?bool $enable = null) : bool
{
}
/**
 * Draw a line
 * @link https://php.net/manual/en/function.imageline.php
 * @param resource|GdImage $image
 * @param int $x1 <p>
 * x-coordinate for first point.
 * </p>
 * @param int $y1 <p>
 * y-coordinate for first point.
 * </p>
 * @param int $x2 <p>
 * x-coordinate for second point.
 * </p>
 * @param int $y2 <p>
 * y-coordinate for second point.
 * </p>
 * @param int $color <p>
 * The line color. A color identifier created with
 * imagecolorallocate.
 * </p>
 * @return bool true on success or false on failure.
 */
function imageline(\GdImage $image, int $x1, int $y1, int $x2, int $y2, int $color) : bool
{
}
/**
 * Load a new font
 * @link https://php.net/manual/en/function.imageloadfont.php
 * @param string $filename <p>
 * The font file format is currently binary and architecture
 * dependent. This means you should generate the font files on the
 * same type of CPU as the machine you are running PHP on.
 * </p>
 * <p>
 * <table>
 * Font file format
 * <tr valign="top">
 * <td>byte position</td>
 * <td>C data type</td>
 * <td>description</td>
 * </tr>
 * <tr valign="top">
 * <td>byte 0-3</td>
 * <td>int</td>
 * <td>number of characters in the font</td>
 * </tr>
 * <tr valign="top">
 * <td>byte 4-7</td>
 * <td>int</td>
 * <td>
 * value of first character in the font (often 32 for space)
 * </td>
 * </tr>
 * <tr valign="top">
 * <td>byte 8-11</td>
 * <td>int</td>
 * <td>pixel width of each character</td>
 * </tr>
 * <tr valign="top">
 * <td>byte 12-15</td>
 * <td>int</td>
 * <td>pixel height of each character</td>
 * </tr>
 * <tr valign="top">
 * <td>byte 16-</td>
 * <td>char</td>
 * <td>
 * array with character data, one byte per pixel in each
 * character, for a total of (nchars*width*height) bytes.
 * </td>
 * </tr>
 * </table>
 * </p>
 * @return int|false The font identifier which is always bigger than 5 to avoid conflicts with
 * built-in fonts or false on errors.
 */
#[LanguageLevelTypeAware(['8.1' => 'GdFont|false'], default: 'int|false')]
function imageloadfont(string $filename)
{
}
/**
 * Draws a polygon
 * @link https://php.net/manual/en/function.imagepolygon.php
 * @param resource|GdImage $image
 * @param int[] $points <p>
 * An array containing the polygon's vertices, e.g.:
 * <tr valign="top">
 * <td>points[0]</td>
 * <td>= x0</td>
 * </tr>
 * <tr valign="top">
 * <td>points[1]</td>
 * <td>= y0</td>
 * </tr>
 * <tr valign="top">
 * <td>points[2]</td>
 * <td>= x1</td>
 * </tr>
 * <tr valign="top">
 * <td>points[3]</td>
 * <td>= y1</td>
 * </tr>
 * </p>
 * @param int $num_points_or_color <p>
 * Total number of points (vertices).
 * </p>
 * @param int|null $color <p>
 * A color identifier created with
 * imagecolorallocate.
 * </p>
 * @return bool true on success or false on failure.
 */
function imagepolygon(\GdImage $image, array $points, int $num_points_or_color, #[PhpStormStubsElementAvailable(from: '5.3', to: '7.4')] ?int $color, #[PhpStormStubsElementAvailable(from: '8.0')] ?int $color = null) : bool
{
}
/**
 * Draw a rectangle
 * @link https://php.net/manual/en/function.imagerectangle.php
 * @param resource|GdImage $image
 * @param int $x1 <p>
 * Upper left x coordinate.
 * </p>
 * @param int $y1 <p>
 * Upper left y coordinate
 * 0, 0 is the top left corner of the image.
 * </p>
 * @param int $x2 <p>
 * Bottom right x coordinate.
 * </p>
 * @param int $y2 <p>
 * Bottom right y coordinate.
 * </p>
 * @param int $color <p>
 * A color identifier created with
 * imagecolorallocate.
 * </p>
 * @return bool true on success or false on failure.
 */
function imagerectangle(\GdImage $image, int $x1, int $y1, int $x2, int $y2, int $color) : bool
{
}
/**
 * Set a single pixel
 * @link https://php.net/manual/en/function.imagesetpixel.php
 * @param resource|GdImage $image
 * @param int $x <p>
 * x-coordinate.
 * </p>
 * @param int $y <p>
 * y-coordinate.
 * </p>
 * @param int $color <p>
 * A color identifier created with
 * imagecolorallocate.
 * </p>
 * @return bool true on success or false on failure.
 */
function imagesetpixel(\GdImage $image, int $x, int $y, int $color) : bool
{
}
/**
 * Draw a string horizontally
 * @link https://php.net/manual/en/function.imagestring.php
 * @param resource|GdImage $image
 * @param int $font
 * @param int $x <p>
 * x-coordinate of the upper left corner.
 * </p>
 * @param int $y <p>
 * y-coordinate of the upper left corner.
 * </p>
 * @param string $string <p>
 * The string to be written.
 * </p>
 * @param int $color <p>
 * A color identifier created with
 * imagecolorallocate.
 * </p>
 * @return bool true on success or false on failure.
 */
function imagestring(\GdImage $image, #[LanguageLevelTypeAware(['8.1' => 'GdFont|int'], default: 'int')] $font, int $x, int $y, string $string, int $color) : bool
{
}
/**
 * Draw a string vertically
 * @link https://php.net/manual/en/function.imagestringup.php
 * @param resource|GdImage $image
 * @param int $font
 * @param int $x <p>
 * x-coordinate of the upper left corner.
 * </p>
 * @param int $y <p>
 * y-coordinate of the upper left corner.
 * </p>
 * @param string $string <p>
 * The string to be written.
 * </p>
 * @param int $color <p>
 * A color identifier created with
 * imagecolorallocate.
 * </p>
 * @return bool true on success or false on failure.
 */
function imagestringup(\GdImage $image, #[LanguageLevelTypeAware(['8.1' => 'GdFont|int'], default: 'int')] $font, int $x, int $y, string $string, int $color) : bool
{
}
/**
 * Get image width
 * @link https://php.net/manual/en/function.imagesx.php
 * @param resource|GdImage $image
 * @return int|false Return the width of the image or false on
 * errors.
 */
#[Pure]
function imagesx(\GdImage $image) : int
{
}
/**
 * Get image height
 * @link https://php.net/manual/en/function.imagesy.php
 * @param resource|GdImage $image
 * @return int|false Return the height of the image or false on
 * errors.
 */
#[Pure]
function imagesy(\GdImage $image) : int
{
}
/**
 * Draw a dashed line
 * @link https://php.net/manual/en/function.imagedashedline.php
 * @param resource|GdImage $image
 * @param int $x1 <p>
 * Upper left x coordinate.
 * </p>
 * @param int $y1 <p>
 * Upper left y coordinate 0, 0 is the top left corner of the image.
 * </p>
 * @param int $x2 <p>
 * Bottom right x coordinate.
 * </p>
 * @param int $y2 <p>
 * Bottom right y coordinate.
 * </p>
 * @param int $color <p>
 * The fill color. A color identifier created with
 * imagecolorallocate.
 * </p>
 * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
 * @see imagesetstyle()
 * @see imageline()
 */
#[Deprecated("Use combination of imagesetstyle() and imageline() instead")]
function imagedashedline(\GdImage $image, int $x1, int $y1, int $x2, int $y2, int $color) : bool
{
}
/**
 * Give the bounding box of a text using TrueType fonts
 * @link https://php.net/manual/en/function.imagettfbbox.php
 * @param float $size <p>
 * The font size. Depending on your version of GD, this should be
 * specified as the pixel size (GD1) or point size (GD2).
 * </p>
 * @param float $angle <p>
 * Angle in degrees in which text will be measured.
 * </p>
 * @param string $font_filename <p>
 * The name of the TrueType font file (can be a URL). Depending on
 * which version of the GD library that PHP is using, it may attempt to
 * search for files that do not begin with a leading '/' by appending
 * '.ttf' to the filename and searching along a library-defined font path.
 * </p>
 * @param string $text <p>
 * The string to be measured.
 * </p>
 * @return array|false imagettfbbox returns an array with 8
 * elements representing four points making the bounding box of the
 * text on success and false on error.
 * <tr valign="top">
 * <td>key</td>
 * <td>contents</td>
 * </tr>
 * <tr valign="top">
 * <td>0</td>
 * <td>lower left corner, X position</td>
 * </tr>
 * <tr valign="top">
 * <td>1</td>
 * <td>lower left corner, Y position</td>
 * </tr>
 * <tr valign="top">
 * <td>2</td>
 * <td>lower right corner, X position</td>
 * </tr>
 * <tr valign="top">
 * <td>3</td>
 * <td>lower right corner, Y position</td>
 * </tr>
 * <tr valign="top">
 * <td>4</td>
 * <td>upper right corner, X position</td>
 * </tr>
 * <tr valign="top">
 * <td>5</td>
 * <td>upper right corner, Y position</td>
 * </tr>
 * <tr valign="top">
 * <td>6</td>
 * <td>upper left corner, X position</td>
 * </tr>
 * <tr valign="top">
 * <td>7</td>
 * <td>upper left corner, Y position</td>
 * </tr>
 * </p>
 * <p>
 * The points are relative to the text regardless of the
 * angle, so "upper left" means in the top left-hand
 * corner seeing the text horizontally.
 */
#[Pure]
function imagettfbbox($size, $angle, $font_filename, $text)
{
}
/**
 * Write text to the image using TrueType fonts
 * @link https://php.net/manual/en/function.imagettftext.php
 * @param resource|GdImage $image
 * @param float $size <p>
 * The font size. Depending on your version of GD, this should be
 * specified as the pixel size (GD1) or point size (GD2).
 * </p>
 * @param float $angle <p>
 * The angle in degrees, with 0 degrees being left-to-right reading text.
 * Higher values represent a counter-clockwise rotation. For example, a
 * value of 90 would result in bottom-to-top reading text.
 * </p>
 * @param int $x <p>
 * The coordinates given by x and
 * y will define the basepoint of the first
 * character (roughly the lower-left corner of the character). This
 * is different from the imagestring, where
 * x and y define the
 * upper-left corner of the first character. For example, "top left"
 * is 0, 0.
 * </p>
 * @param int $y <p>
 * The y-ordinate. This sets the position of the fonts baseline, not the
 * very bottom of the character.
 * </p>
 * @param int $color <p>
 * The color index. Using the negative of a color index has the effect of
 * turning off antialiasing. See imagecolorallocate.
 * </p>
 * @param string $font_filename <p>
 * The path to the TrueType font you wish to use.
 * </p>
 * <p>
 * Depending on which version of the GD library PHP is using, when
 * font_filename does not begin with a leading
 * / then .ttf will be appended
 * to the filename and the library will attempt to search for that
 * filename along a library-defined font path.
 * </p>
 * <p>
 * When using versions of the GD library lower than 2.0.18, a space character,
 * rather than a semicolon, was used as the 'path separator' for different font files.
 * Unintentional use of this feature will result in the warning message:
 * Warning: Could not find/open font. For these affected versions, the
 * only solution is moving the font to a path which does not contain spaces.
 * </p>
 * <p>
 * In many cases where a font resides in the same directory as the script using it
 * the following trick will alleviate any include problems.
 * </p>
 * <pre>
 * <?php
 * // Set the enviroment variable for GD
 * putenv('GDFONTPATH=' . realpath('.'));
 *
 * // Name the font to be used (note the lack of the .ttf extension)
 * $font = 'SomeFont';
 * ?>
 * </pre>
 * <p>
 * <strong>Note:</strong>
 * <code>open_basedir</code> does <em>not</em> apply to font_filename.
 * </p>
 * @param string $text <p>
 * The text string in UTF-8 encoding.
 * </p>
 * <p>
 * May include decimal numeric character references (of the form:
 * &amp;#8364;) to access characters in a font beyond position 127.
 * The hexadecimal format (like &amp;#xA9;) is supported.
 * Strings in UTF-8 encoding can be passed directly.
 * </p>
 * <p>
 * Named entities, such as &amp;copy;, are not supported. Consider using
 * html_entity_decode
 * to decode these named entities into UTF-8 strings (html_entity_decode()
 * supports this as of PHP 5.0.0).
 * </p>
 * <p>
 * If a character is used in the string which is not supported by the
 * font, a hollow rectangle will replace the character.
 * </p>
 * @return array|false an array with 8 elements representing four points making the
 * bounding box of the text. The order of the points is lower left, lower
 * right, upper right, upper left. The points are relative to the text
 * regardless of the angle, so "upper left" means in the top left-hand
 * corner when you see the text horizontally.
 * Returns false on error.
 */
function imagettftext(\GdImage $image, float $size, float $angle, int $x, int $y, int $color, string $font_filename, string $text, array $options = []) : array|false
{
}
/**
 * Give the bounding box of a text using fonts via freetype2
 * @link https://php.net/manual/en/function.imageftbbox.php
 * @param float $size <p>
 * The font size. Depending on your version of GD, this should be
 * specified as the pixel size (GD1) or point size (GD2).
 * </p>
 * @param float $angle <p>
 * Angle in degrees in which text will be
 * measured.
 * </p>
 * @param string $font_filename <p>
 * The name of the TrueType font file (can be a URL). Depending on
 * which version of the GD library that PHP is using, it may attempt to
 * search for files that do not begin with a leading '/' by appending
 * '.ttf' to the filename and searching along a library-defined font path.
 * </p>
 * @param string $text <p>
 * The string to be measured.
 * </p>
 * @param array $extrainfo [optional] <p>
 * <table>
 * Possible array indexes for extrainfo
 * <tr valign="top">
 * <td>Key</td>
 * <td>Type</td>
 * <td>Meaning</td>
 * </tr>
 * <tr valign="top">
 * <td>linespacing</td>
 * <td>float</td>
 * <td>Defines drawing linespacing</td>
 * </tr>
 * </table>
 * </p>
 * @return array|false imageftbbox returns an array with 8
 * elements representing four points making the bounding box of the
 * text:
 * <tr valign="top">
 * <td>0</td>
 * <td>lower left corner, X position</td>
 * </tr>
 * <tr valign="top">
 * <td>1</td>
 * <td>lower left corner, Y position</td>
 * </tr>
 * <tr valign="top">
 * <td>2</td>
 * <td>lower right corner, X position</td>
 * </tr>
 * <tr valign="top">
 * <td>3</td>
 * <td>lower right corner, Y position</td>
 * </tr>
 * <tr valign="top">
 * <td>4</td>
 * <td>upper right corner, X position</td>
 * </tr>
 * <tr valign="top">
 * <td>5</td>
 * <td>upper right corner, Y position</td>
 * </tr>
 * <tr valign="top">
 * <td>6</td>
 * <td>upper left corner, X position</td>
 * </tr>
 * <tr valign="top">
 * <td>7</td>
 * <td>upper left corner, Y position</td>
 * </tr>
 * </p>
 * <p>
 * The points are relative to the text regardless of the
 * angle, so "upper left" means in the top left-hand
 * corner seeing the text horizontally.
 * Returns false on error.
 */
#[Pure]
function imageftbbox($size, $angle, $font_filename, $text, $extrainfo = null)
{
}
/**
 * Write text to the image using fonts using FreeType 2
 * @link https://php.net/manual/en/function.imagefttext.php
 * @param resource|GdImage $image
 * @param float $size <p>
 * The font size to use in points.
 * </p>
 * @param float $angle <p>
 * The angle in degrees, with 0 degrees being left-to-right reading text.
 * Higher values represent a counter-clockwise rotation. For example, a
 * value of 90 would result in bottom-to-top reading text.
 * </p>
 * @param int $x <p>
 * The coordinates given by x and
 * y will define the basepoint of the first
 * character (roughly the lower-left corner of the character). This
 * is different from the imagestring, where
 * x and y define the
 * upper-left corner of the first character. For example, "top left"
 * is 0, 0.
 * </p>
 * @param int $y <p>
 * The y-ordinate. This sets the position of the fonts baseline, not the
 * very bottom of the character.
 * </p>
 * @param int $color <p>
 * The index of the desired color for the text, see
 * imagecolorexact.
 * </p>
 * @param string $font_filename <p>
 * The path to the TrueType font you wish to use.
 * </p>
 * <p>
 * Depending on which version of the GD library PHP is using, when
 * font_filename does not begin with a leading
 * / then .ttf will be appended
 * to the filename and the library will attempt to search for that
 * filename along a library-defined font path.
 * </p>
 * <p>
 * When using versions of the GD library lower than 2.0.18, a space character,
 * rather than a semicolon, was used as the 'path separator' for different font files.
 * Unintentional use of this feature will result in the warning message:
 * Warning: Could not find/open font. For these affected versions, the
 * only solution is moving the font to a path which does not contain spaces.
 * </p>
 * <p>
 * In many cases where a font resides in the same directory as the script using it
 * the following trick will alleviate any include problems.
 * </p>
 * <pre>
 * <?php
 * // Set the enviroment variable for GD
 * putenv('GDFONTPATH=' . realpath('.'));
 *
 * // Name the font to be used (note the lack of the .ttf extension)
 * $font = 'SomeFont';
 * ?>
 * </pre>
 * <p>
 * <strong>Note:</strong>
 * <code>open_basedir</code> does <em>not</em> apply to font_filename.
 * </p>
 * @param string $text <p>
 * Text to be inserted into image.
 * </p>
 * @param array $extrainfo [optional] <p>
 * <table>
 * Possible array indexes for extrainfo
 * <tr valign="top">
 * <td>Key</td>
 * <td>Type</td>
 * <td>Meaning</td>
 * </tr>
 * <tr valign="top">
 * <td>linespacing</td>
 * <td>float</td>
 * <td>Defines drawing linespacing</td>
 * </tr>
 * </table>
 * </p>
 * @return array|false This function returns an array defining the four points of the box, starting in the lower left and moving counter-clockwise:
 * <tr valign="top">
 * <td>0</td>
 * <td>lower left x-coordinate</td>
 * </tr>
 * <tr valign="top">
 * <td>1</td>
 * <td>lower left y-coordinate</td>
 * </tr>
 * <tr valign="top">
 * <td>2</td>
 * <td>lower right x-coordinate</td>
 * </tr>
 * <tr valign="top">
 * <td>3</td>
 * <td>lower right y-coordinate</td>
 * </tr>
 * <tr valign="top">
 * <td>4</td>
 * <td>upper right x-coordinate</td>
 * </tr>
 * <tr valign="top">
 * <td>5</td>
 * <td>upper right y-coordinate</td>
 * </tr>
 * <tr valign="top">
 * <td>6</td>
 * <td>upper left x-coordinate</td>
 * </tr>
 * <tr valign="top">
 * <td>7</td>
 * <td>upper left y-coordinate</td>
 * </tr>
 * Returns false on error.
 */
function imagefttext($image, $size, $angle, $x, $y, $color, $font_filename, $text, $extrainfo = null)
{
}
/**
 * Load a PostScript Type 1 font from file
 * @link https://php.net/manual/en/function.imagepsloadfont.php
 * @param string $filename <p>
 * Path to the Postscript font file.
 * </p>
 * @return resource|GdImage|false In the case everything went right, a valid font index will be returned and
 * can be used for further purposes. Otherwise the function returns false.
 * @removed 7.0 This function was REMOVED in PHP 7.0.0.
 */
function imagepsloadfont($filename)
{
}
/**
 * Free memory used by a PostScript Type 1 font
 * @link https://php.net/manual/en/function.imagepsfreefont.php
 * @param resource|GdImage $font_index <p>
 * A font resource, returned by imagepsloadfont.
 * </p>
 * @return bool true on success or false on failure.
 * @removed 7.0
 */
function imagepsfreefont($font_index)
{
}
/**
 * Change the character encoding vector of a font
 * @link https://php.net/manual/en/function.imagepsencodefont.php
 * @param resource|GdImage $font_index <p>
 * A font resource, returned by imagepsloadfont.
 * </p>
 * @param string $encodingfile <p>
 * The exact format of this file is described in T1libs documentation.
 * T1lib comes with two ready-to-use files,
 * IsoLatin1.enc and
 * IsoLatin2.enc.
 * </p>
 * @return bool true on success or false on failure.
 * @removed 7.0
 */
function imagepsencodefont($font_index, $encodingfile)
{
}
/**
 * Extend or condense a font
 * @link https://php.net/manual/en/function.imagepsextendfont.php
 * @param resource|GdImage $font_index <p>
 * A font resource, returned by imagepsloadfont.
 * </p>
 * @param float $extend <p>
 * Extension value, must be greater than 0.
 * </p>
 * @return bool true on success or false on failure.
 * @removed 7.0
 */
function imagepsextendfont($font_index, $extend)
{
}
/**
 * Slant a font
 * @link https://php.net/manual/en/function.imagepsslantfont.php
 * @param resource|GdImage $font_index <p>
 * A font resource, returned by imagepsloadfont.
 * </p>
 * @param float $slant <p>
 * Slant level.
 * </p>
 * @return bool true on success or false on failure.
 * @removed 7.0 This function was REMOVED in PHP 7.0.0.
 */
function imagepsslantfont($font_index, $slant)
{
}
/**
 * Draws a text over an image using PostScript Type1 fonts
 * @link https://php.net/manual/en/function.imagepstext.php
 * @param resource|GdImage $image
 * @param string $text <p>
 * The text to be written.
 * </p>
 * @param resource|GdImage $font_index <p>
 * A font resource, returned by imagepsloadfont.
 * </p>
 * @param int $size <p>
 * size is expressed in pixels.
 * </p>
 * @param int $foreground <p>
 * The color in which the text will be painted.
 * </p>
 * @param int $background <p>
 * The color to which the text will try to fade in with antialiasing.
 * No pixels with the color background are
 * actually painted, so the background image does not need to be of solid
 * color.
 * </p>
 * @param int $x <p>
 * x-coordinate for the lower-left corner of the first character.
 * </p>
 * @param int $y <p>
 * y-coordinate for the lower-left corner of the first character.
 * </p>
 * @param int $space [optional] <p>
 * Allows you to change the default value of a space in a font. This
 * amount is added to the normal value and can also be negative.
 * Expressed in character space units, where 1 unit is 1/1000th of an
 * em-square.
 * </p>
 * @param int $tightness [optional] <p>
 * tightness allows you to control the amount
 * of white space between characters. This amount is added to the
 * normal character width and can also be negative.
 * Expressed in character space units, where 1 unit is 1/1000th of an
 * em-square.
 * </p>
 * @param float $angle [optional] <p>
 * angle is in degrees.
 * </p>
 * @param int $antialias_steps [optional] <p>
 * Allows you to control the number of colours used for antialiasing
 * text. Allowed values are 4 and 16. The higher value is recommended
 * for text sizes lower than 20, where the effect in text quality is
 * quite visible. With bigger sizes, use 4. It's less computationally
 * intensive.
 * </p>
 * @return array|false This function returns an array containing the following elements:
 * <tr valign="top">
 * <td>0</td>
 * <td>lower left x-coordinate</td>
 * </tr>
 * <tr valign="top">
 * <td>1</td>
 * <td>lower left y-coordinate</td>
 * </tr>
 * <tr valign="top">
 * <td>2</td>
 * <td>upper right x-coordinate</td>
 * </tr>
 * <tr valign="top">
 * <td>3</td>
 * <td>upper right y-coordinate</td>
 * </tr>
 * Returns false on error.
 * @removed 7.0 This function was REMOVED in PHP 7.0.0.
 */
function imagepstext($image, $text, $font_index, $size, $foreground, $background, $x, $y, $space = null, $tightness = null, $angle = null, $antialias_steps = null)
{
}
/**
 * Give the bounding box of a text rectangle using PostScript Type1 fonts
 * @link https://php.net/manual/en/function.imagepsbbox.php
 * @param string $text <p>
 * The text to be written.
 * </p>
 * @param resource|GdImage $font
 * @param int $size <p>
 * size is expressed in pixels.
 * </p>
 * @return array|false an array containing the following elements:
 * <tr valign="top">
 * <td>0</td>
 * <td>left x-coordinate</td>
 * </tr>
 * <tr valign="top">
 * <td>1</td>
 * <td>upper y-coordinate</td>
 * </tr>
 * <tr valign="top">
 * <td>2</td>
 * <td>right x-coordinate</td>
 * </tr>
 * <tr valign="top">
 * <td>3</td>
 * <td>lower y-coordinate</td>
 * </tr>
 * Returns false on error.
 * @removed 7.0
 */
function imagepsbbox($text, $font, $size)
{
}
/**
 * Return the image types supported by this PHP build
 * @link https://php.net/manual/en/function.imagetypes.php
 * @return int a bit-field corresponding to the image formats supported by the
 * version of GD linked into PHP. The following bits are returned,
 * IMG_BMP | IMG_GIF | IMG_JPG | IMG_PNG | IMG_WBMP | IMG_XPM | IMG_WEBP
 */
#[Pure]
function imagetypes() : int
{
}
/**
 * Convert JPEG image file to WBMP image file
 * @link https://php.net/manual/en/function.jpeg2wbmp.php
 * @param string $jpegname <p>
 * Path to JPEG file.
 * </p>
 * @param string $wbmpname <p>
 * Path to destination WBMP file.
 * </p>
 * @param int $dest_height <p>
 * Destination image height.
 * </p>
 * @param int $dest_width <p>
 * Destination image width.
 * </p>
 * @param int $threshold <p>
 * Threshold value, between 0 and 8 (inclusive).
 * </p>
 * @return bool true on success or false on failure.
 * @removed 8.0
 * @see imagecreatefromjpeg()
 */
#[Deprecated(reason: "Use imagecreatefromjpeg() and imagewbmp() instead", since: "7.2")]
function jpeg2wbmp($jpegname, $wbmpname, $dest_height, $dest_width, $threshold)
{
}
/**
 * Convert PNG image file to WBMP image file
 * @link https://php.net/manual/en/function.png2wbmp.php
 * @param string $pngname <p>
 * Path to PNG file.
 * </p>
 * @param string $wbmpname <p>
 * Path to destination WBMP file.
 * </p>
 * @param int $dest_height <p>
 * Destination image height.
 * </p>
 * @param int $dest_width <p>
 * Destination image width.
 * </p>
 * @param int $threshold <p>
 * Threshold value, between 0 and 8 (inclusive).
 * </p>
 * @return bool true on success or false on failure.
 * @removed 8.0
 * @see imagecreatefrompng()
 * @see imagewbmp()
 */
#[Deprecated("Use imagecreatefrompng() and imagewbmp() instead", since: "7.2")]
function png2wbmp($pngname, $wbmpname, $dest_height, $dest_width, $threshold)
{
}
/**
 * Output image to browser or file
 * @link https://php.net/manual/en/function.image2wbmp.php
 * @param resource|GdImage $image
 * @param string $filename [optional] <p>
 * Path to the saved file. If not given, the raw image stream will be
 * outputted directly.
 * </p>
 * @param int $threshold [optional] <p>
 * Threshold value, between 0 and 255 (inclusive).
 * </p>
 * @return bool true on success or false on failure.
 * @removed 8.0
 * @see imagewbmp()
 */
#[Deprecated(replacement: "imagewbmp(%parametersList%)", since: "7.3")]
function image2wbmp($image, $filename = null, $threshold = null)
{
}
/**
 * Set the alpha blending flag to use the bundled libgd layering effects
 * @link https://php.net/manual/en/function.imagelayereffect.php
 * @param resource|GdImage $image
 * @param int $effect <p>
 * One of the following constants:
 * IMG_EFFECT_REPLACE
 * Use pixel replacement (equivalent of passing true to
 * imagealphablending)</p>
 * @return bool true on success or false on failure.
 */
function imagelayereffect(\GdImage $image, int $effect) : bool
{
}
/**
 * Makes the colors of the palette version of an image more closely match the true color version
 * @link https://php.net/manual/en/function.imagecolormatch.php
 * @param resource|GdImage $image1 <p>
 * A truecolor image link resource.
 * </p>
 * @param resource|GdImage $image2 <p>
 * A palette image link resource pointing to an image that has the same
 * size as image1.
 * </p>
 * @return bool true on success or false on failure.
 */
function imagecolormatch(\GdImage $image1, \GdImage $image2) : bool
{
}
/**
 * Output XBM image to browser or file
 * @link https://php.net/manual/en/function.imagexbm.php
 * @param resource|GdImage $image
 * @param string|null $filename <p>
 * The path to save the file to. If not set or null, the raw image stream
 * will be outputted directly.
 * </p>
 * @param int|null $foreground_color [optional] <p>
 * You can set the foreground color with this parameter by setting an
 * identifier obtained from imagecolorallocate.
 * The default foreground color is black.
 * </p>
 * @return bool true on success or false on failure.
 */
function imagexbm(\GdImage $image, ?string $filename, ?int $foreground_color = null) : bool
{
}
/**
 * Applies a filter to an image
 * @link https://php.net/manual/en/function.imagefilter.php
 * @param resource|GdImage $image
 * @param int $filter <p>
 * filtertype can be one of the following:
 * IMG_FILTER_NEGATE: Reverses all colors of
 * the image.</p>
 * @param int ...$args
 * @return bool true on success or false on failure.
 */
function imagefilter(\GdImage $image, int $filter, #[PhpStormStubsElementAvailable(from: '5.3', to: '7.4')] $arg1 = null, #[PhpStormStubsElementAvailable(from: '5.3', to: '7.4')] $arg2 = null, #[PhpStormStubsElementAvailable(from: '5.3', to: '7.4')] $arg3 = null, #[PhpStormStubsElementAvailable(from: '5.3', to: '7.4')] $arg4 = null, #[PhpStormStubsElementAvailable(from: '8.0')] ...$args) : bool
{
}
/**
 * Apply a 3x3 convolution matrix, using coefficient and offset
 * @link https://php.net/manual/en/function.imageconvolution.php
 * @param resource|GdImage $image
 * @param array $matrix <p>
 * A 3x3 matrix: an array of three arrays of three floats.
 * </p>
 * @param float $divisor <p>
 * The divisor of the result of the convolution, used for normalization.
 * </p>
 * @param float $offset <p>
 * Color offset.
 * </p>
 * @return bool true on success or false on failure.
 */
function imageconvolution(\GdImage $image, array $matrix, float $divisor, float $offset) : bool
{
}
/**
 * @param resource|GdImage $image An image resource, returned by one of the image creation functions, such as {@see imagecreatetruecolor()}.
 * @param int|null $resolution_x The horizontal resolution in DPI.
 * @param int|null $resolution_y The vertical resolution in DPI.
 * @return array|bool When used as getter (that is without the optional parameters), it returns <b>TRUE</b> on success, or <b>FALSE</b> on failure. When used as setter (that is with one or both optional parameters given), it returns an indexed array of the horizontal and vertical resolution on success, or <b>FALSE</b> on failure.
 * @link https://php.net/manual/en/function.imageresolution.php
 * @since 7.2
 */
#[LanguageLevelTypeAware(['8.2' => 'array|true'], default: 'array|bool')]
function imageresolution(\GdImage $image, ?int $resolution_x = null, ?int $resolution_y = null) : array|bool
{
}
/**
 * <b>imagesetclip()</b> sets the current clipping rectangle, i.e. the area beyond which no pixels will be drawn.
 * @param resource|GdImage $image An image resource, returned by one of the image creation functions, such as {@see imagecreatetruecolor()}.
 * @param int $x1 The x-coordinate of the upper left corner.
 * @param int $y1 The y-coordinate of the upper left corner.
 * @param int $x2 The x-coordinate of the lower right corner.
 * @param int $y2 The y-coordinate of the lower right corner.
 * @return bool Returns <b>TRUE</b> on success or <b>FALSE</b> on failure.
 * @link https://php.net/manual/en/function.imagesetclip.php
 * @see imagegetclip()
 * @since 7.2
 */
function imagesetclip(\GdImage $image, int $x1, int $y1, int $x2, int $y2) : bool
{
}
/**
 * <b>imagegetclip()</b> retrieves the current clipping rectangle, i.e. the area beyond which no pixels will be drawn.
 * @param resource|GdImage $image An image resource, returned by one of the image creation functions, such as {@see imagecreatetruecolor()}
 * @return array|false an indexed array with the coordinates of the clipping rectangle which has the following entries:
 * <ul>
 * <li>x-coordinate of the upper left corner</li>
 * <li>y-coordinate of the upper left corner</li>
 * <li>x-coordinate of the lower right corner</li>
 * <li>y-coordinate of the lower right corner</li>
 * </ul>
 * Returns <b>FALSE</b> on error.
 * @link https://php.net/manual/en/function.imagegetclip.php
 * @see imagesetclip()
 * @since 7.2
 */
function imagegetclip(\GdImage $image) : array
{
}
/**
 * <b>imageopenpolygon()</b> draws an open polygon on the given <b>image.</b> Contrary to {@see imagepolygon()}, no line is drawn between the last and the first point.
 * @param resource|GdImage $image An image resource, returned by one of the image creation functions, such as {@see imagecreatetruecolor()}.
 * @param int[] $points An array containing the polygon's vertices, e.g.:
 * <pre>
 * points[0]	= x0
 * points[1]	= y0
 * points[2]	= x1
 * points[3]	= y1
 * </pre>
 * @param int $num_points_or_color Total number of points (vertices).
 * @param int|null $color A color identifier created with {@see imagecolorallocate()}.
 * @return bool Returns <b>TRUE</b> on success or <b>FALSE</b> on failure.
 * @link https://php.net/manual/en/function.imageopenpolygon.php
 * @since 7.2
 * @see imageplygon()
 */
function imageopenpolygon(\GdImage $image, array $points, #[Deprecated(since: "8.1")] int $num_points_or_color, #[PhpStormStubsElementAvailable(from: '5.3', to: '7.4')] ?int $color, #[PhpStormStubsElementAvailable(from: '8.0')] ?int $color = null) : bool
{
}
/**
 * <b>imagecreatefrombmp()</b> returns an image identifier representing the image obtained from the given filename.
 * <b>TIP</b> A URL can be used as a filename with this function if the fopen wrappers have been enabled. See {@see fopen()} for more details on how to specify the filename. See the Supported Protocols and Wrappers for links to information about what abilities the various wrappers have, notes on their usage, and information on any predefined variables they may provide.
 * @param string $filename Path to the BMP image.
 * @return resource|GdImage|false Returns an image resource identifier on success, <b>FALSE</b> on errors.
 * @link https://php.net/manual/en/function.imagecreatefrombmp.php
 * @since 7.2
 */
function imagecreatefrombmp(string $filename) : \GdImage|false
{
}
/**
 * Outputs or saves a BMP version of the given <b>image</b>.
 * @param resource|GdImage $image An image resource, returned by one of the image creation functions, such as {@see imagecreatetruecolor()}.
 * @param mixed $file The path or an open stream resource (which is automatically being closed after this function returns) to save the file to. If not set or <b>NULL</b>, the raw image stream will be outputted directly.
 * <br />
 * <b>Note:</b> <b>NULL</b> is invalid if the <b>compressed</b> arguments is not used.
 * @param bool $compressed Whether the BMP should be compressed with run-length encoding (RLE), or not.
 * @return bool Returns <b>TRUE</b> on success or <b>FALSE</b> on failure.
 * <br />
 * <b>Caution</b> However, if libgd fails to output the image, this function returns <b>TRUE</b>.
 * @link https://php.net/manual/en/function.imagebmp.php
 * @since 7.2
 */
function imagebmp(\GdImage $image, $file = null, bool $compressed = \true) : bool
{
}
/**
 * @param string $filename
 * @return resource|GdImage|false
 */
function imagecreatefromtga(string $filename) : \GdImage|false
{
}
/**
 * Captures the whole screen
 *
 * https://www.php.net/manual/en/function.imagegrabscreen.php
 *
 * @return resource|GdImage|false
 */
#[Pure]
function imagegrabscreen()
{
}
/**
 * Captures a window
 *
 * @link https://www.php.net/manual/en/function.imagegrabwindow.php
 *
 * @param int $handle
 * @param int|null $client_area
 * @return resource|GdImage|false
 */
#[Pure]
function imagegrabwindow($handle, $client_area = null)
{
}
/**
 * Gets the currently set interpolation method of the image.
 *
 * @link https://www.php.net/manual/en/function.imagegetinterpolation.php
 *
 * @param GdImage $image
 * @return int
 */
#[Pure]
function imagegetinterpolation(\GdImage $image) : int
{
}
/**
 * Used as a return value by {@see imagetypes()}
 * @link https://php.net/manual/en/image.constants.php#constant.img-gif
 */
\define('IMG_GIF', 1);
/**
 * Used as a return value by {@see imagetypes()}
 * @link https://php.net/manual/en/image.constants.php#constant.img-jpg
 */
\define('IMG_JPG', 2);
/**
 * Used as a return value by {@see imagetypes()}
 * <p>
 * This constant has the same value as {@see IMG_JPG}
 * </p>
 * @link https://php.net/manual/en/image.constants.php#constant.img-jpeg
 */
\define('IMG_JPEG', 2);
/**
 * Used as a return value by {@see imagetypes()}
 * @link https://php.net/manual/en/image.constants.php#constant.img-png
 */
\define('IMG_PNG', 4);
/**
 * Used as a return value by {@see imagetypes()}
 * @link https://php.net/manual/en/image.constants.php#constant.img-wbmp
 */
\define('IMG_WBMP', 8);
/**
 * Used as a return value by {@see imagetypes()}
 * @link https://php.net/manual/en/image.constants.php#constant.img-xpm
 */
\define('IMG_XPM', 16);
/**
 * Used as a return value by {@see imagetypes()}
 * @since 5.6.25
 * @since 7.0.10
 * @link https://php.net/manual/en/image.constants.php#constant.img-webp
 */
\define('IMG_WEBP', 32);
/**
 * Used as a return value by {@see imagetypes()}
 * @since 7.2
 * @link https://php.net/manual/en/image.constants.php#constant.img-bmp
 */
\define('IMG_BMP', 64);
/**
 * Special color option which can be used instead of color allocated with
 * {@see imagecolorallocate()} or {@see imagecolorallocatealpha()}
 * @link https://php.net/manual/en/image.constants.php#constant.img-color-tiled
 */
\define('IMG_COLOR_TILED', -5);
/**
 * Special color option which can be used instead of color allocated with
 * {@see imagecolorallocate()} or {@see imagecolorallocatealpha()}
 * @link https://php.net/manual/en/image.constants.php#constant.img-color-styled
 */
\define('IMG_COLOR_STYLED', -2);
/**
 * Special color option which can be used instead of color allocated with
 * {@see imagecolorallocate()} or {@see imagecolorallocatealpha()}
 * @link https://php.net/manual/en/image.constants.php#constant.img-color-brushed
 */
\define('IMG_COLOR_BRUSHED', -3);
/**
 * Special color option which can be used instead of color allocated with
 * {@see imagecolorallocate()} or {@see imagecolorallocatealpha()}
 * @link https://php.net/manual/en/image.constants.php#constant.img-color-styledbrushed
 */
\define('IMG_COLOR_STYLEDBRUSHED', -4);
/**
 * Special color option which can be used instead of color allocated with
 * {@see imagecolorallocate()} or {@see imagecolorallocatealpha()}
 * @link https://php.net/manual/en/image.constants.php#constant.img-color-transparent
 */
\define('IMG_COLOR_TRANSPARENT', -6);
/**
 * A style constant used by the {@see imagefilledarc()} function.
 * <p>
 * This constant has the same value as {@see IMG_ARC_PIE}
 * </p>
 * @link https://php.net/manual/en/image.constants.php#constant.img-arc-rounded
 */
\define('IMG_ARC_ROUNDED', 0);
/**
 * A style constant used by the {@see imagefilledarc()} function.
 * @link https://php.net/manual/en/image.constants.php#constant.img-arc-pie
 */
\define('IMG_ARC_PIE', 0);
/**
 * A style constant used by the {@see imagefilledarc()} function.
 * @link https://php.net/manual/en/image.constants.php#constant.img-arc-chord
 */
\define('IMG_ARC_CHORD', 1);
/**
 * A style constant used by the {@see imagefilledarc()} function.
 * @link https://php.net/manual/en/image.constants.php#constant.img-arc-nofill
 */
\define('IMG_ARC_NOFILL', 2);
/**
 * A style constant used by the {@see imagefilledarc()} function.
 * @link https://php.net/manual/en/image.constants.php#constant.img-arc-edged
 */
\define('IMG_ARC_EDGED', 4);
/**
 * A type constant used by the {@see imagegd2()} function.
 * @link https://php.net/manual/en/image.constants.php#constant.img-gd2-raw
 */
\define('IMG_GD2_RAW', 1);
/**
 * A type constant used by the {@see imagegd2()} function.
 * @link https://php.net/manual/en/image.constants.php#constant.img-gd2-compressed
 */
\define('IMG_GD2_COMPRESSED', 2);
/**
 * Alpha blending effect used by the {@see imagelayereffect()} function.
 * @link https://php.net/manual/en/image.constants.php#constant.img-effect-replace
 */
\define('IMG_EFFECT_REPLACE', 0);
/**
 * Alpha blending effect used by the {@see imagelayereffect()} function.
 * @link https://php.net/manual/en/image.constants.php#constant.img-effect-alphablend
 */
\define('IMG_EFFECT_ALPHABLEND', 1);
/**
 * Alpha blending effect used by the {@see imagelayereffect()} function.
 * @link https://php.net/manual/en/image.constants.php#constant.img-effect-normal
 */
\define('IMG_EFFECT_NORMAL', 2);
/**
 * Alpha blending effect used by the {@see imagelayereffect()} function.
 * @link https://php.net/manual/en/image.constants.php#constant.img-effect-overlay
 */
\define('IMG_EFFECT_OVERLAY', 3);
/**
 * Alpha blending effect used by the {@see imagelayereffect()} function.
 * @link https://php.net/manual/en/image.constants.php#constant.img-effect-multiply
 * @since 7.2
 */
\define('IMG_EFFECT_MULTIPLY', 4);
/**
 * When the bundled version of GD is used this is 1 otherwise
 * it's set to 0.
 * @link https://php.net/manual/en/image.constants.php
 */
\define('GD_BUNDLED', 1);
/**
 * Special GD filter used by the {@see imagefilter()} function.
 * @link https://php.net/manual/en/image.constants.php#constant.img-filter-negate
 */
\define('IMG_FILTER_NEGATE', 0);
/**
 * Special GD filter used by the {@see imagefilter()} function.
 * @link https://php.net/manual/en/image.constants.php#constant.img-filter-grayscale
 */
\define('IMG_FILTER_GRAYSCALE', 1);
/**
 * Special GD filter used by the {@see imagefilter()} function.
 * @link https://php.net/manual/en/image.constants.php#constant.img-filter-brightness
 */
\define('IMG_FILTER_BRIGHTNESS', 2);
/**
 * Special GD filter used by the {@see imagefilter()} function.
 * @link https://php.net/manual/en/image.constants.php#constant.img-filter-contrast
 */
\define('IMG_FILTER_CONTRAST', 3);
/**
 * Special GD filter used by the {@see imagefilter()} function.
 * @link https://php.net/manual/en/image.constants.php#constant.img-filter-colorize
 */
\define('IMG_FILTER_COLORIZE', 4);
/**
 * Special GD filter used by the {@see imagefilter()} function.
 * @link https://php.net/manual/en/image.constants.php#constant.img-filter-edgedetect
 */
\define('IMG_FILTER_EDGEDETECT', 5);
/**
 * Special GD filter used by the {@see imagefilter()} function.
 * @link https://php.net/manual/en/image.constants.php#constant.img-filter-gaussian-blur
 */
\define('IMG_FILTER_GAUSSIAN_BLUR', 7);
/**
 * Special GD filter used by the {@see imagefilter()} function.
 * @link https://php.net/manual/en/image.constants.php#constant.img-filter-selective-blur
 */
\define('IMG_FILTER_SELECTIVE_BLUR', 8);
/**
 * Special GD filter used by the {@see imagefilter()} function.
 * @link https://php.net/manual/en/image.constants.php#constant.img-filter-emboss
 */
\define('IMG_FILTER_EMBOSS', 6);
/**
 * Special GD filter used by the {@see imagefilter()} function.
 * @link https://php.net/manual/en/image.constants.php#constant.img-filter-mean-removal
 */
\define('IMG_FILTER_MEAN_REMOVAL', 9);
/**
 * Special GD filter used by the {@see imagefilter()} function.
 * @link https://php.net/manual/en/image.constants.php#constant.img-filter-smooth
 */
\define('IMG_FILTER_SMOOTH', 10);
/**
 * Special GD filter used by the {@see imagefilter()} function.
 * @link https://php.net/manual/en/image.constants.php#constant.img-filter-pixelate
 */
\define('IMG_FILTER_PIXELATE', 11);
/**
 * Special GD filter used by the {@see imagefilter()} function.
 * @link https://php.net/manual/en/image.constants.php#constant.img-filter-scatter
 * @since 7.4
 */
\define('IMG_FILTER_SCATTER', 12);
/**
 * The GD version PHP was compiled against.
 * @since 5.2.4
 * @link https://php.net/manual/en/image.constants.php#constant.gd-version
 */
\define('GD_VERSION', "2.0.35");
/**
 * The GD major version PHP was compiled against.
 * @since 5.2.4
 * @link https://php.net/manual/en/image.constants.php#constant.gd-major-version
 */
\define('GD_MAJOR_VERSION', 2);
/**
 * The GD minor version PHP was compiled against.
 * @since 5.2.4
 * @link https://php.net/manual/en/image.constants.php#constant.gd-minor-version
 */
\define('GD_MINOR_VERSION', 0);
/**
 * The GD release version PHP was compiled against.
 * @since 5.2.4
 * @link https://php.net/manual/en/image.constants.php#constant.gd-release-version
 */
\define('GD_RELEASE_VERSION', 35);
/**
 * The GD "extra" version (beta/rc..) PHP was compiled against.
 * @since 5.2.4
 * @link https://php.net/manual/en/image.constants.php#constant.gd-extra-version
 */
\define('GD_EXTRA_VERSION', "");
/**
 * A special PNG filter, used by the {@see imagepng()} function.
 * @link https://php.net/manual/en/image.constants.php#constant.png-no-filter
 */
\define('PNG_NO_FILTER', 0);
/**
 * A special PNG filter, used by the {@see imagepng()} function.
 * @link https://php.net/manual/en/image.constants.php#constant.png-filter-none
 */
\define('PNG_FILTER_NONE', 8);
/**
 * A special PNG filter, used by the {@see imagepng()} function.
 * @link https://php.net/manual/en/image.constants.php#constant.png-filter-sub
 */
\define('PNG_FILTER_SUB', 16);
/**
 * A special PNG filter, used by the {@see imagepng()} function.
 * @link https://php.net/manual/en/image.constants.php#constant.png-filter-up
 */
\define('PNG_FILTER_UP', 32);
/**
 * A special PNG filter, used by the {@see imagepng()} function.
 * @link https://php.net/manual/en/image.constants.php#constant.png-filter-avg
 */
\define('PNG_FILTER_AVG', 64);
/**
 * A special PNG filter, used by the {@see imagepng()} function.
 * @link https://php.net/manual/en/image.constants.php#constant.png-filter-paeth
 */
\define('PNG_FILTER_PAETH', 128);
/**
 * A special PNG filter, used by the {@see imagepng()} function.
 * @link https://php.net/manual/en/image.constants.php#constant.png-all-filters
 */
\define('PNG_ALL_FILTERS', 248);
/**
 * An affine transformation type constant used by the {@see imageaffinematrixget()} function.
 * @since 5.5
 * @link https://php.net/manual/en/image.constants.php#constant.img-affine-translate
 */
\define('IMG_AFFINE_TRANSLATE', 0);
/**
 * An affine transformation type constant used by the {@see imageaffinematrixget()} function.
 * @since 5.5
 * @link https://php.net/manual/en/image.constants.php#constant.img-affine-scale
 */
\define('IMG_AFFINE_SCALE', 1);
/**
 * An affine transformation type constant used by the {@see imageaffinematrixget()} function.
 * @since 5.5
 * @link https://php.net/manual/en/image.constants.php#constant.img-affine-rotate
 */
\define('IMG_AFFINE_ROTATE', 2);
/**
 * An affine transformation type constant used by the {@see imageaffinematrixget()} function.
 * @since 5.5
 * @link https://php.net/manual/en/image.constants.php#constant.img-affine-shear-horizontal
 */
\define('IMG_AFFINE_SHEAR_HORIZONTAL', 3);
/**
 * An affine transformation type constant used by the {@see imageaffinematrixget()} function.
 * @since 5.5
 * @link https://php.net/manual/en/image.constants.php#constant.img-affine-shear-vertical
 */
\define('IMG_AFFINE_SHEAR_VERTICAL', 4);
/**
 * Same as {@see IMG_CROP_TRANSPARENT}. Before PHP 7.4.0, the bundled libgd fell back to
 * {@see IMG_CROP_SIDES}, if the image had no transparent color.
 * Used together with {@see imagecropauto()}.
 * @since 5.5
 */
\define('IMG_CROP_DEFAULT', 0);
/**
 * Crops out a transparent background.
 * Used together with {@see imagecropauto()}.
 * @since 5.5
 */
\define('IMG_CROP_TRANSPARENT', 1);
/**
 * Crops out a black background.
 * Used together with {@see imagecropauto()}.
 * @since 5.5
 */
\define('IMG_CROP_BLACK', 2);
/**
 * Crops out a white background.
 * Used together with {@see imagecropauto()}.
 * @since 5.5
 */
\define('IMG_CROP_WHITE', 3);
/**
 * Uses the 4 corners of the image to attempt to detect the background to crop.
 * Used together with {@see imagecropauto()}.
 * @since 5.5
 */
\define('IMG_CROP_SIDES', 4);
/**
 * Crops an image using the given <b>threshold</b> and <b>color</b>.
 * Used together with {@see imagecropauto()}.
 * @since 5.5
 */
\define('IMG_CROP_THRESHOLD', 5);
/**
 * Used together with {@see imageflip()}
 * @since 5.5
 * @link https://php.net/manual/en/image.constants.php#constant.img-flip-both
 */
\define('IMG_FLIP_BOTH', 3);
/**
 * Used together with {@see imageflip()}
 * @since 5.5
 * @link https://php.net/manual/en/image.constants.php#constant.img-flip-horizontal
 */
\define('IMG_FLIP_HORIZONTAL', 1);
/**
 * Used together with {@see imageflip()}
 * @since 5.5
 * @link https://php.net/manual/en/image.constants.php#constant.img-flip-vertical
 */
\define('IMG_FLIP_VERTICAL', 2);
/**
 * Used together with {@see imagesetinterpolation()}.
 * @link https://php.net/manual/en/image.constants.php#constant.img-bell
 * @since 5.5
 */
\define('IMG_BELL', 1);
/**
 * Used together with {@see imagesetinterpolation()}.
 * @link https://php.net/manual/en/image.constants.php#constant.img-bessel
 * @since 5.5
 */
\define('IMG_BESSEL', 2);
/**
 * Used together with {@see imagesetinterpolation()}.
 * @link https://php.net/manual/en/image.constants.php#constant.img-bicubic
 * @since 5.5
 */
\define('IMG_BICUBIC', 4);
/**
 * Used together with {@see imagesetinterpolation()}.
 * @link https://php.net/manual/en/image.constants.php#constant.img-bicubic-fixed
 * @since 5.5
 */
\define('IMG_BICUBIC_FIXED', 5);
/**
 * Used together with {@see imagesetinterpolation()}.
 * @link https://php.net/manual/en/image.constants.php#constant.img-bilinear-fixed
 * @since 5.5
 */
\define('IMG_BILINEAR_FIXED', 3);
/**
 * Used together with {@see imagesetinterpolation()}.
 * @link https://php.net/manual/en/image.constants.php#constant.img-blackman
 * @since 5.5
 */
\define('IMG_BLACKMAN', 6);
/**
 * Used together with {@see imagesetinterpolation()}.
 * @link https://php.net/manual/en/image.constants.php#constant.img-box
 * @since 5.5
 */
\define('IMG_BOX', 7);
/**
 * Used together with {@see imagesetinterpolation()}.
 * @link https://php.net/manual/en/image.constants.php#constant.img-bspline
 * @since 5.5
 */
\define('IMG_BSPLINE', 8);
/**
 * Used together with {@see imagesetinterpolation()}.
 * @link https://php.net/manual/en/image.constants.php#constant.img-catmullrom
 * @since 5.5
 */
\define('IMG_CATMULLROM', 9);
/**
 * Used together with {@see imagesetinterpolation()}.
 * @link https://php.net/manual/en/image.constants.php#constant.img-gaussian
 * @since 5.5
 */
\define('IMG_GAUSSIAN', 10);
/**
 * Used together with {@see imagesetinterpolation()}.
 * @link https://php.net/manual/en/image.constants.php#constant.img-generalized-cubic
 * @since 5.5
 */
\define('IMG_GENERALIZED_CUBIC', 11);
/**
 * Used together with {@see imagesetinterpolation()}.
 * @link https://php.net/manual/en/image.constants.php#constant.img-hermite
 * @since 5.5
 */
\define('IMG_HERMITE', 12);
/**
 * Used together with {@see imagesetinterpolation()}.
 * @link https://php.net/manual/en/image.constants.php#constant.img-hamming
 * @since 5.5
 */
\define('IMG_HAMMING', 13);
/**
 * Used together with {@see imagesetinterpolation()}.
 * @link https://php.net/manual/en/image.constants.php#constant.img-hanning
 * @since 5.5
 */
\define('IMG_HANNING', 14);
/**
 * Used together with {@see imagesetinterpolation()}.
 * @link https://php.net/manual/en/image.constants.php#constant.img-mitchell
 * @since 5.5
 */
\define('IMG_MITCHELL', 15);
/**
 * Used together with {@see imagesetinterpolation()}.
 * @link https://php.net/manual/en/image.constants.php#constant.img-power
 * @since 5.5
 */
\define('IMG_POWER', 17);
/**
 * Used together with {@see imagesetinterpolation()}.
 * @link https://php.net/manual/en/image.constants.php#constant.img-quadratic
 * @since 5.5
 */
\define('IMG_QUADRATIC', 18);
/**
 * Used together with {@see imagesetinterpolation()}.
 * @link https://php.net/manual/en/image.constants.php#constant.img-sinc
 * @since 5.5
 */
\define('IMG_SINC', 19);
/**
 * Used together with {@see imagesetinterpolation()}.
 * @link https://php.net/manual/en/image.constants.php#constant.img-nearest-neighbour
 * @since 5.5
 */
\define('IMG_NEAREST_NEIGHBOUR', 16);
/**
 * Used together with {@see imagesetinterpolation()}.
 * @link https://php.net/manual/en/image.constants.php#constant.img-weighted4
 * @since 5.5
 */
\define('IMG_WEIGHTED4', 21);
/**
 * Used together with {@see imagesetinterpolation()}.
 * @link https://php.net/manual/en/image.constants.php#constant.img-triangle
 * @since 5.5
 */
\define('IMG_TRIANGLE', 20);
\define('IMG_TGA', 128);
/**
 * @since 8.1
 */
\define('IMG_AVIF', 256);
/**
 * @since 8.1
 */
\define('IMG_WEBP_LOSSLESS', 101);
/**
 * Outputs or saves a AVIF Raster image from the given image
 * @link https://www.php.net/manual/function.imageavif.php
 * @param GdImage $image A GdImage object, returned by one of the image creation functions, such as imagecreatetruecolor().
 * @param resource|string|null $file The path or an open stream resource (which is automatically closed after this function returns) to save the file to. If not set or null, the raw image stream will be output directly.
 * @param int $quality quality is optional, and ranges from 0 (worst quality, smaller file) to 100 (best quality, larger file). If -1 is provided, the default value 30 is used.
 * @param int $speed speed is optional, and ranges from 0 (slow, smaller file) to 10 (fast, larger file). If -1 is provided, the default value 6 is used.
 * @return bool Returns true on success or false on failure. However, if libgd fails to output the image, this function returns true.
 * @since 8.1
 */
function imageavif(\GdImage $image, string|null $file = null, int $quality = -1, int $speed = -1) : bool
{
}
/**
 * Return an image containing the affine tramsformed src image, using an optional clipping area
 * @link https://secure.php.net/manual/en/function.imageaffine.php
 * @param resource|GdImage $image <p>An image resource, returned by one of the image creation functions,
 * such as {@link https://secure.php.net/manual/en/function.imagecreatetruecolor.php imagecreatetruecolor()}.</p>
 * @param array $affine <p>Array with keys 0 to 5.</p>
 * @param array|null $clip [optional] <p>Array with keys "x", "y", "width" and "height".</p>
 * @return resource|GdImage|false Return affined image resource on success or FALSE on failure.
 */
function imageaffine(\GdImage $image, array $affine, ?array $clip = null) : \GdImage|false
{
}
/**
 * Concat two matrices (as in doing many ops in one go)
 * @link https://secure.php.net/manual/en/function.imageaffinematrixconcat.php
 * @param array $matrix1 <p>Array with keys 0 to 5.</p>
 * @param array $matrix2 <p>Array with keys 0 to 5.</p>
 * @return float[]|false Array with keys 0 to 5 and float values or <b>FALSE</b> on failure.
 * @since 5.5
 */
function imageaffinematrixconcat(array $matrix1, array $matrix2) : array|false
{
}
/**
 * Return an image containing the affine tramsformed src image, using an optional clipping area
 * @link https://secure.php.net/manual/en/function.imageaffinematrixget.php
 * @param int $type <p> One of <b>IMG_AFFINE_*</b> constants.</p>
 * @param mixed $options
 * @return float[]|false Array with keys 0 to 5 and float values or <b>FALSE</b> on failure.
 * @since 5.5
 */
function imageaffinematrixget(int $type, #[PhpStormStubsElementAvailable(from: '5.3', to: '7.4')] $options = null, #[PhpStormStubsElementAvailable(from: '8.0')] $options) : array|false
{
}
/**
 * Crop an image using the given coordinates and size, x, y, width and height
 * @link https://secure.php.net/manual/en/function.imagecrop.php
 * @param resource|GdImage $image <p>
 * An image resource, returned by one of the image creation functions, such as {@link https://secure.php.net/manual/en/function.imagecreatetruecolor.php imagecreatetruecolor()}.
 * </p>
 * @param array $rectangle <p>Array with keys "x", "y", "width" and "height".</p>
 * @return resource|GdImage|false Return cropped image resource on success or FALSE on failure.
 * @since 5.5
 */
function imagecrop(\GdImage $image, array $rectangle) : \GdImage|false
{
}
/**
 * Crop an image automatically using one of the available modes
 * @link https://secure.php.net/manual/en/function.imagecropauto.php
 * @param resource|GdImage $image <p>
 * An image resource, returned by one of the image creation functions, such as {@link https://secure.php.net/manual/en/function.imagecreatetruecolor.php imagecreatetruecolor()}.
 * </p>
 * @param int $mode [optional] <p>
 * One of <b>IMG_CROP_*</b> constants.
 * </p>
 * @param float $threshold [optional] <p>
 * Used <b>IMG_CROP_THRESHOLD</b> mode.
 * </p>
 * @param int $color [optional]
 * <p>
 * Used in <b>IMG_CROP_THRESHOLD</b> mode.
 * </p>
 * @return resource|GdImage|false Return cropped image resource on success or <b>FALSE</b> on failure.
 * @since 5.5
 */
function imagecropauto(\GdImage $image, int $mode = \IMG_CROP_DEFAULT, float $threshold = 0.5, int $color = -1) : \GdImage|false
{
}
/**
 * Flips an image using a given mode
 * @link https://secure.php.net/manual/en/function.imageflip.php
 * @param resource|GdImage $image <p>
 * An image resource, returned by one of the image creation functions, such as {@link https://secure.php.net/manual/en/function.imagecreatetruecolor.php imagecreatetruecolor()}.
 * </p>
 * @param int $mode <p>
 * Flip mode, this can be one of the <b>IMG_FLIP_*</b> constants:
 * </p>
 * <table>
 * <thead>
 * <tr>
 * <th>Constant</th>
 * <th>Meaning</th>
 * </tr>
 * </thead>
 * <tbody>
 * <tr>
 * <td><b>IMG_FLIP_HORIZONTAL</b></td>
 * <td>
 * Flips the image horizontally.
 * </td>
 * </tr>
 * <tr>
 * <td><b>IMG_FLIP_VERTICAL</b></td>
 * <td>
 * Flips the image vertically.
 * </td>
 * </tr>
 * <tr>
 * <td><b>IMG_FLIP_BOTH</b></td>
 * <td>
 * Flips the image both horizontally and vertically.
 * </td>
 * </tr>
 * </tbody>
 * </table>
 * @return bool Returns <b>TRUE</b> on success or <b>FALSE</b> on failure.
 * @since 5.5
 */
function imageflip(\GdImage $image, int $mode) : bool
{
}
/**
 * Converts a palette based image to true color
 * @link https://secure.php.net/manual/en/function.imagepalettetotruecolor.php
 * @param resource|GdImage $image <p>
 * An image resource, returnd by one of the image creation functions, such as {@link https://secure.php.net/manual/en/function.imagecreatetruecolor.php imagecreatetruecolor()}.
 * </p>
 * @return bool Returns <b>TRUE</b> if the convertion was complete, or if the source image already is a true color image, otherwise <b>FALSE</b> is returned.
 * @since 5.5
 */
function imagepalettetotruecolor(\GdImage $image) : bool
{
}
/**
 * @param resource|GdImage $image <p>
 * An image resource, returnd by one of the image creation functions, such as {@link https://secure.php.net/manual/en/function.imagecreatetruecolor.php imagecreatetruecolor()}.
 * </p>
 * @param int $width
 * @param int $height [optional]
 * @param int $mode [optional] One of <b>IMG_NEAREST_NEIGHBOUR</b>, <b>IMG_BILINEAR_FIXED</b>, <b>IMG_BICUBIC</b>, <b>IMG_BICUBIC_FIXED</b> or anything else (will use two pass).
 * @return resource|GdImage|false Return scaled image resource on success or <b>FALSE</b> on failure.
 *@link https://secure.php.net/manual/en/function.imagescale.php
 * @since 5.5
 * Scale an image using the given new width and height
 */
function imagescale(\GdImage $image, int $width, int $height = -1, int $mode = \IMG_BILINEAR_FIXED) : \GdImage|false
{
}
/**
 * Set the interpolation method
 * @link https://secure.php.net/manual/en/function.imagesetinterpolation.php
 * @param resource|GdImage $image <p>
 * An image resource, returned by one of the image creation functions, such as {@link https://secure.php.net/manual/en/function.imagecreatetruecolor.php imagecreatetruecolor()}.
 * </p>
 * @param int $method <p>
 * The interpolation method, which can be one of the following:
 * <ul>
 * <li>
 * IMG_BELL: Bell filter.
 * </li>
 * <li>
 * IMG_BESSEL: Bessel filter.
 * </li>
 * <li>
 * IMG_BICUBIC: Bicubic interpolation.
 * </li>
 * <li>
 * IMG_BICUBIC_FIXED: Fixed point implementation of the bicubic interpolation.
 * </li>
 * <li>
 * IMG_BILINEAR_FIXED: Fixed point implementation of the  bilinear interpolation (<em>default (also on image creation)</em>).
 * </li>
 * <li>
 * IMG_BLACKMAN: Blackman window function.
 * </li>
 * <li>
 * IMG_BOX: Box blur filter.
 * </li>
 * <li>
 * IMG_BSPLINE: Spline interpolation.
 * </li>
 * <li>
 * IMG_CATMULLROM: Cubbic Hermite spline interpolation.
 * </li>
 * <li>
 * IMG_GAUSSIAN: Gaussian function.
 * </li>
 * <li>
 * IMG_GENERALIZED_CUBIC: Generalized cubic spline fractal interpolation.
 * </li>
 * <li>
 * IMG_HERMITE: Hermite interpolation.
 * </li>
 * <li>
 * IMG_HAMMING: Hamming filter.
 * </li>
 * <li>
 * IMG_HANNING: Hanning filter.
 * </li>
 * <li>
 * IMG_MITCHELL: Mitchell filter.
 * </li>
 * <li>
 * IMG_POWER: Power interpolation.
 * </li>
 * <li>
 * IMG_QUADRATIC: Inverse quadratic interpolation.
 * </li>
 * <li>
 * IMG_SINC: Sinc function.
 * </li>
 * <li>
 * IMG_NEAREST_NEIGHBOUR: Nearest neighbour interpolation.
 * </li>
 * <li>
 * IMG_WEIGHTED4: Weighting filter.
 * </li>
 * <li>
 * IMG_TRIANGLE: Triangle interpolation.
 * </li>
 * </ul>
 * </p>
 * @return bool Returns TRUE on success or FALSE on failure.
 * @since 5.5
 */
function imagesetinterpolation(\GdImage $image, int $method = \IMG_BILINEAR_FIXED) : bool
{
}
/**
 * @since 8.0
 */
final class GdImage
{
    /**
     * You cannot initialize a GdImage object except through helper functions.
     */
    private function __construct()
    {
    }
    private function __clone()
    {
    }
}
/**
 * @since 8.0
 */
\class_alias('DEPTRAC_202401\\GdImage', 'GdImage', \false);
