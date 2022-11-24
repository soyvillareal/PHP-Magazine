// +------------------------------------------------------------------------+
// | @author Oscar Garcés (SoyVillareal)
// | @author_url 1: https://soyvillareal.com
// | @author_url 2: https://github.com/soyvillareal
// | @author_email: hi@soyvillareal.com   
// +------------------------------------------------------------------------+
// | PHP Magazine - The best digital magazine for newspapers or bloggers
// | Licensed under the MIT License. Copyright (c) 2022 PHP Magazine.
// +------------------------------------------------------------------------+

const info = require('../info'),
    mysql = require('mysql');

// connect to the db
//create mysql connection pool
var dbconnection = mysql.createPool({
  host: info.db_hostname,
  user: info.db_username,
  password: info.db_password,
  connectionLimit: 10, //mysql connection pool length
  database: info.db_dbname
});

// Attempt to catch disconnects 
dbconnection.on('connection', function (connection) {
  connection.on('error', function (err) {
    console.error(new Date(), 'MySQL error', err.code);
  });
  connection.on('close', function (err) {
    console.error(new Date(), 'MySQL close', err);
  });

});

module.exports = dbconnection;