// +------------------------------------------------------------------------+
// | @author Oscar Garcés (SoyVillareal)
// | @author_url 1: https://soyvillareal.com
// | @author_url 2: https://github.com/soyvillareal
// | @author_email: hi@soyvillareal.com   
// +------------------------------------------------------------------------+
// | PHP Magazine - The best digital magazine for newspapers or bloggers
// | Licensed under the MIT License. Copyright (c) 2022 PHP Magazine.
// +------------------------------------------------------------------------+

const info = require('./info'),
      express = require('express'),
      app = express(),
      fs = require('fs');

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
    io: require('socket.io')(server),
    app: app
};

try {
    server.listen(info.server_port, info.server_ip);
} catch (e) {
    console.log(e);
}

require('./request_init.js')();