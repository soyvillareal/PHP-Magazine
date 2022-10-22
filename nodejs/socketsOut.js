var connection = require('./mysql/DB');
var unixTime = require('unix-time');
var specific = require('./includes/specific');

module.exports = function(socket){
    var TEMP = global.TEMP[socket.id],
        io = global.TEMP.io;

}