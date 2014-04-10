<?php

class Log {
    function __construct() {
        $this->file = fopen('debug.log', 'ab');
    }

    function __destruct() {
        fclose($this->file);
    }

    function query($sql) {
        $this->log('Query: ' . $sql);
    }

    function request($url) {
	if (isset($_REQUEST['pass'])) {
		$_REQUEST['pass'] = 'sadflkhwv;bwen;kjsljf';
	}

        $this->log('POST ' . $url . ': ' . print_r($_REQUEST, true));
    }

    function log($str) {
        fwrite($this->file, date('Y-m-d H:i:s') . ' - ' . $str);
    }
}

global $Log;
$Log = new Log();
