<?php
/**
 * $Header$
 *
 * Copyright (c) 2004 bitweaver.org
 * Copyright (c) 2003 tikwiki.org
 * Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
 * All Rights Reserved. See below for details and a complete list of authors.
 * Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See http://www.gnu.org/copyleft/lesser.html for details
 *
 * $Id$
 * @package stats
 * @subpackage functions
 */

/**
 * required setup
 */
use Bitweaver\KernelTools;
use Bitweaver\Stats\Statistics;

require_once '../kernel/includes/setup_inc.php';
include_once UTIL_PKG_INCLUDE_PATH . "phplot.php";

$gBitSystem->isPackageActive( 'stats' );
$gBitSystem->verifyPermission( 'p_stats_view' );

$stats = new Statistics();

$days = isset( $_REQUEST["days"] ) ? $_REQUEST['days'] : 7;
$data = $stats->getPageviewChartData( $days );

// initialise phplot and insert data
$graph = new PHPlot( 600, 600 );
$graph->SetDataValues( $data );
$graph->SetTitle( KernelTools::tra( 'Total Pageviews' ) );
$graph->SetYTitle( KernelTools::tra( 'Pageviews' ) );
$graph->SetXTitle( KernelTools::tra( 'Time') ); 
$graph->SetPlotType( ( count( $data ) > 50 ) ? 'lines' : 'linepoints' );
$graph->SetDrawXDataLabels( true );
$graph->SetXLabelAngle( 90 );
$graph->SetXTickPos('none');
$graph->DrawGraph();
