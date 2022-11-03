const connection = require('./mysql/DB'),
      specific = require('./includes/specific'),
      info = require('./info'),
      T = require('./includes/tables'),
      R = require('./includes/rutes'),
      util = require('util'),
      forEach = require('async-foreach').forEach,
      forEachAsync = util.promisify(forEach),
      formidable = require('formidable'),
      fs = require('fs'),
      query = util.promisify(connection.query).bind(connection),
      _ = require('lodash'),
      path = require('path'),
      striptags = require('striptags'),
      oembed = require('oembed-parser');

module.exports = async function(socket){
    const TEMP = global.TEMP[socket.id],
          USER = TEMP.user,
          WORD = TEMP.word,
          SETTINGS = specific.Settings(),
          PAGE = socket.handshake.query.page,
          blocked_inusers = TEMP.blocked_inusers,
          blocked_arrusers = TEMP.blocked_arrusers,
          ERR = {
            S: 400,
            E: `*${WORD.oops_error_has_occurred}`
          },
          cors = require('cors');
          
    var is_typing = !1;

    global.TEMP.app.use(cors({
        origin: [info.site_url],
        methods: ['POST']
    }));
    
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

    socket.on('setIncomment', function(e, x){
        var post_id = specific.Filter(e.post_id),
            text = specific.Filter(e.text);
        
        if(post_id != '' && !isNaN(post_id) && text.trim() != '' && striptags(specific.htmlEntityDecode(text)).length <= SETTINGS.max_words_comments){
            connection.query(`SELECT user_id, COUNT(*) as count FROM ${T.POST} WHERE id = ? AND user_id NOT IN (${blocked_inusers}) AND status = "approved"`, post_id, function(error, result, field){
                if(error){
                    return x(ERR);
                }
                var post = result[0];
                if(post.count > 0){
                    var created_at = specific.Time(),
                        count_comment = 0;
                    
                    connection.query(`INSERT INTO ${T.COMMENTS} (user_id, post_id, text, created_at) VALUES (?, ?, ? ,?)`, [USER.id, post_id, text, created_at], function(error, result, field){
                        if(error){
                            return x(ERR);
                        }
                        if(result.insertId > 0){
                            specific.CommentMaket(socket, {
                                id: result.insertId,
                                user_id: USER.id,
                                post_id: post_id,
                                text: text,
                                created_at: created_at
                            }).then(async function(res){
                                if(res.return){
                                    await query(`SELECT COUNT(*) as count FROM ${T.COMMENTS} WHERE post_id = ?`, post_id).then(function(res){
                                        count_comment = res[0].count;
                                    }).catch(function(err){
                                        console.log(err);
                                    });

                                    await specific.SetNotify(socket, {
                                        user_id: post.user_id,
                                        notified_id: result.insertId,
                                        type: 'pcomment',
                                    }).catch(function(err){
                                        console.log(err);
                                    });

                                    await specific.ToMention(socket, {
                                        text: text,
                                        user_id: post.user_id,
                                        insert_id: result.insertId
                                    }).catch(function(err){
                                        console.log(err);
                                    });

                                    x({
                                        S: 200,
                                        HT: res.html,
                                        CC: count_comment
                                    });
                                } else x(ERR);
                            }).catch(function(err){
                                console.log(err);
                            });
                        } else x(ERR);
                    })
                } else x(ERR);
            })
        } else x(ERR);
    });


    socket.on('setInreply', function(e, x){
		var comment_id = specific.Filter(e.comment_id),
            text = specific.Filter(e.text);
            
        if(comment_id != '' && !isNaN(comment_id) && text.trim() != '' && striptags(specific.htmlEntityDecode(text)).length <= SETTINGS.max_words_comments){
            connection.query(`SELECT COUNT(*) as count FROM ${T.POST} p WHERE (SELECT post_id FROM ${T.COMMENTS} WHERE id = ? AND post_id = p.id) = id AND user_id NOT IN (${blocked_inusers}) AND status = "approved"`, comment_id, function(error, result, field){
                if(error){
                    return x(ERR);
                }
                if(result[0].count > 0){
                    var created_at = specific.Time(),
                        count_replies = 0;

                    connection.query(`INSERT INTO ${T.REPLY} (user_id, comment_id, text, created_at) VALUES (?, ?, ? ,?)`, [USER.id, comment_id, text, created_at], function(error, result, field){
                        if(error){
                            return x(ERR);
                        }
                        if(result.insertId > 0){
                            specific.ReplyMaket(socket, {
                                id: result.insertId,
                                user_id: USER.id,
                                comment_id: comment_id,
                                text: text,
                                created_at: created_at
                            }, 'new').then(async function(res){
                                if(res.return){
                                    await query(`SELECT COUNT(*) as count FROM ${T.REPLY} WHERE comment_id = ?`, comment_id).then(function(res){
                                        count_replies = res[0].count;
                                    }).catch(function(err){
                                        console.log(err);
                                    });

                                    await query(`SELECT user_id FROM ${T.COMMENTS} WHERE id = ?`, comment_id).then(async function(res){
                                        if(res.length > 0){
                                            await specific.SetNotify(socket, {
                                                user_id: res[0].user_id,
                                                notified_id: result.insertId,
                                                type: 'preply',
                                            }).catch(function(err){
                                                console.log(err);
                                            });

                                            await specific.ToMention(socket, {
                                                text: text,
                                                user_id: res[0].user_id,
                                                insert_id: result.insertId
                                            }, 'reply').catch(function(err){
                                                console.log(err);
                                            });
                                        }
                                    }).catch(function(err){
                                        console.log(err);
                                    });

                                    x({
                                        S: 200,
                                        HT: res.html,
                                        HC: count_replies
                                    });
                                } else x(ERR);
                            }).catch(function(err){
                                console.log(err);
                            });
                        } else x(ERR);
                    })
                } else x(ERR);
            })
		} else x(ERR);

    });

    socket.on('setIntyping', function(e){
        var profile_id = specific.Filter(e.profile_id),
            typing = specific.Filter(e.typing),
            sockets = specific.getSockets(profile_id);


        if(profile_id != '' && !isNaN(profile_id) && [true, false].indexOf(e.typing) !== -1){
            is_typing = e.typing;
            if(typing == 'true'){
                connection.query(`SELECT COUNT(*) as count FROM ${T.TYPING} WHERE user_id = ? AND profile_id = ?`, [USER.id, profile_id], function(error, result, field){
                    if(error){
                        return console.log(error);
                    }
                    if(result[0].count == 0){
                        connection.query(`INSERT INTO ${T.TYPING} (user_id, profile_id, created_at) VALUES (?, ?, ?)`, [USER.id, profile_id, specific.Time()]);
                    }

                    specific.Data(socket, USER.id, ['username', 'name', 'surname', 'avatar']).then(function(res){
                        var temp = {
                            username: res.username,
                            avatar_s: res.avatar_s
                        };

                        specific.emitChamgesTo(profile_id, 'setOuttyping', {
                            S: 200,
                            DL: !1,
                            PID: USER.id,
                            HTT: specific.Maket(socket, "messages/dot", temp)
                        });

                        if(sockets.length > 0){
                            specific.emitChamgesTo(profile_id, 'setOutttyping', {
                                S: 200,
                                TX: global.TEMP[sockets[0]].word.is_writing,
                                EL: `.content_pnuser[data-id=${USER.id}]`
                            });
                        }
                    }).catch(function(err){
                        console.log(err)
                    });

                })
            } else {
                connection.query(`DELETE FROM ${T.TYPING} WHERE user_id = ? AND profile_id = ?`, [USER.id, profile_id], function(error, result, field){
                    if(error){
                        return console.log(error);
                    }
                    if(result.affectedRows > 0){
                        specific.emitChamgesTo(profile_id, 'setOuttyping', {
                            S: 200,
                            DL: !0,
                            PID: USER.id
                        });

                        if(sockets.length > 0){
                            specific.LastMessage({id: sockets[0]}, USER.id, !0).then(function(res){
                                specific.emitChamgesTo(profile_id, 'setOutttyping', {
                                    S: 200,
                                    TX: res.text,
                                    EL: `.content_pnuser[data-id=${USER.id}]`
                                });
                            }).catch(function(err){
                                console.log(err);
                            })
                        }
                        
                    }
                });
            }
        }

    });

    socket.on('setInmseen', function(e){
        connection.query(`SELECT profile_id FROM ${T.MESSAGE} WHERE id = ?`, e.message_id, function(error, result, field){
            if(error){
                return console.log(error);
            }
            if(result.length > 0){
                if(result[0].profile_id == USER.id){
                    connection.query(`UPDATE ${T.MESSAGE} SET seen = 1 WHERE id = ?`, e.message_id);
                }
            }
        })
    });

    PAGE == 'messages' && (global.TEMP[socket.id].interval = setInterval(function(){
        var profile_id = specific.Filter(socket.handshake.query.profile_id);

        if(profile_id != '' && !isNaN(profile_id)){
            var qury = ``;
            if(is_typing){
                qury = ` AND user_id <> ${profile_id}`;
            }
            connection.query(`SELECT * FROM ${T.TYPING} WHERE profile_id = ?${qury}`, USER.id, async function(error, result, field){
                if(error){
                    return console.log(error);
                }
                if(result.length > 0){
                    var typings = [],
                        delete_dot = !1;
                    await forEachAsync(result, function(item, index, arr){
                        var done = this.async();
                        connection.query(`DELETE FROM ${T.TYPING} WHERE profile_id = ?`, item.profile_id, async function(error, result, field){
                            if(error){
                                done();
                                return console.log(error);
                            }
                            if(result.affectedRows > 0){
                                if(profile_id != '' && !isNaN(profile_id)){
                                    if(item.user_id == profile_id){
                                        delete_dot = !0;
                                    }
                                }
                                await specific.LastMessage(socket, item.user_id).then(function(res){
                                    typings.push({
                                        TX: res.text,
                                        EL: `.content_pnuser[data-id=${item.user_id}]`
                                    });
                                }).catch(function(err){
                                    console.log(err);
                                })
                                done();
                            } else done();
                        })
                    }).catch(function(err){});
                    
                    specific.emitChamgesTo(USER.id, 'setOutdMytyping', {
                        S: 200,
                        DL: delete_dot,
                        TPS: typings
                    });
                }
            })
        }
    }, 20000));

    global.TEMP.app.post('/create-post', cors(), function(request, response, next){
        formidable({ multiples: true }).parse(request, async function(err, fields, files){
            if(fields.socket_id !== undefined){
                var socket = {
                        id: specific.Filter(fields.socket_id)
                    },
                    TEMP = global.TEMP[socket.id],
                    USER = TEMP.user,
                    WORD = TEMP.word,
                    SETTINGS = specific.Settings(),
                    blocked_inusers = TEMP.blocked_inusers;
                if(specific.Publisher(socket) == !0){
                    var empty = [],
                        error = [],
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

                                                fetch(image).then(function(res){
                                                    if(['image/jpeg', 'image/png'].indexOf(res.headers.get('content-type')) == -1){
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
                            
                            if(['normal', 'video'].indexOf(type) === -1){
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
                                if(SETTINGS.approve_posts == 'on' && specific.Admin(socket) == false){
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
                                                    content_text = null,
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

                                                if(['text', 'image', 'carousel'].indexOf(item[0]) !== -1){
                                                    if(item[0] == 'text'){
                                                        if(item[3] != ''){
                                                            entry_source = item[3];
                                                        }
                                                    } else if(item[0] == 'image'){
                                                        if(item[4] != ''){
                                                            entry_source = item[4];
                                                        }
                                                    } else {
                                                        if(item[5] != ''){
                                                            entry_source = item[5];
                                                        }
                                                    }
                                                }

                                                if(entry_source != null){
                                                    entry_source = entry_source.replace(st_regex, '');
                                                }

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
                                                        oembed.extract(item[2], {omit_script: !0}).then((json) => {
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
                                                if(SETTINGS.approve_posts == 'off' || specific.Admin(socket) == true){
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

                                                response.send({
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
                                                response.send(ERR);
                                            }

                                        }
                                    }).catch(function(err){
                                        response.send(ERR);
                                    })
                                    
                                }

                            } else {
                                response.send({
                                    S: 400,
                                    E: error
                                });
                            }

                        } else {
                            response.send({
                                S: 400,
                                E: empty
                            });
                        }
                    }
                } else {
                    response.send(ERR);
                }
            } else {
                response.send(ERR);
            }
        });
    });








    global.TEMP.app.post('/edit-post', cors(), function(request, response, next){
        formidable({ multiples: true }).parse(request, async function(err, fields, files){
            if(fields.socket_id !== undefined){
                var socket = {
                        id: specific.Filter(fields.socket_id)
                    },
                    TEMP = global.TEMP[socket.id],
                    USER = TEMP.user,
                    WORD = TEMP.word,
                    SETTINGS = specific.Settings(),
                    blocked_inusers = TEMP.blocked_inusers;
                if(specific.Publisher(socket) == !0){
                    var ids = [],
                        empty = [],
                        error = [],
                        post_id = specific.Filter(fields.post_id),
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
                        tags = specific.Filter(fields.tags);

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

                    if(post_id != ''){
                        connection.query(`SELECT slug, thumbnail, COUNT(*) as count FROM ${T.POST} WHERE id = ? AND user_id = ?`, [post_id, USER.id], async function(qerror, result, field){
                            if(qerror){
                                return response.send(ERR);
                            }
                            var post = result[0];

                            if(post.count > 0){
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
                                    
                                    await forEachAsync(entries, function(item, index, arr){;
                                        if(item[item.length-1] != null){
                                            ids.push(parseInt(item[item.length-1]));
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
                                                        fetch(image).then(function(res){
                                                            if(['image/jpeg', 'image/png'].indexOf(res.headers.get('content-type')) == -1){
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
            
                                        if(item[0] == 'embed'){
                                            if(!specific.ValidateUrl(item[2])){
                                                error.push({
                                                    EL: index,
                                                    CS: '.item_url',
                                                    TX: `*${WORD.enter_a_valid_url}`
                                                })
                                            }
                                        }

                                        if(!Boolean(item[3])){
                                            if(item[0] == 'video'){
                                                if(!item[2].match(/^(?:http(?:s)?:\/\/)?(?:[a-z0-9.]+\.)?(?:youtu\.be|youtube\.com)\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/)([^\?&\"'>]+)/) && !item[2].match(/^(?:http(?:s)?:\/\/)?(?:[a-z0-9.]+\.)?vimeo\.com\/([0-9]+)$/) && !item[2].match(/^.+dailymotion.com\/(video|hub)\/([^_]+)[^#]*(#video=([^_&]+))?/)){
                                                    error.push({
                                                        EL: index,
                                                        CS: '.item-input',
                                                        TX: `*${WORD.enter_a_valid_url}`
                                                    })
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
                                        }
                                    }).catch(function(err){});
                                    
                                    if(['normal', 'video'].indexOf(type) === -1){
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
                                        var desthumb = Object.keys(files).indexOf('thumbnail');
                                        if(Object.keys(thumbnail).length > 0 && (desthumb !== -1 || (desthumb === -1 && thumbnail.indexOf(info.domain)) === -1)){
                                            var thmbFn = function(){
                                                fs.unlink(`uploads/posts/${post.thumbnail}-s.jpeg`, function(err) {
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
                                                fs.unlink(`uploads/posts/${post.thumbnail}-b.jpeg`, function(err) {
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
                                            }
                                            if(desthumb !== -1){
                                                await specific.UploadImage({
                                                    name: files.thumbnail.originalFilename,
                                                    tmp_name: files.thumbnail.filepath,
                                                    size: files.thumbnail.size,
                                                    type: files.thumbnail.mimetype,
                                                    folder: 'posts',
                                                }).then(function(res){
                                                    thumbnail = res;
                                                    thmbFn();
                                                }).catch(function(err){
                                                    arr_trues.push(!1);
                                                });
                                            } else {
                                                await specific.UploadThumbnail({
                                                    media: thumbnail,
                                                    folder: 'posts'
                                                }).then(function(res){
                                                    thumbnail = res;
                                                    thmbFn();
                                                }).catch(function(err){
                                                    arr_trues.push(!1);
                                                });
                                            }
                                        } else {
                                            thumbnail = {
                                                return: true,
								                image: post.thumbnail
                                            };
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
        
                                        if(thumbnail.return){
                                            var st_regex = '/<(?:script|style)[^>]*>(.*?)<\/(?:script|style)>/is',
                                                updated_at = created_at = specific.Time(),
                                                slug = post.slug;

                                            await query(`UPDATE ${T.POST} SET category_id = ?, title = ?, description = ?, thumbnail = ?, post_sources = ?, thumb_sources = ?, type = ?, updated_at = ? WHERE id = ?`, [category, title, description, thumbnail.image, post_sources, thumb_sources, type, updated_at, post_id]).then(async function(qres){
                                                if(qres.affectedRows > 0){
                                                    var arr_trues = [],
                                                        del_entries = [],
                                                        entries_ids = [];

                                                    await query(`SELECT id FROM ${T.ENTRY} WHERE post_id = ?`, post_id).then(async function(res){
                                                        if(res.length > 0){
                                                            await forEachAsync(res, function(item, index, arr) {
                                                                entries_ids.push(item.id);
                                                            }).catch(function(err){});

                                                            del_entries = specific.ArrayDiff(entries_ids, ids);
                                                        } else {
                                                            arr_trues.push(!1);
                                                        }
                                                    }).catch(function(err){
                                                        arr_trues.push(!1);
                                                    });
        
                                                    await forEachAsync(entries, function(item, index, arr){
                                                        var done = this.async(),
                                                            entry_id = item[item.length-1],
                                                            entry_id = entry_id == null ? entry_id : parseInt(entry_id),
                                                            entry_title = null,
                                                            entry_source = null,
                                                            entFn = async function(entry_exists = [], content_frame = null, content_text = null, carousel_accept = !1){

                                                                if(['text', 'embed'].indexOf(item[0]) !== -1){

                                                                    if(entry_id != null && entries_ids.indexOf(entry_id) !== -1){
                                                                        await query(`UPDATE ${T.ENTRY} SET title = ?, body = ?, frame = ?, esource = ?, eorder = ?, updated_at = ? WHERE id = ?`, [entry_title, content_text, content_frame, entry_source, index, updated_at, entry_id]).then(function(res){
                                                                            if(res.affectedRows > 0){
                                                                                arr_trues.push(!0);
                                                                            } else {
                                                                                arr_trues.push(!1);
                                                                            }
                                                                            done();
                                                                        }).catch(function(err){
                                                                            arr_trues.push(!1);
                                                                            done();
                                                                        })
                                                                    } else {
                                                                        await query(`INSERT INTO ${T.ENTRY} (post_id, type, title, body, frame, esource, eorder, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)`, [post_id, item[0], entry_title, content_text, content_frame, entry_source, index, created_at]).then(function(res){
                                                                            if(res.insertId > 0){
                                                                                arr_trues.push(!0);
                                                                            } else {
                                                                                arr_trues.push(!1);
                                                                            }
                                                                            done();
                                                                        }).catch(function(err){
                                                                            arr_trues.push(!1);
                                                                            done();
                                                                        })
                                                                    }
                                                                } else {
                                                                    if((item[0] == 'carousel' && carousel_accept) || item[0] != 'carousel'){
                                                                        if(entry_id != null && entries_ids.indexOf(entry_id) !== -1){
                                                                            await query(`UPDATE ${T.ENTRY} SET title = ?, esource = ?, eorder = ?, updated_at = ? WHERE id = ?`, [entry_title, entry_source, index, updated_at, entry_id]).then(async function(res){
                                                                                // Aqui esta el error, entra pero parece que se duplican las imagenes ¿? pq?
                                                                                if(res.affectedRows > 0){
                                                                                    if(item[0] == 'carousel'){
                                                                                        await query(`UPDATE ${T.ENTRY} SET frame = ? WHERE id = ?`, [content_frame, entry_id]).then(async function(res){
                                                                                            if(res.affectedRows > 0){
                                                                                                var carousel = JSON.parse(entry_exists.frame);
                                                                                                await forEachAsync(carousel, function(m, i, a){
                                                                                                    var done3 = this.async();
                                                                                                    fs.unlink(`uploads/entries/${m.image}`, function(err){
                                                                                                        done3();
                                                                                                    });
                                                                                                    if(i == (a.length-1)){
                                                                                                        done();
                                                                                                    } 
                                                                                                }).catch(function(err){});

                                                                                                arr_trues.push(!0);
                                                                                            } else {
                                                                                                arr_trues.push(!1);
                                                                                                done();
                                                                                            }
                                                                                        }).catch(function(err){
                                                                                            arr_trues.push(!1);
                                                                                            done();
                                                                                        })
                                                                                    } else if(item[0] == 'image' && index != entry_exists.eorder){
                                                                                        var ext_frame = "uploads/entries/",
                                                                                            old_frame = entry_exists.frame,
                                                                                            new_frame = _.replace(old_frame, `${post_id}-${entry_exists.eorder}-`, `${post_id}-${index}-`);

                                                                                        await query(`UPDATE ${T.ENTRY} SET frame = ? WHERE id = ?`, [new_frame, entry_id]).then(function(res){
                                                                                            if(res.affectedRows > 0){
                                                                                                fs.rename(`${ext_frame}${old_frame}`, `${ext_frame}${new_frame}`, function() {
                                                                                                    done();
                                                                                                });

                                                                                                arr_trues.push(!0);
                                                                                            } else {
                                                                                                arr_trues.push(!1);
                                                                                                done();
                                                                                            }
                                                                                        }).catch(function(err){
                                                                                            arr_trues.push(!1);
                                                                                            done();
                                                                                        })
                                                                                    } else {
                                                                                        done();
                                                                                    }

                                                                                    arr_trues.push(!0);
                                                                                } else {
                                                                                    arr_trues.push(!1);
                                                                                    done();
                                                                                }
                                                                            }).catch(function(err){
                                                                                arr_trues.push(!1);
                                                                                done();
                                                                            })
                                                                        } else done();
                                                                    } else done();
                                                                }
                                                            },
                                                            goentFn = async function(){

                                                                var entry_exists = [];
                                                                
                                                                if(entry_id != null){
                                                                    await query(`SELECT eorder, frame FROM ${T.ENTRY} WHERE id = ? AND post_id = ?`, [entry_id, post_id]).then(function(res){
                                                                        entry_exists = res[0];
                                                                    }).catch(function(err){
                                                                        arr_trues.push(!1);
                                                                        done();
                                                                    });
                                                                }

                                                                var insrenFn = async function(content_frame = null, carousel_accept = !1){
                                                                        await query(`INSERT INTO ${T.ENTRY} (post_id, type, title, frame, esource, eorder, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)`, [post_id, item[0], entry_title, content_frame, entry_source, index, created_at]).then(function(res){
                                                                            if(res.insertId > 0){
                                                                                arr_trues.push(!0);
                                                                            } else {
                                                                                arr_trues.push(!1);
                                                                            }
                                                                        }).catch(function(err){
                                                                            arr_trues.push(!1);
                                                                        })
                                                                        entFn(entry_exists, content_frame, null, carousel_accept);
                                                                    };

                                                                if(item[1] != ''){
                                                                    entry_title = item[1];
                                                                }

                                                                if(['text', 'image', 'carousel'].indexOf(item[0]) !== -1){
                                                                    if(item[0] == 'text'){
                                                                        if(item[3] != ''){
                                                                            entry_source = item[3];
                                                                        }
                                                                    } else if(item[0] == 'image'){
                                                                        if(item[4] != ''){
                                                                            entry_source = item[4];
                                                                        }
                                                                    } else {
                                                                        if(item[5] != ''){
                                                                            entry_source = item[5];
                                                                        }
                                                                    }
                                                                }
                
                                                                if(entry_source != null){
                                                                    entry_source = entry_source.replace(st_regex, '');
                                                                }
                
                                                                if(item[0] == 'text'){
                                                                    entFn(entry_exists, null, item[2].replace(st_regex, ''));
                                                                } else {
                                                                    if(item[0] == 'image'){
                                                                        if(item[2].indexOf(entry_exists.frame) === -1){
                                                                            var thumbnail_id = `thumbnail_${index}`,
                                                                                updtenFn = async function(content_frame = null){
                                                                                    if(entry_id != null && entries_ids.indexOf(entry_id) !== -1){
                                                                                        await query(`SELECT frame FROM ${T.ENTRY} WHERE id = ? AND post_id = ?`, [entry_id, post_id]).then(async function(res){
                                                                                            fs.unlink(`uploads/entries/${res[0].frame}`, function(err) {
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

                                                                                            await query(`UPDATE ${T.ENTRY} SET frame = ? WHERE id = ?`, [content_frame, entry_id]).then(function(res){
                                                                                                if(res.affectedRows > 0){
                                                                                                    arr_trues.push(!0);
                                                                                                } else {
                                                                                                    arr_trues.push(!1);
                                                                                                }
                                                                                                done();
                                                                                            }).catch(function(err){
                                                                                                arr_trues.push(!1);
                                                                                                done();
                                                                                            });
                                                                                        }).catch(function(err){
                                                                                            arr_trues.push(!1);
                                                                                            done();
                                                                                        });
                                                                                    } else {
                                                                                        insrenFn(content_frame);
                                                                                    }
                                                                                };
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
                                                                                    updtenFn(res.image_ext);
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
                                                                                    updtenFn(res.image_ext);
                                                                                }).catch(function(err){
                                                                                    arr_trues.push(!1);
                                                                                    done();
                                                                                });
                                                                            }
                                                                        } else {
                                                                            entFn(entry_exists, null);
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
                                                                                        carousel.push({
                                                                                            image: res.image_ext,
                                                                                            caption: captions[i]
                                                                                        });
                                                                                    }
                                                                                    if(i == (item[2]-1)){
                                                                                        if(entry_id == null){
                                                                                            insrenFn(JSON.stringify(carousel), !0);
                                                                                        } else {
                                                                                            entFn(entry_exists, JSON.stringify(carousel), null, !0);
                                                                                        }
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
                                                                                        carousel.push({
                                                                                            image: res.image_ext,
                                                                                            caption: captions[i]
                                                                                        });
                                                                                    }
                                                                                    if(i == (item[2]-1)){
                                                                                        if(entry_id == null){
                                                                                            insrenFn(JSON.stringify(carousel), !0);
                                                                                        } else {
                                                                                            entFn(entry_exists, JSON.stringify(carousel), null, !0);
                                                                                        }
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
                                                                    } else {
                                                                        if(item[0] == 'embed'){
                                                                            var attrs = specific.Filter(fields[`embed_${index}`]),
                                                                                frame = specific.MaketFrame(item[2], attrs);
                    
                                                                            frame = {
                                                                                url: item[2],
                                                                                attrs: frame.attrs
                                                                            };
                                    
                                                                            entFn(entry_exists, JSON.stringify(frame));
                                                                        } else if(entry_id == null){
                                                                            if(['tweet', 'soundcloud', 'spotify', 'tiktok'].indexOf(item[0]) !== -1){
                                                                                oembed.extract(item[2], {omit_script: !0}).then((json) => {
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
                                                                                            insrenFn(content_frame);
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
                                                                            } else {
                                                                                insrenFn(item[2]);
                                                                            }
                                                                        } else {
                                                                            entFn(entry_exists);
                                                                        }
                                                                    }
                                                                }
                                                            };

                                                        goentFn();
                                                    }).catch(function(err){});

                                                    if(del_entries.length > 0){
                                                        await forEachAsync(del_entries, function(item, index, arr){
                                                            var done = this.async();
                                                            connection.query(`SELECT type, frame FROM ${T.ENTRY} WHERE id = ? AND post_id = ?`, [item, post_id], function(error, result, field){
                                                                if(error){
                                                                    done();
                                                                    return arr_trues.push(!1);
                                                                }
                                                                if(result.length > 0){
                                                                    var entry = result[0];
                                                                    connection.query(`DELETE FROM ${T.ENTRY} WHERE id = ? AND post_id = ?`, [item, post_id], async function(error, result, field){
                                                                        if(error){
                                                                            done();
                                                                            return arr_trues.push(!1);
                                                                        }
                                                                        if(result.affectedRows > 0){
                                                                            if(entry.type == 'image'){
                                                                                fs.unlink(`uploads/entries/${entry.frame}`, function(err) {
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
                                                                                done();
                                                                            } else if(entry.type == 'carousel'){
                                                                                var carousel = JSON.parse(entry.frame);

                                                                                await forEachAsync(carousel, function(m, i, a){
                                                                                    var done2 = this.async();
                                                                                    fs.unlink(`uploads/entries/${m.image}`, function(err) {
                                                                                        if(err && err.code == 'ENOENT') {
                                                                                            // file doens't exist
                                                                                            console.info("File doesn't exist, won't remove it.");
                                                                                        } else if (err) {
                                                                                            // other errors, e.g. maybe we don't have enough permission
                                                                                            console.error("Error occurred while trying to remove file");
                                                                                        } else {
                                                                                            console.info(`removed`);
                                                                                        }
                                                                                        done2();
                                                                                    });
                                                                                    if(i == (a.length-1)){
                                                                                        done();
                                                                                    }
                                                                                }).catch(function(err){});
                                                                            } else done();
                                                                        } else done();
                                                                    })
                                                                } else done();
                                                            })

                                                        }).catch(function(err){});
                                                    }

                                                    
                                                    await query(`SELECT name FROM ${T.LABEL} l WHERE (SELECT label_id FROM ${T.TAG} WHERE post_id = ? AND label_id = l.id) = id`, post_id).then(async function(res){
                                                        if(res.length > 0){
                                                            var tags_names = [];

                                                            await forEachAsync(res, function(item, index, arr) {
                                                                tags_names.push(item.name);
                                                            }).catch(function(err){});

                                                            var del_tags = specific.ArrayDiff(tags_names, tags),
                                                                add_tags = specific.ArrayDiff(tags, tags_names);

                                                                if(add_tags.length > 0){
                                                                    await forEachAsync(add_tags, function(item, index, arr){
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
                                                                }

                                                                if(del_tags.length > 0){
                                                                    await forEachAsync(del_tags, function(item, index, arr){
                                                                        var done = this.async();
                                                                        connection.query(`DELETE FROM ${T.TAG} WHERE post_id = ? AND (SELECT id FROM ${T.LABEL} WHERE name = ?) = label_id`, [post_id, item], function(error, result, field){
                                                                            if(error){
                                                                                done();
                                                                                return arr_trues.push(!1);
                                                                            }
                                                                            if(result.affectedRows > 0){
                                                                                arr_trues.push(!0);
                                                                            } else {
                                                                                arr_trues.push(!1);
                                                                            }
                                                                            done();
                                                                        })
                                                                    }).catch(function(err){});
                                                                }
                                                        } else {
                                                            arr_trues.push(!1);
                                                        }
                                                    }).catch(function(err){
                                                        arr_trues.push(!1);
                                                    });

                                                    await query(`SELECT recommended_id FROM ${T.RECOBO} WHERE post_id = ?`, post_id).then(async function(res){
                                                        if(res.length > 0){
                                                            var recobo_ids = [];

                                                            await forEachAsync(res, function(item, index, arr) {
                                                                recobo_ids.push(item.recommended_id);
                                                            }).catch(function(err){});

                                                            var add_recobo = specific.ArrayDiff(recobo, recobo_ids),
                                                                del_recobo = specific.ArrayDiff(recobo_ids, recobo);

                                                            if(add_recobo.length > 0){
                                                                await forEachAsync(recobo, function(item, index, arr){
                                                                    var done = this.async();
                                                                    connection.query(`SELECT COUNT(*) as count FROM ${T.POST} WHERE id = ? AND status = "approved"`, item, function(error, result, field){
                                                                        if(error){
                                                                            done();
                                                                            return arr_trues.push(!1);
                                                                        }
                                                                        if(result[0].count > 0){
                                                                            connection.query(`INSERT INTO ${T.RECOBO} (post_id, recommended_id, created_at) VALUES (?, ?, ?)`, [post_id, item, specific.Time()], function(error, result, field){
                                                                                if(error){
                                                                                    return arr_trues.push(!1);
                                                                                }
                                                                                done();
                                                                            })
                                                                        } else done();
                                                                    })
                                                                }).catch(function(err){});
                                                            }

                                                            if(del_recobo.length > 0){
                                                                await forEachAsync(del_recobo, function(item, index, arr){
                                                                    var done = this.async();
                                                                    connection.query(`DELETE FROM ${T.RECOBO} WHERE post_id = ? AND recommended_id = ?`, [post_id, item], function(error, result, field){
                                                                        if(error){
                                                                            done();
                                                                            return arr_trues.push(!1);
                                                                        }
                                                                        if(result.affectedRows > 0){
                                                                            arr_trues.push(!0);
                                                                        } else {
                                                                            arr_trues.push(!1);
                                                                        };
                                                                        done();
                                                                    })
                                                                }).catch(function(err){});
                                                            }
                            
                                                            await forEachAsync(recobo, function(item, index, arr){
                                                                var done = this.async();

                                                                connection.query(`UPDATE ${T.RECOBO} SET rorder = ? WHERE recommended_id = ?`, [index, item], function(error, result, field){
                                                                    if(error){
                                                                        done();
                                                                        return arr_trues.push(!1);
                                                                    }
                                                                    if(result.affectedRows > 0){
                                                                        arr_trues.push(!0);
                                                                    } else {
                                                                        arr_trues.push(!1);
                                                                    };
                                                                    done();
                                                                })

                                                            }).catch(function(err){});

                                                        }
                                                    }).catch(function(err){
                                                        arr_trues.push(!1);
                                                    });


                                                    await query(`SELECT user_id FROM ${T.COLLABORATOR} WHERE post_id = ?`, [post_id]).then(async function(res){
                                                        if(res.length > 0){
                                                            var collaborators_ids = [];

                                                            await forEachAsync(res, function(item, index, arr) {
                                                                collaborators_ids.push(item.user_id);
                                                            }).catch(function(err){});


                                                            var add_collaborators = specific.ArrayDiff(collaborators, collaborators_ids),
                                                                del_collaborators = specific.ArrayDiff(collaborators_ids, collaborators);

                                                            if(add_collaborators.length > 0){
                                                                await forEachAsync(add_collaborators, function(item, index, arr){
                                                                    var done = this.async();
                                                                    connection.query(`SELECT about, facebook, twitter, instagram, main_sonet, COUNT(*) as count FROM ${T.USER} WHERE id = ? AND id NOT IN (${blocked_inusers}) AND status = "active"`, item, function(error, result, field){
                                                                        if(error){
                                                                            done();
                                                                            return arr_trues.push(!1);
                                                                        }
                                                                        if(result[0].count > 0 && result[0].about != '' && result[0][result[0].main_sonet] != ''){
                                                                            connection.query(`INSERT INTO ${T.COLLABORATOR} (user_id, post_id, created_at) VALUES (?, ?, ?)`, [item, post_id, specific.Time()], async function(error, result, field){
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
                                                                            });
                                                                        } else done();
                                                                    });
                                                                }).catch(function(err){});
                                                            }

                                                            if(del_collaborators.length > 0){
                                                                await forEachAsync(del_collaborators, function(item, index, arr){
                                                                    var done = this.async();
                                                                    
                                                                    connection.query(`SELECT id FROM ${T.COLLABORATOR} WHERE user_id = ? AND post_id = ?`, [item, post_id], function(error, result, field){
                                                                        if(error){
                                                                            done();
                                                                            return arr_trues.push(!1);
                                                                        }
                                                                        if(result.length > 0){
                                                                            var collab_id = result[0].id;
                                                                            connection.query(`DELETE FROM ${T.NOTIFICATION} WHERE notified_id = ? AND type = "n_collab"`, collab_id, function(error, result, field){
                                                                                if(error){
                                                                                    done();
                                                                                    return arr_trues.push(!1);
                                                                                }
                                                                                if(result.affectedRows > 0){
                                                                                    connection.query(`DELETE FROM ${T.COLLABORATOR} WHERE id = ?`, collab_id, function(error, result, field){
                                                                                        if(error){
                                                                                            done();
                                                                                            return arr_trues.push(!1);
                                                                                        }
                                                                                        if(result.affectedRows > 0){
                                                                                            arr_trues.push(!0);
                                                                                        } else {
                                                                                            arr_trues.push(!1);
                                                                                        }
                                                                                        done();
                                                                                    })
                                                                                } else done();
                                                                            })
                                                                        } else done();
                                                                    })

                                                                }).catch(function(err){});
                                                            }
                            
                                                            await forEachAsync(add_collaborators, function(item, index, arr){
                                                                var done = this.async();
                                                                connection.query(`UPDATE ${T.COLLABORATOR} SET aorder = ? WHERE user_id = ?`, [index, item], function(error, result, field){
                                                                    if(error){
                                                                        done();
                                                                        return arr_trues.push(!1);
                                                                    }
                                                                    if(result.affectedRows > 0){
                                                                        arr_trues.push(!0);
                                                                    } else {
                                                                        arr_trues.push(!1);
                                                                    }
                                                                    done();
                                                                })
                                                            }).catch(function(err){})
                                                        }
                                                    }).catch(function(err){
                                                        arr_trues.push(!1);
                                                    });
        
                                                    if(arr_trues.indexOf(!1) === -1){
                                                        response.send({
                                                            S: 200,
                                                            LK: specific.Url(slug)
                                                        });
                                                    }
                                                    
                                                }
                                            }).catch(function(err){
                                                response.send(ERR);
                                            })
                                            
                                        }
        
                                    } else {
                                        response.send({
                                            S: 400,
                                            E: error
                                        });
                                    }
        
                                } else {
                                    response.send({
                                        S: 400,
                                        E: empty
                                    });
                                }

                                
                            }
                        })

                        
                    }

                } else {
                    response.send(ERR);
                }
            } else {
                response.send(ERR);
            }
        });

    });
    
    global.TEMP.app.post('/messages/send', cors(), function(request, response, next){
        formidable({ multiples: true }).parse(request, async function(err, fields, files){
            if(fields.socket_id !== undefined){
                var socket = {
                        id: specific.Filter(fields.socket_id)
                    },
                    TEMP = global.TEMP[socket.id],
                    USER = TEMP.user,
                    WORD = TEMP.word,
                    blocked_arrusers = TEMP.blocked_arrusers;

                if(TEMP.loggedin == !0){
                    var profile_id = specific.Filter(fields.profile_id),
                        answered_id = specific.Filter(fields.answered_id),
                        count_files = specific.Filter(fields.count_files),
                        text = specific.Filter(fields.text),
                        type = specific.Filter(fields.type);

                    if(profile_id != '' && !isNaN(profile_id) && (text.trim() != '' || count_files > 0) && !isNaN(count_files) && ['text', 'file', 'image'].indexOf(type) !== -1 && blocked_arrusers.indexOf(profile_id) === -1){

                        connection.query(`SELECT *, COUNT(*) as count FROM ${T.USER} WHERE id = ?`, profile_id, function(error, result, field){
                            if(error){
                                return response.send(ERR);
                            }
                            if(result[0].count > 0){
                                specific.Data(socket, result, 3).then(function(res){
                                    if(res.shows.messages == 'on' && USER.shows.messages == 'on'){
                                        var temp = {
                                                avatar_s: USER.avatar_s,
                                                username: USER.username,
                                                outimages: '',
                                                inimages: '',
                                                outfiles: '',
                                                infiles: '',
                                                type: 'normal',
                                                ans_type: 'text',
                                                deleted_fuser: 0,
                                                deleted_fprofile: 0
                                            },
                                            created_at = updated_at = specific.Time();

                                        temp.messafi = !1;
                                        temp.has_image = !1;
                                        temp.has_file = !1;
                        
                                        if(count_files > 0){
                                            if(text.trim() == ''){
                                                text = null;
                                            }
                                        }

                                        connection.query(`SELECT *, COUNT(*) as count FROM ${T.CHAT} WHERE (user_id = ? AND profile_id = ?) OR (profile_id = ? AND user_id = ?)`, [USER.id, profile_id, USER.id, profile_id], async function(error, result, field){
                                            if(error){
                                                return response.send(ERR);
                                            }
                                            if(result.length > 0){
                                                var chat_id = result[0].id,
                                                    send_notify = !0;
                                                if(result[0].count > 0){
                                                    await query(`UPDATE ${T.CHAT} SET updated_at = ? WHERE id = ?`, [updated_at, chat_id]).then(function(res){
                                                        if(res.affectedRows > 0){
                                                            var socks = specific.getSockets(profile_id);
                                                            if(socks.length > 0){
                                                                var last_text = text,
                                                                    seen = 1;
                                                                if(count_files > 0){
                                                                    last_text = WORD.deputy_file_deleted;
                                                                }
                                                                forEach(socks, function(item, index, arr){
                                                                    if(USER.id == global.TEMP[item].profile_id){
                                                                        return seen = 0, !1;
                                                                    }
                                                                });
                                                                specific.emitChamgesTo(profile_id, 'setOutupchat', {
                                                                    S: 200,
                                                                    TX: last_text,
                                                                    CA: specific.DateString(socket, created_at),
                                                                    LU: seen,
                                                                    EL: `.content_pnuser[data-id=${USER.id}]`
                                                                });

                                                            }
                                                        }
                                                    }).catch(function(err){
                                                        console.log(err);
                                                    });
                                                } else {
                                                    await query(`INSERT INTO ${T.CHAT} (user_id, profile_id, updated_at, created_at) VALUES (?, ?, ?, ?)`, [USER.id, profile_id, updated_at, created_at]).then(async function(res){
                                                        var temp2 = {
                                                            id: res.insertId,
                                                            last_created_at: specific.DateString(socket, created_at),
                                                            last_text: text,
                                                            profile_id: profile_id,
                                                            last_unseen: !0,
                                                            user: USER.user,
                                                            role: USER.role,
                                                            user_id: USER.id,
                                                            user_deleted: !0,
                                                            r_messages: R.r_messages
                                                        };
                                                        
                                                        if(count_files > 0){
                                                            temp2.last_text = WORD.deputy_file_deleted;
                                                        }
                                                        
                                                        if(USER.status == 'deleted'){
                                                            temp2.user_deleted = !1;
                                                            temp2.username = WORD.user;
                                                            temp2.avatar_s = specific.Url('/themes/default/images/users/default-holder-s.jpeg');
                                                        } else {
                                                            temp2.username = USER.username;
                                                            temp2.avatar_s = USER.avatar_s;
                                                        }
                                                        
                                                        specific.emitChamgesTo(profile_id, 'setOutichat', {
                                                            S: 200,
                                                            HTU: specific.Maket(socket, "messages/user", temp2),
                                                            LCU: updated_at,
                                                            UID: res.insertId,
                                                            TX: temp2.last_text,
                                                            CA: temp.last_created_at,
                                                            EL: `.content_pnuser[data-id=${USER.id}]`
                                                        });

                                                        specific.emitChamgesTo(USER.id, 'setOutochat', {
                                                            S: 200,
                                                            HTU: specific.Maket(socket, "messages/user", temp),
                                                            LCU: updated_at,
                                                            UID: res.insertId,
                                                            TX: temp2.last_text,
                                                            CA: temp.last_created_at,
                                                            EL: `.content_pnuser[data-id=${profile_id}]`
                                                        }, socket.id);

                                                        chat_id = res.insertId;
                                                    }).catch(function(err){
                                                        console.log(err);
                                                    });
                                                }

                                                if(chat_id != null){
                                                    var message_exists = 0;
                                                    await query(`SELECT COUNT(*) as count FROM ${T.MESSAGE} WHERE chat_id = ? AND ((user_id = ? AND deleted_fuser = 0) OR (profile_id = ? AND deleted_fprofile = 0))`, [chat_id, USER.id, USER.id]).then(function(res){
                                                        message_exists = res[0].count;
                                                    }).catch(function(err){
                                                        console.log(err);
                                                    });
                                                    
                                                    connection.query(`INSERT INTO ${T.MESSAGE} (chat_id, user_id, profile_id, text, created_at) VALUES (?, ?, ?, ?, ?)`, [chat_id, USER.id, profile_id, text, created_at], async function(error, result, field){
                                                        if(error){
                                                            return console.log(err);
                                                        }
                                                        if(result.insertId > 0){
                                                            var insert_id = result.insertId,
                                                                pass = !0;

                                                            await query(`DELETE FROM ${T.TYPING} WHERE user_id = ? AND profile_id = ?`, [USER.id, profile_id]).catch(function(err){
                                                                console.log(err);
                                                            });
                                                            
                                                            if(count_files > 0){
                                                                var fils = [],
                                                                    arr_files = Array(parseInt(count_files)).fill(0);
                                                                
                                                                temp.messafi = !0;
                                                                await forEachAsync(arr_files, function(item, index, arr){
                                                                    var done = this.async(),
                                                                        messafi = `file_${index}`;
                                                                    if(Object.keys(files).indexOf(`file_${index}`) !== -1){
                                                                        var mess = files[messafi];
                                                                        specific.UploadMessagefi({
                                                                            name: mess.originalFilename,
                                                                            tmp_name: mess.filepath,
                                                                            size: mess.size,
                                                                            type: mess.mimetype,
                                                                            message_id: insert_id,
                                                                        }).then(function(res){
                                                                            if(res.return){
                                                                                fils.push(`('${mess.originalFilename}', ${insert_id}, '${res.file}', ${mess.size}, ${created_at})`);
                                                                                done();
                                                                            } else done();
                                                                        }).catch(async function(err){
                                                                            if(text == null){
                                                                                await query(`DELETE FROM ${T.MESSAGE} WHERE id = ?`, insert_id).then(function(res){
                                                                                    if(res.affectedRows > 0){
                                                                                        pass = !1;
                                                                                    }
                                                                                }).catch(function(err) {
                                                                                    console.log(err)
                                                                                })
                                                                            }
                                                                            done();
                                                                        });
                                                                    }
                                                                }).catch(function(err){});

                                                                if(fils.length > 0){
                                                                    await query(`INSERT INTO ${T.MESSAFI} (name, message_id, file, size, created_at) VALUES ${fils.join(',')}`).catch(function(err){
                                                                        console.log(err);
                                                                    });
                                                                }
                                                            }

                                                            if(pass){
                                                                if(answered_id != '' && !isNaN(answered_id)){
                                                                    await query(`SELECT COUNT(*) as count FROM ${T.MESSAGE} WHERE id = ? AND chat_id = ?`, [answered_id, chat_id]).then(async function(res1){
                                                                        if(res1.length > 0){
                                                                            await query(`SELECT COUNT(*) as count FROM ${T.MESSAFI} WHERE id = ?`, answered_id).then(async function(res2){
                                                                                if(res2.length > 0){
                                                                                    if((type == 'text' && res1[0].count > 0) || (['file', 'image'].indexOf(type) !== -1 && res2[0].count > 0)){
                                                                                        await query(`SELECT COUNT(*) as count FROM ${T.MESSAAN} WHERE message_id = ? AND answered_id = ? AND type = ?`, [insert_id, answered_id, type]).then(async function(res){
                                                                                            if(res[0].count == 0){
                                                                                                await query(`INSERT INTO ${T.MESSAAN} (message_id, answered_id, type, created_at) VALUES (?, ?, ?, ?)`, [insert_id, answered_id, type, created_at]).then(async function(res){
                                                                                                    if(res.insertId > 0){
                                                                                                        var answeredFn = async function(ans_pid, user_id){
                                                                                                                await specific.Data(socket, user_id, ['username', 'name', 'surname']).then(function(res){
                                                                                                                    temp.ans_title = `${WORD.you_responded_to} ${res.username}`;
                                                                                                                }).catch(function(err){
                                                                                                                    console.log(err)
                                                                                                                });

                                                                                                                temp.type = 'answered';
                                                                                                                temp.ans_type = type;
                                                                                                                if(ans_pid == profile_id){
                                                                                                                    temp.ans_title = WORD.you_replied_own_message;
                                                                                                                }
                                                                                                            };
                                                                                                        temp.ans_deleted = !1;
                                                                                                        if(type == 'text'){
                                                                                                            await query(`SELECT * FROM ${T.MESSAGE} WHERE id = ?`, answered_id).then(async function(res){
                                                                                                                
                                                                                                                var answered = res[0],
                                                                                                                    ans_pid = answered.profile_id,
                                                                                                                    user_id = answered.user_id;

                                                                                                                temp.ans_text = specific.TextFilter(socket, answered.text, !1);
                                                                                                                await answeredFn(ans_pid, user_id);
                                                                                                            }).catch(function(err){
                                                                                                                console.log(err);
                                                                                                            })
                                                                                                        } else {
                                                                                                            await query(`SELECT f.*, m.user_id, m.profile_id FROM ${T.MESSAFI} f INNER JOIN ${T.MESSAGE} m WHERE f.id = ? AND m.id = f.message_id`, answered_id).then(async function(res){
                                                                                                                var amessafi = res[0],
                                                                                                                    ans_pid = amessafi.profile_id,
                                                                                                                    user_id = amessafi.user_id;

                                                                                                                temp.fi_aname = amessafi.name;
                                                                                                                temp.fi_asize = specific.SizeFormat(amessafi.size);
                                                                                                                if(type == 'image'){
                                                                                                                    temp.fi_aurl = specific.Url(`uploads/messages/${amessafi.file}`);
                                                                                                                }
                                                                                                                await answeredFn(ans_pid, user_id);
                                                                                                            }).catch(function(err){
                                                                                                                console.log(err);
                                                                                                            })
                                                                                                        }
                                                                                                    }
                                                                                                }).catch(function(err){
                                                                                                    console.log(err)
                                                                                                })
                                                                                            }
                                                                                        }).catch(function(err){
                                                                                            console.log(err);
                                                                                        })
                                                                                    }   
                                                                                }
                                                                            }).catch(function(err){
                                                                                console.log(err);
                                                                            }) 
                                                                        }
                                                                    }).catch(function(err){
                                                                        console.log(err);
                                                                    })
                                                                }


                                                                await query(`SELECT * FROM ${T.MESSAFI} WHERE message_id = ?`, insert_id).then(async function(res){
                                                                    if(res.length > 0){
                                                                        temp.messafi = !0;
                                                                        await forEachAsync(res, function(item, index, arr){
                                                                            temp.fi_id = item.id;
                                                                            temp.fi_name = item.name;
                                                                            temp.fi_url = specific.Url(`uploads/messages/${item.file}`);
                                                                            temp.fi_size = specific.SizeFormat(item.size);

                                                                            if(['.jpeg', '.jpg', '.png', '.gif'].indexOf(path.extname(item.name)) !== -1){
                                                                                temp.has_image = !0;
                                                                                temp.outimages += specific.Maket(socket, "messages/outimage", temp);
                                                                                temp.inimages += specific.Maket(socket, "messages/inimage", temp);
                                                                            } else {
                                                                                temp.has_file = !0;
                                                                                temp.outfiles += specific.Maket(socket, "messages/outfile", temp);
                                                                                temp.infiles += specific.Maket(socket, "messages/infile", temp);
                                                                            }
                                                                        }).catch(function(err){});
                                                                    }
                                                                }).catch(function(err){
                                                                    console.log(err);
                                                                });


                                                                
                                                                temp.id = insert_id;
                                                                if(text != null){
                                                                    temp.text = specific.TextFilter(socket, text);
                                                                }
                                                                temp.created_at = specific.DateString(socket, created_at);

                                                                if(send_notify){
                                                                    await specific.Notifies(profile_id).catch(function(err){
                                                                        console.log(err);
                                                                    });
                                                                }

                                                                var deliver = {
                                                                    S: 200,
                                                                    CID: chat_id,
                                                                    MID: insert_id,
                                                                    CO: message_exists == 0,
                                                                    HTM: specific.Maket(socket, 'messages/outgoing', temp),
                                                                    TX: `${WORD.you}: ${specific.TextFilter(socket, text, !1)}`,
                                                                    CA: temp.created_at,
                                                                    EL: `.content_pnuser[data-id=${profile_id}]`
                                                                };

                                                                if(text == null){
                                                                    deliver.TX = `${WORD.you}: ${WORD.attached_file}`;
                                                                }


                                                                specific.emitChamgesTo(USER.id, 'setOutomessage', {
                                                                    S: 200,
                                                                    HTM: specific.Maket(socket, "messages/outgoing", temp),
                                                                    MID: insert_id
                                                                }, socket.id);
                                                                
                                                                specific.emitChamgesTo(profile_id, 'setOutimessage', {
                                                                    S: 200,
                                                                    HTM: specific.Maket(socket, "messages/incoming", temp),
                                                                    MID: insert_id,
                                                                    PID: USER.id,
                                                                    DLD: !0
                                                                });

                                                                response.send(deliver);
                                                            } else {
                                                                response.send(ERR);
                                                            }
                                                        } else {
                                                            response.send(ERR);
                                                        }
                                                    })

                                                } else {
                                                    response.send(ERR);
                                                }



                                            } else {
                                                response.send(ERR);
                                            }
                                        });


                                    } else {
                                        response.send(ERR);
                                    }
                                }).catch(function(err){
                                    console.log(err);
                                    response.send(ERR);
                                })
                            } else {
                                response.send(ERR);
                            }
                        })
                    } else {
                        response.send(ERR);
                    }  
                } else {
                    response.send(ERR);
                }
            } else {
                response.send(ERR);
            }
        });

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