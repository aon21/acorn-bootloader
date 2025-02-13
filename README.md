# Acorn Bootloader Package

For acorn ^v4.0
Acorn bootloader adds csrf protection to your application routes.
It is a middleware that checks for csrf token in the request header and compares it with the csrf token stored in the session. 
If the tokens match, the request is allowed to proceed. If the tokens do not match, the request is blocked and an error message is returned.

Wrap everything in web middleware to enable csrf protection for all routes.
