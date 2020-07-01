<?php

require_once 'vendor/autoload.php';

use App\DB\Connector;

$dbConnection = (new Connector())->getConnection();