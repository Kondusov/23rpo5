<?php
// Force JSON for all API endpoints and convert errors to JSON
header('Content-Type: application/json; charset=utf-8');

set_error_handler(function($severity, $message, $file, $line){
    // Convert warnings/notices to exceptions to be caught by exception handler
    throw new ErrorException($message, 0, $severity, $file, $line);
});

set_exception_handler(function($e){
    http_response_code(500);
    echo json_encode(['error' => 'server_error', 'message' => $e->getMessage()], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    exit;
});

register_shutdown_function(function(){
    $err = error_get_last();
    if ($err && in_array($err['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])){
        http_response_code(500);
        echo json_encode(['error' => 'fatal_error', 'message' => $err['message']], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }
});


