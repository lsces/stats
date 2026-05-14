<?php
/**
 * @version $Header$
 */
global $gBitInstaller;

$infoHash = [
	'package'      => STATS_PKG_NAME,
	'version'      => str_replace( '.php', '', basename( __FILE__ )),
	'description'  => "Add referer URL tracking for user registrations.",
	'post_upgrade' => NULL,
];
$gBitInstaller->registerPackageUpgrade( $infoHash, [
	[ 'DATADICT' => [
		[ 'CREATE' => [
			'stats_referer_urls' => "
				referer_url_id I4 PRIMARY,
				referer_url C(4096) NOTNULL
			",
		]],
		[ 'CREATEINDEX' => [
			'stats_referer_url_idx'       => [ 'stats_referer_urls', 'referer_url', [] ],
		]],
		[ 'CREATESEQUENCE' => [
			'stats_referer_url_id_seq',
		]],
		[ 'CREATE' => [
			'stats_referer_users_map' => "
				referer_url_id I4 PRIMARY,
				user_id I4 PRIMARY
				CONSTRAINT '
					, CONSTRAINT `stats_referer_users_url_ref`  FOREIGN KEY (`referer_url_id`) REFERENCES `".BIT_DB_PREFIX."stats_referer_urls` (`referer_url_id`)
					, CONSTRAINT `stats_referer_users_user_ref` FOREIGN KEY (`user_id`) REFERENCES `".BIT_DB_PREFIX."users_users` (`user_id`) '
			",
		]],
		[ 'CREATEINDEX' => [
			'stats_referer_urls_idx'     => [ 'stats_referer_urls', 'referer_url', [] ],
			'stats_referer_map_url_idx'  => [ 'stats_referer_users_map', 'referer_url_id', [] ],
			'stats_referer_map_user_idx' => [ 'stats_referer_users_map', 'user_id', [] ],
		]],
		[ 'CREATESEQUENCE' => [
			'stats_referer_url_id_seq',
		]],
	]],
]);
