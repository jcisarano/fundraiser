<?PHP

//
// Access control options for cross domain calls
//
  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
  header('Access-Control-Max-Age: 1000');
  if(array_key_exists('HTTP_ACCESS_CONTROL_REQUEST_HEADERS', $_SERVER)) {
    header('Access-Control-Allow-Headers: '
           . $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'] . ',Set-Cookie');
  } else {
    header('Access-Control-Allow-Headers: Set-Cookie');
  }
if("OPTIONS" == $_SERVER['REQUEST_METHOD']) {
  exit(0);
}