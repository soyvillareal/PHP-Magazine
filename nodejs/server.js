var info = require('./info');
var express = require('express');
var app = express();
var fs = require('fs');
var path = require('path');

if (info.ssl == true) {
    const options = {
        key: fs.readFileSync(info.ssl_privatekey_full_path),
        cert: fs.readFileSync(info.ssl_cert_full_path),
    };
   var server = require('https').createServer(options, app);
} else {
   var server = require('http').createServer(app);
}

app.use(express.static(__dirname + '/node_modules'));

app.get('/', function(req, res, next) {
    res.send('Hello World! Node.js is working correctly.');
});


global.TEMP = {
    path: path.join(__dirname, '../'),
    io: require('socket.io')(server),
    app: app
};

try {
    server.listen(info.server_port, info.server_ip);
} catch (e) {
    console.log(e);
}

require('./request_init.js')();