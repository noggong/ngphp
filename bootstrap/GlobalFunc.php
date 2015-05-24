<?php

function elog($log, $lf = false) {
    $trace = debug_backtrace();
    $trace_log = '[' . $trace[0]['file'] . ' : line : ' . $trace[0]['line'] . ']';
    error_log($trace_log);

    if (is_array($log) || is_object($log)) {
        $log = print_r($log, true);
        if ($lf) {
            $log = str_replace("\n", " \r\n", $log);
        }
        error_log($log);
    } else {
        error_log($log);
    }
}