<?php

session_set_cookie_params(
    36400, // Session cookie will expire when the browser is closed
    '/', // Path for which the cookie is valid
    '', // Domain for which the cookie is valid (empty means current domain)
    false, // Secure flag (only send cookie over HTTPS)
    true  // HttpOnly flag (not accessible via JavaScript)
);

session_start();

?>