<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();
Route::impersonate();


Route::get('logout', 'Auth\LoginController@logout');

Route::group(['middleware' => ['config.company']], function () {
    Route::get('verify', 'VerifyController@index')->name('user.verify.index');
    Route::get('verify/{token}', 'VerifyController@verify')->name('user.verify');
    Route::post('verify/{token}/confirm', 'VerifyController@confirmVerify')->name('user.verify.confirm');
    Route::post('verify/resend', 'VerifyController@resend')->name('user.verify.resend');
});

Route::post('/reports/user/statistic/data', 'ReportController@learnerStatisticsData')->name('reports.user.statistic.data');

Route::get('/reports/course/data/history/{id}/{course_id}/{option}', 'ReportController@courseUserHistoryData')->name('reports.course.users.history.data');

Route::get('users/leave-impersonate', 'UserController@leaveImpersonate')->name('users.leave-impersonate');

Route::middleware(['authorize:sys-admin|company-admin'])->group(function () {
    Route::get('users/{user}/impersonate', 'UserController@impersonate')->name('users.impersonate');
});

Route::group(['middleware' => ['auth', '2fa', 'config.company']], function () {


    Route::get('/', 'HomeController@index')->name('home');
    Route::get('/home', function(){
        return redirect('/');
    });
    Route::get('language/{locale}', 'LocalizationController@update');


    Route::get('api/get-roles', 'ApiController@getRoles')->name('api.get-roles');
    Route::get('api/get-teams', 'ApiController@getTeams')->name('api.get-teams');
    Route::get('api/get-users-by-teams', 'ApiController@getUsersByTeams')->name('api.get-users-by-teams');
    Route::get('api/get-modules-by-courses', 'ApiController@getModulesByCourses')->name('api.get-modules-by-courses');

    Route::middleware(['authorize:sys-admin|company-admin'])->group(function () {
        Route::get('companies/data', 'CompanyController@anyData')->name('companies.data');
        Route::get('companies/restore', 'CompanyController@restore')->name('companies.restore');
        Route::get('companies/restore/{id}', 'CompanyController@restoreCompany')->name('companies.restore.event');
        Route::delete('companies/delete/trash/{id}', 'CompanyController@deleteTrashCompany')->name('companies.delete.trash');

        Route::get('companies/trash/cron', 'CompanyController@cronToDeleteTrashCompany');
        Route::get('companies/restore-data', 'CompanyController@trashedData')->name('companies.restore.data');
        Route::post('companies/{company_id}/courses/enroll', 'CourseCompanyController@enrollMultipleCourse')->name('companies.courses.enrollMultipleCourse');
        Route::resource('companies', 'CompanyController');

        Route::get('roles/data', 'RoleController@anyData')->name('roles.data');
        Route::resource('roles', 'RoleController');
    });

    // Parameters of authorize middleware can be delimited by '|' symbol
    // For example, authorize:company-admin|sys-admin
     
    Route::middleware(['authorize:company-admin|user-management.users.index'])->group(function () {
        Route::get('users/adsetup', 'UserController@adSetup')->name('users.adsetup');
        Route::put('users/adsetup', 'UserController@updateAdSetup')->name('users.adsetup.update');
    });

    Route::middleware(['authorize:sys-admin|company-admin|user-management.teams.index'])->group(function () {
        Route::get('teams/data', 'TeamController@anyData')->name('teams.data');
        Route::resource('teams', 'TeamController');
        Route::get('teams/{id}/users', 'TeamController@enrolledUsers')->name('teams.users');
        Route::get('teams/{company_id}/users/unenrolled', 'TeamController@unenrolledUsers')->name('teams.users.unenrolled');
        Route::post('teams/{id}/users/enroll', 'TeamController@enroll')->name('teams.users.enroll');
        Route::get('teams/{id}/courses', 'TeamController@enrolledCourses')->name('teams.courses');
        Route::post('teams/company/list', 'TeamController@companyTeamList')->name('company.team.manager.list');
    });

    Route::middleware(['authorize:sys-admin|company-admin|user-management.users.index'])->group(function () {
        Route::get('users/data', 'UserController@anyData')->name('users.data');
        Route::get('users/import', 'UserController@import')->name('users.import');
        Route::get('users/import/log', 'UserController@importLog')->name('users.import.log');
        Route::post('users/import', 'UserController@doImport')->name('users.import.process');
        Route::post('users/bulk/active', 'UserController@bulkActiveUser')->name('users.bulk.active');
        Route::post('users/bulk/inactive', 'UserController@bulkInactiveUser')->name('users.bulk.inactive');
        Route::post('users/bulk/delete', 'UserController@bulkDeleteUser')->name('users.bulk.delete');

        Route::resource('users', 'UserController');
    });

    Route::get('users/course-data/{user_id}', 'UserController@courseData')->name('users.course.data');
    Route::post('/status', 'UserController@statusUpdate')->name('user.status.update');

    Route::middleware(['authorize:sys-admin|company-admin|courses.index'])->group(function () {
        Route::get('courses/data', 'CourseController@anyData')->name('courses.data');
        Route::get('courses/list', 'CourseController@courseList')->name('courses.list');
        Route::get('courses/list/data', 'CourseController@courseListData')->name('courses.list.data');

        Route::get('courses/{id}/modules', 'CourseController@modulesData')->name('courses.modules.data');
        Route::get('courses/{slug}/config', 'CourseController@config')->name('courses.config');
        Route::put('courses/{slug}/config', 'CourseController@updateConfig')->name('courses.config.update');

        Route::get('courses/company', 'CourseController@courseCompany')->name('company.courses.index');

        Route::resource('courses', 'CourseController');

        Route::get('courses/{id}/elearning/create', 'ElearningController@create')->name('elearning.create');
        Route::post('courses/elearning/store', 'ElearningController@store')->name('elearning.store');
        Route::get('courses/{course}/elearning/{slug}', 'ElearningController@show')->name('elearning.show');
        Route::get('courses/{course}/elearning/{slug}/edit', 'ElearningController@edit')->name('elearning.edit');
        Route::put('courses/elearning/{id}', 'ElearningController@update')->name('elearning.update');
        Route::delete('courses/elearning/{id}', 'ElearningController@destroy')->name('elearning.destroy');
        
        
        Route::get('courses/{course_id}/companies', 'CourseCompanyController@enrolledCompanies')->name('courses.companies');
        Route::get('courses/{course_id}/companies/unenrolled', 'CourseCompanyController@unenrolledCompanies')->name('courses.companies.unenrolled');
        
        Route::get('courses/{course}/{company}', 'CourseCompanyController@show')->name('courses.companies.show');
        Route::get('courses/{course_id}/{company_id}/schedule/{module_id}', 'CourseCompanyController@scheduleEdit')->name('courses.companies.schedule.edit');
        Route::post('courses/{course_id}/{company_id}/schedule/{module_id}', 'CourseCompanyController@scheduleUpdate')->name('courses.companies.schedule.update');
        Route::post('courses/{course_id}/companies/enroll', 'CourseCompanyController@enroll')->name('courses.companies.enroll');
        Route::get('courses/{course_id}/{company_id}/team-members', 'CourseCompanyController@teamMembers')->name('courses.companies.team-members');
        Route::get('courses/{course_id}/{company_id}/team-members/unenrolled', 'CourseCompanyController@teamMembersUnenrolled')->name('courses.companies.team-members.unenrolled');
        
        Route::get('courses/{course_id}/{company_id}/user-members', 'CourseCompanyController@userMembers')->name('courses.companies.user-members');
        Route::get('courses/{course_id}/{company_id}/user-members/unenrolled', 'CourseCompanyController@userMembersUnenrolled')->name('courses.companies.user-members.unenrolled');
        
        Route::post('courses/{course_id}/{company_id}/members', 'CourseCompanyController@enrollMember')->name('courses.companies.members.enroll');
        Route::post('courses/{course_id}/{company_id}/users', 'CourseCompanyController@enrollAllUsers')->name('courses.companies.users.enroll.all');
    
    });
        
    Route::middleware(['authorize:sys-admin|company-admin'])->group(function () {
        Route::get('smtp-account', 'SysConfigController@smtp')->name('smtp-account.index');
        Route::post('smtp-account', 'SysConfigController@smtpUpdate')->name('smtp-account.update');
    });

    Route::get('scormdispatch-api', 'SysConfigController@scormAPI')->name('scormdispatch-api.index');
    Route::post('scormdispatch-api', 'SysConfigController@scormAPIUpdate')->name('scormdispatch-api.update');

    Route::get('pusher', 'SysConfigController@pusher')->name('pusher.index');
    Route::post('pusher', 'SysConfigController@pusherUpdate')->name('pusher.update');
    
    Route::middleware(['authorize:sys-admin'])->group(function () {
        Route::get('menu', 'MenuController@index')->name('menu.index');
        Route::post('menu', 'MenuController@store')->name('menu.store');
    });
    
    Route::middleware(['authorize:sys-admin|company-admin|portal-management.configuration.email-setup'])->group(function () {
        Route::get('email-setup', 'EmailTemplateController@index')->name('email-setup.index');
        Route::get('email-setup/data', 'EmailTemplateController@anyData')->name('email-setup.data');
        Route::get('email-setup/{slug}/{language?}', 'EmailTemplateController@show')->name('email-setup.show');
        Route::get('email-setup/{slug}/{language?}/edit', 'EmailTemplateController@edit')->name('email-setup.edit');
        Route::put('email-setup/{id}/{language?}', 'EmailTemplateController@update')->name('email-setup.update');
        Route::get('email-setup/{slug}/{language?}/editor', 'EmailTemplateController@visualEditor')->name('email-setup.editor');
    });
    
    Route::post('email-setup', 'EmailTemplateController@ajaxUpdate')->name('email-template.ajax.update');
    Route::post('email-get', 'EmailTemplateController@ajaxGet')->name('email-get');
    Route::post('email-template-variable-get', 'EmailTemplateController@ajaxVariableGet')->name('email-variable');


    Route::get('/avatar/{id}/initial', 'UserProfileController@initialAvatar')->name('user.avatar.initial');
    Route::get('/profile', 'UserProfileController@index')->name('user.profile');
    Route::post('/profile', 'UserProfileController@update')->name('user.profile.update');

    Route::get('/change-password', 'UserProfileController@changePassword')->name('user.password');
    Route::post('/update-password', 'UserProfileController@updatePassword')->name('user.password.update');

    Route::group(['middleware' => ['activate']], function () {
        Route::get('/my-courses/{slug}', 'MyCourseController@show')->name('my-courses.show');
    });

    Route::get('/my-courses', 'MyCourseController@index')->name('my-courses.index');
    Route::get('/my-courses/{course}/{module}', 'MyCourseController@module')->name('my-courses.module');
    Route::get('/my-courses/{slug}/result/{id}', 'MyCourseController@result')->name('my-courses.result');
    Route::post('/my-courses/{slug}/launch', 'MyCourseController@launch')->name('my-courses.launch');
    Route::post('/my-courses/{slug}/stream/presenter', 'MyCourseController@getStreamPresenter')->name('my-courses.stream.presenter');
    Route::post('/my-courses/{slug}/stream/launch', 'MyCourseController@streamLaunch')->name('my-courses.stream.launch');
    Route::get('/my-courses/{slug}/stream/result/{id}', 'MyCourseController@streamResult')->name('my-courses.stream.result');

    Route::get('/presenter-courses', 'PresenterCourseController@index')->name('presenter-courses.index');
    Route::post('/presenter-courses/launch/elearning', 'PresenterCourseController@launchElearning')->name('presenter-courses.launch.elearning');



    Route::get('/my-schedules', 'MyScheduleController@index')->name('my-schedules.index');

    Route::get('/my-certificates', 'MyCertificateController@index')->name('my-certificates.index');


    Route::middleware(['authorize:sys-admin|company-admin|reports.superadmin.index'])->group(function () {
        Route::get('/reports/enrollment', 'ReportController@enrollment')->name('reports.enrollment');
        Route::post('/reports/enrollment/generate', 'ReportController@enrollmentGenerate')->name('reports.enrollment.generate');
        Route::get('/reports/enrollment/chart', 'ReportController@enrollmentChart')->name('reports.enrollment.chart');

        
        Route::get('/reports/sadmin/{filter?}', 'ReportController@suReport')->name('reports.superadmin.index');
        Route::post('/reports/sadmin', 'ReportController@suReport')->name('reports.superadmin.index');
        
        Route::post('/reports/overdue-mail', 'ReportController@overdueEmail')->name('reports.overdue');
        Route::get('/reports/course/{filter?}', 'ReportController@courseReport')->name('reports.course');
        Route::post('/reports/course', 'ReportController@courseReport')->name('reports.course');

        Route::get('/reports/logs', 'ReportController@logs')->name('reports.log');
        
        Route::get('/reports/course/data/{option}', 'ReportController@reportCourseData')->name('reports.course.data');
        Route::get('/reports/course/{id}/{filter}', 'ReportController@reportCourseStatistic')->name('reports.course.statistic');
        
        Route::get('/reports/course/data/{id}/{option}/{csv?}', 'ReportController@courseUserData')->name('reports.course.users.data');
        
        Route::get('/reports/course/users/{id}/{filter}', 'ReportController@courseUsers')->name('reports.course.users');
        //Route::get('/reports/course/data/{id}', 'ReportController@courseUserData')->name('reports.course.users.data');
        Route::get('/reports/course/user/statistic/{id}/{course_id}/{option}', 'ReportController@reportUserStatistic')->name('reports.course.users.statistic');
    });

    Route::get('/reports', 'ReportController@index')->name('reports.index');
    Route::post('/reports/generate', 'ReportController@generate')->name('reports.generate');
    Route::get('/reports/user/data/{filter}/{csv?}', 'ReportController@reportUserData')->name('reports.user.data');
    

    Route::get('/reports/ladmin/{id}/{export?}/{type?}/{filter?}', 'ReportController@learnerReport')->name('reports.learneradmin.index');
    
    // Route::get('/reports/ladmin/{id}/{type}', 'ReportController@learnerReport')->name('reports.learneradmin.export');
    Route::get('/reports/ladmin/{id}/csv/normal/{filter}', 'ReportController@learnerReport')->name('reports.learneradmin.export');
    Route::get('/reports/ladmin/{id}/csv/course/{filter}', 'ReportController@learnerReport')->name('reports.learneradmin.export.course');
    Route::get('/reports/learner/course/{id}/{filter}', 'ReportController@learnerCourses')->name('reports.learner.course.index');
    Route::get('/reports/learner/course/data/{id}/{filter}', 'ReportController@learnerCoursesData')->name('reports.learner.course.data');




    /**
     * Client
     */
    // Configuration:
    Route::middleware(['authorize:sys-admin|company-admin|portal-management.configuration.index'])->group(function () {
        Route::get('configuration', 'ConfigController@index')->name('configuration.index');
        Route::put('configuration', 'ConfigController@update')->name('configuration.update');
    });

    Route::middleware(['authorize:sys-admin|company-admin|portal-management.configuration.smtp-account'])->group(function () {
        Route::get('client-smtp-account', 'ConfigController@smtp')->name('client-smtp-account.index');
        Route::post('client-smtp-account', 'ConfigController@smtpUpdate')->name('client-smtp-account.update');
        Route::get('client-smtp-account-reset', 'ConfigController@smtpConfigReset')->name('client-smtp-account.reset');
    });

    Route::post('client-mail-update', 'ConfigController@mailUpdate')->name('client.mail.update');

    /**
     * System
     */
    // Configuration:
    Route::middleware(['authorize:sys-admin|company-admin'])->group(function () {
        Route::get('setting', 'SysConfigController@setting')->name('setting.index');
        Route::put('setting', 'SysConfigController@updateSetting')->name('setting.update');

        Route::post('certificate-templates/{id}/duplicate', 'CertificateTemplateController@duplicate')->name('certificate-templates.duplicate');
        Route::post('certificate-templates/{id}/publish', 'CertificateTemplateController@publish')->name('certificate-templates.publish');
        Route::get('certificate-templates/{id}/preview', 'CertificateTemplateController@preview')->name('certificate-templates.preview');
        Route::resource('certificate-templates', 'CertificateTemplateController');
    });

    Route::middleware(['authorize:sys-admin|company-admin|portal-management.configuration.index'])->group(function () {
        Route::get('certificate-config', 'CertificateController@config')->name('client-certificate-config.index');
        Route::post('certificate-config', 'CertificateController@configUpdate')->name('client-certificate-config.update');
    });

    Route::middleware(['authorize:company-admin'])->group(function () {
        Route::get('welcome-config', 'ConfigController@showWelcome')->name('welcome-config.show');
        Route::post('welcome-config', 'ConfigController@updateWelcome')->name('welcome-config.update');
    });

    Route::get('team-results/{slug}', 'HomeController@teamResult')->name('dashboard.team-results');

    Route::get('tickets', 'TicketController@index')->name('tickets.index');
    Route::get('tickets/create', 'TicketController@create')->name('ticktes.create');
    Route::get('tickets/open', 'TicketController@open')->name('tickets.open');
    Route::get('tickets/closed', 'TicketController@closed')->name('tickets.closed');
    Route::get('tickets/data/{status}', 'TicketController@anyData')->name('tickets.data');
    Route::post('tickets/create', 'TicketController@store')->name('tickets.store');
    Route::get('tickets/{id}', 'TicketController@show')->name('tickets.show');
    Route::post('tickets/response', 'TicketController@response')->name('tickets.response');
    Route::get('tickets/attachment/{id}', 'TicketController@downloadAttachment')->name('tickets.attachment');
    Route::post('tickets/status/{id}', 'TicketController@setStatus')->name('tickets.status');
    Route::post('tickets/assign', 'TicketController@assignTo')->name('tickets.assign');
    Route::post('tickets/get-assignee', 'TicketController@getAssignee')->name('tickets.get.assignee');

    Route::get('my-tickets', 'MyTicketController@index')->name('my-tickets.index');
    Route::get('my-tickets/{id}', 'MyTicketController@show')->name('my-tickets.show');
    Route::post('my-tickets/{id}', 'MyTicketController@response')->name('my-tickets.response');
    Route::get('my-tickets/attachment/{id}', 'MyTicketController@downloadAttachment')->name('my-tickets.attachment');

    Route::post('google-auth', 'UserProfileController@ajaxGoogleAuth')->name('google-auth');

    Route::middleware(['authorize:sys-admin|company-admin|courses.index'])->group(function () {
        /*course category*/
        Route::get('categories/{id?}', 'CourseCategoryController@index')->name('category.index');
        Route::get('categories/list/{id?}', 'CourseCategoryController@list')->name('category.list');
        Route::get('category/create/{id?}', 'CourseCategoryController@create')->name('category.create');
        Route::post('category/store', 'CourseCategoryController@store')->name('category.store');
        Route::put('category/update/{id}', 'CourseCategoryController@update')->name('category.update');
        Route::get('category/{id}/edit', 'CourseCategoryController@edit')->name('category.edit');
        Route::get('category/{id}/show', 'CourseCategoryController@show')->name('category.show');
        Route::delete('category/delete/{id}', 'CourseCategoryController@destroy')->name('category.destroy');

        Route::post('category/ajax-subcategory', 'CourseCategoryController@subCategories')->name('ajax_subcategory');
        Route::post('ajax-courses', 'CourseController@ajaxCourses')->name('ajax.courses');

        Route::get('courses/{course}/document/create', 'DocumentController@create')->name('document.create');
        Route::get('courses/{course}/document/{slug}', 'DocumentController@show')->name('document.show');
        Route::post('courses/document/store', 'DocumentController@store')->name('document.store');
        Route::post('courses/module/store', 'DocumentController@moduleStore')->name('document.module.store');

        Route::get('courses/{course}/document/{slug}/edit', 'DocumentController@edit')->name('document.edit');
        Route::put('courses/document/{id}', 'DocumentController@update')->name('document.update');
        Route::delete('courses/document/{id}', 'DocumentController@destroy')->name('document.destroy');
        Route::get('courses/document/download/{id}', 'DocumentController@download')->name('document.download');
        Route::get('course/attachments/{id}', 'DocumentController@courseAttachmentData')->name('course.attachments');

        Route::get('course/attachments/show/{id}', 'DocumentController@showAttachment')->name('attachment.show');
        Route::get('course/attachments/delete/{id}', 'DocumentController@deleteAttachment')->name('attachment.delete');
    });

    Route::get('elearning/{id}/{user}/{module?}', 'Scorm\SCORMController@deliveryAction')->name('elearning.scorm.deliver');
    Route::get('elearning/loader/{id}/{sco}/{user}/{vs}', 'Scorm\SCORMController@playSCOAction')->name('elearning.scorm.load');
    Route::get('elearning/json/{id}/{sco}/{user}/{vs}/{attempt}/{mode}', 'Scorm\SCORMController@trackAction');
    Route::post('elearning/json/{id}/{sco}/{user}/{vs}/{attempt}/{mode}', 'Scorm\SCORMController@trackAction');
});

////////////***[SCORM]***////////////
/*
Route::group(['namespace' => 'Scorm'], function () {
    Route::get('elearning/{id}/{user}', ['as' => 'elearning.scorm.deliver', 'uses' => 'SCORMController@deliveryAction', 'middleware' => 'auth']);
    Route::get('elearning/loader/{id}/{sco}/{user}/{vs}', ['as' => 'elearning.scorm.load', 'uses' => 'SCORMController@playSCOAction', 'middleware' => 'auth']);
    Route::get('elearning/json/{id}/{sco}/{user}/{vs}/{attempt}/{mode}', ['uses' => 'SCORMController@trackAction', 'middleware' => 'auth']);
    Route::post('elearning/json/{id}/{sco}/{user}/{vs}/{attempt}/{mode}', ['uses' => 'SCORMController@trackAction', 'middleware' => 'auth']);

    Route::group(['prefix' => 'api/v1'], function () {
        /* Route to get the listing of chunk for a service */
/*        Route::post('scorm', ['as' => 'api.scorm.upload', 'uses' => 'SCORMController@uploadAction']);
        Route::post('video', ['as' => 'api.video.upload', 'uses' => 'SCORMController@uploadAction']);
        Route::get('elearning', ['as' => 'api.scorm.index', 'uses' => 'SCORMController@index']);
        Route::get('reports', ['as' => 'api.reports.index', 'uses' => 'ReportController@index']);
        Route::get('reports/{id}', ['as' => 'api.reports.show', 'uses' => 'ReportController@show']);
        Route::post('reports/filters', ['as' => 'api.reports.search', 'uses' => 'ReportController@searchAction']);
        Route::post('reports/filters/{id}', ['as' => 'api.reports.', 'uses' => 'ReportController@searchUserAction']);
        Route::get('elearning/{id}', ['as' => 'api.scorm.show', 'uses' => 'SCORMController@show']);
        Route::get('elearning/delete/{id}', ['as' => 'api.scorm.delete', 'uses' => 'SCORMController@destroy']);
    });
});
*/
//Route::group(['middleware' =>['api']


/*google 2fa*/
Route::post('/google2fa/authenticate', 'UserProfileController@ajaxGoogleAuth')->name('google-authenticate');
Route::post('/google2fa/user/authenticate', 'UserProfileController@googleAuthCheck')->name('google-user-auth');
Route::get('/google2fa/user/authenticate', 'UserProfileController@googleAuth')->name('google-user-auth');
Route::get('/complete-registration', 'Auth\RegisterController@completeRegistration');

Route::get('certificate/{user_id}/{course_id}', 'CertificateController@preview')->name('certificate.preview');
