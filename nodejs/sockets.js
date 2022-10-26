var connection = require('./mysql/DB');
var specific = require('./includes/specific');

var info = require('./info');
var R = require('./includes/rutes');
var T = require('./includes/tables');


var oembed = require('oembed-parser');
var util = require('util');
var async = require('async');
const _ = require('lodash');
var forEach = require('async-foreach').forEach;
const query = util.promisify(connection.query).bind(connection);

module.exports = async function(socket){
    const io = global.TEMP.io,
          TEMP = global.TEMP[socket.id],
          USER = TEMP.user,
          WORD = TEMP.word
          SETTINGS = specific.Settings(),
          loggedin = TEMP.loggedin,
          blocked_inusers = TEMP.blocked_inusers,
          blocked_arrusers = TEMP.blocked_arrusers,
          ADMIN = specific.Admin(socket),
          PUBLISHER = specific.Publisher(socket),
          ERR = {
            S: 400,
            E: `*${WORD.oops_error_has_occurred}`
          };

    function emitChamgesMe(emit, data) {
        var sockets = specific.getSockets(USER.id);
        io.to(sockets).emit(emit, data);
    }

    function emitChamgesBMe(emit, data) {
        var sockets = specific.getSockets(USER.id, socket.id);
        io.to(sockets).emit(emit, data);
    }
    
    socket.on('setInpreaction', async function(e, x){
        var post_id = specific.Filter(e.post_id),
            type = specific.Filter(e.type);

        if(post_id != '' && !isNaN(post_id) && ['like', 'dislike'].indexOf(type) != -1){
			connection.query(`SELECT *, COUNT(*) as count FROM ${T.POST} WHERE id = ? AND status = "approved"`, [post_id], function(error, result, field){
                if(error){
                    return x(ERR);
                }
                var post = result[0];
                if(post.count > 0){
                    if(type == 'like'){
                        connection.query(`SELECT COUNT(*) as count FROM ${T.REACTION} WHERE user_id = ? AND reacted_id = ? AND type = "like" AND place = "post"`, [USER.id, post_id], async function(error, result, field){
                            if(error){
                                return x(ERR);
                            }
                            if(result[0].count > 0){
                                var likes = (post.likes-1);
        
                                connection.query(`UPDATE ${T.POST} SET likes = ? WHERE id = ?`, [likes, post_id], function(error, result, field){
                                    if(error){
                                        return x(ERR);
                                    }
                                    if(result.affectedRows > 0){
                                        connection.query(`DELETE FROM ${T.NOTIFICATION} WHERE (SELECT id FROM ${T.REACTION} WHERE user_id = ? AND reacted_id = ? AND type = "like" AND place = "post") = notified_id AND type = "n_preact"`, [USER.id, post_id], async function(error, result, field){
                                            if(error){
                                                return x(ERR);
                                            }
                                            connection.query(`DELETE FROM ${T.REACTION} WHERE user_id = ? AND reacted_id = ? AND type = "like" AND place = "post"`, [USER.id, post_id], function(error, result, field){
                                                if(error){
                                                    return x(ERR);
                                                }
                                                var deliver = {
                                                    S: 200,
                                                    DB: `.btn_plike[data-id=${post.id}]`,
                                                    CR: likes
                                                };
                                                if(result.affectedRows > 0){
                                                    specific.emitChangesOffme(socket, 'setOutpreaction', deliver);
                                                    x(deliver);
                                                } else {
                                                    x(ERR)
                                                }
                                            })
                                        })
                                    } else {
                                        x(ERR)
                                    }
                                })
                            } else {
                                var deliver = {};
    
                                await query(`SELECT COUNT(*) as count FROM ${T.REACTION} WHERE user_id = ? AND reacted_id = ? AND type = "dislike" AND place = "post"`, [USER.id, post_id]).then(async function(res){
                                    if(res[0].count > 0){
                                        var dislikes = (post.dislikes-1);
                                        await query(`UPDATE ${T.POST} SET dislikes = ? WHERE id = ?`, [dislikes, post_id]).then(async function(res){
                                            if(res.affectedRows > 0){
                                                await query(`DELETE FROM ${T.NOTIFICATION} WHERE (SELECT id FROM ${T.REACTION} WHERE user_id = ? AND reacted_id = ? AND type = "dislike" AND place = "post") = notified_id AND type = "n_preact"`, [USER.id, post_id]).then(async function(res){
                                                    await query(`DELETE FROM ${T.REACTION} WHERE user_id = ? AND reacted_id = ? AND type = "dislike" AND place = "post"`, [USER.id, post_id]).then(function(res){
                                                        if(res.affectedRows > 0){
                                                            deliver.DO = `.btn_pdislike[data-id=${post.id}]`;
                                                            deliver.CO = dislikes;
                                                        } else {
                                                            x(ERR)
                                                        }
                                                    }).catch(function(){
                                                        x(ERR)
                                                    })
                                                }).catch(function(){
                                                    x(ERR)
                                                })
                                            } else {
                                                x(ERR)
                                            }
                                        }).catch(function(){
                                            x(ERR)
                                        })
                                    }
                                }).catch(function(){
                                    x(ERR)
                                })
    
                                var likes = (post.likes+1);
                                await query(`UPDATE ${T.POST} SET likes = ? WHERE id = ?`, [likes, post_id]).then(async function(res){
                                    if(res.affectedRows > 0){
                                        await query(`INSERT INTO ${T.REACTION} (user_id, reacted_id, type, place, created_at) VALUES (?, ?, "like", "post", ?)`, [USER.id, post_id, specific.Time()]).then(function(res){
                                            if(res.insertId > 0){
                                                deliver.S = 200;
                                                deliver.AB = `.btn_plike[data-id=${post.id}]`;
                                                deliver.CR = likes;
                                                specific.SetNotify(socket, {
                                                    user_id: post.user_id,
                                                    notified_id: res.insertId,
                                                    type: 'preact',
                                                });
                                            }
                                        }).catch(function(err){
                                            x(ERR)
                                        })
                                    } else {
                                        x(ERR)
                                    }
                                }).catch(function(err){
                                    x(ERR)
                                })
                                specific.emitChangesOffme(socket, 'setOutpreaction', deliver);
                                x(deliver);
                            }
                        })
                    } else {
    
                        connection.query(`SELECT COUNT(*) as count FROM ${T.REACTION} WHERE user_id = ? AND reacted_id = ? AND type = "dislike" AND place = "post"`, [USER.id, post_id], async function(error, result, field){
                            if(error){
                                return x(ERR);
                            }
                            if(result[0].count > 0){
                                var dislikes = (post.dislikes-1);
                        
                                connection.query(`UPDATE ${T.POST} SET dislikes = ? WHERE id = ?`, [dislikes, post_id], function(error, result, field){
                                    if(error){
                                        return x(ERR);
                                    }
                                    if(result.affectedRows > 0){
                                        connection.query(`DELETE FROM ${T.NOTIFICATION} WHERE (SELECT id FROM ${T.REACTION} WHERE user_id = ? AND reacted_id = ? AND type = "dislike" AND place = "post") = notified_id AND type = "n_preact"`, [USER.id, post_id], function(error, result, field){
                                            if(error){
                                                return x(ERR);
                                            }
                                            connection.query(`DELETE FROM ${T.REACTION} WHERE user_id = ? AND reacted_id = ? AND type = "dislike" AND place = "post"`, [USER.id, post_id], function(error, result, field){
                                                if(error){
                                                    return x(ERR);
                                                }
                                                var deliver = {
                                                    S: 200,
                                                    DB: `.btn_pdislike[data-id=${post.id}]`,
                                                    CR: dislikes
                                                };
                                                if(result.affectedRows > 0){
                                                    specific.emitChangesOffme(socket, 'setOutpreaction', deliver);
                                                    x(deliver);
                                                } else {
                                                    x(ERR)
                                                }
                                            })
                                        })
                                    } else {
                                        x(ERR)
                                    }
                                })
                            } else {
                                var deliver = {};
                        
                                await query(`SELECT COUNT(*) as count FROM ${T.REACTION} WHERE user_id = ? AND reacted_id = ? AND type = "like" AND place = "post"`, [USER.id, post_id]).then(async function(res){
    
                                    if(res[0].count > 0){
                                        var likes = (post.likes-1);
                                        await query(`UPDATE ${T.POST} SET likes = ? WHERE id = ?`, [likes, post_id]).then(async function(res){
                                            if(res.affectedRows > 0){
                                                await query(`DELETE FROM ${T.NOTIFICATION} WHERE (SELECT id FROM ${T.REACTION} WHERE user_id = ? AND reacted_id = ? AND type = "like" AND place = "post") = notified_id AND type = "n_preact"`, [USER.id, post_id]).then(async function(res){
                                                    await query(`DELETE FROM ${T.REACTION} WHERE user_id = ? AND reacted_id = ? AND type = "like" AND place = "post"`, [USER.id, post_id]).then(function(res){
                                                        if(res.affectedRows > 0){
                                                            deliver.DO = `.btn_plike[data-id=${post.id}]`;
                                                            deliver.CO = likes;
                                                        } else {
                                                            x(ERR)
                                                        }
                                                    }).catch(function(err){
                                                        x(ERR)
                                                    })
                                                }).catch(function(err){
                                                    x(ERR)
                                                })
                                            } else {
                                                x(ERR)
                                            }
                                        }).catch(function(err){
                                            x(ERR)
                                        })
                                    }
                                }).catch(function(err){
                                    x(ERR)
                                })
                        
                                var dislikes = (post.dislikes+1);
                                await query(`UPDATE ${T.POST} SET dislikes = ? WHERE id = ?`, [dislikes, post_id]).then(async function(res){
                                    if(res.affectedRows > 0){
                                        await query(`INSERT INTO ${T.REACTION} (user_id, reacted_id, type, place, created_at) VALUES (?, ?, "dislike", "post", ?)`, [USER.id, post_id, specific.Time()]).then(function(res){
                                            if(res.insertId > 0){
                                                deliver.S = 200;
                                                deliver.AB = `.btn_pdislike[data-id=${post.id}]`;
                                                deliver.CR = dislikes;
                                                specific.SetNotify(socket, {
                                                    user_id: post.user_id,
                                                    notified_id: res.insertId,
                                                    type: 'preact',
                                                });
                                            } else {
                                                x(ERR)
                                            }
                                        }).catch(function(err){
                                            x(ERR)
                                        })
                                    } else {
                                        x(ERR)
                                    }
                                }).catch(function(err){
                                    x(ERR)
                                })
                                specific.emitChangesOffme(socket, 'setOutpreaction', deliver);
                                x(deliver);
                            }
                        })
                    }
                }
            });
		}
    })
    

    socket.on('setIncrection', async function(e, x){
        var comment_id = specific.Filter(e.comment_id),
            type = specific.Filter(e.type),
		    treact = specific.Filter(e.treact);
        
        if(comment_id != '' && !isNaN(comment_id) && ['like', 'dislike'].indexOf(type) != -1 && ['comment', 'reply'].indexOf(treact) != -1){

            var n_react = 'creact',
                t_query = T.COMMENTS;
			if(treact == 'reply'){
				n_react = 'rreact';
				t_query = T.REPLY;
			}

			var likes = 0,
                dislikes = 0,
                n_creact = "n_{$n_react}";
            
            await query(`SELECT COUNT(*) as count FROM ${T.REACTION} WHERE reacted_id = ? AND type = "like" AND place = ?`, [comment_id, treact]).then(function(res){
                likes = res[0].count;
            }).catch(function(err){
                x(ERR)
            });
			await query(`SELECT COUNT(*) as count FROM ${T.REACTION} WHERE reacted_id = ? AND type = "dislikes" AND place = ?`, [comment_id, treact]).then(function(res){
                dislikes = res[0].count;
            }).catch(function(err){
                x(ERR)
            });

            connection.query(`SELECT COUNT(*) as count FROM ${t_query} WHERE id = ?`, [comment_id], function(error, result, field){
                if(error){
                    return x(ERR);
                }
                if(result[0].count > 0){
                    if(type == 'like'){
                        connection.query(`SELECT COUNT(*) as count FROM ${T.REACTION} WHERE user_id = ? AND reacted_id = ? AND type = "like" AND place = ?`, [USER.id, comment_id, treact], async function(error, result, field){
                            if(error){
                                return x(ERR);
                            }
                            if(result[0].count > 0){
                                connection.query(`DELETE FROM ${T.NOTIFICATION} WHERE (SELECT id FROM ${T.REACTION} WHERE user_id = ? AND reacted_id = ? AND type = "like" AND place = ?) = notified_id AND type = ?`, [USER.id, comment_id, treact, n_creact], function(error, result, field){
                                    if(error){
                                        return x(ERR);
                                    }
                                    connection.query(`DELETE FROM ${T.REACTION} WHERE user_id = ? AND reacted_id = ? AND type = "like" AND place = ?`, [USER.id, comment_id, treact], function(error, result, field){
                                        if(error){
                                            return x(ERR);
                                        }
                                        var deliver = {
                                            S: 200,
                                            DB: `.btn_clike[data-id=${comment_id}]`,
                                            CR: likes > 0 ? (likes-1) : 0
                                        };
                                        if(result.affectedRows > 0){
                                            specific.emitChangesOffme(socket, 'setOutcreaction', deliver);
                                            x(deliver);
                                        } else {
                                            x(ERR)
                                        }
                                    })
                                })
                            } else {
                                var deliver = {};
                                await query(`SELECT COUNT(*) as count FROM ${T.REACTION} WHERE user_id = ? AND reacted_id = ? AND type = "dislike" AND place = ?`, [USER.id, comment_id, treact]).then(async function(res){
                                    if(res[0].count > 0){
                                        await query(`DELETE FROM ${T.NOTIFICATION} WHERE (SELECT id FROM ${T.REACTION} WHERE user_id = ? AND reacted_id = ? AND type = "dislike" AND place = ?) = notified_id AND type = ?`, [USER.id, comment_id, treact, n_creact]).then(async function(res){
                                            await query(`DELETE FROM ${T.REACTION} WHERE user_id = ? AND reacted_id = ? AND type = "dislike" AND place = ?`, [USER.id, comment_id, treact]).then(function(res){
                                                if(res.affectedRows > 0){
                                                    deliver.DO = `.btn_cdislike[data-id=${comment_id}]`;
                                                    deliver.CO = dislikes > 0 ? (dislikes-1) : 0;
                                                } else {
                                                    x(ERR)
                                                }
                                            }).catch(function(err){
                                                x(ERR)
                                            })
                                        }).catch(function(err){
                                            x(ERR)
                                        })
                                    }
                                }).catch(function(err){
                                    x(ERR)
                                })

                                await query(`INSERT INTO ${T.REACTION} (user_id, reacted_id, type, place, created_at) VALUES (?, ?, "like", ?, ?)`, [USER.id, comment_id, treact, specific.Time()]).then(function(res){
                                    if(res.insertId > 0){
                                        deliver.S = 200;
                                        deliver.AB = `.btn_clike[data-id=${comment_id}]`;
                                        deliver.CR = (likes+1)
                                        connection.query(`SELECT user_id FROM ${t_query} WHERE (SELECT reacted_id FROM ${T.REACTION} WHERE id = ?) = id`, [res.insertId], function(error, result, field){
                                            if(error){
                                                return x(ERR);
                                            }
                                            if(result.length > 0){
                                                specific.SetNotify(socket, {
                                                    user_id: result[0].user_id,
                                                    notified_id: res.insertId,
                                                    type: n_react,
                                                });
                                            }
                                        })
                                    }
                                }).catch(function(err){
                                    x(ERR)
                                })
    
                                specific.emitChangesOffme(socket, 'setOutcreaction', deliver);
                                x(deliver);
                            }
                        })
                    } else {
                        connection.query(`SELECT COUNT(*) as count FROM ${T.REACTION} WHERE user_id = ? AND reacted_id = ? AND type = "dislike" AND place = ?`, [USER.id, comment_id, treact], async function(error, result, field){
                            if(error){
                                return x(ERR);
                            }
                            if(result[0].count > 0){
                                connection.query(`DELETE FROM ${T.NOTIFICATION} WHERE (SELECT id FROM ${T.REACTION} WHERE user_id = ? AND reacted_id = ? AND type = "dislike" AND place = ?) = notified_id AND type = ?`, [USER.id, comment_id, treact, n_creact], async function(error, result, field){
                                    if(error){
                                        return x(ERR);
                                    }
                                    connection.query(`DELETE FROM ${T.REACTION} WHERE user_id = ? AND reacted_id = ? AND type = "dislike" AND place = ?`, [USER.id, comment_id, treact], function(error, result, field){
                                        if(error){
                                            return x(ERR);
                                        }
                                        var deliver = {
                                            S: 200,
                                            DB: `.btn_cdislike[data-id=${comment_id}]`,
                                            CR: dislikes > 0 ? (dislikes-1) : 0
                                        };
                                        if(result.affectedRows > 0){
                                            specific.emitChangesOffme(socket, 'setOutcreaction', deliver);
                                            x(deliver);
                                        } else {
                                            x(ERR)
                                        }
                                    })
                                })
                            } else {
                                var deliver = {};
                        
                                await query(`SELECT COUNT(*) as count FROM ${T.REACTION} WHERE user_id = ? AND reacted_id = ? AND type = "like" AND place = ?`, [USER.id, comment_id, treact]).then(async function(res){
                                    if(res[0].count > 0){
                                        await query(`DELETE FROM ${T.NOTIFICATION} WHERE (SELECT id FROM ${T.REACTION} WHERE user_id = ? AND reacted_id = ? AND type = "like" AND place = ?) = notified_id AND type = ?`, [USER.id, comment_id, treact, n_creact]).then(async function(res){
                                            await query(`DELETE FROM ${T.REACTION} WHERE user_id = ? AND reacted_id = ? AND type = "like" AND place = ?`, [USER.id, comment_id, treact]).then(function(res){
                                                if(res.affectedRows > 0){
                                                    deliver.DO = `.btn_clike[data-id=${comment_id}]`;
                                                    deliver.CO = likes > 0 ? (likes-1) : 0;
                                                } else {
                                                    x(ERR)
                                                }
                                            }).catch(function(err){
                                                x(ERR)
                                            })
                                        }).catch(function(err){
                                            x(ERR)
                                        })
                                    }
                                }).catch(function(err){
                                    x(ERR)
                                })

                                await query(`INSERT INTO ${T.REACTION} (user_id, reacted_id, type, place, created_at) VALUES (?, ?, "dislike", ?, ?)`, [USER.id, comment_id, treact, specific.Time()]).then(function(res){
                                    if(res.insertId > 0){
                                        deliver.S = 200;
                                        deliver.AB = `.btn_cdislike[data-id=${comment_id}]`;
                                        deliver.CR = (dislikes+1);
                                        connection.query(`SELECT user_id FROM ${t_query} WHERE (SELECT reacted_id FROM ${T.REACTION} WHERE id = ?) = id`, [res.insertId], function(error, result, field){
                                            if(error){
                                                return x(ERR);
                                            }
                                            if(result.length > 0){
                                                specific.SetNotify(socket, {
                                                    user_id: result[0].user_id,
                                                    notified_id: res.insertId,
                                                    type: n_react,
                                                });
                                            }
                                        })
                                    }
                                }).catch(function(err){
                                    x(ERR)
                                })

                                specific.emitChangesOffme(socket, 'setOutcreaction', deliver);
                                x(deliver);
                            }
                        })
                    }
                }
            })

        }
    });

    socket.on('setInfollow', function(e, x){
        var user_id = specific.Filter(e.user_id);


        if(user_id != '' && !isNaN(user_id) && blocked_arrusers.indexOf(user_id) == -1){

            connection.query(`SELECT COUNT(*) as count FROM ${T.USER} WHERE id = ? AND status = "active"`, [user_id], function(error, result, field){
                if(error){
                    return x(ERR);
                }
                specific.IsOwner(socket, user_id).then(function(res){
                    if(!res && result[0].count > 0){
                        connection.query(`SELECT COUNT(*) as count FROM ${T.FOLLOWER} WHERE user_id = ? AND profile_id = ?`, [USER.id, user_id], function(error, result, field){
                            if(error){
                                return x(ERR);
                            }
                            
                            if(result[0].count > 0){
                                connection.query(`DELETE FROM ${T.NOTIFICATION} WHERE (SELECT id FROM ${T.FOLLOWER} WHERE user_id = ? AND profile_id = ?) = notified_id`, [USER.id, user_id], function(error, result, field){
                                    if(error){
                                        return x(ERR);
                                    }
                                    connection.query(`DELETE FROM ${T.FOLLOWER} WHERE user_id = ? AND profile_id = ?`, [USER.id, user_id], async function(error, result, field){
                                        if(error){
                                            return x(ERR);
                                        }
                                        if(result.affectedRows > 0){
                                            var deliver = {
                                                    S: 200,
                                                    T: 'follow',
                                                    L: WORD.follow
                                                };
                                            await specific.Data(socket, user_id).then(async function(res){
                                                if(res['shows']['followers'] == 'on'){
                                                    await specific.Followers(socket, user_id).then(function(res){
                                                        if(res.number > 0){
                                                            deliver.TX = res.text;
                                                            specific.emitChangesOffme(socket, 'setOutfollow', {
                                                                TX: res.text
                                                            });
                                                        }
                                                    }).catch(function(err){
                                                        console.log(err)
                                                    });
                                                }
                                            }).catch(function(err){
                                                console.log(err)
                                            })
                                            x(deliver);
                                        }
                                    })
                                })
                            } else {
                                connection.query(`INSERT INTO ${T.FOLLOWER} (user_id, profile_id, created_at) VALUES (?, ?, ?)`, [USER.id, user_id, specific.Time()], async function(error, result, field){
                                    if(error){
                                        return x(ERR);
                                    }
                                    if(result.insertId){
                                        var deliver = {
                                                S: 200,
                                                T: 'following',
                                                L: WORD.following
                                            };
                                        await specific.Data(socket, user_id).then(async function(res){
                                            if(res['shows']['followers'] == 'on'){
                                                await specific.Followers(socket, user_id).then(function(res){
                                                    if(res.number > 0){
                                                        deliver.TX = res.text;
                                                        specific.emitChangesOffme(socket, 'setOutfollow', {
                                                            TX: res.text
                                                        });
                                                    }
                                                }).catch(function(err){
                                                    console.log(err)
                                                });
                                            }
                                        }).catch(function(err){
                                            console.log(err)
                                        });
                                        specific.SetNotify(socket, {
                                            user_id: user_id,
                                            notified_id: result.insertId,
                                            type: 'followers',
                                        });
                                        x(deliver);
                                    }
                                })
                            }
    
                        })
                    }
                }).catch(function(err){
                    return x(ERR);
                });
            })

        }
    });

    const cors = require('cors');
    global.TEMP.app.use(cors({
        origin: [info.site_url],
        methods: ['POST']
    }));

    const formidable = require('formidable');
    var fs = require('fs'),
        forEachAsync = util.promisify(forEach);

    global.TEMP.app.post('/create-post', cors(), function(req, res, next){
        if(PUBLISHER == !0){
            formidable({ multiples: true }).parse(req, async function(err, fields, files){
                if(fields.socket_id !== undefined){
                    var empty = [],
                        error = [],
                        socket = {
                            id: specific.Filter(fields.socket_id)
                        },
                        title = specific.Filter(fields.title),
                        category = specific.Filter(fields.category),
                        type = specific.Filter(fields.type),
                        description = specific.Filter(fields.description),
                        entries = specific.Filter(fields.entries),
                        entries = specific.htmlEntityDecode(entries),
                        entries = JSON.parse(entries),
                        recobo = specific.Filter(fields.recobo),
                        recobo = specific.htmlEntityDecode(recobo),
                        recobo = JSON.parse(recobo),
                        collaborators = specific.Filter(fields.collaborators),
                        collaborators = specific.htmlEntityDecode(collaborators),
                        collaborators = JSON.parse(collaborators),
                        post_sources = specific.Filter(fields.post_sources),
                        post_sources = specific.htmlEntityDecode(post_sources),
                        post_sources = JSON.parse(post_sources),
                        thumb_sources = specific.Filter(fields.thumb_sources),
                        thumb_sources = specific.htmlEntityDecode(thumb_sources),
                        thumb_sources = JSON.parse(thumb_sources),
                        thumbnail = Object.keys(files).indexOf('thumbnail') !== -1 ? files.thumbnail : specific.Filter(fields.thumbnail),
                        tags = specific.Filter(fields.tags),
                        action = specific.Filter(fields.action);

                    if(title == ''){
                        empty.push({
                            EL: '#title',
                            TX: `*${WORD.this_field_is_empty}`
                        });
                    }
                    if(description == ''){
                        empty.push({
                            EL: '#description',
                            TX: `*${WORD.this_field_is_empty}`
                        });
                    } 
                    if(entries.length == 0){
                        empty.push({
                            EL: '.btn_aentry',
                            SW: 0,
                            TX: `*${WORD.you_create_least_entry}`
                        });
                    } else {
                        await forEachAsync(entries, function(item, index, arr){
                            if(arr[index][2] == ''){
                                empty.push({
                                    EL: index,
                                    CS: item[0] == 'text' ? '.simditor' : (item[0] == 'image' || item[0] == 'carousel' ? '.item-placeholder' : '.item-input'),
                                    TX: `*${WORD.this_field_is_empty}`
                                });
                            }
                        }).catch(function(err){});
                    }
                    
                    if(Object.keys(thumbnail).length == 0){
                        empty.push({
                            EL: '#post-right .item-placeholder',
                            TX: `*${WORD.this_field_is_empty}`
                        });
                    }
                    if(tags == ''){
                        empty.push({
                            EL: '#content-tags',
                            TX: `*${WORD.this_field_is_empty}`
                        });
                    }

                    if(post_sources.length > 0){
                        var empty_positions = [];
                        await forEachAsync(post_sources, function(item, index, arr){
                            if(item.name == '' && item.source != ''){
                                empty_positions.push(index);
                            }
                            if(item.name == '' && item.source == ''){
                                arr.splice(index, 1)[0];
                            }
                        }).catch(function(err){});

                        if(empty_positions.length > 0){
                            empty.push({
                                EL: '.post_sources',
                                PS: empty_positions,
                                CT: 0,
                                FD: 1,
                                SW: 0,
                                TX: `*${WORD.some_fields_empty}`
                            })
                        }
                        
                    }
                    
                    if(thumb_sources.length > 0){
                        var empty_positions = [];
                        await forEachAsync(thumb_sources, function(item, index, arr){
                            if(item.name == '' && item.source != ''){
                                empty_positions.push(index);
                            }
                            if(item.name == '' && item.source == ''){
                                arr.splice(index, 1)[0];
                            }
                        }).catch(function(err){});
                        if(empty_positions.length > 0){
                            empty.push({
                                EL: '.post_sources',
                                PS: empty_positions,
                                CT: 1,
                                FD: 1,
                                SW: 0,
                                TX: `*${WORD.some_fields_empty}`
                            })
                        }
                    }

                    if(['post', 'eraser'].indexOf(action) !== -1){
                        if(empty.length == 0){

                            if(Object.keys(files).indexOf('thumbnail') !== -1){
                                if(files.thumbnail.size > SETTINGS.file_size_limit){
                                    error.push({
                                        EL: '#post-right .item-placeholder',
                                        TX: _.replace(WORD.file_too_big_maximum_size, '{$file_size_limit}', specific.SizeFormat(SETTINGS.file_size_limit))
                                    })
                                }
                            }

                            await query(`SELECT id FROM ${T.CATEGORY}`).then(async function(res){
                                if(res.length > 0){

                                    var categories = [];

                                    await forEachAsync(res, function(item, index, arr) {
                                        categories.push(item.id);
                                    }).catch(function(err){});

                                    if(categories.indexOf(parseInt(category)) == -1){
                                        error.push({
                                            EL: '#category',
                                            TX: `*${WORD.oops_error_has_occurred}`
                                        });
                                    }
                                }
                            }).catch(function(err){
                                error.push({
                                    EL: '#category',
                                    TX: `*${WORD.oops_error_has_occurred}`
                                });
                            });

                            await forEachAsync(entries, function(item, index, arr){
                                if(item[0] == 'video'){
                                    if(!item[2].match(/^(?:http(?:s)?:\/\/)?(?:[a-z0-9.]+\.)?(?:youtu\.be|youtube\.com)\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/)([^\?&\"'>]+)/) && !item[2].match(/^(?:http(?:s)?:\/\/)?(?:[a-z0-9.]+\.)?vimeo\.com\/([0-9]+)$/) && !item[2].match(/^.+dailymotion.com\/(video|hub)\/([^_]+)[^#]*(#video=([^_&]+))?/)){
                                        error.push({
                                            EL: index,
                                            CS: '.item-input',
                                            TX: `*${WORD.enter_a_valid_url}`
                                        })
                                    }
                                }

                                if(item[0] == 'embed'){
                                    if(!specific.ValidateUrl(item[2])){
                                        error.push({
                                            EL: index,
                                            CS: '.item_url',
                                            TX: `*${WORD.enter_a_valid_url}`
                                        })
                                    }
                                }

                                if(item[0] == 'image'){
                                    if(Object.keys(files).indexOf(`thumbnail_${index}`) !== -1){
                                        if(files[`thumbnail_${index}`].size > SETTINGS.file_size_limit){
                                            error.push({
                                                EL: index,
                                                CS: '.item-placeholder',
                                                TX: _.replace(WORD.file_too_big_maximum_size, '{$file_size_limit}', specific.SizeFormat(SETTINGS.file_size_limit))
                                            })
                                        }
                                    } else {
                                        if(item[2] != ''){
                                            var validate_url = specific.ValidateUrl(item[2], true),
                                                image = validate_url.url;

                                            arr[index][2] = image;
                                            if(!validate_url.return){
                                                error.push({
                                                    EL: index,
                                                    CS: '.item-placeholder',
                                                    TX: `*${WORD.enter_a_valid_url}`
                                                })
                                            } else {
                                                var done = this.async(),
                                                    deliver = {
                                                        EL: index,
                                                        CS: '.item-placeholder',
                                                        TX: `*${WORD.download_could_not_completed}`
                                                    };
                                                fetch(image).then(function(response){
                                                    if(['image/jpeg', 'image/png'].indexOf(response.headers.get('content-type')) == -1){
                                                        done();
                                                        error.push(deliver);
                                                    } else {
                                                        done();
                                                    }
                                                }).catch(function(err) {
                                                    error.push(deliver);
                                                    done();
                                                });
                                            }
                                        }
                                    }
                                }


                                if(item[0] == 'carousel'){
                                    var carousel_id = `carousel_${index}_1`;

                                    if(Object.keys(files).indexOf(carousel_id) === -1 && fields[carousel_id] == undefined){
                                        error.push({
                                            EL: index,
                                            CS: '.content-carrusel',
                                            TX: `*${WORD.must_insert_more_one_image}`
                                        })
                                    } else {
                                        for (let i = 0; i < item[0]; i++) {
                                            var carousel_id = `carousel_${index}_${i}`;
                                            if(files[carousel_id].size > SETTINGS.file_size_limit){
                                                error.push({
                                                    EL: index,
                                                    CS: '.content-carrusel',
                                                    TX: _.replace(WORD.one_file_too_big_maximum_size, '{$file_size_limit}', specific.SizeFormat(SETTINGS.file_size_limit))
                                                });
                                                break;
                                            }
                                        }
                                    }
                                }

                                if(item[0] == 'facebookpost'){
                                    if(!item[2].match(/(?:(?:http|https):\/\/)?(?:www\.)?(?:facebook\.com)\/(\d+|[A-Za-z0-9\.]+)\/?/)){
                                        error.push({
                                            EL: index,
                                            CS: '.item-input',
                                            TX: `${WORD.enter_a_valid_url}`
                                        });
                                    }
                                }

                                if(item[0] == 'instagrampost'){
                                    if(!item[2].match(/(?:(?:http|https):\/\/)?(?:www\.)?(?:instagram\.com|instagr\.am)\/(?:p|tv|reel)\/([A-Za-z0-9-_\.]+)/)){
                                        error.push({
                                            EL: index,
                                            CS: '.item-input',
                                            TX: `${WORD.enter_a_valid_url}`
                                        });
                                    }
                                }

                                if(item[0] == 'tweet'){
                                    if(!item[2].match(/^https?:\/\/(mobile.|)twitter\.com\/(?:#!\/)?(\w+)\/status(?:es)?\/(\d+)(?:\/.*)?$/)){
                                        error.push({
                                            EL: index,
                                            CS: '.item-input',
                                            TX: `${WORD.enter_a_valid_url}`
                                        });
                                    }
                                }

                                if(item[0] == 'tiktok'){
                                    var tiktok_url = item[2].match(/(?:http(?:s)?:\/\/)?(?:(?:www)\.(?:tiktok\.com)(?:\/)(?!foryou)(@[a-zA-z0-9]+)(?:\/)(?:video)(?:\/)([\d]+)|(?:m)\.(?:tiktok\.com)(?:\/)(?!foryou)(?:v)(?:\/)?(?=([\d]+)\.html))/),
                                        tiktok_param = item[2].match(/#\/(@[a-zA-z0-9]*|.*)(?:\/)?(?:v|video)(?:\/)?([\d]+)/);

                                    if(tiktok_param || (tiktok_param && specific.ValidateUrl(item[2]))){
                                        tiktok_url = !0;
                                        arr[index][2] = `https://www.tiktok.com/${tiktok_param[1]}/video/${tiktok_param[2]}`;
                                    }
            
                                    if(!tiktok_url){
                                        error.push({
                                            EL: index,
                                            CS: '.item-input',
                                            TX: `*${WORD.enter_a_valid_url}`
                                        })
                                    }
                                }

                                if(item[0] == 'soundcloud'){
                                    if(!item[2].match(/^(?:(https?):\/\/)?(?:(?:www|m)\.)?(soundcloud\.com|snd\.sc)\/[a-z0-9](?!.*?(-|_){2})[\w-]{1,23}[a-z0-9](?:\/.+)?$/)){
                                        error.push({
                                            EL: index,
                                            CS: '.item-input',
                                            TX: `${WORD.enter_a_valid_url}`
                                        });
                                    }
                                }

                                if(item[0] == 'spotify'){
                                    if(!item[2].match(/https?:\/\/(?:embed\.|open\.)(?:spotify\.com\/)(?:(track|artist|album|playlist|episode)|user\/([a-zA-Z0-9]+)\/playlist)\/([a-zA-Z0-9]+)|spotify:((track|artist|album|playlist|episode):([a-zA-Z0-9]+)|user:([a-zA-Z0-9]+):playlist:([a-zA-Z0-9]+))/)){
                                        error.push({
                                            EL: index,
                                            CS: '.item-input',
                                            TX: `${WORD.enter_a_valid_url}`
                                        });
                                    }
                                }
                            }).catch(function(err){});
                            
                            if(['normal', 'video'].indexOf(type) == -1){
                                error.push({
                                    EL: '#type',
                                    TX: `*${WORD.oops_error_has_occurred}`
                                })
                            }

                            if(post_sources.length > 0){
                                var error_positions = [];

                                if(post_sources.length < SETTINGS.number_of_fonts){
                                    await forEachAsync(post_sources, function(item, index, arr){
                                        if(item.name != '' && item.source != ''){
                                            if(!specific.ValidateUrl(item.source)){
                                                error_positions.push(index);
                                            }
                                        }
                                    }).catch(function(err){});
                                    if(error_positions.length > 0){
                                        error.push({
                                            EL: '.post_sources',
                                            PS: error_positions,
                                            CT: 0,
                                            FD: 2,
                                            SW: 0,
                                            TX: `*${WORD.enter_a_valid_url}`
                                        });
                                    }
                                } else {
                                    error.push({
                                        EL: '.post_sources',
                                        CT: 0,
                                        SW: 0,
                                        TX: `*${WORD.oops_error_has_occurred}`
                                    });
                                }
                            }


                            if(thumb_sources.length > 0){
                                var error_positions = [];

                                if(thumb_sources.length < SETTINGS.number_of_fonts){
                                    await forEachAsync(thumb_sources, function(item, index, arr){
                                        if(item.name != '' && item.source != ''){
                                            if(!specific.ValidateUrl(item.source)){
                                                error_positions.push(index);
                                            }
                                        }
                                    }).catch(function(err){});
                                    if(error_positions.length > 0){
                                        error.push({
                                            EL: '.post_sources',
                                            PS: error_positions,
                                            CT: 1,
                                            FD: 2,
                                            SW: 0,
                                            TX: `*${WORD.enter_a_valid_url}`
                                        });
                                    }
                                } else {
                                    error.push({
                                        EL: '.post_sources',
                                        CT: 1,
                                        SW: 0,
                                        TX: `*${WORD.oops_error_has_occurred}`
                                    });
                                }
                            }
            
                            tags = tags.split(',');
                            if(tags.length > SETTINGS.number_labels){
                                error.push({
                                    EL: '#content-tags',
                                    TX: `*${WORD.oops_error_has_occurred}`
                                });
                            }

                            if(error.length == 0){
                                var fils = [];
                                if(Object.keys(files).indexOf('thumbnail') !== -1){
                                    await specific.UploadImage({
                                        name: files.thumbnail.originalFilename,
                                        tmp_name: files.thumbnail.filepath,
                                        size: files.thumbnail.size,
                                        type: files.thumbnail.mimetype,
                                        folder: 'posts',
                                    }).then(function(res){
                                        thumbnail = res;
                                        fils.push(`uploads/posts/${res.image}`);
                                    }).catch(function(err){
                                        arr_trues.push(!1);
                                    });
                                } else {
                                    await specific.UploadThumbnail({
                                        media: thumbnail,
                                        folder: 'posts'
                                    }).then(function(res){
                                        thumbnail = res;
                                        fils.push(`uploads/posts/${res.image}`);
                                    }).catch(function(err){
                                        arr_trues.push(!1);
                                    });
                                }
                                
                                
                                if(post_sources.length > 0){
                                    post_sources = JSON.stringify(Object.values(post_sources));
                                } else {
                                    post_sources = null;
                                }
                                if(thumb_sources.length > 0){
                                    thumb_sources = JSON.stringify(Object.values(thumb_sources));
                                } else {
                                    thumb_sources = null;
                                }

                                var status = 'approved';
                                if(SETTINGS.approve_posts == 'on' && ADMIN == false){
                                    status = 'pending';
                                }

                                if(thumbnail.return){
                                    var st_regex = '/<(?:script|style)[^>]*>(.*?)<\/(?:script|style)>/is',
                                        published_at = created_at = specific.Time(),
                                        slug = specific.CreateSlug(title);

                                    await query(`SELECT COUNT(*) as count FROM ${T.POST} WHERE slug = ?`, [slug]).then(function(res){
                                        if(res[0].count > 0){
                                            slug = `${slug}-${res[0].count}`;
                                        }
                                    }).catch(function(err){
                                        arr_trues.push(!1);
                                    })

                                    if(action == 'eraser'){
                                        published_at = 0;
                                    }

                                    await query(`INSERT INTO ${T.POST} (user_id, category_id, title, description, slug, thumbnail, post_sources, thumb_sources, type, status, published_at, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`, [USER.id, category, title, description, slug, thumbnail.image, post_sources, thumb_sources, type, status, published_at, created_at]).then(async function(qres){
                                        if(qres.insertId > 0){
                                            var arr_trues = [],
                                                post_id = qres.insertId;


                                            await forEachAsync(entries, function(item, index, arr){
                                                var done = this.async(),
                                                    entry_title = null,
                                                    entry_source = null,
                                                    entFn = async function(content_frame = null, thumbnail_accept = !1, carousel_accept = !1){
                                                        if((item[0] == 'image' && thumbnail_accept) || (item[0] == 'carousel' && carousel_accept) || ['image', 'carousel'].indexOf(item[0]) === -1){
                                                            await query(`INSERT INTO ${T.ENTRY} (post_id, type, title, body, frame, esource, eorder, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)`, [post_id, item[0], entry_title, content_text, content_frame, entry_source, index, created_at]).then(function(res){
                                                                if(res.insertId > 0){
                                                                    arr_trues.push(!0);
                                                                } else {
                                                                    arr_trues.push(!1);
                                                                }
                                                            }).catch(function(err){
                                                                arr_trues.push(!1);
                                                            });
                                                        }
                                                        done();
                                                    };
                                                if(item[1] != ''){
                                                    entry_title = item[1];
                                                }
                                                if(item[0] != 'image'){
                                                    if(item[3] != ''){
                                                        entry_source = item[3];
                                                    }
                                                } else {
                                                    entry_source = item[4];
                                                }

                                                if(entry_source != null){
                                                    entry_source = entry_source.replace(st_regex, '');
                                                }
                                                
                                                var content_text = null;

                                                if(item[0] == 'text'){
                                                    content_text = item[2].replace(st_regex, '');
                                                    entFn();
                                                } else {
                                                    if(item[0] == 'image'){
                                                        var thumbnail_id = `thumbnail_${index}`;
                                                        if(Object.keys(files).indexOf(thumbnail_id) !== -1){
                                                            var thumbnail = files[thumbnail_id];

                                                            specific.UploadImage({
                                                                name: thumbnail.originalFilename,
                                                                tmp_name: thumbnail.filepath,
                                                                size: thumbnail.size,
                                                                type: thumbnail.mimetype,
                                                                post_id: post_id,
                                                                eorder: index,
                                                                folder: 'entries',
                                                            }).then(function(res){
                                                                entFn(res.image_ext, res.return);
                                                                fils.push(res.image_ext);
                                                            }).catch(function(err){
                                                                arr_trues.push(!1);
                                                                done();
                                                            });
                                                        } else if(item[2] != ''){
                                                            specific.UploadThumbnail({
                                                                media: item[2],
                                                                post_id: post_id,
                                                                eorder: index,
                                                                folder: 'entries'
                                                            }).then(function(res){
                                                                entFn(res.image_ext, res.return);
                                                                fils.push(res.image_ext);
                                                            }).catch(function(err){
                                                                arr_trues.push(!1);
                                                                done();
                                                            });
                                                        }
                                                    } else if(item[0] == 'carousel'){
                                                        var captions = specific.Filter(fields[`carousel_captions_${index}`]),
                                                            carousel = [],
                                                            car_size = Array(item[2]).fill(0);

                                                        captions = specific.htmlEntityDecode(captions);
                                                        captions = JSON.parse(captions);

                                                        forEachAsync(car_size, function(m, i, a){
                                                            var done2 = this.async(),
                                                                carousel_id = `carousel_${index}_${i}`;

                                                            if(Object.keys(files).indexOf(carousel_id) !== -1){
                                                                var thumbnail = files[carousel_id];

                                                                specific.UploadImage({
                                                                    name: thumbnail.originalFilename,
                                                                    tmp_name: thumbnail.filepath,
                                                                    size: thumbnail.size,
                                                                    type: thumbnail.mimetype,
                                                                    post_id: post_id,
                                                                    eorder: index,
                                                                    folder: 'entries',
                                                                }).then(function(res){
                                                                    if(res.return){
                                                                        fils.push(res.image_ext);
                                                                        carousel.push({
                                                                            image: res.image_ext,
                                                                            caption: captions[i]
                                                                        });
                                                                    }
                                                                    if(i == (item[2]-1)){
                                                                        entFn(JSON.stringify(carousel), !1, res.return);
                                                                    }
                                                                    done2();
                                                                }).catch(function(err){
                                                                    arr_trues.push(!1);
                                                                    if(i == (item[2]-1)){
                                                                        done();
                                                                    }
                                                                    done2();
                                                                });
                                                            } else if(fields[carousel_id] != undefined){
                                                                specific.UploadThumbnail({
                                                                    media: specific.Filter(fields[carousel_id]),
                                                                    post_id: post_id,
                                                                    eorder: index,
                                                                    folder: 'entries'
                                                                }).then(function(res){
                                                                    if(res.return){
                                                                        fils.push(res.image_ext);
                                                                        carousel.push({
                                                                            image: res.image_ext,
                                                                            caption: captions[i]
                                                                        });
                                                                    }
                                                                    if(i == (item[2]-1)){
                                                                        entFn(JSON.stringify(carousel), !1, res.return);
                                                                    }
                                                                    done2();
                                                                }).catch(function(err){
                                                                    arr_trues.push(!1);
                                                                    if(i == (item[2]-1)){
                                                                        done();
                                                                    }
                                                                    done2();
                                                                });
                                                            }
                                                        }).catch(function(err){});
                                                    } else if(['tweet', 'soundcloud', 'spotify', 'tiktok'].indexOf(item[0]) !== -1){
                                                        oembed.extract(item[2]).then((json) => {
                                                            if(json.error === undefined && json.errors === undefined && json.status_msg === undefined){
                                                                if(json != ''){
                                                                    var content_frame = json.html;
                                                                    if(item[0] == 'tiktok'){
                                                                        content_frame = content_frame.replace(/(\s+)?(?:<script [^>]*><\/script>)/, '');
                                                                    } else if(item[0] == 'soundcloud'){
                                                                        var src = content_frame.match(/(?<=src=").*?(?=[\*"])/);
                                                                        if(src){
                                                                            content_frame = src[0];
                                                                        }
                                                                    }
                                                                    arr_trues.push(!0);
                                                                    entFn(content_frame);
                                                                } else {
                                                                    arr_trues.push(!1);
                                                                    done();
                                                                }
                                                            } else {
                                                                arr_trues.push(!1);
                                                                done();
                                                            }
                                                        }).catch((err) => {
                                                            console.trace(err);
                                                            done();
                                                        })
                                                    } else if(item[0] == 'embed'){

                                                        var attrs = specific.Filter(fields[`embed_${index}`]),
                                                            frame = specific.MaketFrame(item[2], attrs);

                                                        frame = {
                                                            url: item[2],
                                                            attrs: frame.attrs
                                                        };
                
                                                        entFn(JSON.stringify(frame));
                                                    } else {
                                                        entFn(item[2]);
                                                    }
                                                }
                                            }).catch(function(err){});

                                            await forEachAsync(tags, function(item, index, arr){
                                                var done = this.async();
                                                connection.query(`SELECT id, COUNT(*) as count FROM ${T.LABEL} WHERE name = ?`, [item], async function(error, result, field){
                                                    if(error){
                                                        return arr_trues.push(!1);
                                                    }
                                                    var fnTag = function(label_id){
                                                            connection.query(`INSERT INTO ${T.TAG} (post_id, label_id, created_at) VALUES (?, ?, ?)`, [post_id, label_id, specific.Time()], function(error, result, field){
                                                                if(error){
                                                                    done();
                                                                    return arr_trues.push(!1);
                                                                }
                                                                if(result.insertId > 0){
                                                                    arr_trues.push(!0);
                                                                } else {
                                                                    arr_trues.push(!1);
                                                                }
                                                                done();
                                                            });
                                                        };

                                                    if(result[0].count > 0){
                                                        fnTag(result[0].id);
                                                    } else {
                                                        connection.query(`INSERT INTO ${T.LABEL} (name, slug, created_at) VALUES (?, ?, ?)`, [item, specific.CreateSlug(item), specific.Time()], function(error, result, field){
                                                            if(error){
                                                                done();
                                                                return arr_trues.push(!1);
                                                            }
                                                            if(result.insertId > 0){
                                                                fnTag(result.insertId);
                                                                arr_trues.push(!0);
                                                            } else {
                                                                arr_trues.push(!1);
                                                                done();
                                                            }
                                                        });
                                                    }
                                                });
                                            }).catch(function(err){});

                                            if(recobo.length > 0){
                                                var recount = 0;
                                                await forEachAsync(recobo, function(item, index, arr){
                                                    var done = this.async();
                                                    connection.query(`SELECT COUNT(*) as count FROM ${T.POST} WHERE id = ? AND status = "approved"`, [item], function(error, result, field){
                                                        if(error){
                                                            done();
                                                            return arr_trues.push(!1);
                                                        }
                                                        if(result[0].count > 0){
                                                            connection.query(`INSERT INTO ${T.RECOBO} (post_id, recommended_id, rorder, created_at) VALUES (?, ?, ?, ?)`, [post_id, item, recount, specific.Time()], function(error, result, field){
                                                                if(error){
                                                                    return arr_trues.push(!1);
                                                                }
                                                                if(result.insertId > 0){
                                                                    recount++;
                                                                }
                                                                done();
                                                            })
                                                        } else done();
                                                    })
                                                }).catch(function(err){});
                                            }


                                            if(collaborators.length > 0){
                                                var cocount = 0;

                                                await forEachAsync(collaborators, function(item, index, arr){
                                                    var done = this.async();
                                                    connection.query(`SELECT about, facebook, twitter, instagram, main_sonet, COUNT(*) as count FROM ${T.USER} WHERE id = ? AND id NOT IN (${blocked_inusers}) AND status = "active"`, [item], function(error, result, field){
                                                        if(error){
                                                            done();
                                                            return arr_trues.push(!1);
                                                        }
                                                        if(result[0].count > 0 && result[0].about != '' && result[0][result[0].main_sonet] != ''){
                                                            connection.query(`INSERT INTO ${T.COLLABORATOR} (user_id, post_id, aorder, created_at) VALUES (?, ?, ?, ?)`, [item, post_id, cocount, specific.Time()], async function(error, result, field){
                                                                if(error){
                                                                    done();
                                                                    return arr_trues.push(!1);
                                                                }
                                                                if(result.insertId > 0){
                                                                    await specific.SetNotify(socket, {
                                                                        user_id: item,
                                                                        notified_id: result.insertId,
                                                                        type: 'collab',
                                                                    }).then(function(res){
                                                                        done();
                                                                    }).catch(function(err){
                                                                        done();
                                                                    });
                                                                } else {
                                                                    done();
                                                                }
                                                                cocount++;
                                                            });
                                                        } else done();
                                                    });
                                                }).catch(function(err){});
                                            }

                                            if(arr_trues.indexOf(!1) === -1){
                                                if(SETTINGS.approve_posts == 'off' || ADMIN == true){
                                                    await query(`SELECT user_id FROM ${T.FOLLOWER} WHERE profile_id = ?`, [USER.id]).then(async function(res){
                                                        if(res.length > 0){
                                                            await forEachAsync(res, function(item, index, arr){
                                                                var done = this.async();
                                                                specific.SetNotify(socket, {
                                                                    user_id: item.user_id,
                                                                    notified_id: post_id,
                                                                    type: 'post',
                                                                }).then(function(res){
                                                                    done();
                                                                }).catch(function(err){
                                                                    done();
                                                                });
                                                            }).catch(function(err){});
                                                        }
                                                    }).catch(function(err){
                                                        arr_trues.push(!1);
                                                    });
                                                } else {

                                                }

                                                res.send({
                                                    S: 200,
                                                    LK: specific.Url(slug)
                                                });
                                            } else {
                                                await query(`DELETE FROM ${T.POST} WHERE id = ?`, post_id).then(async function(res){
                                                    if(res.affectedRows > 0){
                                                        await forEachAsync(fils, function(item, index, arr){
                                                            var done = this.async();
                                                            if(item.indexOf('posts') !== -1){
                                                                fs.unlink(`${item}-b.jpeg`, function(err){
                                                                    done();
                                                                });
                                                                fs.unlink(`${item}-s.jpeg`, function(err){
                                                                    done();
                                                                });
                                                            } else {
                                                                fs.unlink(`uploads/entries/${item}`, function(err){
                                                                    done();
                                                                });
                                                            }    
                                                        }).catch(function(err){});
                                                    }
                                                });
                                                res.send(ERR);
                                            }

                                        }
                                    }).catch(function(err){
                                        res.send(ERR);
                                    })
                                    
                                }

                            } else {
                                res.send({
                                    S: 400,
                                    E: error
                                });
                            }

                        } else {
                            res.send({
                                S: 400,
                                E: empty
                            });
                        }
                    }
                } else {
                    res.send(ERR);
                }
               

            });
        } else {
            res.send(ERR);
        }

    });

    /*
    socket.on('setIndelete', function(e){
        try {
            
        } catch (err) {
            console.log(err)
        }
    })
    */


}