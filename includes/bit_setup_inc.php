<?php
use Bitweaver\Stats\Statistics;

global $gBitSystem, $gBitUser ;

$pRegisterHash = [
	'package_name' => 'stats',
	'package_path' => dirname( dirname( __FILE__ ) ) . '/',
];

// fix to quieten down VS Code which can't see the dynamic creation of these ...
define( 'STATS_PKG_NAME', $pRegisterHash['package_name'] );
define( 'STATS_PKG_URL', BIT_ROOT_URL . basename( $pRegisterHash['package_path'] ) . '/' );
define( 'STATS_PKG_PATH', BIT_ROOT_PATH . basename( $pRegisterHash['package_path'] ) . '/' );
define( 'STATS_PKG_INCLUDE_PATH', BIT_ROOT_PATH . basename( $pRegisterHash['package_path'] ) . '/includes/'); 
define( 'STATS_PKG_CLASS_PATH', BIT_ROOT_PATH . basename( $pRegisterHash['package_path'] ) . '/includes/classes/');
define( 'STATS_PKG_ADMIN_PATH', BIT_ROOT_PATH . basename( $pRegisterHash['package_path'] ) . '/admin/'); 

$gBitSystem->registerPackage( $pRegisterHash );

if( $gBitSystem->isPackageActive( 'stats' )) {
	if( $gBitUser->hasPermission( 'p_stats_view' ) || $gBitUser->hasPermission( 'p_stats_view_referer' ) ) {
		$menuHash = [
			'package_name'  => STATS_PKG_NAME,
			'index_url'     => STATS_PKG_URL . 'index.php',
			'menu_template' => 'bitpackage:stats/menu_stats.tpl',
		];
		$gBitSystem->registerAppMenu( $menuHash );
	}

	$gLibertySystem->registerService( STATS_PKG_NAME, STATS_PKG_NAME, [
		'users_expunge_function'  => 'stats_user_expunge',
		'users_register_function' => 'stats_user_register',
	] );

	require_once STATS_PKG_CLASS_PATH . 'Statistics.php';
	$stats = new Statistics();
	if( $gBitSystem->isFeatureActive('stats_pageviews') ) {
		$stats->addPageview();
	}

	if( !$gBitUser->isRegistered() && !empty( $_SERVER['HTTP_REFERER'] )  && strlen( $_SERVER['HTTP_REFERER'] ) > 9 ) {
		// Explode the HTTP_REFERER address to split up the string 
		if( $ref = explode('/', $_SERVER['HTTP_REFERER']) ) {
			if( count( $ref ) > 1 && $ref[2] != $_SERVER['HTTP_HOST'] ) {
				// we have a standard refering URL
				if( empty( $_COOKIE['referer_url'] ) ) {
					setcookie( 'referer_url', $_SERVER['HTTP_REFERER'], time()+60*60*24*180, $gBitSystem->getConfig( 'cookie_path', BIT_ROOT_URL ), $gBitSystem->getConfig( 'cookie_domain', '' ));
				}
			}
		}
	}

	// store referer stats if desired
	if( $gBitSystem->isFeatureActive( 'stats_referers' )) {
		$stats->storeReferer();
	}

	// make sure all referrals are removed
	function stats_user_expunge( &$pObject ) {
		if( is_a( $pObject, 'BitUser' ) && !empty( $pObject->mUserId ) ) {
			$pObject->StartTrans();
			$pObject->mDb->query( "DELETE FROM `".BIT_DB_PREFIX."stats_referer_users_map` WHERE user_id=?", [ $pObject->mUserId ] );
			$pObject->CompleteTrans();
		}
	}
	
	function stats_user_register( &$pObject ) {
		if( !empty( $_COOKIE['referer_url'] ) && is_a( $pObject, 'BitUser' ) && !empty( $pObject->mUserId ) ) {
			$pObject->StartTrans();
			if( !$refererId = $pObject->mDb->getOne( "SELECT `referer_url_id` FROM `".BIT_DB_PREFIX."stats_referer_urls` WHERE `referer_url`=?", [ $_COOKIE['referer_url'] ] ) ) {
				$refererId = $pObject->mDb->GenID( 'stats_referer_url_id_seq' );
				$pObject->mDb->query( "INSERT INTO `".BIT_DB_PREFIX."stats_referer_urls` (`referer_url_id`,`referer_url`) VALUES(?,?)", [ $refererId, $_COOKIE['referer_url'] ] );
			}
			$pObject->mDb->query( "INSERT INTO `".BIT_DB_PREFIX."stats_referer_users_map` (`user_id`,`referer_url_id`) VALUES(?,?)", [ $pObject->mUserId, $refererId ] );
			$pObject->CompleteTrans();
		}
	}

	function stats_referer_display_short( $pRefererUrl ) {
		$ret = '';
		if( ($urlHash = parse_url( $pRefererUrl )) && !empty( $urlHash['host'] ) ) {
			$ret = $urlHash['host'];
			// q= google and bing search param, p= yahoo search param
			$searchStrings = [ 'q', 'p' ];
			foreach( $searchStrings as $param ) {
				if( !empty( $urlHash['query'] ) && strpos( $urlHash['query'], $param.'=' ) !== FALSE ) {
					$result = [];
					parse_str( $urlHash['query'], $result );
					if( !empty( $result[$param] ) ) {
						$ret .= '/...'.$param.'='.$result[$param];
					}
				}
			}
		} else {
//			$ret = tra( 'Unknown URL' );
		}
		return $ret;
	}
}
