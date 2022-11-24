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
      connection = require('../mysql/DB'),
      unixTime = require('unix-time'),
      R = require('./routes'),
      T = require('./tables'),
      entities = require('../utils/entities'),
      url_regex = require('../utils/url_regex'),
      async = require('async'),
      forEach = require('async-foreach').forEach,
      _ = require('lodash'),
      cookie = require('cookie'),
      SETTINGS = Settings(),
      util = require('util'),
      fs = require('fs'),
      forEachAsync = util.promisify(forEach),
      Sha1 = require('sha1'),
      Md5 = require('md5'),
      Download = require('image-downloader'),
      Sharp = require('sharp'),
      path = require('path'),
      slug = require('slug'),
      query = util.promisify(connection.query).bind(connection),
      sockets = [];

async function Init(socket){
    var data = {
            loggedin: !1,
            word: {},
            language: SETTINGS.language
        },
        validUsers = {},
        blocked_users = [],
        cookies = cookie.parse(socket.handshake.headers.cookie);

    if (!!cookies._LOGIN_TOKEN) {
        try {
            if (_.isString(cookies._LOGIN_TOKEN)) {
                if (!_.includes(validUsers, cookies._LOGIN_TOKEN)) {
                    await query(`SELECT user_id FROM ${T.SESSION} WHERE token = ? LIMIT 1`, [cookies._LOGIN_TOKEN]).then(async function(res){
                        if (res.length > 0) {
                            var user_id = res[0].user_id,
                                socket_id = socket.id;
                                
                            setSocket(socket_id, user_id);
                            validUsers[cookies._LOGIN_TOKEN] = user_id;
                            await BlockedUsers(user_id).then(function(res) {
                                blocked_users = res;
                            });

                            data.loggedin = !0;
                            data.user_id = user_id;
                            data.blocked_users = blocked_users;
                        }
                    }).catch(function(err){
                        console.log(err)
                    });
                }
            }
        } catch (err) {
            console.log(err);
        }
    }
    if (!!cookies.language) {
        data.language = cookies.language;
    }
    await Words(data.language).then(function(res){
        data.word = res;
    }).catch(function(err){
        console.log(err);
    });
    return data;
}


function Admin(socket) {
    var TEMP = global.TEMP[socket.id];
    return TEMP.loggedin === !1 ? !1 : TEMP.user.role == 'admin' ? !0 : !1;
}

function Moderator(socket) {
    var TEMP = global.TEMP[socket.id],
        role = TEMP.user.role;
    return TEMP.loggedin === !1 ? !1 : role == 'moderator' || role == 'admin' ? !0 : !1;
}

function Publisher(socket) {
    var TEMP = global.TEMP[socket.id],
        role = TEMP.user.role;
    return TEMP.loggedin === !1 ? !1 : role == 'publisher' || role == 'moderator' || role == 'admin' ? !0 : !1;
}

function Settings() {
    var data = {};
    try {
        connection.query(`SELECT * FROM ${T.SETTING}`, function(error, result, field){
            if (error) { 
                console.log(error);
                return;
            };
            forEach(result, function(item, index, arr){
                data[item['name']] = item['value'];
            })
        });
    } catch (err) {
        console.log(err);
    }
    return data;
}

function BlockedUsers(user_id){
    var data = [];
    return new Promise(function(resolve, reject) {
        try {
            if (SETTINGS.blocked_users == 'on') {
                connection.query(`SELECT user_id FROM ${T.BLOCK} WHERE user_id <> ? AND profile_id = ?`, [user_id, user_id], function(error, result, field){
                    if (error) { 
                        console.log(error);
                        return !1;
                    };
                    if(result.length > 0){
                        forEach(result, function(item, index, arr){
                            data.push(item.user_id);
                        })
                    }
                });
                connection.query(`SELECT profile_id FROM ${T.BLOCK} WHERE user_id = ? AND profile_id <> ?`, [user_id, user_id], function(error, result, field){
                    if (error) { 
                        console.log(error);
                        return !1;
                    };
                    if(result.length > 0){
                        forEach(result, function(item, index, arr){
                            data.push(item.profile_id);
                        })
                    }
                    return resolve(data)
                });
            }
        } catch (e) {
            return reject(e);
        }
    });
}

async function Data(socket, data, type = 1) {
    var user = {};

    if(!isNaN(type)){
        if(type == 1){
            await query(`SELECT * FROM ${T.USER} WHERE id = ?`, [data]).then(function(res){
                user = res;
            }).catch(function(err){
                console.log(err);
            });
        } else if(type == 3){
            user = data;
        } else {
            var cookies = cookie.parse(socket.handshake.headers.cookie);
            await query(`SELECT * FROM ${T.USER} WHERE (SELECT user_id FROM ${T.SESSION} WHERE token = ?) = id`, [cookies._LOGIN_TOKEN]).then(function(res){
                user = res;
            }).catch(function(err){
                console.log(err);
            });
        }
        
        if (user.length == 0) {
            return !1;
        }

        user = user[0];
        
        if(user.username != undefined){
            user.user = user.username;
            user.slug = ProfileUrl(user.username);
        }

        if(user.notifications != null){
            user.notifications = JSON.parse(user.notifications);
        } else {
            user.notifications = [
                'followers',
                'post',
                'followed',
                'collab',
                'react',
                'pcomment',
                'preply',
                'ucomment',
                'ureply'
            ];

            await AllCategories().then(function(res){
                user.notifications = user.notifications.concat(res);
            }).catch(function(err){
                console.log(err);
            });
        }
            
        if(user.shows != null){
            user.shows = JSON.parse(user.shows);
        } else {
            user.shows = {
                birthday: 'on',
                gender: 'on',
                contact_email: 'on',
                followers: 'on',
                messages: 'on'
            };
        }
    } else {
        user = await query(`SELECT ${type.join(',')} FROM ${T.USER} WHERE id = ?`, [data]);
        user = user[0];
    }

    if (Object.keys(user).length == 0) {
        return !1;
    }

    if(user.name != '' && user.name != undefined && user.surname != '' && user.surname != undefined){
        user.username = `${user.name} ${user.surname}`;
    } else if(user.username != '' && user.username != undefined){
        user.username = user.username;
    }

    if(user.birthday != 0 && user.birth_day != undefined){
        var birthday = new Date(user.birthday*1000);
        user.birth_day = birthday.getUTCDate();
        user.birthday_month = birthday.getUTCMonth()+1;
        user.birthday_year = birthday.getFullYear();
        user.birthday_format = DateFormat(socket, user.birthday);
    }
    if(Object.keys(user).indexOf('avatar') !== -1 || type.indexOf('avatar') !== -1){
        var rute = 4;
        if(user.avatar == 'default-holder'){
            rute = 5;
        }
        user.ex_avatar_b = `uploads/users/${user.avatar}-b.jpeg`;
        user.ex_avatar_s = `uploads/users/${user.avatar}-s.jpeg`;
        user.avatar_b = GetFile(user.avatar, rute, 'b');
        user.avatar_s = GetFile(user.avatar, rute, 's');
    }
    if(user.gender != '' && user.gender != undefined){
        user.gender_txt = global.TEMP[socket.id].word[user.gender];
    }
    if(user.created_at != '' && user.created_at != undefined){
        user.created_fat = DateFormat(socket, user.created_at);
        user.created_sat = DateString(socket, user.created_at);
    }
    return Object.keys(user).length > 1 ? user : Object.values(user);
}

function DateFormat(socket, ptime, type = 'normal') {
    var word = global.TEMP[socket.id].word;

    var date = new Date(ptime*1000);
    var days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
    var months = ['january', 'february', 'march', 'april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december'];
    
    var day = days[date.getDay()];
    var month = months[date.getMonth()+1];
    day = word[day];
    month = word[month];
    var B = month.substr(0, 3);

    var dateFinaly = `${date.getUTCDate()} ${B} ${date.getFullYear()}`;
    if(type == 'day'){
        dateFinaly = date.getUTCDate();
    }
    if(type == 'month'){
        dateFinaly = month;
    }
    if(type == 'complete'){
        dateFinaly =  `${day}, ${date.getUTCDate()} ${word.of} ${month} ${date.getFullYear()}`;
    }

    return dateFinaly;
}

function DateString(socket, time) {
    var TEMP = global.TEMP[socket.id],
        word = TEMP.word;

    var diff = Time() - time,
        string = String;
    if (diff < 1) {
        return word.now;
    }
    dates = {
        31536000: [word.year, word.years],
        2592000: [word.month, word.months],
        86400: [word.day, word.days],
        3600: [word.hour, word.hours],
        60: [word.minute, word.minutes],
        1: [word.second, word.seconds]
    };
    async.forEachOf(dates, function(value, key){
        var was = diff/key;
        if (was >= 1) {
            var was_int = parseInt(was);
            string = was_int > 1 ? value[1] : value[0];
            string = TEMP.rtl_languages.indexOf(TEMP.language) === -1 ? `${word.does} ${was_int} ${string}` : `${was_int} ${string} ${word.does}`;
        }
    });
    return string;
}

function GetFile(file, type = 1, size = ''){

    var prefix = '',
        suffix = '',
        folder = 'posts';

    if(type == 4){
        folder = 'users';
    }
    if(size != ''){
        suffix = `-${size}.jpeg`;
    }

    if(type == 2){
        prefix = `themes/${SETTINGS.theme}/`;
    } else {
        if(type == 3) {
            prefix = 'uploads/entries/';
        } else if(type == 5){
            prefix = `themes/${SETTINGS.theme}/images/users/`;
        } else {
            prefix = `uploads/${folder}/`;
        }
    }

    if(file == '' || !fs.existsSync(`${prefix}${file}${suffix}`)){
        file = 'default-holder';
        prefix = `themes/${SETTINGS.theme}/images/${folder}/`;
        if(type == 3){
            suffix = "-b.jpeg";
        }
    }

    return Url(`${prefix}${file}${suffix}`);
}

async function Words(language = 'en', paginate = false, page = 1, keyword = ''){
    var data = {};
    if(paginate == true){
        query = '';
        if(keyword != ''){
            query = ` WHERE word LIKE '%${keyword}%' OR ${language} LIKE '%${keyword}%'`;
        }
        /*
        $data['sql'] = $dba->query("SELECT * FROM word{$query} LIMIT ? OFFSET ?", 10, $page)->fetchAll();
        $data['total_pages'] = $dba->totalPages;
        */
    } else {
        await query(`SELECT word, ${language} FROM ${T.WORD}`).then(function(res){
            forEach(res, function(value){
                data[value.word] = value[language];
            })
        }).catch(function(err){
            console.log(err);
        });
    }
    return data;
}

async function SetNotify(socket, data = {}){
    var retrn = !1,
        typet = data.type;

    if(['preact', 'creact', 'rreact'].indexOf(data.type) != -1){
        typet = "react";
    }

    await IsOwner(socket, data.user_id).then(async function(res){
        if(!res){
            await Data(socket, data.user_id).then(async function(res){
                if(res.notifications.indexOf(typet) !== -1){
                    var type = `n_${data.type}`;
                    await query(`SELECT COUNT(*) as count FROM ${T.NOTIFICATION} WHERE user_id = ? AND notified_id = ? AND type = ?`, [data.user_id, data.notified_id, type]).then(async function(res){
                        if (res[0].count == 0) {
                            await query(`INSERT INTO ${T.NOTIFICATION} (user_id, notified_id, type, created_at) VALUES (?, ?, ?, ?)`, [data.user_id, data.notified_id, type, Time()]).then(async function(res){
                                retrn = !0;
                                await Notifies(data.user_id).catch(function(err){
                                    console.log(err);
                                });
                            }).catch(function(err){
                                retrn = !1;
                            })
                        }
                    }).catch(function(err){
                        console.log(err);
                    });
                }
            }).catch(function(err){
                console.log(err);
            });
        }
    }).catch(function(err){
        retrn = !1;
    })

    return retrn;
}

async function Notifies(user_id){

    var sockets = getSockets(user_id);

    if(sockets.length > 0){
        var notifications = 0,
            messages = 0,
            notifies = 0,
            USER = global.TEMP[sockets[0]].user;

        await query(`SELECT COUNT(*) as count FROM ${T.NOTIFICATION} WHERE user_id = ? AND seen = 0`, USER.id).then(function(res){
            notifies = notifications = res[0].count;
        }).catch(function(err){
            console.log(err);
        });

        await query(`SELECT COUNT(*) as count FROM ${T.MESSAGE} WHERE profile_id = ? AND seen = 0`, USER.id).then(function(res){
            notifies += messages = res[0].count;
        }).catch(function(err){
            console.log(err);
        });

        if(notifies > 0){
            emitChamgesTo(user_id, 'setOutnotifies', {
                S: 200,
                CT: notifies <= 9 ? notifies : "9+",
                CN: notifications,
                CM: messages
            });
            return !0;
        }
    }

    return !1;
}

async function Followers(socket, user_id){
    var number = 0;
    await query(`SELECT COUNT(*) as count FROM ${T.FOLLOWER} WHERE profile_id = ?`, user_id).then(function(res){
        number = res[0].count;
    }).catch(function(error){
        console.log(error);
    });

    var followers = NumberShorten(number);
    var text = `${followers} ${global.TEMP[socket.id].word.follower}`;
    if(number > 1){
        text = `${followers} ${global.TEMP[socket.id].word.followers}`;
    }

    return {number, text};
}

function NumberShorten(n, precision = 1) {
    // 0.9qi+
    var n_format = NumberFormat(n / 1000000000000000000, precision),
        suffix = 'Qi';
    if(n < 999) {
        // 0 - 900
        n_format = NumberFormat(n, precision);
        suffix = '';
    } else if(n < 999999) {
        // 0.9k-850k
        n_format = NumberFormat(n / 1000, precision);
        suffix = 'K';
    } else if(n < 999999999) {
        // 0.9m-850m
        n_format = NumberFormat(n / 1000000, precision);
        suffix = 'M';
    } else if(n < 999999999999) {
        // 0.9b-850b
        n_format = NumberFormat(n / 1000000000, precision);
        suffix = 'B';
    } else if(n < 999999999999999) {
        // 0.9t+
        n_format = NumberFormat(n / 1000000000000, precision);
        suffix = 'T';
    } else if(n < 999999999999999999) {
        // 0.9qa+
        n_format = NumberFormat(n / 1000000000000000, precision);
        suffix = 'Qa';
    }

      // Remove unecessary zeroes after decimal. "1.0" -> "1"; "1.00" -> "1"
      // Intentionally does not affect partials, eg "1.50" -> "1.50"
    if(precision > 0) {
        var dotzero = `.${'0'.repeat(precision)}`;
        n_format = n_format.replace(dotzero, '');
    }

    return `${n_format} ${suffix}`;
}


function NumberFormat(number, decimals, dec_point, thousands_sep) {
    // Strip all characters but numerical ones.
    number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function (n, prec) {
            var k = Math.pow(10, prec);
            return '' + Math.round(n * k) / k;
        };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}

function SizeFormat(bytes, precision = 2) {
    var unit = ["B", "KB", "MB", "GB"],
        exp = Math.floor(Math.log(bytes) / Math.log(1024)) | 0;

    return `${(bytes / Math.pow(1024, exp)).toFixed(precision)} ${unit[exp]}`;
}

async function IsOwner(socket, user_id) {
    var retrn = !1;
    if (global.TEMP[socket.id].loggedin == true) {
        await getCurrentUser(socket.id).then(function(res){
            if (res.user_id == user_id) {
                retrn = !0;
            }
        }).catch(function(err){
            retrn = !1;
        })
    }
    return retrn;
}
    
function CreateDirImage(folder){
    var time = new Date(),
        month = time.toLocaleString("en-US", {month: '2-digit'}),
        year = time.getFullYear(),
        folder_first = `../../uploads/${folder}/${year}-${month}`,
        dates = `${year}-${month}/${month}`,
        folder_last = `../../uploads/${folder}/${dates}`;

    
    if (!fs.existsSync(folder_first)) {
        fs.mkdirSync(folder_first, {recursive: true});
    }

    if (!fs.existsSync(folder_last)) {
        fs.mkdirSync(folder_last, {recursive: true});
    }
    return {
        full: folder_last,
        dates: dates
    };
}

async function UploadThumbnail(data = {}) {
    if(Object.keys(data).length == 0 || data.media.toLowerCase().indexOf('.gif') != -1){
        return {
            return: !1
        };
    }

    var dir_image = CreateDirImage(data.folder),
        getImage = data.media,
        retrn = {
            return: !1
        };

    if(data.folder == 'posts'){
        var image_one = `${Sha1(`${Rand(111,666)}${RandomKey()}`)}_${Time()}`,
            file_one = `${dir_image.full}/${image_one}`,
            image_two = `${Sha1(`${Rand(111,666)}${RandomKey()}`)}_${Time()}`,
            file_two = `${dir_image.full}/${image_two}`,
            filename_one_b = `${file_one}-b.jpeg`,
            filename_one_s = `${file_one}-s.jpeg`,
            filename_two_b = `${file_two}-b.jpeg`,
            filename_two_s = `${file_two}-s.jpeg`,
            url_dates = `${dir_image.dates}/${image_two}`;

        if (getImage != ''){
            await Download.image({
                url: getImage,
                dest: filename_one_b
            }).then(async function(filename){
                await Sharp(filename_one_b).resize(780, 440).toFile(filename_two_b).then(function(res) {
                    fs.unlink(filename_one_b, function(err) {
                        if(err && err.code == 'ENOENT') {
                            // file doens't exist
                            console.info("File doesn't exist, won't remove it.");
                        } else if (err) {
                            // other errors, e.g. maybe we don't have enough permission
                            console.error("Error occurred while trying to remove file");
                        } else {
                            console.info(`removed`);
                        }
                    });
                }).catch(function(err) {
                    console.log(err);
                });
            }).catch(function(err){
                console.log(err);
            })

            await Download.image({
                url: getImage,
                dest: filename_one_s
            }).then(async function(filename){
                await Sharp(filename_one_s).resize(400, 266).toFile(filename_two_s).then(function(res) {
                    fs.unlink(filename_one_s, function(err) {
                        if(err && err.code == 'ENOENT') {
                            // file doens't exist
                            console.info("File doesn't exist, won't remove it.");
                        } else if (err) {
                            // other errors, e.g. maybe we don't have enough permission
                            console.error("Error occurred while trying to remove file");
                        } else {
                            console.info(`removed`);
                        }
                    });
                }).catch(function(err) {
                    console.log(err);
                });
            }).catch(function(err){
                console.log(err);
            })

            if (fs.existsSync(filename_two_b) && fs.existsSync(filename_two_s)){
                retrn = {
                    return: !0,
                    image: url_dates,
                    image_ext: `${url_dates}.jpeg`
                };
            }
        }
    } else {
        var image = `${data.post_id}-${data.eorder}-${Md5(`${Time()}${RandomKey()}`)}`,
            filename = `${dir_image.full}/${image}.jpeg`;

        await Download.image({
            url: getImage,
            dest: filename
        }).then(function(filename){
            var url_dates = `${dir_image.dates}/${image}`;
            retrn = {
                return: !0,
                image: url_dates,
                image_ext: `${url_dates}.jpeg`
            };
        }).catch(function(err){
            console.log(err);
        });
    }
    return retrn;
}

async function UploadImage(data = {}){
    var dir_image = CreateDirImage(data.folder),
        retrn = {
            return: !1
        };

    if (Object.keys(data).length == 0 || ['.jpeg','.jpg','.png'].indexOf(path.extname(data.name)) === -1 || ['image/jpeg', 'image/png'].indexOf(data.type) === -1 || data.size > SETTINGS.file_size_limit) {
        return retrn;
    }

    if(data.folder == 'posts'){
        var image_one = `${Sha1(`${Rand(111,666)}${RandomKey()}`)}_${Time()}`,
            image_two = `${Sha1(`${Rand(111,666)}${RandomKey()}`)}_${Time()}`,
            file_one = `${dir_image.full}/${image_one}`,
            file_two = `${dir_image.full}/${image_two}`,
            filename_one_b = `${file_one}-b.jpeg`,
            filename_one_s = `${file_one}-s.jpeg`,
            filename_two_b = `${file_two}-b.jpeg`,
            filename_two_s = `${file_two}-s.jpeg`,
            url_dates = `${dir_image.dates}/${image_two}`;
        
        await fs.promises.readFile(data.tmp_name).then(async function(res){
            await fs.promises.writeFile(filename_one_b, res).then(async function(res){
                await Sharp(filename_one_b).resize(780, 440).toFile(filename_two_b).then(function(res) {
                    fs.copyFile(filename_one_b, filename_one_s, async function(error){
                        if (error) {
                            console.log(error)
                        }
                        fs.unlink(filename_one_b, function(err) {
                            if(err && err.code == 'ENOENT') {
                                // file doens't exist
                                console.info("File doesn't exist, won't remove it.");
                            } else if (err) {
                                // other errors, e.g. maybe we don't have enough permission
                                console.error("Error occurred while trying to remove file");
                            } else {
                                console.info(`removed`);
                            }
                        });
                        await Sharp(filename_one_s).resize(400, 266).toFile(filename_two_s).then(function(res) {
                            fs.unlink(filename_one_s, function(err) {
                                if(err && err.code == 'ENOENT') {
                                    // file doens't exist
                                    console.info("File doesn't exist, won't remove it.");
                                } else if (err) {
                                    // other errors, e.g. maybe we don't have enough permission
                                    console.error("Error occurred while trying to remove file");
                                } else {
                                    console.info(`removed`);
                                }
                            });
                        }).catch(function(err) {
                            console.log(err);
                        });
                    });
                }).catch(function(err) {
                    console.log(err);
                });
            }).catch(function(err){
                return retrn;
            })
        }).catch(function(err){
            return retrn;
        });

        retrn = {
            return: !0,
            image: url_dates,
            image_ext: `${url_dates}.jpeg`
        };
    } else {
        var image = `${data.post_id}-${data.eorder}-${Md5(`${Time()}${RandomKey()}`)}`,
            filename = `${dir_image.full}/${image}.jpeg`,
            url_dates = `${dir_image.dates}/${image}`;
        
        await fs.promises.readFile(data.tmp_name).then(async function(res){
            await fs.promises.writeFile(filename, res).then(function(res){
                retrn = {
                    return: true,
                    image: url_dates,
                    image_ext: `${url_dates}.jpeg`
                }
            }).catch(function(err){
                return retrn;
            });
        }).catch(function(err){
            return retrn;
        });
    }
    return retrn;
}

function CreateSlug(str, char = "-", tf = "lowercase", max = 120){
    var tft = !1;
    if(tf == "lowercase"){
        tft = !0;
    }
    str = str.substr(0, max);
    str = slug(str, {replacement: char, lower: tft});
    return str;
}

function BuildFrame(url, attrs = [], defult = !0, is_amp = !1){
    if(!Array.isArray(attrs)){
        attrs = htmlEntityDecode(attrs);
        attrs = JSON.parse(attrs);
    }

    var defaults = {
        width: '100%',
        height: '450',
        frameborder: 0
    };

    if(is_amp){
        defaults = {
            width: '200',
            height: '100',
            layout: 'responsive',
            sandbox: 'allow-scripts allow-same-origin',
            frameborder: 0
        };
    }

    var attributes = '';

    forEach(attrs, function(item, index, arr){
        if(item.name == 'attribute'){
            if(!item.value.match(/[^A-Za-z0-9\-\_]+/)){
                attributes += ` ${item.value}`;
                if(arr[index+1].name == 'value' && arr[index+1].value.match(/[^\"]+/)){
                    attributes += `="${arr[index+1].value}"`;
                }
                if(Object.values(defaults).indexOf(item.value) !== -1){
                    delete defaults[item.value];
                }
            } else {
                if(defult){
                    attrs.splice(index, 1)[0];
                    attrs.splice(index+1, 1)[0];
                }
            }
        }
    })

    if(Object(defaults).length > 0 && defult){
        forEach(defaults, function(item, index, arr){
            attributes += ` ${index}="${item}"`;
        })
    }
    
    if(ValidateUrl(url)){
        url = url.replace(/([h|H][t|T]{2}[p|P][s|S]?|[r|R][t|T][s|S][p|P]):\/\//, '//');
    }

    var html = `<iframe src="${url}"${attributes}></iframe>`
    if(is_amp){
        html = `<amp-iframe src="${url}"${attributes}></amp-iframe>`;
    }

    return {
        attrs: attrs,
        html: html
    };
}

function TextFilter(socket, text, links = !0){
    if(text != ''){
        if(SETTINGS.censored_words != ''){
            var censored_words = SETTINGS.censored_words.split(',');

            forEach(censored_words, function(item, index, arr){
                var word = item.replace(/\s+/gi, '');
                text = _.replace(text, word, word.replace(/(.+?)/ig, '*'));
            });
        }
        
        if(SETTINGS.hidden_domains != ''){
            var hidden_domains = SETTINGS.hidden_domains.split(',');

            forEach(hidden_domains, function(item, index, arr){
                var domain = item.replace(/\s+/gi, ''),
                    regex = new RegExp(`(?:(?:[\S]*)(${domain})(?:[\S])*)`, 'i');

                text = text.replace(regex, `[${global.TEMP[socket.id].word.hidden_link}]`);
            });
        }

        if(links){
            text = text.replace(url_regex, '<a class="color-blue hover-button animation-ease3s" href="//$3$4" target="_blank">$3$4</a>');
        }
    }

    return text;
}

async function CommentFilter(socket, text, mention_uid){
    text = TextFilter(socket, text);

    var username_exists = Array.from(text.matchAll(/@([a-zA-Z0-9]+)/ig), (u) => u[1]);

    if(username_exists.length > 0){
        await forEachAsync(username_exists, function(item, index, arr){
            var done = this.async();
            connection.query(`SELECT id, username, COUNT(*) as count FROM ${T.USER} WHERE username = ? AND status = "active"`, item, function(error, result, field){
                if(error){
                    done();
                    return console.log(error);
                }
                if(result[0].count > 0){
                    if(result[0].id != mention_uid){
                        var regex = new RegExp(`@(${item}+)`, 'i');
                        text = text.replace(regex, `<a class="color-blue hover-button" href="${ProfileUrl(result[0].username)}" target="_blank">@${result[0].username}</a>`);
                    }
                }
                done();
            })
        }).catch(function(err){})
    }
    return text;
}

async function ToMention(socket, data = {}, type = 'comment'){

    var username_exists = Array.from(data.text.matchAll(/@([a-zA-Z0-9]+)/ig), (u) => u[1]),
        retrn = !1;

    if(username_exists.length > 0){
        await forEachAsync(username_exists, function(item, index, arr){
            var done = this.async();
            connection.query(`SELECT id, COUNT(*) as count FROM ${T.USER} WHERE username = ? AND status = "active"`, item, async function(error, result, field){
                if(error){
                    done();
                    return console.log(error);
                }
                if(result[0].count > 0){
                    if(result[0].id != data.user_id){
                        retrn = !0;
                        await SetNotify(socket, {
                            user_id: result[0].id,
                            notified_id: data.insert_id,
                            type: `u${type}`,
                        }).catch(function(err){
                            console.log(err);
                        });
                    }
                }
                done();
            })
        }).catch(function(err){})
    }
    return retrn;
}


async function BuildComment(socket, comment = {}, order = 'recent', type = 'normal'){
    var retrn = {
            return: !1
        };

    if(Object.keys(comment).length > 0){
        var temp = new Object;


        await Data(socket, comment.user_id, ['username', 'avatar']).then(async function(res){
            var user = res,
                USER = global.TEMP[socket.id].user;

            await query(`SELECT user_id, slug FROM ${T.POST} WHERE id = ?`, comment.post_id).then(async function(res){
                var post = res[0];

                temp.url_comment = Url(`${post.slug}?${R.p_comment_id}=${comment.id}`);
                temp.comment_id = comment.id;

                temp.comment_owner = !1;
                await IsOwner(socket, comment.user_id).then(async function(res){
                    temp.comment_owner = res;
                }).catch(function(err){
                    console.log(err);
                });

                temp.comment_powner = !1;
                await IsOwner(socket, post.user_id).then(async function(res){
                    temp.comment_powner = res;
                }).catch(function(err){
                    console.log(err);
                });

                temp.cusername = global.TEMP[socket.id].word.user_without_login;
                temp.avatar_cs = Url('/themes/default/images/users/default-holder-s.jpeg');

                if(global.TEMP[socket.id].loggedin){
                    temp.cusername = USER.username;
                    temp.avatar_cs = USER.avatar_s;
                };
                
                var reply_ids = [];

                temp.comment_type = type;
                if(type == 'featured-reply'){
                    // To fetch the reply sent by url
                    var featured_rid = global.TEMP[socket.id].featured_rid;
                    await query(`SELECT * FROM ${T.REPLY} WHERE id = ?`, ).then(function(res){
                        var featured_reply = BuildReply(res[0], 'featured-reply');

                        if(featured_reply.return){
                            temp.featured_reply = featured_reply.html;
                            reply_ids.push(featured_rid);
                        }
                    }).catch(function(err){
                        console.log(err);
                    })
                }

                temp.replies = '';
                temp.reply_owner = !1;
                
                temp.count_replies = 0;
                await query(`SELECT COUNT(*) as count FROM ${T.REPLY} WHERE comment_id = ?`, comment.id).then(function(res){
                    temp.count_replies = res[0].count;
                }).catch(function(err){
                    console.log(err);
                });

                await Replies(comment.id, order, reply_ids).then(function(res){
                    if(res.return){
                        temp.replies = res.html;
                    }
                }).catch(function(err){
                    console.log(err);
                });

                temp.post_id = comment.post_id;
                await CommentFilter(socket, comment.text, comment.user_id).then(function(res){
                    temp.text = res;
                }).catch(function(err){
                    console.log(err);
                });

                temp.author_name = user.username;
                temp.author_url = ProfileUrl(temp.author_name);
                temp.author_avatar = user.avatar_s;

                temp.likes_active = 0;
                await query(`SELECT COUNT(*) as count FROM ${T.REACTION} WHERE user_id = ? AND reacted_id = ? AND type = "like" AND place = "comment"`, [USER.id, comment.id]).then(function(res){
                    temp.likes_active = res[0].count;
                }).catch(function(err){
                    console.log(err);
                });
    
                temp.dislikes_active = 0;
                await query(`SELECT COUNT(*) as count FROM ${T.REACTION} WHERE user_id = ? AND reacted_id = ? AND type = "dislike" AND place = "comment"`, [USER.id, comment.id]).then(function(res){
                    temp.dislikes_active = res[0].count;
                }).catch(function(err){
                    console.log(err);
                });


                temp.likes = 0;
                await query(`SELECT COUNT(*) as count FROM ${T.REACTION} WHERE reacted_id = ? AND type = "like" AND place = "comment"`, comment.id).then(function(res){
                    temp.likes = NumberShorten(res[0].count);
                }).catch(function(err){
                    console.log(err);
                });
    
                temp.dislikes = 0;
                await query(`SELECT COUNT(*) as count FROM ${T.REACTION} WHERE reacted_id = ? AND type = "dislike" AND place = "comment"`, comment.id).then(function(res){
                    temp.dislikes = NumberShorten(res[0].count);
                }).catch(function(err){
                    console.log(err);
                });

                temp.created_date = new Date(comment.created_at).toISOString();
                temp.created_at = DateString(socket, comment.created_at);
                
                var build = 'comment';
                if(comment.pinned == 1){
                    build = 'pinned-comment';
                }

                retrn = {
                    return: !0,
                    html: Build(socket, `comments/${build}`, temp)
                };

            }).catch(function(err){
                console.log(err);
            })
        }).catch(function(err){
            console.log(err);
        })
    }

    return retrn;
}


async function BuildReply(socket, reply = {}, type = 'normal'){
    var retrn = {
            return: !1
        };

    if(Object.keys(reply).length > 0){
        var temp = new Object;
        temp.reply_type = type;

        await Data(socket, reply.user_id, ['username', 'avatar']).then(async function(res){
            var user = res,
                USER = global.TEMP[socket.id].user;

            await query(`SELECT user_id, slug FROM ${T.POST} WHERE (SELECT post_id FROM ${T.COMMENTS} WHERE id = ?) = id`, reply.comment_id).then(async function(res){
                var post = res[0];
                
                temp.comment_id = reply.comment_id;
                temp.url_reply = Url(`${post.slug}?${R.p_reply_id}=${reply.id}`);
                temp.reply_id = reply.id;
                temp.reply_owner = !1;
                await IsOwner(socket, reply.user_id).then(async function(res){
                    temp.reply_owner = res;
                }).catch(function(err){
                    console.log(err);
                });
                temp.cusername = global.TEMP[socket.id].word.user_without_login;
                temp.avatar_cs = Url(`../../themes/default/images/users/default-holder-s.jpeg`);
    
                
                if(global.TEMP[socket.id].loggedin){
                    temp.cusername = USER.username;
                    temp.avatar_cs = USER.avatar_s;
                }
    
                temp.reply_powner = !1;
                await IsOwner(socket, post.user_id).then(async function(res){
                    temp.reply_powner = res;
                }).catch(function(err){
                    console.log(err);
                });
    
                await CommentFilter(socket, reply.text, reply.user_id).then(function(res){
                    temp.text = res;
                }).catch(function(err){
                    console.log(err);
                });

                temp.author_name = user.username;
                temp.author_url = ProfileUrl(temp.author_name);
                temp.author_avatar = user.avatar_s;
    
                temp.likes_active = 0;
                await query(`SELECT COUNT(*) as count FROM ${T.REACTION} WHERE user_id = ? AND reacted_id = ? AND type = "like" AND place = "reply"`, [USER.id, reply.id]).then(function(res){
                    temp.likes_active = res[0].count;
                }).catch(function(err){
                    console.log(err);
                });
    
                temp.dislikes_active = 0;
                await query(`SELECT COUNT(*) as count FROM ${T.REACTION} WHERE user_id = ? AND reacted_id = ? AND type = "dislike" AND place = "reply"`, [USER.id, reply.id]).then(function(res){
                    temp.dislikes_active = res[0].count;
                }).catch(function(err){
                    console.log(err);
                });
                
                temp.likes = 0;
                await query(`SELECT COUNT(*) as count FROM ${T.REACTION} WHERE reacted_id = ? AND type = "like" AND place = "reply"`, reply.id).then(function(res){
                    temp.likes = NumberShorten(res[0].count);
                }).catch(function(err){
                    console.log(err);
                });
    
                temp.dislikes = 0;
                await query(`SELECT COUNT(*) as count FROM ${T.REACTION} WHERE reacted_id = ? AND type = "dislike" AND place = "reply"`, reply.id).then(function(res){
                    temp.dislikes = NumberShorten(res[0].count);
                }).catch(function(err){
                    console.log(err);
                });
    
                temp.created_date = new Date(reply.created_at).toISOString();
                temp.created_at = DateString(socket, reply.created_at);
    
                retrn = {
                    return: !0,
                    html: Build(socket, 'comments/reply', temp)
                };
    
            }).catch(function(err){
                console.log(err);
            })
        }).catch(function(err){
            console.log(err);
        })
    }
    return retrn;
}

async function Replies(comment_id, order = 'recent', reply_ids = []){

    var qury = '',
        retrn = {
            return: !1
        };
    if(reply_ids.length > 0){
        qury = ` AND id NOT IN (${reply_ids.join(',')})`;
    }

    var sql = `SELECT * FROM ${T.REPLY} WHERE comment_id = ?${qury} LIMIT 5`;
    if(order == 'featured'){
        if(reply_ids.length > 0){
            qury = ` AND a.id NOT IN (${reply_ids.join(',')})`;
        }
        sql = `SELECT a.*, COUNT(c.id) as count FROM ${T.REPLY} a LEFT JOIN ${T.REACTION} c ON a.id = c.reacted_id AND c.type = "like" AND c.place = "reply" WHERE a.comment_id = ?${qury} GROUP BY a.id ORDER BY count DESC, a.id ASC LIMIT 5`;
    }

    await query(sql, comment_id).then(async function(res){
        var html = '';
        if(res.length > 0){
            await forEachAsync(res, function(item, index, arr){
                var done = this.async();
                BuildReply(item).then(async function(res){
                    html += res.html;
                    done();
                }).catch(function(err){
                    console.log(err);
                });
            }).catch(function(err){});
        }

        retrn = {
            return: !0,
            html: html
        };
    }).catch(function(err){
        console.log(err);
    });

    return retrn;
}


async function UploadMessagefi(data = {}){
    var dir_file = CreateDirImage('messages'),
        retrn = {
            return: !1
        };
    
    if(Object.keys(data).length > 0){
        if (data.size <= SETTINGS.file_size_limit) {
            var ext = path.extname(data.name),
                filename = `${data.message_id}-${Md5(`${Time()}${RandomKey()}`)}${ext}`;
            
            await fs.promises.readFile(data.tmp_name).then(async function(res){
                await fs.promises.writeFile(`${dir_file.full}/${filename}`, res).then(function(res){
                    retrn = {
                        return: !0,
                        file: `${dir_file.dates}/${filename}`
                    }
                }).catch(function(err){
                    return retrn;
                });
            }).catch(function(err){
                return retrn;
            });

        }
    }

    return retrn;
}


async function LastMessage(socket, profile_id, del_typing = !1){

    var data = {
            return: !1
        },
        temp = new Object,
        USER = global.TEMP[socket.id].user,
        WORD = global.TEMP[socket.id].word,
        blocked_inusers = global.TEMP[socket.id].blocked_inusers;

        await query(`SELECT * FROM ${T.MESSAGE} m WHERE user_id NOT IN (${blocked_inusers}) AND profile_id NOT IN (${blocked_inusers}) AND ((user_id = ? AND deleted_fuser = 0) OR (profile_id = ? AND deleted_fprofile = 0)) AND (SELECT id FROM ${T.CHAT} WHERE ((user_id = ? AND profile_id = ?) OR (user_id = ? AND profile_id = ?) AND id = m.chat_id)) = chat_id ORDER BY id DESC LIMIT 1`, [USER.id, USER.id, USER.id, profile_id, profile_id, USER.id]).then(async function(res){
            var last_message = res[0];

            if(Object.keys(last_message).length > 0){
                var unseen = !1;
                temp.last_unseen = !1;

                await IsOwner(socket, last_message.profile_id).then(function(res){
                    if(res && last_message.seen == 0){
                        temp.last_unseen = unseen = !0;
                    }
                }).catch(function(err){
                    console.log(err);
                });

                await query(`SELECT COUNT(*) as count FROM ${T.TYPING} WHERE user_id = ? AND profile_id = ?`, [profile_id, USER.id]).then(async function(res){
                    if(res[0].count > 0 && del_typing == !1){
                        unseen = !1;
                        last_text = WORD.is_writing;
                    } else {
                        var qury = ' AND deleted_fprofile = 0';
                        await IsOwner(socket, last_message.user_id).then(function(res){
                            if(res){
                                qury = ' AND deleted_fuser = 0';
                            }
                        }).catch(function(err){
                            console.log(err);
                        });

                        await query(`SELECT COUNT(*) as count FROM ${T.MESSAFI} WHERE message_id = ?${qury}`, last_message.id).then(async function(res){
                            if(res[0].count > 0){
                                await query(`SELECT * FROM ${T.MESSAFI} WHERE message_id = ?${qury} ORDER BY id DESC LIMIT 1`, last_message.id).then(async function(res){
                                    if(res[0].deleted_at == 0){
                                        last_text = WORD.attached_file;
                                        await IsOwner(socket, last_message.user_id).then(function(res){
                                            if(res){
                                                unseen = !1;
                                                last_text = `${WORD.you}: ${WORD.attached_file}`;
                                            }
                                        }).catch(function(err){
                                            console.log(err);
                                        });
                                    } else {
                                        last_text = WORD.deputy_file_deleted;
                                        await IsOwner(socket, last_message.user_id).then(function(res){
                                            if(res){
                                                unseen = !1;
                                                last_text = `${WORD.you}: ${last_text}`;
                                            }
                                        }).catch(function(err){
                                            console.log(err);
                                        });
                                    }
                                }).catch(function(err){
                                    console.log(err);
                                })
                            } else {
                                if(last_message.deleted_at == 0){
                                    last_text = TextFilter(socket, last_message.text, !1);
                                } else {
                                    last_text = WORD.message_was_deleted;
                                }
                                await IsOwner(socket, last_message.user_id).then(function(res){
                                    if(res){
                                        unseen = !1;
                                        last_text = `${WORD.you}: ${last_text}`;
                                    }
                                }).catch(function(err){
                                    console.log(err);
                                });
                            }
                        }).catch(function(err){
                            console.log(err);
                        })
                    }
                }).catch(function(err){
                    console.log(err);
                })
        
        
                data = {
                    return: !0,
                    unseen: unseen,
                    text: last_text,
                    chat_id: last_message.chat_id,
                    created_at: DateString(socket, last_message.created_at)
                };
            }

        }).catch(function(err){
            console.log(err);
        })

    return data;
}



function Build(socket, page, temp){
    var html = require(`../html/${page}.js`)(socket, temp);

    html = html.replace(/{\$word->(.+?)}/ig, function(match, word){
        var WORD = global.TEMP[socket.id].word;
        return WORD[word] != undefined ? WORD[word] : "";
    });
    
    html = html.replace(/{\$url->\{(.+?)\}}/ig, function(match, url){
        return Url(url != "home" ? url : "");
    });

    html = html.replace(/{\#([a-zA-Z0-9_]+)}/ig, function(match, rute){
        return R[rute] != undefined ? R[rute] : "";
    });

    html = html.replace(/{\$([a-zA-Z0-9_]+)}/ig, function(match, vr){
        return temp[vr] != undefined ? temp[vr] : "";
    });
    
    html = html.replace(/{\!([a-zA-Z0-9_]+)}/ig, function(match, vr){
        return temp[vr] != undefined ? temp[vr] : "";
    });

    return html;
}

function Rand(min, max){
    return Math.floor(Math.random() * (max - min + 1) + min);
}

function RandomKey(minlength = 12, maxlength = 20, number = !0) {
    var length = Math.floor(Rand(maxlength, minlength));
    number = number == true ? "1234567890" : "";
    return _.shuffle(`ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz${number}`).join('').substr(0, length);
}

function Filter(input = ''){
    if(input != ''){
        input = htmlEntities(input);
        input = input.replace(/(\r\n|\n\r|\r|\n)/gm, " <br>");
        input = MysqlRealEscapeString(input);
        input = stripSlashes(input);
    }
    return input;
}

function MysqlRealEscapeString(str){
    return !isNaN(str) ? str : str.replace(/[\0\x08\x09\x1a\n\r"'\\\%]/g, function(char){
        switch (char) {
            case "\0":
                return "\\0";
            case "\x08":
                return "\\b";
            case "\x09":
                return "\\t";
            case "\x1a":
                return "\\z";
            case "\n":
                return "\\n";
            case "\r":
                return "\\r";
            case "\"":
            case "'":
            case "\\":
            case "%":
                return "\\"+char; // prepends a backslash to backslash, percent,
                                  // and double/single quotes
        }
    });
}

async function AllCategories() {
    var categories = [];
    await query(`SELECT id FROM ${T.CATEGORY}`).then(async function(res){
        if(res.length > 0){
            await forEachAsync(res, function(item, index, arr) {
                categories.push(item.id.toString());
            }).catch(function(err){});
        }
    }).catch(function(err){
        console.log(err);
    });
    return categories;
}

function htmlEntities(str) {
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

function htmlEntityDecode(str){
    return str.replace(/&([a-z0-9]+|#[0-9]{1,6}|#x[0-9a-f]{1,6});/gi, (t) => (entities[t] && entities[t].characters) || t);
}

function ValidateUrl(url, protocol = !1){
    var match = url.match(url_regex);

    if(protocol){
        if(match == null){
            match = !1;
        } else {
            if(match[2] == undefined){
                url = `http://${url}`;
            }
        }
        return {
            return: !!match,
            url: url
        };
    }
    return !!match;
}


function stripSlashes(str) {
    return (str + '').replace(/\\(.?)/g, function(s,n1) {
        switch (n1) {
            case '\\':
                return '\\';
            case '0':
                return '\u0000';
            case '':
                return '';
            default:
                return n1;
        }
    });
}

function ArrayDiff(first, second) {
    return first.filter(x => second.indexOf(x) === -1);
}

function Time(){
    return unixTime(new Date());
}

function Url(params = '') {
    return `${info.site_url}/${params}`;
}

function ProfileUrl(username){
    return Url(`${R.r_user}/${username}`);
}

function setSocket(socket_id, user_id){
    var user = {socket_id, user_id};           
    sockets.push(user);
}

function getSockets(user_id, socket_id = ''){
    var data = [];
    forEach(sockets, function(sock){
        if(sock.user_id == user_id && (socket_id == '' || sock.socket_id != socket_id)){
            data.push(sock.socket_id);
        }
    })
    return data;
}

async function getCurrentUser(socket_id){
    var user = await sockets.find(sock => sock.socket_id === socket_id);
    return user;
}

function pullSocket(socket_id){
    const index = sockets.findIndex(sock=>sock.socket_id === socket_id);
    if(index !== -1){
        sockets.splice(index, 1)[0];
    }
}

function emitChangesAll(emit, data) {
    global.TEMP.io.sockets.emit(emit, data);
}

function emitChangesJustMe(socket_id, emit, data) {
    global.TEMP.io.to(socket_id).emit(emit, data);
}

function emitChangesOffme(socket, emit, data) {
    socket.broadcast.emit(emit, data);
}

function emitChamgesTo(user_id, emit, data, socket_id = '') {
    var socks = getSockets(user_id, socket_id);
    if(socks.length > 0){
        return global.TEMP.io.to(socks).emit(emit, data);
    }
    return !1;
}

module.exports = {
    Init,
    Admin,
    Moderator,
    Publisher,
    Settings,
    setSocket,
    getSockets,
    pullSocket,
    BlockedUsers,
    Url,
    ProfileUrl,
    Data,
    DateFormat,
    DateString,
    GetFile,
    Words,
    Filter,
    htmlEntities,
    htmlEntityDecode,
    ValidateUrl,
    stripSlashes,
    ArrayDiff,
    Time,
    SetNotify,
    Notifies,
    IsOwner,
    CreateDirImage,
    UploadThumbnail,
    UploadImage,
    CreateSlug,
    BuildFrame,
    TextFilter,
    CommentFilter,
    ToMention,
    BuildReply,
    BuildComment,
    Replies,
    UploadMessagefi,
    LastMessage,
    Build,
    Followers,
    NumberShorten,
    SizeFormat,
    Rand,
    RandomKey,
    MysqlRealEscapeString,
    AllCategories,
    getCurrentUser,
    emitChangesAll,
    emitChangesOffme,
    emitChangesJustMe,
    emitChamgesTo
};