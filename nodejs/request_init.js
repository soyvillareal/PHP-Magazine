const specific = require('./includes/specific'),
      T = require('./includes/tables'),
      connection = require('./mysql/DB'),
      util = require('util'),
      query = util.promisify(connection.query).bind(connection),
      forEach = require('async-foreach').forEach;

module.exports = function(){

    global.TEMP.io.on('connection', function(socket) {

        global.TEMP[socket.id] = {
            blocked_arrusers: [],
            blocked_inusers: 0,
            loggedin: !1,
            word: {},
            user: {},
            interval: 0,
            profile_id: socket.handshake.query.profile_id
        };

        specific.Init(socket).then(async function(res){
            if(res.blocked_users !== undefined && res.blocked_users.length > 0){
                global.TEMP[socket.id].blocked_arrusers = res.blocked_users;
                global.TEMP[socket.id].blocked_inusers = res.blocked_users.join(',');
            }
            global.TEMP[socket.id].loggedin = res.loggedin;
            global.TEMP[socket.id].word = res.word;

            global.TEMP[socket.id].language = res.language;
            global.TEMP[socket.id].rtl_languages = [];
    
            await query(`SELECT lang, dir FROM ${T.LANGUAGE}`).then(function(res){
                forEach(res, function(item, index, arr){
                    if(item.dir == 'rtl'){
                        global.TEMP[socket.id].rtl_languages.push(item.lang);
                    }
                });
            }).catch(function(err){
                console.log(err);
            });

            specific.Data(socket, null, 4).then(function(res){
                global.TEMP[socket.id].user = res;

                require('./sockets')(socket);
            }).catch(function(err){
                console.log(err);
            })
        });

        socket.on('disconnect', function(data) {
            specific.pullSocket(socket.id);
            specific.pullSocket(socket.id, 'chat');
            clearInterval(global.TEMP[socket.id].interval);
            delete global.TEMP[socket.id];
        });
    });
}