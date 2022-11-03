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