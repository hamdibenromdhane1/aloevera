=== XT WooCommerce Variation Swatches ===

Plugin Name: XT WooCommerce Variation Swatches
Contributors: XplodedThemes
Author: XplodedThemes
Author URI: https://www.xplodedthemes.com
Tags: woocommerce, variation swatches, color swatches, image swatches, label swatches, product attributes
Requires at least: 4.6
Tested up to: 5.3.0
Stable tag: trunk
Requires PHP: 5.4+
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A WooCommerce extension that transforms variation dropdowns to beautiful color, image or label swatches. Image swatches will automatically be applied for variation color attributes that contains an image.

== Description ==

XT Woo Variation Swatches is a WooCommerce extension that transforms variation dropdowns to beautiful color, image or label swatches. Image swatches will automatically be applied for variation color attributes that contains an image.

The plugin offers an aesthetic and professional experience to select attributes for variation products. It turns the product variation select options fields into radio images, colors, and label.

XT Woo Variation Swatches for WooCommerce not only offers the color, image and label attributes in the single WooCommerce product page. It also enables them within the catalog page as well as product quick view modals including XT Woo Quick View.

With a friendly and easy-to-use interface, you can add a default color, image or label to each attribute in the attribute management page. It can also help you pick the right style for quick-add attribute right inside the editing product page.

If a color attribute is associated with a variation image, image swatches can automatically be applied.

**Demo**

[https://demos.xplodedthemes.com/woo-variation-swatches/](https://demos.xplodedthemes.com/woo-variation-swatches/)

**Free Version**

- Live Preview Customizer (Limited Options)
- Attribute types: (Dropdown, Label, Color, Image)
- Swatch Style: (Square, Circle, Rounded)
- Adjust Swatch (Width, Height, Alignment)
- Customize options for single product / archives page independently
- Attribute Quick Edit Supported
- Automatically convert Dropdowns to Label Swatch by default
- Automatically convert Color Dropdowns to Image Swatch if variation has an image

**Premium Features**

Fully customizable right from WordPress Customizer with Live Preview.

- All Free Features
- Live Preview Customizer
- Attribute Label Position (Above / Before Swatches)
- Hide Attribute Label
- Hide Variation Reset Link
- Adjust Swatches Container Spacing
- Adjust Swatch Vertical & Horizontal Gap
- Adjust Swatch (Width, Height, Font Size, Colors)
- Enable Tooltip (Text or Image)
- Adjust Tooltip Background & Text Color
- Adjust Tooltip Image Size
- Adjust Tooltip Border Size & Radius
- Override global swatch settings within product page
- Mix swatch types for the same product. ex. Image / Color
- Enable Catalog Mode
- Support display in Quick Views
- Automated Updates & Security Patches
- Priority Email & Help Center Support

**Compatible With <a target="_blank" href="https://xplodedthemes.com/products/woo-floating-cart/">Woo Floating Cart</a>**
**Compatible With <a target="_blank" href="https://xplodedthemes.com/products/woo-quick-view/">Woo Quick View</a>**

**Translations**

- English - default

*Note:* All our plugins are localized / translatable by default. This is very important for all users worldwide. So please contribute your language to the plugin to make it even more useful.

== Installation ==

Installing "Woo Variation Swatches" can be done by following these steps:

1. Download the plugin from the customer area at "XplodedThemes.com"
2. Upload the plugin ZIP file through the 'Plugins > Add New > Upload' screen in your WordPress dashboard
3. Activate the plugin through the 'Plugins' menu in WordPress

== Changelog ==

#### V.1.1.7 - 22.11.2019
- **fix**: Minor Fixes

#### V.1.1.6 - 13.11.2019
- **Fix**: Fixed issue with Divi theme, swatches not being loaded on the single page.
- **New**: **Pro** Allow selection of multiple quick attributes to be used when auto converting dropdowns to image swatches. Only the first one found will be used.

#### V.1.1.5 - 05.11.2019
- **Fix**: Make sure swatches re-initialize correctly after applying sorting / filters via ajax.

#### V.1.1.4 - 28.10.2019
- **New**: **Pro** Catalog Mode: Allow selection of multiple global attributes. Only the first one will be displayed.
- **Support**: Support WordPress v5.2.4

#### V.1.1.3 - 23.10.2019
- **Update**: **Pro** Update customizer library to v3.0.45
- **Fix**: **Pro** Fixed issue with some customizer fields hidden on Flatsome theme and others.
- **Enhance**: Faster swatch auto selection when multiple attribute swatches available.

#### V.1.1.2 - 23.10.2019
- **fix**: Fixed infinite loop with some themes when auto selecting multiple attribute swatches on first swatch click

#### V.1.1.1 - 14.10.2019
- **Update**: Update Freemius SDK to v2.3.1

#### V.1.1.0 - 19.08.2019
- **Update**: **Pro** Updated customizer library to V3.0.44

#### V.1.0.9 - 06.08.2019
- **Fix**: **Pro** When adding product quick attributes, the swatch type was being set as the previous attribute swatch type instead of the default "select" type. This has been fixed now which also fixes the "automated dropdown to label swatch" option for quick attributes.
- **Fix**: Minor CSS Fixes

#### V.1.0.8.3 - 08.06.2019
- **Fix**: Fixed issue with automated swatch selection causing infinite loop when attribute is not available and the swatch is disabled.
- **Fix**: Fixed issue with quick edit image uploader

#### V.1.0.8.2 - 05.06.2019
- **Fix**: Customizer: Fix swatch container spacing css
- **Fix**: Minor css fixes and code improvements.

#### V.1.0.8.1 - 25.04.2019
- **Fix**: **Pro** Fixed bug with Disabled Attribute Behavior
- **Fix**: Fixed tooltip flickering issue

#### V.1.0.8 - 24.04.2019
- **New**: **Pro** Added option to select a behavior for Disabled Variations. Select between (Hide, Blur, Blur & Cross)
- **Fix**: **Pro** Product edit page swatch settings: Fixed issue with color pickers not loading after modifying WooCommerce attributes
- **New**: Fixed conflict with the "WooCommerce Recommendation Engine" plugin
- **Support**: Better Theme Support

#### V.1.0.7 - 11.04.2019
- **Support**: Better theme support
- **Fix**: Fixed issue with duplicated "select options" buttons appearing on some themes.
- **Fix**: Fixed issue on product archives page on some themes, product images not being switched on swatch variation select

#### V.1.0.6 - 04.04.2019
- **fix**: **Pro** Fixed licensing issue
- **Fix**: Fixed issue with variation price switching on some themes

#### V.1.0.5 - 29.03.2019
- **Fix**: Fixed issue with product price disappearing on variation select.

#### V.1.0.4 - 26.03.2019
- **Fix**: **Pro** Fix css issue with tooltips
- **Fix**: **Pro** Fix issue with some product level options not saving.
- **Update**: Update translation POT

#### V.1.0.3 - 18.03.2019
- **Update**: **Pro** Updated Customizer Framework
- **Support**: Better WPML Support

#### V.1.0.2 - 13.03.2019
- **New**: Added option to enable / disable swatches on Shop or Single pages. By default, swatches will be disabled on Shop / Archives

#### V.1.0.1 - 26.02.2019
- **Fix**: Fixed bug with customizer default values

#### V.1.0.0 - 20.02.2019
- **Initial**: Initial Version

