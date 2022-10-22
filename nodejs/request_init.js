var specific = require('./includes/specific');

module.exports = function(io){

    var user = {},
        blocked_users = [];

    global.TEMP = {
        io: io
    };

    io.on('connection', async function(socket) {

        var init = specific.Init(socket);
        
        global.TEMP[socket.id] = new Object;

        global.TEMP[socket.id].blocked_users = [];
        global.TEMP[socket.id].loggedin = !1;
        global.TEMP[socket.id].word = {};
        global.TEMP[socket.id].user = {};

        await init.then(async function(res){
            global.TEMP[socket.id].blocked_users = res.blocked_users;
            global.TEMP[socket.id].loggedin = res.loggedin;
            global.TEMP[socket.id].word = res.word;

            await specific.Data(socket, null, 4).then(function(res){
                global.TEMP[socket.id].user = res;
            }).catch(function(err){
                console.log(err);
            })
        });

        if(global.TEMP[socket.id].loggedin){
            require('./socketsIn')(socket);
        }
        require('./socketsOut')(socket);

        socket.on('disconnect', function(data) {
            specific.pullSocket(socket.id);
            delete global.TEMP[socket.id];
        });
    });
}