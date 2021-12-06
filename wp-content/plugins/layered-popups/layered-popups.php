<?php
/*
Plugin Name: Layered Popups
Plugin URI: https://layeredpopups.com/
Description: Create multi-layers animated popups.
Version: 6.46
Author: Halfdata, Inc.
Author URI: https://codecanyon.net/user/halfdata?ref=halfdata
*/
if (!defined('UAP_CORE') && !defined('ABSPATH')) exit;
define('ULP_RECORDS_PER_PAGE', '50');
define('ULP_VERSION', 6.46);
define('ULP_WEBFONTS_VERSION', 3);
define('ULP_EXPORT_VERSION', '0001');
define('ULP_API_URL', 'http://layeredpopups.com/updates/');
define('ULP_UPLOADS_DIR', 'ulp');
define('ULP_SUBSCRIBER_UNCONFIRMED', 1);
define('ULP_SUBSCRIBER_CONFIRMED', 2);

register_activation_hook(__FILE__, array("ulp_class", "install"));
register_deactivation_hook(__FILE__, array("ulp_class", "uninstall"));

class ulp_class {
	var $plugins_url;
	var $options;
	var $error;
	var $info;
	var $google_fonts = array();
	var $postdata = array();
	var $front_header = '';
	var $front_footer = '';
	var $font_awesome = array('fa-500px','fa-address-book','fa-address-book-o','fa-address-card','fa-address-card-o','fa-adjust','fa-adn','fa-align-center','fa-align-justify','fa-align-left','fa-align-right','fa-amazon','fa-ambulance','fa-american-sign-language-interpreting','fa-anchor','fa-android','fa-angellist','fa-angle-double-down','fa-angle-double-left','fa-angle-double-right','fa-angle-double-up','fa-angle-down','fa-angle-left','fa-angle-right','fa-angle-up','fa-apple','fa-archive','fa-area-chart','fa-arrow-circle-down','fa-arrow-circle-left','fa-arrow-circle-o-down','fa-arrow-circle-o-left','fa-arrow-circle-o-right','fa-arrow-circle-o-up','fa-arrow-circle-right','fa-arrow-circle-up','fa-arrow-down','fa-arrow-left','fa-arrow-right','fa-arrow-up','fa-arrows','fa-arrows-alt','fa-arrows-h','fa-arrows-v','fa-asl-interpreting','fa-assistive-listening-systems','fa-asterisk','fa-at','fa-audio-description','fa-automobile','fa-backward','fa-balance-scale','fa-ban','fa-bandcamp','fa-bank','fa-bar-chart','fa-bar-chart-o','fa-barcode','fa-bars','fa-bath','fa-bathtub','fa-battery','fa-battery-0','fa-battery-1','fa-battery-2','fa-battery-3','fa-battery-4','fa-battery-empty','fa-battery-full','fa-battery-half','fa-battery-quarter','fa-battery-three-quarters','fa-bed','fa-beer','fa-behance','fa-behance-square','fa-bell','fa-bell-o','fa-bell-slash','fa-bell-slash-o','fa-bicycle','fa-binoculars','fa-birthday-cake','fa-bitbucket','fa-bitbucket-square','fa-bitcoin','fa-black-tie','fa-blind','fa-bluetooth','fa-bluetooth-b','fa-bold','fa-bolt','fa-bomb','fa-book','fa-bookmark','fa-bookmark-o','fa-braille','fa-briefcase','fa-btc','fa-bug','fa-building','fa-building-o','fa-bullhorn','fa-bullseye','fa-bus','fa-buysellads','fa-cab','fa-calculator','fa-calendar','fa-calendar-check-o','fa-calendar-minus-o','fa-calendar-o','fa-calendar-plus-o','fa-calendar-times-o','fa-camera','fa-camera-retro','fa-car','fa-caret-down','fa-caret-left','fa-caret-right','fa-caret-square-o-down','fa-caret-square-o-left','fa-caret-square-o-right','fa-caret-square-o-up','fa-caret-up','fa-cart-arrow-down','fa-cart-plus','fa-cc','fa-cc-amex','fa-cc-diners-club','fa-cc-discover','fa-cc-jcb','fa-cc-mastercard','fa-cc-paypal','fa-cc-stripe','fa-cc-visa','fa-certificate','fa-chain','fa-chain-broken','fa-check','fa-check-circle','fa-check-circle-o','fa-check-square','fa-check-square-o','fa-chevron-circle-down','fa-chevron-circle-left','fa-chevron-circle-right','fa-chevron-circle-up','fa-chevron-down','fa-chevron-left','fa-chevron-right','fa-chevron-up','fa-child','fa-chrome','fa-circle','fa-circle-o','fa-circle-o-notch','fa-circle-thin','fa-clipboard','fa-clock-o','fa-clone','fa-close','fa-cloud','fa-cloud-download','fa-cloud-upload','fa-cny','fa-code','fa-code-fork','fa-codepen','fa-codiepie','fa-coffee','fa-cog','fa-cogs','fa-columns','fa-comment','fa-comment-o','fa-commenting','fa-commenting-o','fa-comments','fa-comments-o','fa-compass','fa-compress','fa-connectdevelop','fa-contao','fa-copy','fa-copyright','fa-creative-commons','fa-credit-card','fa-credit-card-alt','fa-crop','fa-crosshairs','fa-css3','fa-cube','fa-cubes','fa-cut','fa-cutlery','fa-dashboard','fa-dashcube','fa-database','fa-deaf','fa-deafness','fa-dedent','fa-delicious','fa-desktop','fa-deviantart','fa-diamond','fa-digg','fa-dollar','fa-dot-circle-o','fa-download','fa-dribbble','fa-drivers-license','fa-drivers-license-o','fa-dropbox','fa-drupal','fa-edge','fa-edit','fa-eercast','fa-eject','fa-ellipsis-h','fa-ellipsis-v','fa-empire','fa-envelope','fa-envelope-o','fa-envelope-open','fa-envelope-open-o','fa-envelope-square','fa-envira','fa-eraser','fa-etsy','fa-eur','fa-euro','fa-exchange','fa-exclamation','fa-exclamation-circle','fa-exclamation-triangle','fa-expand','fa-expeditedssl','fa-external-link','fa-external-link-square','fa-eye','fa-eye-slash','fa-eyedropper','fa-fa','fa-facebook','fa-facebook-f','fa-facebook-official','fa-facebook-square','fa-fast-backward','fa-fast-forward','fa-fax','fa-feed','fa-female','fa-fighter-jet','fa-file','fa-file-archive-o','fa-file-audio-o','fa-file-code-o','fa-file-excel-o','fa-file-image-o','fa-file-movie-o','fa-file-o','fa-file-pdf-o','fa-file-photo-o','fa-file-picture-o','fa-file-powerpoint-o','fa-file-sound-o','fa-file-text','fa-file-text-o','fa-file-video-o','fa-file-word-o','fa-file-zip-o','fa-files-o','fa-film','fa-filter','fa-fire','fa-fire-extinguisher','fa-firefox','fa-first-order','fa-flag','fa-flag-checkered','fa-flag-o','fa-flash','fa-flask','fa-flickr','fa-floppy-o','fa-folder','fa-folder-o','fa-folder-open','fa-folder-open-o','fa-font','fa-font-awesome','fa-fonticons','fa-fort-awesome','fa-forumbee','fa-forward','fa-foursquare','fa-free-code-camp','fa-frown-o','fa-futbol-o','fa-gamepad','fa-gavel','fa-gbp','fa-ge','fa-gear','fa-gears','fa-genderless','fa-get-pocket','fa-gg','fa-gg-circle','fa-gift','fa-git','fa-git-square','fa-github','fa-github-alt','fa-github-square','fa-gitlab','fa-gittip','fa-glass','fa-glide','fa-glide-g','fa-globe','fa-google','fa-google-plus','fa-google-plus-circle','fa-google-plus-official','fa-google-plus-square','fa-google-wallet','fa-graduation-cap','fa-gratipay','fa-grav','fa-group','fa-h-square','fa-hacker-news','fa-hand-grab-o','fa-hand-lizard-o','fa-hand-o-down','fa-hand-o-left','fa-hand-o-right','fa-hand-o-up','fa-hand-paper-o','fa-hand-peace-o','fa-hand-pointer-o','fa-hand-rock-o','fa-hand-scissors-o','fa-hand-spock-o','fa-hand-stop-o','fa-handshake-o','fa-hard-of-hearing','fa-hashtag','fa-hdd-o','fa-header','fa-headphones','fa-heart','fa-heart-o','fa-heartbeat','fa-history','fa-home','fa-hospital-o','fa-hotel','fa-hourglass','fa-hourglass-1','fa-hourglass-2','fa-hourglass-3','fa-hourglass-end','fa-hourglass-half','fa-hourglass-o','fa-hourglass-start','fa-houzz','fa-html5','fa-i-cursor','fa-id-badge','fa-id-card','fa-id-card-o','fa-ils','fa-image','fa-imdb','fa-inbox','fa-indent','fa-industry','fa-info','fa-info-circle','fa-inr','fa-instagram','fa-institution','fa-internet-explorer','fa-intersex','fa-ioxhost','fa-italic','fa-joomla','fa-jpy','fa-jsfiddle','fa-key','fa-keyboard-o','fa-krw','fa-language','fa-laptop','fa-lastfm','fa-lastfm-square','fa-leaf','fa-leanpub','fa-legal','fa-lemon-o','fa-level-down','fa-level-up','fa-life-bouy','fa-life-buoy','fa-life-ring','fa-life-saver','fa-lightbulb-o','fa-line-chart','fa-link','fa-linkedin','fa-linkedin-square','fa-linode','fa-linux','fa-list','fa-list-alt','fa-list-ol','fa-list-ul','fa-location-arrow','fa-lock','fa-long-arrow-down','fa-long-arrow-left','fa-long-arrow-right','fa-long-arrow-up','fa-low-vision','fa-magic','fa-magnet','fa-mail-forward','fa-mail-reply','fa-mail-reply-all','fa-male','fa-map','fa-map-marker','fa-map-o','fa-map-pin','fa-map-signs','fa-mars','fa-mars-double','fa-mars-stroke','fa-mars-stroke-h','fa-mars-stroke-v','fa-maxcdn','fa-meanpath','fa-medium','fa-medkit','fa-meetup','fa-meh-o','fa-mercury','fa-microchip','fa-microphone','fa-microphone-slash','fa-minus','fa-minus-circle','fa-minus-square','fa-minus-square-o','fa-mixcloud','fa-mobile','fa-mobile-phone','fa-modx','fa-money','fa-moon-o','fa-mortar-board','fa-motorcycle','fa-mouse-pointer','fa-music','fa-navicon','fa-neuter','fa-newspaper-o','fa-object-group','fa-object-ungroup','fa-odnoklassniki','fa-odnoklassniki-square','fa-opencart','fa-openid','fa-opera','fa-optin-monster','fa-outdent','fa-pagelines','fa-paint-brush','fa-paper-plane','fa-paper-plane-o','fa-paperclip','fa-paragraph','fa-paste','fa-pause','fa-pause-circle','fa-pause-circle-o','fa-paw','fa-paypal','fa-pencil','fa-pencil-square','fa-pencil-square-o','fa-percent','fa-phone','fa-phone-square','fa-photo','fa-picture-o','fa-pie-chart','fa-pied-piper','fa-pied-piper-alt','fa-pied-piper-pp','fa-pinterest','fa-pinterest-p','fa-pinterest-square','fa-plane','fa-play','fa-play-circle','fa-play-circle-o','fa-plug','fa-plus','fa-plus-circle','fa-plus-square','fa-plus-square-o','fa-podcast','fa-power-off','fa-print','fa-product-hunt','fa-puzzle-piece','fa-qq','fa-qrcode','fa-question','fa-question-circle','fa-question-circle-o','fa-quora','fa-quote-left','fa-quote-right','fa-ra','fa-random','fa-ravelry','fa-rebel','fa-recycle','fa-reddit','fa-reddit-alien','fa-reddit-square','fa-refresh','fa-registered','fa-remove','fa-renren','fa-reorder','fa-repeat','fa-reply','fa-reply-all','fa-resistance','fa-retweet','fa-rmb','fa-road','fa-rocket','fa-rotate-left','fa-rotate-right','fa-rouble','fa-rss','fa-rss-square','fa-rub','fa-ruble','fa-rupee','fa-s15','fa-safari','fa-save','fa-scissors','fa-scribd','fa-search','fa-search-minus','fa-search-plus','fa-sellsy','fa-send','fa-send-o','fa-server','fa-share','fa-share-alt','fa-share-alt-square','fa-share-square','fa-share-square-o','fa-shekel','fa-sheqel','fa-shield','fa-ship','fa-shirtsinbulk','fa-shopping-bag','fa-shopping-basket','fa-shopping-cart','fa-shower','fa-sign-in','fa-sign-language','fa-sign-out','fa-signal','fa-signing','fa-simplybuilt','fa-sitemap','fa-skyatlas','fa-skype','fa-slack','fa-sliders','fa-slideshare','fa-smile-o','fa-snapchat','fa-snapchat-ghost','fa-snapchat-square','fa-snowflake-o','fa-soccer-ball-o','fa-sort','fa-sort-alpha-asc','fa-sort-alpha-desc','fa-sort-amount-asc','fa-sort-amount-desc','fa-sort-asc','fa-sort-desc','fa-sort-down','fa-sort-numeric-asc','fa-sort-numeric-desc','fa-sort-up','fa-soundcloud','fa-space-shuttle','fa-spinner','fa-spoon','fa-spotify','fa-square','fa-square-o','fa-stack-exchange','fa-stack-overflow','fa-star','fa-star-half','fa-star-half-empty','fa-star-half-full','fa-star-half-o','fa-star-o','fa-steam','fa-steam-square','fa-step-backward','fa-step-forward','fa-stethoscope','fa-sticky-note','fa-sticky-note-o','fa-stop','fa-stop-circle','fa-stop-circle-o','fa-street-view','fa-strikethrough','fa-stumbleupon','fa-stumbleupon-circle','fa-subscript','fa-subway','fa-suitcase','fa-sun-o','fa-superpowers','fa-superscript','fa-support','fa-table','fa-tablet','fa-tachometer','fa-tag','fa-tags','fa-tasks','fa-taxi','fa-telegram','fa-television','fa-tencent-weibo','fa-terminal','fa-text-height','fa-text-width','fa-th','fa-th-large','fa-th-list','fa-themeisle','fa-thermometer','fa-thermometer-0','fa-thermometer-1','fa-thermometer-2','fa-thermometer-3','fa-thermometer-4','fa-thermometer-empty','fa-thermometer-full','fa-thermometer-half','fa-thermometer-quarter','fa-thermometer-three-quarters','fa-thumb-tack','fa-thumbs-down','fa-thumbs-o-down','fa-thumbs-o-up','fa-thumbs-up','fa-ticket','fa-times','fa-times-circle','fa-times-circle-o','fa-times-rectangle','fa-times-rectangle-o','fa-tint','fa-toggle-down','fa-toggle-left','fa-toggle-off','fa-toggle-on','fa-toggle-right','fa-toggle-up','fa-trademark','fa-train','fa-transgender','fa-transgender-alt','fa-trash','fa-trash-o','fa-tree','fa-trello','fa-tripadvisor','fa-trophy','fa-truck','fa-try','fa-tty','fa-tumblr','fa-tumblr-square','fa-turkish-lira','fa-tv','fa-twitch','fa-twitter','fa-twitter-square','fa-umbrella','fa-underline','fa-undo','fa-universal-access','fa-university','fa-unlink','fa-unlock','fa-unlock-alt','fa-unsorted','fa-upload','fa-usb','fa-usd','fa-user','fa-user-circle','fa-user-circle-o','fa-user-md','fa-user-o','fa-user-plus','fa-user-secret','fa-user-times','fa-users','fa-vcard','fa-vcard-o','fa-venus','fa-venus-double','fa-venus-mars','fa-viacoin','fa-viadeo','fa-viadeo-square','fa-video-camera','fa-vimeo','fa-vimeo-square','fa-vine','fa-vk','fa-volume-control-phone','fa-volume-down','fa-volume-off','fa-volume-up','fa-warning','fa-wechat','fa-weibo','fa-weixin','fa-whatsapp','fa-wheelchair','fa-wheelchair-alt','fa-wifi','fa-wikipedia-w','fa-window-close','fa-window-close-o','fa-window-maximize','fa-window-minimize','fa-window-restore','fa-windows','fa-won','fa-wordpress','fa-wpbeginner','fa-wpexplorer','fa-wpforms','fa-wrench','fa-xing','fa-xing-square','fa-y-combinator','fa-y-combinator-square','fa-yahoo','fa-yc','fa-yc-square','fa-yelp','fa-yen','fa-yoast','fa-youtube','fa-youtube-play','fa-youtube-square');
	var $sort_methods = array('date-za', 'date-az', 'title-za', 'title-az');
	var $local_fonts = array(
		'inherit' => 'Inherit',
		'arial' => 'Arial',
		'verdana' => 'Verdana'
	);
	var $phone_masks = array(
		'none' => 'None',
		'(000)000-0000' => '(000)000-0000',
		'+0(000)000-0000' => '+0(000)000-0000',
		'+00(000)000-0000' => '+00(000)000-0000',
		'(00)0000-0000' => '(00)0000-0000',
		'custom' => 'Custom Mask'
	);
	var $alignments = array(
		'inherit' => 'Inherit',
		'left' => 'Left',
		'right' => 'Right',
		'center' => 'Center',
		'justify' => 'Justify'
	);
	var $background_repeats = array(
		'repeat' => 'Repeat',
		'repeat-x' => 'Repeat X',
		'repeat-y' => 'Repeat Y',
		'no-repeat' => 'No Repeat'
	);
	var $background_sizes = array(
		'auto' => 'Original',
		'cover' => 'Cover',
		'contain' => 'Contain'
	);
	var $border_styles = array(
		'none' => 'None',
		'dotted' => 'Dotted',
		'dashed' => 'Dashed',
		'solid' => 'Solid',
		'double' => 'Double',
		'groove' => 'Groove',
		'ridge' => 'Ridge',
		'inset' => 'Inset',
		'outset' => 'Outset'
	);
	var $display_modes = array(
		'none' => 'Disable popup',
		'every-time' => 'Every time', 
		'once-session' => 'Once per session',
		'once-period' => 'Once per %X days',
		'once-only' => 'Only once'
	);
	var $appearances = array(
		'fade-in' => 'Fade In',
		'slide-up' => 'Slide Up',
		'slide-down' => 'Slide Down',
		'slide-left' => 'Slide Left',
		'slide-right' => 'Slide Right'
	);
	var $css3_appearances = array(
		'bounceIn' => 'Bounce',
		'bounceInUp' => 'Bounce Up',
		'bounceInDown' => 'Bounce Down',
		'bounceInLeft' => 'Bounce Right',
		'bounceInRight' => 'Bounce Left',
		'fadeIn' => 'Fade',
		'fadeInUp' => 'Fade Up',
		'fadeInDown' => 'Fade Down',
		'fadeInLeft' => 'Fade Right',
		'fadeInRight' => 'Fade Left',
		'flipInX' => 'Flip X',
		'flipInY' => 'Flip Y',
		'lightSpeedIn' => 'Light Speed',
		'rotateIn' => 'Rotate',
		'rotateInDownLeft' => 'Rotate Down Left',
		'rotateInDownRight' => 'Rotate Down Right',
		'rotateInUpLeft' => 'Rotate Up Left',
		'rotateInUpRight' => 'Rotate Up Right',
		'rollIn' => 'Roll',
		'zoomIn' => 'Zoom',
		'zoomInUp' => 'Zoom Up',
		'zoomInDown' => 'Zoom Down',
		'zoomInLeft' => 'Zoom Right',
		'zoomInRight' => 'Zoom Left'
	);
	var $font_weights = array(
		'inherit' => 'Inherit',
		'100' => 'Thin',
		'200' => 'Extra-light',
		'300' => 'Light',
		'400' => 'Normal',
		'500' => 'Medium',
		'600' => 'Demi-bold',
		'700' => 'Bold',
		'800' => 'Heavy',
		'900' => 'Black'
	);
	var $ajax_spinners = array(
		'classic' => '<div class="ulp-spinner ulp-spinner-classic"></div>',
		'chasing-dots' => '<div class="ulp-spinner ulp-spinner-chasing-dots"><div class="ulp-spinner-child ulp-spinner-dot1"></div><div class="ulp-spinner-child ulp-spinner-dot2"></div></div>',
		'circle' => '<div class="ulp-spinner ulp-spinner-circle"><div class="ulp-spinner-circle1 ulp-spinner-child"></div><div class="ulp-spinner-circle2 ulp-spinner-child"></div><div class="ulp-spinner-circle3 ulp-spinner-child"></div><div class="ulp-spinner-circle4 ulp-spinner-child"></div><div class="ulp-spinner-circle5 ulp-spinner-child"></div><div class="ulp-spinner-circle6 ulp-spinner-child"></div><div class="ulp-spinner-circle7 ulp-spinner-child"></div><div class="ulp-spinner-circle8 ulp-spinner-child"></div><div class="ulp-spinner-circle9 ulp-spinner-child"></div><div class="ulp-spinner-circle10 ulp-spinner-child"></div><div class="ulp-spinner-circle11 ulp-spinner-child"></div><div class="ulp-spinner-circle12 ulp-spinner-child"></div></div>',
		'double-bounce' => '<div class="ulp-spinner ulp-spinner-double-bounce"><div class="ulp-spinner-child ulp-spinner-double-bounce1"></div><div class="ulp-spinner-child ulp-spinner-double-bounce2"></div></div>',
		'fading-circle' => '<div class="ulp-spinner ulp-spinner-fading-circle"><div class="ulp-spinner-circle1 ulp-spinner-child"></div><div class="ulp-spinner-circle2 ulp-spinner-child"></div><div class="ulp-spinner-circle3 ulp-spinner-child"></div><div class="ulp-spinner-circle4 ulp-spinner-child"></div><div class="ulp-spinner-circle5 ulp-spinner-child"></div><div class="ulp-spinner-circle6 ulp-spinner-child"></div><div class="ulp-spinner-circle7 ulp-spinner-child"></div><div class="ulp-spinner-circle8 ulp-spinner-child"></div><div class="ulp-spinner-circle9 ulp-spinner-child"></div><div class="ulp-spinner-circle10 ulp-spinner-child"></div><div class="ulp-spinner-circle11 ulp-spinner-child"></div><div class="ulp-spinner-circle12 ulp-spinner-child"></div></div>',
		'folding-cube' => '<div class="ulp-spinner ulp-spinner-folding-cube"><div class="ulp-spinner-cube1 ulp-spinner-child"></div><div class="ulp-spinner-cube2 ulp-spinner-child"></div><div class="ulp-spinner-cube4 ulp-spinner-child"></div><div class="ulp-spinner-cube3 ulp-spinner-child"></div></div>',
		'pulse' => '<div class="ulp-spinner ulp-spinner-spinner-pulse"></div>',
		'rotating-plane' => '<div class="ulp-spinner ulp-spinner-rotating-plane"></div>',
		'three-bounce' => '<div class="ulp-spinner ulp-spinner-three-bounce"><div class="ulp-spinner-child ulp-spinner-bounce1"></div><div class="ulp-spinner-child ulp-spinner-bounce2"></div><div class="ulp-spinner-child ulp-spinner-bounce3"></div></div>',
		'wandering-cubes' => '<div class="ulp-spinner ulp-spinner-wandering-cubes"><div class="ulp-spinner-child ulp-spinner-cube1"></div><div class="ulp-spinner-child ulp-spinner-cube2"></div></div>',
		'wave' => '<div class="ulp-spinner ulp-spinner-wave"><div class="ulp-spinner-child ulp-spinner-rect1"></div><div class="ulp-spinner-child ulp-spinner-rect2"></div><div class="ulp-spinner-child ulp-spinner-rect3"></div><div class="ulp-spinner-child ulp-spinner-rect4"></div><div class="ulp-spinner-child ulp-spinner-rect5"></div></div>'
	);
	
	var $default_popup_options = array(
		"title" => "",
		"width" => "640",
		"height" => "400",
		'position' => 'middle-center',
		'disable_overlay' => 'off',
		"overlay_color" => "#333333",
		"overlay_opacity" => 0.8,
		"overlay_animation" => "fadeIn",
		"ajax_spinner" => "classic",
		"ajax_spinner_color" => "#ffffff",
		"enable_close" => "on",
		"enable_enter" => "on",
		'name_placeholder' => 'Enter your name...',
		'email_placeholder' => 'Enter your e-mail...',
		'phone_placeholder' => 'Enter your phone number...',
		'message_placeholder' => 'Enter your message...',
		'email_mandatory' => 'on',
		'name_mandatory' => 'off',
		'phone_mandatory' => 'off',
		'message_mandatory' => 'off',
		'phone_mask' => 'none',
		'phone_custom_mask' => '(000)000-0000',
		'button_label' => 'Subscribe',
		'button_icon' => 'fa-noicon',
		'button_label_loading' => 'Loading...',
		'button_color' => '#0147A3',
		'button_border_radius' => 2,
		'button_gradient' => 'on',
		'button_inherit_size' => 'off',
		'button_css' => '',
		'button_css_hover' => '',
		'input_border_color' => '#444444',
		'input_border_width' => 1,
		'input_border_radius' => 2,
		'input_background_color' => '#FFFFFF',
		'input_background_opacity' => 0.7,
		'input_icons' => 'off',
		'input_css' => '',
		'recaptcha_mandatory' => 'off',
		'recaptcha_theme' => 'light',
		'return_url' => '',
		'close_delay' => 0,
		'thanksgiving_popup' => '',
		'cookie_lifetime' => 360,
		"doubleoptin_enable" => "off",
		"doubleoptin_subject" => "",
		"doubleoptin_message" => "",
		"doubleoptin_confirmation_message" => "",
		"doubleoptin_redirect_url" => ""
	);
	var $default_layer_options = array(
		"title" => "New Layer",
		"content" => "",
		"width" => "",
		"height" => "",
		"scrollbar" => "off",
		"left" => 0,
		"top" => 0,
		"background_color" => "",
		"background_hover_color" => "",
		"background_gradient" => "off",
		"background_gradient_to" => "",
		"background_gradient_angle" => "135",
		"background_hover_gradient_to" => "",
		"background_opacity" => 1,
		"background_image" => "",
		"background_image_repeat" => "repeat",
		"background_image_size" => "auto",
		"border_width" => 1,
		"border_style" => 'none',
		"border_color" => "",
		"border_hover_color" => "",
		"border_radius" => 0,
		"box_shadow" => "off",
		"box_shadow_h" => 0,
		"box_shadow_v" => 5,
		"box_shadow_blur" => 20,
		"box_shadow_spread" => 0,
		"box_shadow_color" => "#202020",
		"box_shadow_inset" => "off",
		"content_align" => "left",
		"padding_v" => 0,
		"padding_h" => 0,
		"index" => 5,
		"appearance" => "fade-in",
		"appearance_delay" => "200",
		"appearance_speed" => "1000",
		"font" => "arial",
		"font_color" => "#000000",
		"font_hover_color" => "",
		"font_weight" => "400",
		"font_size" => 14,
		"text_shadow_size" => 0,
		"text_shadow_color" => "#000000",
		"confirmation_layer" => "off",
		"inline_disable" => "off",
		"style" => ""
	);
	var $ext_options = array(
		'enable_customfields' => 'off',
		'enable_js' => 'off',
		'enable_social' => 'off',
		'enable_social2' => 'off',
		'enable_mailchimp' => 'on',
		'enable_rapidmail' => 'off',
		'enable_omnisend' => 'off',
		'enable_dotmailer' => 'off',
		'enable_mnb' => 'off',
		'enable_markethero' => 'off',
		'enable_kirimemail' => 'off',
		'enable_squalomail' => 'off',
		'enable_unisender' => 'off',
		'enable_moosend' => 'off',
		'enable_zohocampaigns' => 'off',
		'enable_zohocrm' => 'off',
		'enable_mailigen' => 'off',
		'enable_sendloop' => 'off',
		'enable_perfit' => 'off',
		'enable_newsletter2go' => 'off',
		'enable_acellemail' => 'off',
		'enable_streamsend' => 'off',
		'enable_vision6' => 'off',
		'enable_mailleader' => 'off',
		'enable_mpzmail' => 'off',
		'enable_stampready' => 'off',
		'enable_mautic' => 'off',
		'enable_emailoctopus' => 'off',
		'enable_intercom' => 'off',
		'enable_firedrum' => 'off',
		'enable_activetrail' => 'off',
		'enable_userengage' => 'off',
		'enable_jetpack' => 'off',
		'enable_pipedrive' => 'off',
		'enable_sgautorepondeur' => 'off',
		'enable_drip' => 'off',
		'enable_sendlane' => 'off',
		'enable_emma' => 'off',
		'enable_hubspot' => 'off',
		'enable_esputnik' => 'off',
		'enable_thenewsletterplugin' => 'off',
		'enable_klaviyo' => 'off',
		'enable_easysendypro' => 'off',
		'enable_cleverreach' => 'off',
		'enable_mailkitchen' => 'off',
		'enable_rocketresponder' => 'off',
		'enable_salesmanago' => 'off',
		'enable_agilecrm' => 'off',
		'enable_simplycast' => 'off',
		'enable_convertkit' => 'off',
		'enable_totalsend' => 'off',
		'enable_campayn' => 'off',
		'enable_sendinblue' => 'off',
		'enable_sendgrid' => 'off',
		'enable_elasticemail' => 'off',
		'enable_egoi' => 'off',
		'enable_aweber' => 'off',
		'enable_getresponse' => 'off',
		'enable_icontact' => 'off',
		'enable_madmimi' => 'off',
		'enable_campaignmonitor' => 'off',
		'enable_salesautopilot' => 'off',
		'enable_sendy' => 'off',
		'enable_interspire' => 'off',
		'enable_benchmark' => 'off',
		'enable_activecampaign' => 'off',
		'enable_ontraport' => 'off',
		'enable_mailerlite' => 'off',
		'enable_mailrelay' => 'off',
		'enable_mymail' => 'off',
		'enable_fue' => 'off',
		'enable_mailboxmarketing' => 'off',
		'enable_enewsletter' => 'off',
		'enable_arigatopro' => 'off',
		'enable_subscribe2' => 'off',
		'enable_mailpoet' => 'off',
		'enable_tribulant' => 'off',
		'enable_sendpress' => 'off',
		'enable_ymlp' => 'off',
		'enable_freshmail' => 'off',
		'enable_sendreach' => 'off',
		'enable_constantcontact' => 'off',
		'enable_directmail' => 'off',
		'enable_htmlform' => 'off',
		'enable_wpuser' => 'off',
		'enable_mail' => 'on',
		'enable_welcomemail' => 'on',
		'enable_mailwizz' => 'off',
		'enable_avangemail' => 'off',
		'enable_mailautic' => 'off',
		'enable_customerio' => 'off',
		'enable_klicktipp' => 'off',
		'enable_sendpulse' => 'off',
		'enable_mailjet' => 'off',
		'enable_algocheck' => 'on',
		'enable_bulkemailchecker' => 'off',
		'enable_thechecker' => 'off',
		'enable_emaillistverify' => 'off',
		'enable_proofy' => 'off',
		'enable_kickbox' => 'off',
		'enable_neverbounce' => 'off',
		'enable_hunter' => 'off',
		'late_init' => 'off',
		'minified_sources' => 'on',
		'enable_library' => 'on',
		'enable_addons' => 'on',
		'enable_remote' => 'off',
		'admin_only_meta' => 'on',
		'inline_ajaxed' => 'off',
		'log_data' => 'on',
		'advanced_targeting' => 'off',
		'count_impressions' => 'on',
		'async_init' => 'on',
		'clean_database' => 'off'
	);
	var $default_meta = array(
		"version" => ULP_VERSION,
		"onload_mode" => 'default',
		"onload_period" => '5',
		"onload_delay" => 0,
		"onload_close_delay" => 0,
		"onload_popup" => 'default',
		"onload_popup_mobile" => 'default',
		"onexit_mode" => 'default',
		"onexit_period" => '5',
		"onexit_popup" => 'default',
		"onexit_popup_mobile" => 'default',
		"onscroll_popup" => 'default',
		"onscroll_popup_mobile" => 'default',
		"onscroll_mode" => 'default',
		"onscroll_period" => '5',
		"onscroll_offset" => 600,
		"onidle_mode" => 'default',
		"onidle_delay" => 30,
		"onidle_period" => '5',
		"onidle_popup" => 'default',
		"onidle_popup_mobile" => 'default',
		"onabd_mode" => 'default',
		"onabd_period" => '5',
		"onabd_popup" => 'default',
		"onabd_popup_mobile" => 'default'
	);
	var $user_statuses = array(
		ULP_SUBSCRIBER_UNCONFIRMED => array('label' => 'Unconfirmed', 'class' => 'ulp-badge ulp-badge-unconfirmed'),
		ULP_SUBSCRIBER_CONFIRMED => array('label' => 'Confirmed', 'class' => 'ulp-badge ulp-badge-confirmed')
	);
	var $events = array(
		'onload' => array(
			'label' => 'OnLoad',
			'description' => 'Popups are displayed when webpage loaded.'
		),
		'onscroll' => array(
			'label' => 'OnScroll',
			'description' => 'Popups are displayed when user scroll down webpage.'
		),
		'onexit' => array(
			'label' => 'OnExit',
			'description' => 'Popups are displayed when user moves mouse cursor to top edge of browser window, assuming that he/she is going to leave the page.'
		),
		'onidle' => array(
			'label' => 'OnInactivity',
			'description' => 'Popups are displayed when user does nothing on your website (move mouse cursor, press buttons, touch screen) for certain period of time.'
		),
		'onabd' => array(
			'label' => 'OnAdblockDetected',
			'description' => 'Popups are displayed if AdBlock (or similar) software detected.'
		)
	);
	function __construct() {
		global $ulp_admin, $ulp_social2;
		if (function_exists('load_plugin_textdomain')) {
			load_plugin_textdomain('ulp', false, dirname(plugin_basename(__FILE__)).'/languages/');
		}
		$this->plugins_url = plugins_url('', __FILE__);
		
		$url = get_bloginfo('url');
		$domain = parse_url($url, PHP_URL_HOST);
		$this->options = array(
			"version" => ULP_VERSION,
			"webfonts_version" => 0,
			"post_method" => "array",
			"cookie_value" => 'ilovelencha',
			"onload_mode" => 'none',
			"onload_period" => '5',
			"onload_delay" => 0,
			"onload_close_delay" => 0,
			"onload_popup" => '',
			"onload_popup_mobile" => 'same',
			"onexit_mode" => 'none',
			"onexit_period" => '5',
			"onexit_popup" => '',
			"onexit_popup_mobile" => 'same',
			"onscroll_mode" => 'none',
			"onscroll_period" => '5',
			"onscroll_popup" => '',
			"onscroll_popup_mobile" => 'same',
			"onscroll_offset" => 600,
			"onidle_mode" => 'none',
			"onidle_delay" => 30,
			"onidle_period" => '5',
			"onidle_popup" => '',
			"onidle_popup_mobile" => 'same',
			"onabd_mode" => 'none',
			"onabd_period" => '5',
			"onabd_popup" => '',
			"onabd_popup_mobile" => 'same',
			"onexit_limits" => 'off',
			"csv_separator" => ";",
			"email_validation" => "off",
			"ga_tracking" => "off",
			"km_tracking" => "off",
			"css3_enable" => "on",
			"fa_enable" => "off",
			"perfectscrollbar_enable" => "off",
			"spinkit_enable" => "on",
			"linkedbuttons_enable" => "on",
			"fa_css_disable" => "off",
			"mask_enable" => "off",
			"mask_js_disable" => "off",
			"recaptcha_enable" => "off",
			"recaptcha_js_disable" => "off",
			"recaptcha_public_key" => "",
			"recaptcha_secret_key" => "",
			"no_preload" => 'on',
			"preload_event_popups" => 'off',
			"from_type" => 'html',
			"from_name" => get_bloginfo('name'),
			"from_email" => "noreply@".str_replace("www.", "", $domain),
			"popups_sort" => 'date-za',
			"campaigns_sort" => 'date-za',
			"subscribers_sort" => 'date-za',
			"purchase_code" => ''
		);

		$this->get_ext_options();
		$this->get_options();
		if (defined('UAP_CORE')) $this->options['no_preload'] = 'on';

		if (!class_exists('SoapClient')) {
			$this->ext_options = array_merge($this->ext_options, array(
				'enable_cleverreach' => 'off',
				'enable_mailkitchen' => 'off'
			));
		}
		if (!in_array('curl', get_loaded_extensions())) {
			$this->ext_options = array_merge($this->ext_options, array(
				'enable_library' => 'off',
				'enable_addons' => 'off',
				'enable_mailchimp' => 'off',
				'enable_rapidmail' => 'off',
				'enable_omnisend' => 'off',
				'enable_dotmailer' => 'off',
				'enable_mnb' => 'off',
				'enable_markethero' => 'off',
				'enable_kirimemail' => 'off',
				'enable_squalomail' => 'off',
				'enable_unisender' => 'off',
				'enable_moosend' => 'off',
				'enable_zohocampaigns' => 'off',
				'enable_zohocrm' => 'off',
				'enable_mailigen' => 'off',
				'enable_sendloop' => 'off',
				'enable_perfit' => 'off',
				'enable_newsletter2go' => 'off',
				'enable_acellemail' => 'off',
				'enable_streamsend' => 'off',
				'enable_vision6' => 'off',
				'enable_mailleader' => 'off',
				'enable_mpzmail' => 'off',
				'enable_stampready' => 'off',
				'enable_mautic' => 'off',
				'enable_emailoctopus' => 'off',
				'enable_intercom' => 'off',
				'enable_firedrum' => 'off',
				'enable_activetrail' => 'off',
				'enable_userengage' => 'off',
				'enable_pipedrive' => 'off',
				'enable_sgautorepondeur' => 'off',
				'enable_sendlane' => 'off',
				'enable_emma' => 'off',
				'enable_hubspot' => 'off',
				'enable_esputnik' => 'off',
				'enable_klaviyo' => 'off',
				'enable_easysendypro' => 'off',
				'enable_rocketresponder' => 'off',
				'enable_salesmanago' => 'off',
				'enable_agilecrm' => 'off',
				'enable_simplycast' => 'off',
				'enable_convertkit' => 'off',
				'enable_totalsend' => 'off',
				'enable_campayn' => 'off',
				'enable_drip' => 'off',
				'enable_sendinblue' => 'off',
				'enable_klicktipp' => 'off',
				'enable_sendpulse' => 'off',
				'enable_mailjet' => 'off',
				'enable_sendgrid' => 'off',
				'enable_elasticemail' => 'off',
				'enable_egoi' => 'off',
				'enable_customerio' => 'off',
				'enable_mailwizz' => 'off',
				'enable_avangemail' => 'off',
				'enable_mailautic' => 'off',
				'enable_icontact' => 'off',
				'enable_getresponse' => 'off',
				'enable_madmimi' => 'off',
				'enable_directmail' => 'off',
				'enable_campaignmonitor' => 'off',
				'enable_salesautopilot' => 'off',
				'enable_activecampaign' => 'off',
				'enable_benchmark' => 'off',
				'enable_sendy' => 'off',
				'enable_interspire' => 'off',
				'enable_ontraport' => 'off',
				'enable_mailerlite' => 'off',
				'enable_mailrelay' => 'off',
				'enable_ymlp' => 'off',
				'enable_sendreach' => 'off',
				'enable_aweber' => 'off',
				'enable_constantcontact' => 'off',
				'enable_htmlform' => 'off',
				'enable_freshmail' => 'off',
				'enable_algocheck' => 'off',
				'enable_bulkemailchecker' => 'off',
				'enable_thechecker' => 'off',
				'enable_emaillistverify' => 'off',
				'enable_proofy' => 'off',
				'enable_kickbox' => 'off',
				'enable_neverbounce' => 'off',
				'enable_hunter' => 'off'
			));
		}
		if (file_exists(dirname(__FILE__).'/modules/ulp-algocheck.php') && $this->ext_options['enable_algocheck'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-algocheck.php');
		if (file_exists(dirname(__FILE__).'/modules/ulp-bulkemailchecker.php') && $this->ext_options['enable_bulkemailchecker'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-bulkemailchecker.php');
		if (file_exists(dirname(__FILE__).'/modules/ulp-emaillistverify.php') && $this->ext_options['enable_emaillistverify'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-emaillistverify.php');
		if (file_exists(dirname(__FILE__).'/modules/ulp-hunter.php') && $this->ext_options['enable_hunter'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-hunter.php');
		if (file_exists(dirname(__FILE__).'/modules/ulp-kickbox.php') && $this->ext_options['enable_kickbox'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-kickbox.php');
		if (file_exists(dirname(__FILE__).'/modules/ulp-neverbounce.php') && $this->ext_options['enable_neverbounce'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-neverbounce.php');
		if (file_exists(dirname(__FILE__).'/modules/ulp-proofy.php') && $this->ext_options['enable_proofy'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-proofy.php');
		if (file_exists(dirname(__FILE__).'/modules/ulp-thechecker.php') && $this->ext_options['enable_thechecker'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-thechecker.php');
		
		if (file_exists(dirname(__FILE__).'/modules/ulp-custom-fields.php') && $this->ext_options['enable_customfields'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-custom-fields.php');
		if (file_exists(dirname(__FILE__).'/modules/ulp-social.php') && $this->ext_options['enable_social'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-social.php');
		if (file_exists(dirname(__FILE__).'/modules/ulp-social2.php') && $this->ext_options['enable_social2'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-social2.php');
		if (file_exists(dirname(__FILE__).'/modules/ulp-mail.php') && $this->ext_options['enable_mail'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-mail.php');
		if (file_exists(dirname(__FILE__).'/modules/ulp-welcomemail.php') && $this->ext_options['enable_welcomemail'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-welcomemail.php');
		
		if (file_exists(dirname(__FILE__).'/modules/ulp-acellemail.php') && $this->ext_options['enable_acellemail'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-acellemail.php');
		if (file_exists(dirname(__FILE__).'/modules/ulp-active-campaign.php') && $this->ext_options['enable_activecampaign'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-active-campaign.php');
		if (file_exists(dirname(__FILE__).'/modules/ulp-activetrail.php') && $this->ext_options['enable_activetrail'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-activetrail.php');
		
		if (file_exists(dirname(__FILE__).'/modules/ulp-agilecrm.php') && $this->ext_options['enable_agilecrm'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-agilecrm.php');
		if (!defined('UAP_CORE')) {
			if (file_exists(dirname(__FILE__).'/modules/ulp-arigatopro.php') && $this->ext_options['enable_arigatopro'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-arigatopro.php');
		}
		if (file_exists(dirname(__FILE__).'/modules/ulp-avangemail.php') && $this->ext_options['enable_avangemail'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-avangemail.php');
		if (file_exists(dirname(__FILE__).'/modules/ulp-aweber.php') && $this->ext_options['enable_aweber'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-aweber.php');
		if (file_exists(dirname(__FILE__).'/modules/ulp-benchmark.php') && $this->ext_options['enable_benchmark'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-benchmark.php');
		if (file_exists(dirname(__FILE__).'/modules/ulp-campaign-monitor.php') && $this->ext_options['enable_campaignmonitor'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-campaign-monitor.php');
		if (file_exists(dirname(__FILE__).'/modules/ulp-campayn.php') && $this->ext_options['enable_campayn'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-campayn.php');
		if (file_exists(dirname(__FILE__).'/modules/ulp-cleverreach.php') && $this->ext_options['enable_cleverreach'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-cleverreach.php');
		if (file_exists(dirname(__FILE__).'/modules/ulp-constant-contact.php') && $this->ext_options['enable_constantcontact'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-constant-contact.php');
		if (file_exists(dirname(__FILE__).'/modules/ulp-convertkit.php') && $this->ext_options['enable_convertkit'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-convertkit.php');
		if (file_exists(dirname(__FILE__).'/modules/ulp-customerio.php') && $this->ext_options['enable_customerio'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-customerio.php');
		if (file_exists(dirname(__FILE__).'/modules/ulp-direct-mail.php') && $this->ext_options['enable_directmail'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-direct-mail.php');
		if (file_exists(dirname(__FILE__).'/modules/ulp-dotmailer.php') && $this->ext_options['enable_dotmailer'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-dotmailer.php');
		if (file_exists(dirname(__FILE__).'/modules/ulp-drip.php') && $this->ext_options['enable_drip'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-drip.php');
		if (file_exists(dirname(__FILE__).'/modules/ulp-easysendypro.php') && $this->ext_options['enable_easysendypro'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-easysendypro.php');
		if (file_exists(dirname(__FILE__).'/modules/ulp-egoi.php') && $this->ext_options['enable_egoi'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-egoi.php');
		if (file_exists(dirname(__FILE__).'/modules/ulp-elasticemail.php') && $this->ext_options['enable_elasticemail'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-elasticemail.php');
		if (file_exists(dirname(__FILE__).'/modules/ulp-emailoctopus.php') && $this->ext_options['enable_emailoctopus'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-emailoctopus.php');
		if (file_exists(dirname(__FILE__).'/modules/ulp-emma.php') && $this->ext_options['enable_emma'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-emma.php');
		if (!defined('UAP_CORE')) {
			if (file_exists(dirname(__FILE__).'/modules/ulp-enewsletter.php') && $this->ext_options['enable_enewsletter'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-enewsletter.php');
		}
		if (file_exists(dirname(__FILE__).'/modules/ulp-esputnik.php') && $this->ext_options['enable_esputnik'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-esputnik.php');
		if (file_exists(dirname(__FILE__).'/modules/ulp-firedrum.php') && $this->ext_options['enable_firedrum'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-firedrum.php');
		if (!defined('UAP_CORE')) {
			if (file_exists(dirname(__FILE__).'/modules/ulp-fue.php') && $this->ext_options['enable_fue'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-fue.php');
		}
		if (file_exists(dirname(__FILE__).'/modules/ulp-freshmail.php') && $this->ext_options['enable_freshmail'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-freshmail.php');
		if (file_exists(dirname(__FILE__).'/modules/ulp-getresponse.php') && $this->ext_options['enable_getresponse'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-getresponse.php');
		if (file_exists(dirname(__FILE__).'/modules/ulp-hubspot.php') && $this->ext_options['enable_hubspot'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-hubspot.php');
		if (file_exists(dirname(__FILE__).'/modules/ulp-icontact.php') && $this->ext_options['enable_icontact'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-icontact.php');
		if (file_exists(dirname(__FILE__).'/modules/ulp-intercom.php') && $this->ext_options['enable_intercom'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-intercom.php');
		if (file_exists(dirname(__FILE__).'/modules/ulp-interspire.php') && $this->ext_options['enable_interspire'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-interspire.php');
		if (!defined('UAP_CORE')) {
			if (file_exists(dirname(__FILE__).'/modules/ulp-jetpack.php') && $this->ext_options['enable_jetpack'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-jetpack.php');
		}
		if (file_exists(dirname(__FILE__).'/modules/ulp-kirimemail.php') && $this->ext_options['enable_kirimemail'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-kirimemail.php');
		if (file_exists(dirname(__FILE__).'/modules/ulp-klaviyo.php') && $this->ext_options['enable_klaviyo'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-klaviyo.php');
		if (file_exists(dirname(__FILE__).'/modules/ulp-klicktipp.php') && $this->ext_options['enable_klicktipp'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-klicktipp.php');
		if (file_exists(dirname(__FILE__).'/modules/ulp-mad-mimi.php') && $this->ext_options['enable_madmimi'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-mad-mimi.php');
		if (file_exists(dirname(__FILE__).'/modules/ulp-mailautic.php') && $this->ext_options['enable_mailautic'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-mailautic.php');
		if (!defined('UAP_CORE')) {
			if (file_exists(dirname(__FILE__).'/modules/ulp-mailboxmarketing.php') && $this->ext_options['enable_mailboxmarketing'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-mailboxmarketing.php');
		}
		if (file_exists(dirname(__FILE__).'/modules/ulp-mailchimp.php') && $this->ext_options['enable_mailchimp'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-mailchimp.php');
		if (file_exists(dirname(__FILE__).'/modules/ulp-mailerlite.php') && $this->ext_options['enable_mailerlite'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-mailerlite.php');
		if (file_exists(dirname(__FILE__).'/modules/ulp-mailigen.php') && $this->ext_options['enable_mailigen'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-mailigen.php');
		if (file_exists(dirname(__FILE__).'/modules/ulp-mailjet.php') && $this->ext_options['enable_mailjet'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-mailjet.php');
		if (file_exists(dirname(__FILE__).'/modules/ulp-mailkitchen.php') && $this->ext_options['enable_mailkitchen'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-mailkitchen.php');
		if (file_exists(dirname(__FILE__).'/modules/ulp-mailleader.php') && $this->ext_options['enable_mailleader'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-mailleader.php');
		if (!defined('UAP_CORE')) {
			if (file_exists(dirname(__FILE__).'/modules/ulp-mailpoet.php') && $this->ext_options['enable_mailpoet'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-mailpoet.php');
		}
		if (file_exists(dirname(__FILE__).'/modules/ulp-mailrelay.php') && $this->ext_options['enable_mailrelay'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-mailrelay.php');
		if (!defined('UAP_CORE')) {
			if (file_exists(dirname(__FILE__).'/modules/ulp-mymail.php') && $this->ext_options['enable_mymail'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-mymail.php');
		}
		if (file_exists(dirname(__FILE__).'/modules/ulp-mailwizz.php') && $this->ext_options['enable_mailwizz'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-mailwizz.php');
		if (file_exists(dirname(__FILE__).'/modules/ulp-markethero.php') && $this->ext_options['enable_markethero'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-markethero.php');
		if (file_exists(dirname(__FILE__).'/modules/ulp-mautic.php') && $this->ext_options['enable_mautic'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-mautic.php');
		if (file_exists(dirname(__FILE__).'/modules/ulp-moosend.php') && $this->ext_options['enable_moosend'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-moosend.php');
		if (file_exists(dirname(__FILE__).'/modules/ulp-mpzmail.php') && $this->ext_options['enable_mpzmail'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-mpzmail.php');
		if (file_exists(dirname(__FILE__).'/modules/ulp-mnb.php') && $this->ext_options['enable_mnb'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-mnb.php');
		//if (file_exists(dirname(__FILE__).'/modules/ulp-newsletter2go.php') && $this->ext_options['enable_newsletter2go'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-newsletter2go.php');
		if (file_exists(dirname(__FILE__).'/modules/ulp-omnisend.php') && $this->ext_options['enable_omnisend'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-omnisend.php');
		if (file_exists(dirname(__FILE__).'/modules/ulp-ontraport.php') && $this->ext_options['enable_ontraport'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-ontraport.php');
		if (file_exists(dirname(__FILE__).'/modules/ulp-perfit.php') && $this->ext_options['enable_perfit'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-perfit.php');
		if (file_exists(dirname(__FILE__).'/modules/ulp-pipedrive.php') && $this->ext_options['enable_pipedrive'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-pipedrive.php');
		if (file_exists(dirname(__FILE__).'/modules/ulp-rapidmail.php') && $this->ext_options['enable_rapidmail'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-rapidmail.php');
		if (file_exists(dirname(__FILE__).'/modules/ulp-rocketresponder.php') && $this->ext_options['enable_rocketresponder'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-rocketresponder.php');
		if (file_exists(dirname(__FILE__).'/modules/ulp-sales-autopilot.php') && $this->ext_options['enable_salesautopilot'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-sales-autopilot.php');
		if (file_exists(dirname(__FILE__).'/modules/ulp-salesmanago.php') && $this->ext_options['enable_salesmanago'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-salesmanago.php');
		if (file_exists(dirname(__FILE__).'/modules/ulp-sendloop.php') && $this->ext_options['enable_sendloop'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-sendloop.php');
		if (file_exists(dirname(__FILE__).'/modules/ulp-sgautorepondeur.php') && $this->ext_options['enable_sgautorepondeur'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-sgautorepondeur.php');
		if (file_exists(dirname(__FILE__).'/modules/ulp-sendgrid.php') && $this->ext_options['enable_sendgrid'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-sendgrid.php');
		if (file_exists(dirname(__FILE__).'/modules/ulp-sendinblue.php') && $this->ext_options['enable_sendinblue'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-sendinblue.php');
		if (file_exists(dirname(__FILE__).'/modules/ulp-sendlane.php') && $this->ext_options['enable_sendlane'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-sendlane.php');
		if (!defined('UAP_CORE')) {
			if (file_exists(dirname(__FILE__).'/modules/ulp-sendpress.php') && $this->ext_options['enable_sendpress'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-sendpress.php');
		}
		if (file_exists(dirname(__FILE__).'/modules/ulp-sendpulse.php') && $this->ext_options['enable_sendpulse'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-sendpulse.php');
		if (file_exists(dirname(__FILE__).'/modules/ulp-sendreach.php') && $this->ext_options['enable_sendreach'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-sendreach.php');
		if (file_exists(dirname(__FILE__).'/modules/ulp-sendy.php') && $this->ext_options['enable_sendy'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-sendy.php');
		if (file_exists(dirname(__FILE__).'/modules/ulp-simplycast.php') && $this->ext_options['enable_simplycast'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-simplycast.php');
		if (file_exists(dirname(__FILE__).'/modules/ulp-squalomail.php') && $this->ext_options['enable_squalomail'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-squalomail.php');
		if (file_exists(dirname(__FILE__).'/modules/ulp-stampready.php') && $this->ext_options['enable_stampready'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-stampready.php');
		if (file_exists(dirname(__FILE__).'/modules/ulp-streamsend.php') && $this->ext_options['enable_streamsend'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-streamsend.php');
		if (!defined('UAP_CORE')) {
			if (file_exists(dirname(__FILE__).'/modules/ulp-subscribe2.php') && $this->ext_options['enable_subscribe2'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-subscribe2.php');
			if (file_exists(dirname(__FILE__).'/modules/ulp-thenewsletterplugin.php') && $this->ext_options['enable_thenewsletterplugin'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-thenewsletterplugin.php');
		}
		if (file_exists(dirname(__FILE__).'/modules/ulp-totalsend.php') && $this->ext_options['enable_totalsend'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-totalsend.php');
		if (!defined('UAP_CORE')) {
			if (file_exists(dirname(__FILE__).'/modules/ulp-tribulant.php') && $this->ext_options['enable_tribulant'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-tribulant.php');
		}
		if (file_exists(dirname(__FILE__).'/modules/ulp-unisender.php') && $this->ext_options['enable_unisender'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-unisender.php');
		if (file_exists(dirname(__FILE__).'/modules/ulp-userengage.php') && $this->ext_options['enable_userengage'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-userengage.php');
		if (file_exists(dirname(__FILE__).'/modules/ulp-vision6.php') && $this->ext_options['enable_vision6'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-vision6.php');
		if (file_exists(dirname(__FILE__).'/modules/ulp-ymlp.php') && $this->ext_options['enable_ymlp'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-ymlp.php');
		if (file_exists(dirname(__FILE__).'/modules/ulp-zohocampaigns.php') && $this->ext_options['enable_zohocampaigns'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-zohocampaigns.php');
		if (file_exists(dirname(__FILE__).'/modules/ulp-zohocrm.php') && $this->ext_options['enable_zohocrm'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-zohocrm.php');
		if (file_exists(dirname(__FILE__).'/modules/ulp-htmlform.php') && $this->ext_options['enable_htmlform'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-htmlform.php');
		if (!defined('UAP_CORE')) {
			if (file_exists(dirname(__FILE__).'/modules/ulp-wpuser.php') && $this->ext_options['enable_wpuser'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-wpuser.php');
		}
		if (file_exists(dirname(__FILE__).'/modules/ulp-library.php') && $this->ext_options['enable_library'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-library.php');
		if (file_exists(dirname(__FILE__).'/modules/ulp-js.php') && $this->ext_options['enable_js'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-js.php');
		if (!defined('UAP_CORE')) {
			if (file_exists(dirname(__FILE__).'/modules/ulp-addons.php') && $this->ext_options['enable_addons'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-addons.php');
			if (file_exists(dirname(__FILE__).'/modules/ulp-remote.php') && $this->ext_options['enable_remote'] == 'on') include_once(dirname(__FILE__).'/modules/ulp-remote.php');
		} else {
			include_once(dirname(__FILE__).'/modules/ulp-remote.php');
		}

		if (!empty($_COOKIE["ulp_error"])) {
			$this->error = stripslashes($_COOKIE["ulp_error"]);
			setcookie("ulp_error", "", time()+30, "/", ".".str_replace("www.", "", $domain));
		}
		if (!empty($_COOKIE["ulp_info"])) {
			$this->info = stripslashes($_COOKIE["ulp_info"]);
			setcookie("ulp_info", "", time()+30, "/", ".".str_replace("www.", "", $domain));
		}

		if (defined('DOING_AJAX') && DOING_AJAX) {
			include_once(dirname(__FILE__).'/modules/core-ajax.php');
			$ulp_ajax = new ulp_ajax_class();
		} else if (is_admin()) {
			add_action('wpmu_new_blog', array(&$this, 'install_new_blog'), 10, 6);
			add_action('delete_blog', array(&$this, 'uninstall_blog'), 10, 2);
			$this->default_popup_options = array_merge($this->default_popup_options, array(
				"doubleoptin_subject" => __('Confirm your e-mail address', 'ulp'),
				"doubleoptin_message" => __('Dear Friend,', 'ulp').PHP_EOL.PHP_EOL.__('Somebody (probably you) submitted e-mail address {subscription-email} into our list. Please confirm your e-mail address by clicking the link below or igonore this message.', 'ulp').PHP_EOL.'<a href="{confirmation-link}">{confirmation-link}</a>'.PHP_EOL.PHP_EOL.__('Thanks,', 'ulp').PHP_EOL.get_bloginfo("name"),
				"doubleoptin_confirmation_message" => __('Your e-mail address successfully confirmed.', 'ulp')
			));
			include_once(dirname(__FILE__).'/modules/core-admin.php');
			$ulp_admin = new ulp_admin_class();
		} else {
			include_once(dirname(__FILE__).'/modules/core-front.php');
			$ulp_front = new ulp_front_class();
		}
	}

	static function install($_networkwide = null) {
		global $wpdb;
		if (function_exists('is_multisite') && is_multisite()) {
			if ($_networkwide) {
				$old_blog = $wpdb->blogid;
				$blog_ids = $wpdb->get_col('SELECT blog_id FROM '.$wpdb->blogs);
				foreach ($blog_ids as $blog_id) {
					switch_to_blog($blog_id);
					self::activate();
				}
				switch_to_blog($old_blog);
				return;
			}
		}
		self::activate();
	}

	function install_new_blog($_blog_id, $_user_id, $_domain, $_path, $_site_id, $_meta) {
		if (is_plugin_active_for_network(basename(dirname(__FILE__)).'/' ).basename(__FILE__)) {
			switch_to_blog($_blog_id);
			self::activate();
			restore_current_blog();
		}
	}
	
	static function activate() {
		global $wpdb;
		$add_default = false;
		// Create tables for Advanced Targeting - 2017-04-10 - begin
		if (!defined('UAP_CORE')) {
			include_once(dirname(__FILE__).'/modules/core-targeting.php');
			ulp_class_targeting::activate();
		}
		// Create tables for Advanced Targeting - 2017-04-10 - end
		$table_name = $wpdb->prefix."ulp_popups";
		if($wpdb->get_var("SHOW TABLES LIKE '".$table_name."'") != $table_name) {
			$sql = "CREATE TABLE ".$table_name." (
				id int(11) NOT NULL auto_increment,
				str_id varchar(31) collate latin1_general_cs NULL,
				title varchar(255) collate utf8_unicode_ci NULL,
				width int(11) NULL default '640',
				height int(11) NULL default '400',
				options longtext collate utf8_unicode_ci NULL,
				impressions int(11) NULL default '0',
				clicks int(11) NULL default '0',
				created int(11) NULL,
				blocked int(11) NULL default '0',
				deleted int(11) NULL default '0',
				UNIQUE KEY  id (id)
			);";
			$wpdb->query($sql);
			$add_default = true;
		}
		if ($wpdb->get_var("SHOW COLUMNS FROM ".$wpdb->prefix."ulp_popups LIKE 'impressions'") != 'impressions') {
			$sql = "ALTER TABLE ".$wpdb->prefix."ulp_popups ADD impressions int(11) NULL default '0'";
			$wpdb->query($sql);
		}
		if ($wpdb->get_var("SHOW COLUMNS FROM ".$wpdb->prefix."ulp_popups LIKE 'clicks'") != 'clicks') {
			$sql = "ALTER TABLE ".$wpdb->prefix."ulp_popups ADD clicks int(11) NULL default '0'";
			$wpdb->query($sql);
		}
		$table_name = $wpdb->prefix."ulp_layers";
		if($wpdb->get_var("SHOW TABLES LIKE '".$table_name."'") != $table_name) {
			$sql = "CREATE TABLE ".$table_name." (
				id int(11) NOT NULL auto_increment,
				popup_id int(11) NULL,
				title varchar(255) collate utf8_unicode_ci NULL,
				content longtext collate utf8_unicode_ci NULL,
				zindex int(11) NULL default '5',
				details longtext collate utf8_unicode_ci NULL,
				created int(11) NULL,
				deleted int(11) NULL default '0',
				UNIQUE KEY  id (id)
			);";
			$wpdb->query($sql);
		}
		$table_name = $wpdb->prefix."ulp_campaigns";
		if($wpdb->get_var("SHOW TABLES LIKE '".$table_name."'") != $table_name) {
			$sql = "CREATE TABLE ".$table_name." (
				id int(11) NOT NULL auto_increment,
				title varchar(255) collate utf8_unicode_ci NULL,
				str_id varchar(31) collate latin1_general_cs NULL,
				details longtext collate utf8_unicode_ci NULL,
				created int(11) NULL,
				blocked int(11) NULL default '0',
				deleted int(11) NULL default '0',
				UNIQUE KEY  id (id)
			);";
			$wpdb->query($sql);
		}
		$table_name = $wpdb->prefix."ulp_campaign_items";
		if($wpdb->get_var("SHOW TABLES LIKE '".$table_name."'") != $table_name) {
			$sql = "CREATE TABLE ".$table_name." (
				id int(11) NOT NULL auto_increment,
				campaign_id int(11) NULL,
				popup_id int(11) NULL,
				impressions int(11) NULL default '0',
				clicks int(11) NULL default '0',
				created int(11) NULL,
				deleted int(11) NULL default '0',
				UNIQUE KEY  id (id)
			);";
			$wpdb->query($sql);
		}
		$table_name = $wpdb->prefix . "ulp_subscribers";
		if($wpdb->get_var("SHOW TABLES LIKE '".$table_name."'") != $table_name) {
			$sql = "CREATE TABLE ".$table_name." (
				id int(11) NOT NULL auto_increment,
				popup_id int(11) NULL,
				name varchar(255) collate utf8_unicode_ci NULL,
				email varchar(255) collate utf8_unicode_ci NULL,
				phone varchar(255) collate utf8_unicode_ci NULL,
				message longtext collate utf8_unicode_ci NULL,
				custom_fields longtext collate utf8_unicode_ci NULL,
				status int(11) NULL default '0',
				confirmation_id varchar(31) collate latin1_general_cs NULL,
				created int(11) NULL,
				deleted int(11) NULL default '0',
				UNIQUE KEY  id (id)
			);";
			$wpdb->query($sql);
		}
		/*
		$table_name = $wpdb->prefix . "ulp_stats";
		if($wpdb->get_var("SHOW TABLES LIKE '".$table_name."'") != $table_name) {
			$sql = "CREATE TABLE ".$table_name." (
				id int(11) NOT NULL auto_increment,
				popup_id int(11) NULL,
				event int(11) NULL,
				impressions int(11) NULL,
				clicks int(11) NULL,
				impressions_exposure_time int(11) NULL,
				clicks_exposure_time int(11) NULL,
				time int(11) NULL,
				deleted int(11) NULL default '0',
				UNIQUE KEY  id (id)
			);";
			$wpdb->query($sql);
		}
		*/
		if ($wpdb->get_var("SHOW COLUMNS FROM ".$wpdb->prefix."ulp_subscribers LIKE 'phone'") != 'phone') {
			$sql = "ALTER TABLE ".$wpdb->prefix."ulp_subscribers ADD phone varchar(255) collate utf8_unicode_ci NULL";
			$wpdb->query($sql);
		}
		if ($wpdb->get_var("SHOW COLUMNS FROM ".$wpdb->prefix."ulp_subscribers LIKE 'message'") != 'message') {
			$sql = "ALTER TABLE ".$wpdb->prefix."ulp_subscribers ADD message longtext collate utf8_unicode_ci NULL";
			$wpdb->query($sql);
		}
		if ($wpdb->get_var("SHOW COLUMNS FROM ".$wpdb->prefix."ulp_subscribers LIKE 'custom_fields'") != 'custom_fields') {
			$sql = "ALTER TABLE ".$wpdb->prefix."ulp_subscribers ADD custom_fields longtext collate utf8_unicode_ci NULL";
			$wpdb->query($sql);
		}
		if ($wpdb->get_var("SHOW COLUMNS FROM ".$wpdb->prefix."ulp_subscribers LIKE 'status'") != 'status') {
			$sql = "ALTER TABLE ".$wpdb->prefix."ulp_subscribers ADD status int(11) NULL default '0'";
			$wpdb->query($sql);
		}
		if ($wpdb->get_var("SHOW COLUMNS FROM ".$wpdb->prefix."ulp_subscribers LIKE 'confirmation_id'") != 'confirmation_id') {
			$sql = "ALTER TABLE ".$wpdb->prefix."ulp_subscribers ADD confirmation_id varchar(31) collate latin1_general_cs NULL";
			$wpdb->query($sql);
		}
		$table_name = $wpdb->prefix."ulp_webfonts";
		if($wpdb->get_var("SHOW TABLES LIKE '".$table_name."'") != $table_name) {
			$sql = "CREATE TABLE ".$table_name." (
				id int(11) NOT NULL auto_increment,
				family varchar(255) collate utf8_unicode_ci NULL,
				variants varchar(255) collate utf8_unicode_ci NULL,
				subsets varchar(255) collate utf8_unicode_ci NULL,
				source varchar(31) collate latin1_general_cs NULL,
				deleted int(11) NULL default '0',
				UNIQUE KEY  id (id)
			);";
			$wpdb->query($sql);
		}
		$webfont_version = get_option('ulp_webfonts_version', 0);
		if ($webfont_version < ULP_WEBFONTS_VERSION) {
			include(dirname(__FILE__).'/webfonts.php');
			$webfonts_array = json_decode($fonts, true);
			if (is_array($webfonts_array['items'])) {
				$sql = "DELETE FROM ".$wpdb->prefix."ulp_webfonts";
				$wpdb->query($sql);
				$values = array();
				foreach($webfonts_array['items'] as $fontvars) {
					if (!empty($fontvars['family'])) {
						$variants = '';
						if (!empty($fontvars['variants']) && is_array($fontvars['variants'])) {
							foreach ($fontvars['variants'] as $key => $var) {
									if ($var == 'regular') $fontvars['variants'][$key] = '400';
									if ($var == 'italic') $fontvars['variants'][$key] = '400italic';
							}
							$variants = implode(",", $fontvars['variants']);
						}
						$subsets = '';
						if (!empty($fontvars['subsets']) && is_array($fontvars['subsets'])) {
							$subsets = implode(",", $fontvars['subsets']);
						}
						$values[] = "('".esc_sql($fontvars['family'])."', '".esc_sql($variants)."', '".esc_sql($subsets)."', 'google', '0')";
						if (sizeof($values) > 9) {
							$sql = "INSERT INTO ".$wpdb->prefix."ulp_webfonts (family, variants, subsets, source, deleted) 
									VALUES ".implode(', ', $values);
							$wpdb->query($sql);
							$values = array();
						}
					}
				}
				if (sizeof($values) > 0) {
					$sql = "INSERT INTO ".$wpdb->prefix."ulp_webfonts (family, variants, subsets, source, deleted) 
							VALUES ".implode(', ', $values);
					$wpdb->query($sql);
				}
			}
			update_option('ulp_webfonts_version', ULP_WEBFONTS_VERSION);
		}
		update_option('ulp_version', ULP_VERSION);
		update_option('ulp_ext_clean_database', 'off');
		$upload_dir = wp_upload_dir();
		wp_mkdir_p($upload_dir["basedir"].'/'.ULP_UPLOADS_DIR);
		wp_mkdir_p($upload_dir["basedir"].'/'.ULP_UPLOADS_DIR.'/temp');
		if (file_exists($upload_dir["basedir"].'/'.ULP_UPLOADS_DIR) && !file_exists($upload_dir["basedir"].'/'.ULP_UPLOADS_DIR.'/index.html')) {
			file_put_contents($upload_dir["basedir"].'/'.ULP_UPLOADS_DIR.'/index.html', 'Silence is the gold!');
		}
		if (file_exists($upload_dir["basedir"].'/'.ULP_UPLOADS_DIR.'/temp') && !file_exists($upload_dir["basedir"].'/'.ULP_UPLOADS_DIR.'/temp/index.html')) {
			file_put_contents($upload_dir["basedir"].'/'.ULP_UPLOADS_DIR.'/temp/index.html', 'Silence is the gold!');
		}
		if ($add_default) {
			if (file_exists(dirname(__FILE__).'/default') && is_dir(dirname(__FILE__).'/default')) {
				$dircontent = scandir(dirname(__FILE__).'/default');
				for ($i=0; $i<sizeof($dircontent); $i++) {
					if ($dircontent[$i] != "." && $dircontent[$i] != ".." && $dircontent[$i] != "index.html" && $dircontent[$i] != ".htaccess") {
						if (is_file(dirname(__FILE__).'/default/'.$dircontent[$i])) {
							$lines = file(dirname(__FILE__).'/default/'.$dircontent[$i]);
							if (sizeof($lines) != 3) continue;
							$version = intval(trim($lines[0]));
							if ($version > intval(ULP_EXPORT_VERSION)) continue;
							$md5_hash = trim($lines[1]);
							$popup_data = trim($lines[2]);
							$popup_data = base64_decode($popup_data);
							if (!$popup_data || md5($popup_data) != $md5_hash) continue;
							$popup = unserialize($popup_data);
							$popup_details = $popup['popup'];
							$symbols = '123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
							$str_id = '';
							for ($j=0; $j<16; $j++) {
								$str_id .= $symbols[rand(0, strlen($symbols)-1)];
							}
							$sql = "INSERT INTO ".$wpdb->prefix."ulp_popups (str_id, title, width, height, options, created, blocked, deleted) 
								VALUES (
								'".$str_id."', 
								'".esc_sql($popup_details['title'])."', 
								'".intval($popup_details['width'])."', 
								'".intval($popup_details['height'])."', 
								'".esc_sql($popup_details['options'])."', 
								'".time()."', '1', '0')";
							$wpdb->query($sql);
							$popup_id = $wpdb->insert_id;
							$layers = $popup['layers'];
							if (sizeof($layers) > 0) {
								foreach ($layers as $layer) {
									$sql = "INSERT INTO ".$wpdb->prefix."ulp_layers (
										popup_id, title, content, zindex, details, created, deleted) VALUES (
										'".$popup_id."',
										'".esc_sql($layer['title'])."',
										'".esc_sql($layer['content'])."',
										'".esc_sql($layer['zindex'])."',
										'".esc_sql($layer['details'])."',
										'".time()."', '0')";
									$wpdb->query($sql);
								}
							}
						}
					}
				}
			}
		}
	}

	static function uninstall() {
		global $wpdb;
		if (function_exists('is_multisite') && is_multisite()) {
			$old_blog = $wpdb->blogid;
			$blog_ids = $wpdb->get_col('SELECT blog_id FROM '.$wpdb->blogs);
			foreach ($blog_ids as $blog_id) {
				switch_to_blog($blog_id);
				self::deactivate(false);
			}
			switch_to_blog($old_blog);
		} else {
			self::deactivate(false);
		}
	}

	function uninstall_blog($_blog_id, $_drop) {
		if (is_plugin_active_for_network(basename(dirname(__FILE__)).'/'.basename(__FILE__)) && $_drop) {
			switch_to_blog($_blog_id);
			self::deactivate(true);
			restore_current_blog();
		}
	}
	
	static function deactivate($_force_delete = false) {
		global $wpdb;
		$clean_database = get_option('ulp_ext_clean_database', 'off');
		if ($clean_database == 'on' || $_force_delete) {
			$sql = "DELETE FROM ".$wpdb->prefix."postmeta WHERE meta_key LIKE 'ulp_%'";
			$wpdb->query($sql);
			$sql = "DELETE FROM ".$wpdb->prefix."options WHERE option_name LIKE 'ulp_%' AND option_name != 'ulp_ext_clean_database'";
			$wpdb->query($sql);
			$sql = "DROP TABLE IF EXISTS ".$wpdb->prefix."ulp_popups";
			$wpdb->query($sql);
			$sql = "DROP TABLE IF EXISTS ".$wpdb->prefix."ulp_layers";
			$wpdb->query($sql);
			$sql = "DROP TABLE IF EXISTS ".$wpdb->prefix."ulp_campaigns";
			$wpdb->query($sql);
			$sql = "DROP TABLE IF EXISTS ".$wpdb->prefix."ulp_campaign_items";
			$wpdb->query($sql);
			$sql = "DROP TABLE IF EXISTS ".$wpdb->prefix."ulp_subscribers";
			$wpdb->query($sql);
			$sql = "DROP TABLE IF EXISTS ".$wpdb->prefix."ulp_webfonts";
			$wpdb->query($sql);
			// Drop tables for Advanced Targeting - 2017-04-10 - begin
			if (!defined('UAP_CORE')) {
				include_once(dirname(__FILE__).'/modules/core-targeting.php');
				ulp_class_targeting::deactivate();
			}
			// Drop tables for Advanced Targeting - 2017-04-10 - end
		}
	}

	function get_ext_options() {
		foreach ($this->ext_options as $key => $value) {
			$this->ext_options[$key] = get_option('ulp_ext_'.$key, $this->ext_options[$key]);
		}
	}

	function update_ext_options() {
		if (current_user_can('manage_options')) {
			foreach ($this->ext_options as $key => $value) {
				update_option('ulp_ext_'.$key, $value);
			}
		}
	}

	function populate_ext_options() {
		foreach ($this->ext_options as $key => $value) {
			if (isset($_POST['ulp_ext_'.$key])) {
				$this->ext_options[$key] = trim(stripslashes($_POST['ulp_ext_'.$key]));
			}
		}
	}

	function get_options() {
		$exists = get_option('ulp_version');
		if ($exists) {
			foreach ($this->options as $key => $value) {
				$this->options[$key] = get_option('ulp_'.$key, $this->options[$key]);
			}
		}
	}

	function update_options() {
		if (current_user_can('manage_options')) {
			foreach ($this->options as $key => $value) {
				update_option('ulp_'.$key, $value);
			}
		}
	}

	function populate_options() {
		foreach ($this->options as $key => $value) {
			if (isset($_POST['ulp_'.$key])) {
				if (in_array($key, array('onload_popup', 'onload_popup_mobile', 'onexit_popup', 'onexit_popup_mobile', 'onscroll_popup', 'onscroll_popup_mobile', 'onidle_popup', 'onidle_popup_mobile', 'onabd_popup', 'onabd_popup_mobile'))) {
					$this->options[$key] = $this->wpml_compile_popup_id(trim(stripslashes($_POST['ulp_'.$key])), $this->options[$key]);
				} else {
					$this->options[$key] = trim(stripslashes($_POST['ulp_'.$key]));
				}
			}
		}
	}

	function get_meta($post_id) {
		$meta = array();
		$version = get_post_meta($post_id, 'ulp_version', true);
		if (empty($version)) $meta = $this->default_meta;
		else {
			foreach($this->default_meta as $key => $value) {
				$meta[$key] = get_post_meta($post_id, 'ulp_'.$key, true);
			}
			if ($version < 3.50) {
				$meta['onload_popup_mobile'] = $this->default_meta['onload_popup_mobile'];
				$meta['onscroll_popup_mobile'] = $this->default_meta['onscroll_popup_mobile'];
				$meta['onexit_popup_mobile'] = $this->default_meta['onexit_popup_mobile'];
			}
			if ($version < 3.71) {
				$meta['onload_period'] = $this->default_meta['onload_period'];
				$meta['onexit_period'] = $this->default_meta['onexit_period'];
				$meta['onscroll_period'] = $this->default_meta['onscroll_period'];
			}
		}
		if (empty($meta['onexit_mode'])) {
			$meta['onexit_mode'] = $this->default_meta['onexit_mode'];
			$meta['onexit_popup'] = $this->default_meta['onexit_popup'];
		}
		if (empty($meta['onscroll_mode'])) {
			$meta['onscroll_mode'] = $this->default_meta['onscroll_mode'];
			$meta['onscroll_popup'] = $this->default_meta['onscroll_popup'];
			$meta['onscroll_offset'] = $this->default_meta['onscroll_offset'];
		}
		if (empty($meta['onidle_mode'])) {
			$meta['onidle_mode'] = $this->default_meta['onidle_mode'];
			$meta['onidle_popup'] = $this->default_meta['onidle_popup'];
			$meta['onidle_popup_mobile'] = $this->default_meta['onidle_popup_mobile'];
			$meta['onidle_delay'] = $this->default_meta['onidle_delay'];
			$meta['onidle_period'] = $this->default_meta['onidle_period'];
		}
		if (empty($meta['onabd_mode'])) {
			$meta['onabd_mode'] = $this->default_meta['onabd_mode'];
			$meta['onabd_popup'] = $this->default_meta['onabd_popup'];
			$meta['onabd_popup_mobile'] = $this->default_meta['onabd_popup_mobile'];
			$meta['onabd_period'] = $this->default_meta['onabd_period'];
		}
		return $meta;
	}
	
	function shortcode_handler($_atts) {
		include_once(dirname(__FILE__).'/modules/core-front.php');
		$ulp_front = new ulp_front_class();
		$html = $ulp_front->shortcode_handler($_atts);
		return $html;
	}
	
	function wpml_parse_popup_id($_popup_id, $_default_all_value = '', $_current_language = '') {
		$popup_id = $_popup_id;
		$popups = array('all' => $_default_all_value);
		$pairs = explode(',', $_popup_id);
		foreach($pairs as $pair) {
			$data = explode(':', $pair);
			if (sizeof($data) != 2) $popups['all'] = $data[0];
			else $popups[$data[0]] = $data[1];
		}
		if (!defined('ICL_LANGUAGE_CODE')) $popup_id = $popups['all'];
		else {
			if (!empty($_current_language) && array_key_exists($_current_language, $popups)) $popup_id = $popups[$_current_language];
			else if (array_key_exists(ICL_LANGUAGE_CODE, $popups)) $popup_id = $popups[ICL_LANGUAGE_CODE];
			else $popup_id = $popups['all'];
		}
		return $popup_id;
	}
	
	function wpml_compile_popup_id($_popup_id, $_old) {
		$new = $_popup_id;
		if (defined('ICL_LANGUAGE_CODE')) {
			if (ICL_LANGUAGE_CODE == 'all') {
				$new = $_popup_id;
			} else {
				$popups = array();
				$pairs = explode(',', $_old);
				foreach($pairs as $pair) {
					$data = explode(':', $pair);
					if (sizeof($data) != 2) $popups['all'] = $data[0];
					else $popups[$data[0]] = $data[1];
				}
				$popups[ICL_LANGUAGE_CODE] = $_popup_id;
				$data = array();
				foreach ($popups as $key => $value) {
					$data[] = $key.':'.$value;
				}
				$new = implode(',', $data);
			}
		}
		return $new;
	}
	
	function page_switcher ($_urlbase, $_currentpage, $_totalpages) {
		$pageswitcher = "";
		if ($_totalpages > 1) {
			$pageswitcher = '<div class="tablenav bottom"><div class="tablenav-pages">'.__('Pages:', 'ulp').' <span class="pagiation-links">';
			if (strpos($_urlbase,"?") !== false) $_urlbase .= "&amp;";
			else $_urlbase .= "?";
			if ($_currentpage == 1) $pageswitcher .= "<a class='page disabled'>1</a> ";
			else $pageswitcher .= " <a class='page' href='".$_urlbase."p=1'>1</a> ";

			$start = max($_currentpage-3, 2);
			$end = min(max($_currentpage+3,$start+6), $_totalpages-1);
			$start = max(min($start,$end-6), 2);
			if ($start > 2) $pageswitcher .= " <b>...</b> ";
			for ($i=$start; $i<=$end; $i++) {
				if ($_currentpage == $i) $pageswitcher .= " <a class='page disabled'>".$i."</a> ";
				else $pageswitcher .= " <a class='page' href='".$_urlbase."p=".$i."'>".$i."</a> ";
			}
			if ($end < $_totalpages-1) $pageswitcher .= " <b>...</b> ";

			if ($_currentpage == $_totalpages) $pageswitcher .= " <a class='page disabled'>".$_totalpages."</a> ";
			else $pageswitcher .= " <a class='page' href='".$_urlbase."p=".$_totalpages."'>".$_totalpages."</a> ";
			$pageswitcher .= "</span></div></div>";
		}
		return $pageswitcher;
	}

	function datetime_string($_datetime) {
		$dt = (string)$_datetime;
		if (strlen($dt) != 12) return '';
		return substr($dt, 0, 4).'-'.substr($dt, 4, 2).'-'.substr($dt, 6, 2).' '.substr($dt, 8, 2).':'.substr($dt, 10, 2);
	}
	function filter_lp($_layer_options) {
		foreach ($_layer_options as $key => $value) {
			$_layer_options[$key] = str_replace(array('ULP-DEMO-IMAGES-URL', 'http://datastorage.pw/images'), array(plugins_url('/images/default', __FILE__), plugins_url('/images/default', __FILE__)), $value);
		}
		return $_layer_options;
	}
	
	function filter_lp_reverse($_layer_options) {
		foreach ($_layer_options as $key => $value) {
			$_layer_options[$key] = str_replace(array('http://datastorage.pw/images', plugins_url('/images/default', __FILE__)), array('ULP-DEMO-IMAGES-URL', 'ULP-DEMO-IMAGES-URL'), $value);
		}
		return $_layer_options;
	}
	
	function get_rgb($_color) {
		if (strlen($_color) != 7 && strlen($_color) != 4) return false;
		$color = preg_replace('/[^#a-fA-F0-9]/', '', $_color);
		if (strlen($color) != strlen($_color)) return false;
		if (strlen($color) == 7) list($r, $g, $b) = array($color[1].$color[2], $color[3].$color[4], $color[5].$color[6]);
		else list($r, $g, $b) = array($color[1].$color[1], $color[2].$color[2], $color[3].$color[3]);
		return array("r" => hexdec($r), "g" => hexdec($g), "b" => hexdec($b));
	}

	function admin_modal_html() {
		return '
<div class="ulp-modal-overlay"></div>
<div class="ulp-modal">
	<div class="ulp-modal-content">
		<div class="ulp-modal-message"></div>
		<div class="ulp-modal-buttons">
			<a class="ulp-modal-button" id="ulp-modal-button-ok" href="#" onclick="return false;"><i class="fa fa-check"></i><label></label></a>
			<a class="ulp-modal-button" id="ulp-modal-button-cancel" href="#" onclick="return false;"><i class="fa fa-close"></i><label></label></a>
		</div>
	</div>
</div>';
	}
	
	function random_string($_length = 16) {
		$symbols = '123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$string = "";
		for ($i=0; $i<$_length; $i++) {
			$string .= $symbols[rand(0, strlen($symbols)-1)];
		}
		return $string;
	}

	function verify_recaptcha($_response) {
		$request = http_build_query(array(
			'secret' => $this->options['recaptcha_secret_key'],
			'response' => $_response,
			'remoteip' => $_SERVER['REMOTE_ADDR']
		));
		try {
			$curl = curl_init('https://www.google.com/recaptcha/api/siteverify');
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $request);

			curl_setopt($curl, CURLOPT_TIMEOUT, 20);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
			curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($curl, CURLOPT_HEADER, 0);
								
			$response = curl_exec($curl);
			curl_close($curl);
			$result = json_decode($response, true);
			if(!$result) return false;
			if (array_key_exists('success', $result)) {
				return $result['success'];
			} else return false;
		} catch (Exception $e) {
			return false;
		}
	}
}
$ulp_social2 = null;
$ulp = new ulp_class();
?>