<?php

/* Very simple logging. 
 * Always to the same file, and lines are prefixed with a timestamp.
 * Warning, grows to infinity!  Do not log very verbose stuff in there.
 */
function accou_log( $v ) {
	$acc_logfile = ACCOU_LOG_DIR . "accou_logfile.txt";
	$log_date = date_i18n("Y-m-d H-i-s  ");

	$log = fopen($acc_logfile, "a");
	fwrite( $log, $log_date . $v . "\n");
	fclose( $log );
}

