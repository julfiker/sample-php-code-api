<?php

// Auth routes
Route::post('/auth/{social}/login', 'V1\\Auth\\AuthController@socialLogin');
Route::post('/auth/login', 'V1\\Auth\\AuthController@login');


// Data list routes
Route::get('/v1/sport',           'V1\\Lists\\ListController@getSport');
Route::get('/v1/brand',           'V1\\Lists\\ListController@getBrand');
Route::get('/v1/language',       'V1\\Lists\\ListController@getLanguage');
Route::get('/v1/nationality',     'V1\\Lists\\ListController@getNationality');
Route::get('/v1/hotspot-categories',     'V1\\Lists\\ListController@getHotspotCategory');

// User routes
Route::get('/v1/user',                     'V1\\User\\UserController@index');
Route::post('/v1/user',                    'V1\\User\\UserController@create');
Route::put('/v1/user',                     'V1\\User\\UserController@update');
Route::delete('/v1/user',                  'V1\\User\\UserController@delete'); //deletes the currently logged in user
Route::get('/v1/user/{id}',                'V1\\User\\UserController@view');
Route::get('/v1/user/search/{searchTerm}', 'V1\\User\\UserController@search');
Route::put('/v1/user/password/change',     'V1\\Auth\\PasswordController@changePassword');
Route::post('/v1/user/password/reset',     'V1\\Auth\\PasswordController@resetPassword');

// Sport routes
Route::post('/v1/sport',                    'V1\\Sport\\SportController@create');
Route::put('/v1/sport/{id}',                    'V1\\Sport\\SportController@update');
Route::delete('/v1/sport/{id}',                    'V1\\Sport\\SportController@destroy');

// Hotspot
Route::get('/v1/hotspot',                   'V1\\Hotspot\\HotspotController@index');
Route::post('/v1/hotspot',                  'V1\\Hotspot\\HotspotController@create');
Route::put('/v1/hotspot/{id}',                   'V1\\Hotspot\\HotspotController@update');
Route::delete('/v1/hotspot/{id}',                'V1\\Hotspot\\HotspotController@destroy');

// User devices
Route::post('/v1/user/device', 'V1\\Notification\\UserDeviceController@register');

// Notification
Route::get('/v1/user/{id}/notification',               'V1\\Notification\\NotificationController@getList');
Route::put('/v1/notification/{id}/mark-as-read',  'V1\\Notification\\NotificationController@markAsRead');

// Files
Route::get('/v1/file/image/{type}/{id}',  'V1\\File\\FileController@viewImage');
Route::post('/v1/file/image/{type}',      'V1\\File\\FileController@createImage');

// Sport contact requests
Route::post('/v1/user/{id}/sport-contact-request/invite',      'V1\\User\\SportContactController@invite');
Route::delete('/v1/user/{id}/sport-contact-request/cancel',    'V1\\User\\SportContactController@cancelFriendRequest');
Route::put('/v1/user/{id}/sport-contact-request/accept',       'V1\\User\\SportContactController@accept');
Route::put('/v1/user/{id}/sport-contact-request/decline',      'V1\\User\\SportContactController@decline');

// Sport contacts
Route::get('/v1/user/{id}/sport-contact',          'V1\\User\\SportContactController@getFriendList');
Route::delete('/v1/sport-contact/{id}',       'V1\\User\\SportContactController@unfriend');

// Activity routes
Route::post('/v1/activity',            'V1\\Activity\\ActivityController@create');

Route::get('/v1/user/{id}/activity',                'V1\\Activity\\ActivityController@getOwnActivities');
Route::get('/v1/user/{id}/activity/joined',         'V1\\Activity\\ActivityController@getJoinedActivitiesByInvitation');
Route::get('/v1/user/{id}/activity/joined/future',  'V1\\Activity\\ActivityController@getFutureJoinedActivities');
Route::get('/v1/user/{id}/activity/invited',        'V1\\Activity\\ActivityController@getInvitedActivities');
Route::get('/v1/user/{id}/activity/invited/future', 'V1\\Activity\\ActivityController@getFutureInvitedActivities');

Route::get('/v1/activity/explore', 'V1\\Activity\\ActivityController@explore');
Route::get('/v1/activity/search', 'V1\\Activity\\ActivityController@search');

Route::put('/v1/activity/{id}/join',     'V1\\Activity\\ActivityController@join');
Route::put('/v1/activity/{id}/decline',  'V1\\Activity\\ActivityController@decline');
Route::put('/v1/activity/{id}/leave',    'V1\\Activity\\ActivityController@leave');
Route::put('/v1/activity/{id}/invite',   'V1\\Activity\\ActivityController@invite');

Route::get('/v1/activity/{id}',        'V1\\Activity\\ActivityController@view');
Route::put('/v1/activity/{id}',        'V1\\Activity\\ActivityController@update');
Route::delete('/v1/activity/{id}',     'V1\\Activity\\ActivityController@delete');

Route::post('/v1/feedback',     'V1\\Settings\\FeedbackController@send');

//Export user own data to pdf
Route::get('/v1/mydata',     'V1\\Pdf\\PdfController@myData');

//Group
Route::get('/v1/mygroups',                      'V1\\Group\\GroupsController@index');
Route::get('/v1/groups/{id}',                   'V1\\Group\\GroupsController@show');
Route::post('/v1/groups',                       'V1\\Group\\GroupsController@store');
Route::put('/v1/groups/{id}',                   'V1\\Group\\GroupsController@update');
Route::delete('/v1/groups/{id}',                'V1\\Group\\GroupsController@destroy');
Route::post('/v1/join/group/{id}',              'V1\\Group\\GroupsController@joinGroup');
Route::post('/v1/leave/group/{id}',             'V1\\Group\\GroupsController@leaveGroup');
Route::post('/v1/request-join/group/{id}',      'V1\\Group\\GroupsController@joinRequestGroup');

/********************************************************************************
 * the following routes are deprecated; don't use them
 ********************************************************************************/

// Data list routes
Route::get('/sports',           'V1\\Lists\\ListController@getSport');
Route::get('/brands',           'V1\\Lists\\ListController@getBrand');
Route::get('/languages',        'V1\\Lists\\ListController@getLanguage');
Route::get('/nationalities',    'V1\\Lists\\ListController@getNationality');
Route::get('/hotspot-categories',    'V1\\Lists\\ListController@getHotspotCategory');

// Sport routes
Route::post('/sport',           'V1\\Sport\\SportController@create');
Route::put('/sport/{id}',           'V1\\Sport\\SportController@update');
Route::delete('/sport/{id}',           'V1\\Sport\\SportController@destroy');
Route::post('/sport/merge',     'V1\\Sport\\SportController@merge');

// Hotspot
Route::get('/hotspot',          'V1\\Hotspot\\HotspotController@index');
Route::post('/hotspot',          'V1\\Hotspot\\HotspotController@create');

// User routes
Route::get('/user',                     'V1\\User\\UserController@index');
Route::post('/user',                    'V1\\User\\UserController@create');
Route::put('/user',                     'V1\\User\\UserController@update');
Route::delete('/user',                  'V1\\User\\UserController@delete'); //deletes the currently logged in user
Route::get('/user/{id}',                'V1\\User\\UserController@view');
Route::get('/user/search/{searchTerm}', 'V1\\User\\UserController@search');

Route::put('/passwords/change',         'V1\\Auth\\PasswordController@changePassword');
Route::post('/passwords/reset',         'V1\\Auth\\PasswordController@resetPassword');

// User devices
Route::post('/user-devices', 'V1\\Notification\\UserDeviceController@register');

// Notification
Route::get('/notifications/{id}',               'V1\\Notification\\NotificationController@getList');
Route::put('/notifications/{id}/mark-as-read',  'V1\\Notification\\NotificationController@markAsRead');
//Route::get('/notifications/send-test-message',  'V1\\Notification\\UserDeviceController@send');

// Files
Route::get('/files/image/{type}/{id}',  'V1\\File\\FileController@viewImage');
Route::post('/files/image/{type}',      'V1\\File\\FileController@createImage');

// Sport contact requests
Route::post('/sport-contact-requests/{id}/invite',      'V1\\User\\SportContactController@invite');
Route::delete('/sport-contact-requests/{id}/cancel',    'V1\\User\\SportContactController@cancelFriendRequest');
Route::put('/sport-contact-requests/{id}/accept',       'V1\\User\\SportContactController@accept');
Route::put('/sport-contact-requests/{id}/decline',      'V1\\User\\SportContactController@decline');

// Sport contacts
Route::get('/sport-contacts/{id}',          'V1\\User\\SportContactController@getFriendList');
Route::delete('/sport-contacts/{id}',       'V1\\User\\SportContactController@unfriend');

// Activity routes
Route::post('/activities',            'V1\\Activity\\ActivityController@create');

Route::get('/users/{id}/activities',                'V1\\Activity\\ActivityController@getOwnActivities');
Route::get('/users/{id}/activities/joined',         'V1\\Activity\\ActivityController@getJoinedActivitiesByInvitation');
Route::get('/users/{id}/activities/joined/future',  'V1\\Activity\\ActivityController@getFutureJoinedActivities');
Route::get('/users/{id}/activities/invited',        'V1\\Activity\\ActivityController@getInvitedActivities');
Route::get('/users/{id}/activities/invited/future', 'V1\\Activity\\ActivityController@getFutureInvitedActivities');

Route::get('/activities/explore', 'V1\\Activity\\ActivityController@explore');
Route::get('/activities/search', 'V1\\Activity\\ActivityController@search');

Route::put('/activities/{id}/join',     'V1\\Activity\\ActivityController@join');
Route::put('/activities/{id}/decline',  'V1\\Activity\\ActivityController@decline');
Route::put('/activities/{id}/leave',    'V1\\Activity\\ActivityController@leave');
Route::put('/activities/{id}/invite',   'V1\\Activity\\ActivityController@invite');

Route::get('/activities/{id}',        'V1\\Activity\\ActivityController@view');
Route::put('/activities/{id}',        'V1\\Activity\\ActivityController@update');
Route::delete('/activities/{id}',     'V1\\Activity\\ActivityController@delete');

Route::post('/feedbacks',     'V1\\Settings\\FeedbackController@send');