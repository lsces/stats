<?php
/**
 * $Header$
 *
 * $Id$
 * @package stats
 * @subpackage functions
 */

/**
 * required setup
 */
require_once '../kernel/includes/setup_inc.php';
use Bitweaver\KernelTools;
use Bitweaver\Stats\Statistics;

$gBitSystem->verifyPackage( 'stats' );
$gBitSystem->verifyPermission( 'p_stats_view' );

$gStats = new Statistics();

$gBitSmarty->assign( 'siteStats', $gStats->getSiteStats() );
$gBitSmarty->assign( 'contentOverview', $gStats->getContentOverview( $_REQUEST ));
$gBitSmarty->assign( 'contentStats', $gStats->getContentStats() );

$gBitSystem->display( 'bitpackage:stats/stats.tpl', KernelTools::tra( "Statistics" ) , [ 'display_mode' => 'display' ] );
