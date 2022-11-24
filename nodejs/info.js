// +------------------------------------------------------------------------+
// | @author Oscar Garcés (SoyVillareal)
// | @author_url 1: https://soyvillareal.com
// | @author_url 2: https://github.com/soyvillareal
// | @author_email: hi@soyvillareal.com   
// +------------------------------------------------------------------------+
// | PHP Magazine - The best digital magazine for newspapers or bloggers
// | Licensed under the MIT License. Copyright (c) 2022 PHP Magazine.
// +------------------------------------------------------------------------+

const db_hostname =  "localhost",
    db_username = "root",
    db_password = "",
    db_dbname = "php-magazine",
    domain = "192.168.1.52",
    site_url = "http://192.168.1.52/PHP-Magazine",
    server_ip = "192.168.1.52",
    server_port = 3000,
    ssl = false,
    ssl_privatekey_full_path = "",
    ssl_cert_full_path = "";

module.exports = {
    db_hostname,
    db_username,
    db_password,
    db_dbname,
    domain,
    site_url,
    server_ip,
    server_port,
    ssl,
    ssl_privatekey_full_path,
    ssl_cert_full_path,
};