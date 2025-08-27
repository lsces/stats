<?php
$formFeaturesBit = [
	'stats_referers'         => [
		'label' => 'Referer Statistics',
		'type'  => "checkbox",
		'note'  => 'Records statistics including HTTP_REFERRER',
	],
	'google_tagmanager_id'   => [
		'label' => "Google TagManager Container ID for GA4",
		'type'  => "text",
		'note'  => "TagManager Container ID, which should be conected to your GA4 measurement ID; e.g. GTM-ABCD1234 See from https://tagmanager.google.com",
	],
	'google_analytics_ua'    => [
		'label' => "Google Analytics UA (DISCONTINUED)",
		'type'  => "text",
		'note'  => "UA from anayltics.google.com; discontinued June 30, 2023",
	],
	'microsoft_analytics_ti' => [
		'label' => "Microsoft Analytics TI",
		'type'  => "text",
		'note'  => "TI from ads.microsoft.com conversion javascript",
	],
];

$gBitSmarty->assign( 'formFeaturesBit', $formFeaturesBit );

if( !empty( $_POST['change_prefs'] ) ) {
	foreach ( array_keys( $formFeaturesBit ) as $feature) {
		switch ($formFeaturesBit[$feature]['type']) {
			case 'text':
				simple_set_value( $feature, STATS_PKG_NAME );
				break;
			default:
				simple_set_toggle( $feature, STATS_PKG_NAME );
				break;
		}
	}
}

