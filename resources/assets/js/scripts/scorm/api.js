var __bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; };

function SCORMAPI(options) {
  $.support.cors = true;

  // Define SCORM 1.2 RTE API
  this.LMSGetDiagnostic = __bind(this.LMSGetDiagnostic, this);
  this.LMSGetErrorString = __bind(this.LMSGetErrorString, this);
  this.LMSGetLastError = __bind(this.LMSGetLastError, this);
  this.LMSCommit = __bind(this.LMSCommit, this);
  this.LMSSetValue = __bind(this.LMSSetValue, this);
  this.LMSGetValue = __bind(this.LMSGetValue, this);
  this.LMSFinish = __bind(this.LMSFinish, this);
  this.LMSInitialize = __bind(this.LMSInitialize, this);

  this.errorCode = 0;
  this.errorString = '';
  this.Initialized = false;
  this.cmi = null;

  // SCORM 1.2 Standard Data Type Definition
  this.SCORM_PASSED = "passed";
  this.SCORM_FAILED = "failed";
  this.SCORM_COMPLETED = "completed";
  this.SCORM_BROWSED = "browsed";
  this.SCORM_INCOMPLETE = "incomplete";
  this.SCORM_NOT_ATTEMPTED = "not attempted";
  this.SCORM_BROWSE = "browse";
  this.SCORM_NORMAL = "normal";
  this.SCORM_REVIEW = "review";
  this.SCORM_ENTRY_ABINITIO = "ab-initio";
  this.SCORM_ENTRY_RESUME = "resume";
  this.SCORM_ENTRY_NORMAL = "";
  this.SCORM_CREDIT = "credit";
  this.SCORM_NO_CREDIT = "no-credit";

  this.CMIString256 = '^[\\u0000-\\uffff]{0,255}$';
  this.CMIString4096 = '^[\\u0000-\\uffff]{0,4096}$'; //Zac Changed to increase Suspend limit, origanally set to 4096
  this.CMITime = '^([0-2]{1}[0-9]{1}):([0-5]{1}[0-9]{1}):([0-5]{1}[0-9]{1})(\.[0-9]{1,2})?$';
  this.CMITimespan = '^([0-9]{2,4}):([0-9]{2}):([0-9]{2})(\.[0-9]{1,2})?$';
  this.CMIInteger = '^\\d+$';
  this.CMISInteger = '^-?([0-9]+)$';
  this.CMIDecimal = '^-?([0-9]{0,3})(\.[0-9]*)?$';
  this.CMIIdentifier = '^[\\u0021-\\u007E]{0,255}$';
  this.CMIFeedback = this.CMIString256; // This must be redefined
  this.CMIIndex = '[._](\\d+).';
  // Vocabulary Data Type Definition
  this.CMIStatus = '^passed$|^completed$|^failed$|^incomplete$|^browsed$';
  this.CMIStatus2 = '^passed$|^completed$|^failed$|^incomplete$|^browsed$|^not attempted$';
  this.CMIExit = '^time-out$|^suspend$|^logout$|^$';
  this.CMIType = '^true-false$|^choice$|^fill-in$|^matching$|^performance$|^sequencing$|^likert$|^numeric$';
  this.CMIResult = '^correct$|^wrong$|^unanticipated$|^neutral$|^([0-9]{0,3})?(\.[0-9]*)?$';
  this.NAVEvent = '^previous$|^continue$';
  // Children lists
  this.cmi_children = 'core,suspend_data,launch_data,comments,objectives,student_data,student_preference,interactions';
  this.core_children = 'student_id,student_name,lesson_location,credit,lesson_status,entry,score,total_time,lesson_mode,exit,session_time';
  this.score_children = 'raw,min,max';
  this.comments_children = 'content,location,time';
  this.objectives_children = 'id,score,status';
  this.correct_responses_children = 'pattern';
  this.student_data_children = 'mastery_score,max_time_allowed,time_limit_action';
  this.student_preference_children = 'audio,language,speed,text';
  this.interactions_children = 'id,objectives,time,type,correct_responses,weighting,student_response,result,latency';
  // Data ranges
  this.score_range = '0#100';
  this.audio_range = '-1#100';
  this.speed_range = '-100#100';
  this.weighting_range = '-100#100';
  this.text_range = '-1#1';

  // Define SCORM 2004 RTE API
  this.GetDiagnostic = __bind(this.GetDiagnostic, this);
  this.GetErrorString = __bind(this.GetErrorString, this);
  this.GetLastError = __bind(this.GetLastError, this);
  this.Commit = __bind(this.Commit, this);
  this.SetValue = __bind(this.SetValue, this);
  this.GetValue = __bind(this.GetValue, this);
  this.Terminate = __bind(this.Terminate, this);
  this.Initialize = __bind(this.Initialize, this);

  this.SCORM2004_InitialStatus = false;
  this.SCORM2004_errorCode = 0;
  this.SCORM2004_errorString = '';
  this.SCORM2004_Initialized = false;
  this.SCORM2004_Terminated = false;
  this.SCORM2004_diagnostic = "";
  this.ccmi = null;
  this.adl = null;
  // SCORM 2004 Standard Data Type Definition
  this.SCORM2004_LOGOUT = "logout";
  this.SCORM2004_SUSPEND = "suspend";
  this.SCORM2004_NORMAL_EXIT = "normal";
  this.SCORM2004_TIMEOUT = "time-out";

  this.SCORM2004_PASSED = "passed";
  this.SCORM2004_FAILED = "failed";
  this.SCORM2004_UNKNOWN = "unknown";

  this.SCORM2004_COMPLETED = "completed";
  this.SCORM2004_INCOMPLETE = "incomplete";
  this.SCORM2004_NOT_ATTEMPTED = "not attempted";

  this.SCORM2004_BROWSE = "browse";
  this.SCORM2004_NORMAL = "normal";
  this.SCORM2004_REVIEW = "review";

  this.SCORM2004_ENTRY_ABINITIO = "ab-initio";
  this.SCORM2004_ENTRY_RESUME = "resume";
  this.SCORM2004_ENTRY_NORMAL = "";

  this.SCORM2004_CMIString200 = '^[\\u0000-\\uFFFF]{0,200}$';
  this.SCORM2004_CMIString250 = '^[\\u0000-\\uFFFF]{0,250}$';
  this.SCORM2004_CMIString1000 = '^[\\u0000-\\uFFFF]{0,1000}$';
  this.SCORM2004_CMIString4000 = '^[\\u0000-\\uFFFF]{0,4000}$';
  this.SCORM2004_CMIString64000 = '^[\\u0000-\\uFFFF]{0,64000}$';
  this.SCORM2004_CMILang = '^([a-zA-Z]{2,3}|i|x)(\-[a-zA-Z0-9\-]{2,8})?$|^$';
  this.SCORM2004_CMILangString250 = '^(\{lang=([a-zA-Z]{2,3}|i|x)(\-[a-zA-Z0-9\-]{2,8})?\})?([^\{].{0,250}$)?';
  this.SCORM2004_CMILangcr = '^((\{lang=([a-zA-Z]{2,3}|i|x)?(\-[a-zA-Z0-9\-]{2,8})?\}))(.*?)$';
  this.SCORM2004_CMILangString250cr = '^((\{lang=([a-zA-Z]{2,3}|i|x)?(\-[a-zA-Z0-9\-]{2,8})?\})?(.{0,250})?)?$';
  this.SCORM2004_CMILangString4000 = '^(\{lang=([a-zA-Z]{2,3}|i|x)(\-[a-zA-Z0-9\-]{2,8})?\})?([^\{].{0,4000}$)?';
  this.SCORM2004_CMITime = '^(19[7-9]{1}[0-9]{1}|20[0-2]{1}[0-9]{1}|203[0-8]{1})((-(0[1-9]{1}|1[0-2]{1}))((-(0[1-9]{1}|[1-2]{1}[0-9]{1}|3[0-1]{1}))(T([0-1]{1}[0-9]{1}|2[0-3]{1})((:[0-5]{1}[0-9]{1})((:[0-5]{1}[0-9]{1})((\\.[0-9]{1,2})((Z|([+|-]([0-1]{1}[0-9]{1}|2[0-3]{1})))(:[0-5]{1}[0-9]{1})?)?)?)?)?)?)?)?$';
  this.SCORM2004_CMITimespan = '^P(\\d+Y)?(\\d+M)?(\\d+D)?(T(((\\d+H)(\\d+M)?(\\d+(\.\\d{1,2})?S)?)|((\\d+M)(\\d+(\.\\d{1,2})?S)?)|((\\d+(\.\\d{1,2})?S))))?$';
  this.SCORM2004_CMIInteger = '^\\d+$';
  this.SCORM2004_CMISInteger = '^-?([0-9]+)$';
  this.SCORM2004_CMIDecimal = '^-?([0-9]{1,5})(\\.[0-9]{1,18})?$';
  this.SCORM2004_CMIIdentifier = '^\\S{1,250}[a-zA-Z0-9]$';
  this.SCORM2004_CMIShortIdentifier = '^[\\w\.]{1,250}$';
  this.SCORM2004_CMILongIdentifier = '^(?:(?!urn:)\\S{1,4000}|urn:[A-Za-z0-9-]{1,31}:\\S{1,4000})$';
  this.SCORM2004_CMIFeedback = '^.*$'; // This must be redefined
  this.SCORM2004_CMIIndex = '[._](\\d+).';
  this.SCORM2004_CMIIndexStore = '.N(\\d+).';

  // Vocabulary Data Type Definition
  this.SCORM2004_CMICStatus = '^completed$|^incomplete$|^not attempted$|^unknown$';
  this.SCORM2004_CMISStatus = '^passed$|^failed$|^unknown$';
  this.SCORM2004_CMIExit = '^time-out$|^suspend$|^logout$|^normal$|^$';
  this.SCORM2004_CMIType = '^true-false$|^choice$|^(long-)?fill-in$|^matching$|^performance$|^sequencing$|^likert$|^numeric$|^other$';
  this.SCORM2004_CMIResult = '^correct$|^incorrect$|^unanticipated$|^neutral$|^-?([0-9]{1,4})(\\.[0-9]{1,18})?$';
  this.SCORM2004_NAVEvent = '^previous$|^continue$|^exit$|^exitAll$|^abandon$|^abandonAll$|^suspendAll$|^\{target=\\S{0,200}[a-zA-Z0-9]\}choice|jump$';
  this.SCORM2004_NAVBoolean = '^unknown$|^true$|^false$';
  this.SCORM2004_NAVTarget = '^previous$|^continue$|^choice.{target=\\S{0,200}[a-zA-Z0-9]}$';
  // Children lists
  this.SCORM2004_cmi_children = '_version,comments_from_learner,comments_from_lms,completion_status,credit,entry,exit,interactions,launch_data,learner_id,learner_name,learner_preference,location,max_time_allowed,mode,objectives,progress_measure,scaled_passing_score,score,session_time,success_status,suspend_data,time_limit_action,total_time';
  this.SCORM2004_comments_children = 'comment,timestamp,location';
  this.SCORM2004_score_children = 'max,raw,scaled,min';
  this.SCORM2004_objectives_children = 'progress_measure,completion_status,success_status,description,score,id';
  this.SCORM2004_correct_responses_children = 'pattern';
  this.SCORM2004_student_data_children = 'mastery_score,max_time_allowed,time_limit_action';
  this.SCORM2004_student_preference_children = 'audio_level,audio_captioning,delivery_speed,language';
  this.SCORM2004_interactions_children = 'id,type,objectives,timestamp,correct_responses,weighting,learner_response,result,latency,description';
  // Data ranges
  this.SCORM2004_scaled_range = '-1#1';
  this.SCORM2004_audio_range = '0#*';
  this.SCORM2004_speed_range = '0#*';
  this.SCORM2004_text_range = '-1#1';
  this.SCORM2004_progress_range = '0#1';

  this.SCORM2004_learner_response = {
      'true-false':{'format':'^true$|^false$', 'max':1, 'delimiter':'', 'unique':false},
      'choice':{'format':this.SCORM2004_CMIShortIdentifier, 'max':36, 'delimiter':'[,]', 'unique':true},
      'fill-in':{'format':this.SCORM2004_CMILangString250, 'max':10, 'delimiter':'[,]', 'unique':false},
      'long-fill-in':{'format':this.SCORM2004_CMILangString4000, 'max':1, 'delimiter':'', 'unique':false},
      'matching':{'format':this.SCORM2004_CMIShortIdentifier, 'format2':this.SCORM2004_CMIShortIdentifier, 'max':36, 'delimiter':'[,]', 'delimiter2':'[.]', 'unique':false},
      'performance':{'format':'^$|'+this.SCORM2004_CMIShortIdentifier, 'format2':this.SCORM2004_CMIDecimal+'|^$|'+ this.SCORM2004_CMIShortIdentifier, 'max':250, 'delimiter':'[,]', 'delimiter2':'[.]', 'unique':false},
      'sequencing':{'format':this.SCORM2004_CMIShortIdentifier, 'max':36, 'delimiter':'[,]', 'unique':false},
      'likert':{'format':this.SCORM2004_CMIShortIdentifier, 'max':1, 'delimiter':'', 'unique':false},
      'numeric':{'format':this.SCORM2004_CMIDecimal, 'max':1, 'delimiter':'', 'unique':false},
      'other':{'format':this.SCORM2004_CMIString4000, 'max':1, 'delimiter':'', 'unique':false}
  };

  this.SCORM2004_correct_responses = {
      'true-false':{'pre':'', 'max':1, 'delimiter':'', 'unique':false, 'duplicate':false,
                    'format':'^true$|^false$',
                    'limit':1},
      'choice':{'pre':'', 'max':36, 'delimiter':'[,]', 'unique':true, 'duplicate':false,
                'format':this.SCORM2004_CMIShortIdentifier},
      //'fill-in':{'pre':'^(((\{case_matters=(true|false)\})(\{order_matters=(true|false)\})?)|((\{order_matters=(true|false)\})(\{case_matters=(true|false)\})?))(.*?)$',
      'fill-in':{'pre':'',
                 'max':10, 'delimiter':'[,]', 'unique':false, 'duplicate':false,
                 'format':this.SCORM2004_CMILangString250cr},
      'long-fill-in':{'pre':'^(\{case_matters=(true|false)\})?', 'max':1, 'delimiter':'', 'unique':false, 'duplicate':true,
                      'format':this.SCORM2004_CMILangString4000},
      'matching':{'pre':'', 'max':36, 'delimiter':'[,]', 'delimiter2':'[.]', 'unique':false, 'duplicate':false,
                  'format':this.SCORM2004_CMIShortIdentifier, 'format2':this.SCORM2004_CMIShortIdentifier},
      'performance':{'pre':'^(\{order_matters=(true|false)\})?',
                     'max':250, 'delimiter':'[,]', 'delimiter2':'[.]', 'unique':false, 'duplicate':false,
                     'format':'^$|'+this.SCORM2004_CMIShortIdentifier, 'format2':this.SCORM2004_CMIDecimal+'|^$|'+this.SCORM2004_CMIShortIdentifier},
      'sequencing':{'pre':'', 'max':36, 'delimiter':'[,]', 'unique':false, 'duplicate':false,
                    'format':this.SCORM2004_CMIShortIdentifier},
      'likert':{'pre':'', 'max':1, 'delimiter':'', 'unique':false, 'duplicate':false,
                'format':this.SCORM2004_CMIShortIdentifier,
                'limit':1},
      'numeric':{'pre':'', 'max':2, 'delimiter':'[:]', 'unique':false, 'duplicate':false,
                 'format':this.SCORM2004_CMIDecimal,
                 'limit':1},
      'other':{'pre':'', 'max':1, 'delimiter':'', 'unique':false, 'duplicate':false,
               'format':this.SCORM2004_CMIString4000,
               'limit':1}
  };

  this.setSessionId = __bind(this.setSessionId, this);

  this.open = __bind(this.open, this);
  this.Ping = __bind(this.Ping, this);
  this.PopupError = __bind(this.PopupError, this);
  this.LeavedPage = __bind(this.LeavedPage, this);
  this.DoSetLog = __bind(this.DoSetLog, this);
  this.clear = __bind(this.clear, this);

  this.data = {};
  this.datamodel = {};
  this.interactions = {};

  this.SCORM2004_DATA = {};
  this.SCORM2004_datamodel = {};
  this.SCORM2004_interactions = {};

  this.options = options;
  this.options.baseUrl = window.location.protocol + '//' + window.location.host;

  this.clear();
  this.pingTimer= null;
  this.logSession = null;
  this.statusChanged = false;
  this.sentResult = false;
  this.scorm = "";
  this.learning = "";
  this.sco = 0;
  this.user = 0;
  this.vs = "";
  this.preview = 0;
  this.id = "";
  this.token = "";

  this.connectionErrorMsg = "There is an error communicating with the server. Please try again or contact support.";

  var ccmi = new Object();
    ccmi.comments_from_learner = new Object();
    ccmi.comments_from_learner._count = 0;
    ccmi.comments_from_lms = new Object();
    ccmi.comments_from_lms._count = 0;
    ccmi.interactions = new Object();
    ccmi.interactions._count = 0;
    ccmi.learner_preference = new Object();
    ccmi.objectives = new Object();
    ccmi.objectives._count = 0;
    ccmi.score = new Object();

  var cmi = new Object();
      cmi.core = new Object();
      cmi.core.score = new Object();
      cmi.objectives = new Object();
      cmi.student_data = new Object();
      cmi.student_preference = new Object();
      cmi.interactions = new Object();

    var _this = this;
    var url = window.location.protocol + '//' + window.location.host;
    var pathname = window.location.pathname;

    //var params = pathname.split('/');
    var protocol = window.location.protocol + '//';

    var fullUrl = window.location.href;
    fullUrl = fullUrl.substr(protocol.length);
    fullUrl = fullUrl.substr(0, fullUrl.indexOf('/elearning/'));
    //var paramsCount = params.length;
    /*
    this.scorm = params[paramsCount-4];//params[3];
    this.sco = params[paramsCount-3];//params[4];
    this.user = params[paramsCount-2];//params[5];
    this.vs = params[paramsCount-1];//params[6];
    */
    this.scorm = this.options.scorm;
    this.sco = this.options.sco;
    this.user = this.options.user;
    this.vs = this.options.version;
    this.attempt = this.options.attempt;

    //url = url + "/elearning/json/" + this.scorm + "/" + this.sco + "/" + this.user + "/" + this.vs + "/getSCORM";
    url = protocol + fullUrl + "/elearning/json/" + this.scorm + "/" + this.sco + "/" + this.user + "/" + this.vs + "/"  + this.attempt + "/getSCORM";
    //var style = "fullscreen=yes, resizeable=no, menubar=no, location=no";

   $.getJSON(
    url,
    function(data) {
      _this.token = data.token;

      if (_this.vs == "SCORM_12") {

        if(typeof data.cmi_core_lesson_mode == 'undefined' ||
			data.cmi_core_lesson_mode == null ||
            data.cmi_core_lesson_mode === _this.SCORM_INCOMPLETE ||
            !data.cmi_core_lesson_mode) {
           data.cmi_core_lesson_mode = _this.SCORM_NORMAL;
        }

        if(typeof data.cmi_core_lesson_status == 'undefined' ||
			data.cmi_core_lesson_status == null ||
			!data.cmi_core_lesson_status){
           data.cmi_core_lesson_status = _this.SCORM_INCOMPLETE;
           data.cmi_core_entry = _this.SCORM_ENTRY_ABINITIO;
        } else {
           data.cmi_core_entry = _this.SCORM_ENTRY_RESUME;
        }

      } else {
        if(typeof data.cmi_mode == 'undefined' ||
            data.cmi_mode === _this.SCORM_INCOMPLETE ||
            !data.cmi_mode) {
           data.cmi_mode = _this.SCORM2004_NORMAL;
        }

        if(typeof data.cmi_completion_status == 'undefined' || !data.cmi_completion_status){
           data.cmi_completion_status = _this.SCORM2004_INCOMPLETE;
           data.cmi_entry = _this.SCORM2004_ENTRY_ABINITIO;
        } else {
           data.cmi_entry = _this.SCORM2004_ENTRY_RESUME;
        }

        if(typeof data.cmi_success_status == 'undefined' || !data.cmi_success_status){
           data.cmi_success_status = _this.SCORM2004_UNKNOWN;
        }
      }

      if(typeof data.cmi_suspend_data == 'undefined' || !data.cmi_suspend_data){
        data.cmi_suspend_data = "";
      }

      if (_this.vs != "SCORM_12") {
        // The SCORM 2004 data model
        var SCORM2004_datamodel =  {
            'ccmi._children':{'defaultvalue':_this.SCORM2004_cmi_children, 'mod':'r'},
            'ccmi._version':{'defaultvalue':'1.0', 'mod':'r'},
            'ccmi.credit':{'defaultvalue': '' + ((typeof data.cmi_credit != 'undefined') ? data.cmi_credit:'') + '', 'mod':'r'},
            'ccmi.entry':{'defaultvalue':'' + data.cmi_entry +'', 'mod':'r'},
            'ccmi.exit':{'defaultvalue':'' + ((typeof data.cmi_exit != 'undefined') ? data.cmi_exit:'') + '', 'format':_this.SCORM2004_CMIExit, 'mod':'w'},
            'ccmi.success_status':{'defaultvalue':'' + ((typeof data.cmi_success_status != 'undefined') ? data.cmi_success_status:'unknown') + '', 'format':_this.SCORM2004_CMISStatus, 'mod':'rw'},
            'ccmi.completion_status':{'defaultvalue':'' + ((typeof data.cmi_completion_status != 'undefined') ? data.cmi_completion_status:'unknown') + '', 'format':_this.SCORM2004_CMICStatus, 'mod':'rw'},
            'ccmi.launch_data':{'defaultvalue':'' + ((typeof data.cmi_launch_data != 'undefined') ? '\'' + data.cmi_launch_data + '\'':'null') + '', 'mod':'r'},
            'ccmi.learner_id':{'defaultvalue':'' + data.cmi_learner_id + '', 'mod':'r'},
            'ccmi.learner_name':{'defaultvalue':'' + data.cmi_learner_name + '', 'mod':'r'},
            'ccmi.location':{'defaultvalue':'' + ((typeof data.cmi_location != 'undefined') ? '\'' + data.cmi_location + '\'':'') + '', 'format':_this.SCORM2004_CMIString1000, 'mod':'rw'},
            //'ccmi.max_time_allowed':{'defaultvalue': '' + ((typeof data.cmi_max_time_allowed !== 'undefined') ? '\''+ data.cmi_max_time_allowed + '\'':'null') + '', 'mod':'r'},
            'ccmi.mode':{'defaultvalue':'' + data.cmi_mode + '', 'mod':'r'},
            'ccmi.interactions._children':{'defaultvalue':_this.interactions_children, 'mod':'r'},
            'ccmi.interactions._count':{'mod':'r', 'defaultvalue':'0'},
            'ccmi.interactions.n.id':{'pattern':_this.SCORM2004_CMIIndex, 'format':_this.SCORM2004_CMILongIdentifier, 'mod':'rw'},
            'ccmi.interactions.n.type':{'pattern':_this.SCORM2004_CMIIndex, 'format':_this.SCORM2004_CMIType, 'mod':'rw'},
            'ccmi.interactions.n.objectives._count':{'pattern':_this.SCORM2004_CMIIndex, 'mod':'r', 'defaultvalue':'0'},
            'ccmi.interactions.n.objectives.n.id':{'pattern':_this.SCORM2004_CMIIndex, 'format':_this.CMILongIdentifier, 'mod':'rw'},
            'ccmi.interactions.n.timestamp':{'pattern':_this.SCORM2004_CMIIndex, 'format':_this.SCORM2004_CMITime, 'mod':'rw'},
            'ccmi.interactions.n.correct_responses._count':{'defaultvalue':'0', 'pattern':_this.SCORM2004_CMIIndex, 'mod':'r'},
            'ccmi.interactions.n.correct_responses.n.pattern':{'pattern':_this.SCORM2004_CMIIndex, 'format': _this.SCORM2004_CMIFeedback, 'mod':'rw'},
            'ccmi.interactions.n.weighting':{'pattern':_this.SCORM2004_CMIIndex, 'format':_this.SCORM2004_CMIDecimal, 'mod':'rw'},
            'ccmi.interactions.n.learner_response':{'pattern':_this.SCORM2004_CMIIndex, 'format':_this.SCORM2004_CMIFeedback, 'mod':'rw'},
            'ccmi.interactions.n.result':{'pattern':_this.SCORM2004_CMIIndex, 'format':_this.SCORM2004_CMIResult, 'mod':'rw'},
            'ccmi.interactions.n.latency':{'pattern':_this.SCORM2004_CMIIndex, 'format':_this.SCORM2004_CMITimespan, 'mod':'rw'},
            'ccmi.interactions.n.description':{'pattern':_this.SCORM2004_CMIIndex, 'format':_this.SCORM2004_CMILangString250, 'mod':'rw'},
            'ccmi.objectives._children':{'defaultvalue':_this.SCORM2004_objectives_children, 'mod':'r'},
            'ccmi.objectives._count':{'mod':'r', 'defaultvalue':'0'},
            'ccmi.objectives.n.id':{'pattern':_this.SCORM2004_CMIIndex, 'format':_this.SCORM2004_CMILongIdentifier, 'mod':'rw'},
            'ccmi.objectives.n.score._children':{'defaultvalue':_this.SCORM2004_score_children, 'pattern':_this.SCORM2004_CMIIndex, 'mod':'r'},
            'ccmi.objectives.n.score.scaled':{'defaultvalue':null, 'pattern':_this.SCORM2004_CMIIndex, 'format':_this.SCORM2004_CMIDecimal, 'range':_this.SCORM2004_scaled_range, 'mod':'rw'},
            'ccmi.objectives.n.score.raw':{'defaultvalue':null, 'pattern':_this.SCORM2004_CMIIndex, 'format':_this.SCORM2004_CMIDecimal, 'mod':'rw'},
            'ccmi.objectives.n.score.min':{'defaultvalue':null, 'pattern':_this.SCORM2004_CMIIndex, 'format':_this.SCORM2004_CMIDecimal, 'mod':'rw'},
            'ccmi.objectives.n.score.max':{'defaultvalue':null, 'pattern':_this.SCORM2004_CMIIndex, 'format':_this.SCORM2004_CMIDecimal, 'mod':'rw'},
            'ccmi.objectives.n.success_status':{'defaultvalue':'unknown', 'pattern':_this.SCORM2004_CMIIndex, 'format':_this.SCORM2004_CMISStatus, 'mod':'rw'},
            'ccmi.objectives.n.completion_status':{'defaultvalue':'unknown', 'pattern':_this.SCORM2004_CMIIndex, 'format':_this.SCORM2004_CMICStatus, 'mod':'rw'},
            'ccmi.objectives.n.progress_measure':{'defaultvalue':null, 'format':_this.SCORM2004_CMIDecimal, 'range':_this.SCORM2004_progress_range, 'mod':'rw'},
            'ccmi.objectives.n.description':{'pattern':_this.SCORM2004_CMIIndex, 'format':_this.SCORM2004_CMILangString250, 'mod':'rw'},
            'ccmi.progress_measure':{'defaultvalue':'' + ((typeof data.cmi_progress_measure != 'undefined') ? '\''+ data.cmi_progress_measure + '\'':'null') + '', 'format':_this.SCORM2004_CMIDecimal, 'range':_this.SCORM2004_progress_range, 'mod':'rw'},
            'ccmi.scaled_passing_score':{'defaultvalue':'' + ((typeof data.cmi_scaled_passing_score != 'undefined') ? '\''+ data.cmi_scaled_passing_score + '\'':'null') +'', 'format':_this.SCORM2004_CMIDecimal, 'range':_this.SCORM2004_scaled_range, 'mod':'r'},
            'ccmi.score._children':{'defaultvalue':_this.SCORM2004_score_children, 'mod':'r'},
            'ccmi.score.scaled':{'defaultvalue':'' + ((typeof data.cmi_score_scaled != 'undefined') ? '\'' + data.cmi_score_scaled + '\'':'null') + '', 'format':_this.SCORM2004_CMIDecimal, 'range':_this.SCORM2004_scaled_range, 'mod':'rw'},
            'ccmi.score.raw':{'defaultvalue':'' + ((typeof data.cmi_score_raw != 'undefined') ? '\'' + data.cmi_score_raw + '\'':'null') + '', 'format':_this.SCORM2004_CMIDecimal, 'mod':'rw'},
            'ccmi.score.min':{'defaultvalue':'' + ((typeof data.cmi_score_min != 'undefined') ? '\'' + data.cmi_score_min + '\'':'null') + '', 'format':_this.SCORM2004_CMIDecimal, 'mod':'rw'},
            'ccmi.score.max':{'defaultvalue':'' + ((typeof data.cmi_score_max != 'undefined') ? '\'' + data.cmi_score_max + '\'':'null') + '', 'format':_this.SCORM2004_CMIDecimal, 'mod':'rw'},
            'ccmi.session_time':{'format':_this.SCORM2004_CMITimespan, 'mod':'w', 'defaultvalue':'PT0H0M0S'},
            'ccmi.suspend_data':{'defaultvalue':'' + ((typeof data.cmi_suspend_data) ? data.cmi_suspend_data :'') + '', 'format':_this.CMIString4096, 'mod':'rw', 'writeerror':'405'},
            //'ccmi.time_limit_action':{'defaultvalue':'' + ((typeof data.cmi_time_limit_action != 'undefined') ? '\'' + data.cmi_time_limit_action + '\'':'null') + '', 'mod':'r'},
            'ccmi.total_time':{'defaultvalue':'' + ((typeof data.cmi_total_time != '') ? data.cmi_total_time:'PT0H0M0S') + '', 'mod':'r'},
            'adl.nav.request':{'defaultvalue':'_none_', 'format':_this.SCORM2004_NAVEvent, 'mod':'rw'}
        };

        //
        // SCORM2004_datamodel inizialization
        //
        // Navigation Object
        var adl = new Object();
            adl.nav = new Object();
            adl.nav.request_valid = new Array();

        for (element in SCORM2004_datamodel) {
          if (element.match(/\.n\./) === null) {
              if ((typeof eval('SCORM2004_datamodel["' + element + '"].defaultvalue')) !== 'undefined') {
                  eval(element+' = SCORM2004_datamodel["' + element + '"].defaultvalue;');
              } else {
                  eval(element+' = "";');
              }
          }
        }

        _this.ccmi = ccmi;
        _this.adl = adl;
        _this.SCORM2004_datamodel = SCORM2004_datamodel;
     }

        if (_this.vs == "SCORM_12") {
           // The SCORM 1.2 data model
           var datamodel =  {
             'cmi._children':{'defaultvalue': _this.cmi_children, 'mod':'r', 'writeerror':'402'},
             'cmi._version':{'defaultvalue':'3.4', 'mod':'r', 'writeerror':'402'},
             'cmi.core._children':{'defaultvalue': _this.core_children, 'mod':'r', 'writeerror':'402'},
             'cmi.core.student_id':{'defaultvalue': '' + data.cmi_core_student_id + '', 'mod':'r', 'writeerror':'403'},
             'cmi.core.student_name':{'defaultvalue': '' + data.cmi_core_student_name + '', 'mod':'r', 'writeerror':'403'},
             'cmi.core.lesson_location':{'defaultvalue':'' + ((typeof data.cmi_core_lesson_location != 'undefined') ? data.cmi_core_lesson_location :'') + '', 'format':_this.CMIString256, 'mod':'rw', 'writeerror':'405'},
             'cmi.core.credit':{'defaultvalue': '', 'mod':'r', 'writeerror':'403'},
             'cmi.core.lesson_status':{'defaultvalue':'' + ((typeof data.cmi_core_lesson_status != 'undefined') ? data.cmi_core_lesson_status : '') + '', 'format':_this.CMIStatus, 'mod':'rw', 'writeerror':'405'},
             'cmi.core.entry': {'defaultvalue' : '' + data.cmi_core_entry + '', 'mod':'r', 'writeerror':'403'},
             'cmi.core.score._children':{'defaultvalue': _this.score_children, 'mod':'r', 'writeerror':'402'},
             'cmi.core.score.raw':{'defaultvalue':'' + ((typeof data.cmi_core_score_raw != 'undefined') ? data.cmi_core_score_raw :'') + '', 'format':_this.CMIDecimal, 'range':_this.score_range, 'mod':'rw', 'writeerror':'405'},
             'cmi.core.score.max':{'defaultvalue':'' + ((typeof data.cmi_core_score_max != 'undefined') ? data.cmi_core_score_max :'') + '', 'format':_this.CMIDecimal, 'range':_this.score_range, 'mod':'rw', 'writeerror':'405'},
             'cmi.core.score.min':{'defaultvalue':'' + ((typeof data.cmi_core_score_min != 'undefined') ? data.cmi_core_score_min :'') + '', 'format':_this.CMIDecimal, 'range':_this.score_range, 'mod':'rw', 'writeerror':'405'},
             'cmi.core.total_time':{'defaultvalue':'' + ((typeof data.cmi_core_total_time != 'undefined') ? data.cmi_core_total_time :'00:00:00') +'', 'mod':'r', 'writeerror':'403'},
             'cmi.core.lesson_mode':{'defaultvalue': '' + data.cmi_core_lesson_mode + '', 'mod':'r', 'writeerror':'403'},
             'cmi.core.exit':{'defaultvalue':'' + ((typeof data.cmi_core_exit != 'undefined') ? data.cmi_core_exit :'') + '', 'format':_this.CMIExit, 'mod':'w', 'readerror':'404', 'writeerror':'405'},
             'cmi.core.session_time':{'format': _this.CMITimespan, 'mod':'w', 'defaultvalue':'00:00:00', 'readerror':'404', 'writeerror':'405'},
             'cmi.suspend_data':{'defaultvalue':'' + ((typeof data.cmi_suspend_data) ? data.cmi_suspend_data :'') + '', 'format':_this.CMIString4096, 'mod':'rw', 'writeerror':'405'},
             'cmi.launch_data':{'defaultvalue':'' + ((typeof data.cmi_launch_data != 'undefined') ? data.cmi_launch_data:'') + '', 'mod':'r', 'writeerror':'403'},
             'cmi.comments':{'defaultvalue':'' + ((typeof data.cmi_comments != 'undefined') ? data.cmi_comments :'') + '', 'format':this.CMIString4096, 'mod':'rw', 'writeerror':'405'},
             // deprecated evaluation attributes
             'cmi.comments_from_lms':{'mod':'r', 'writeerror':'403'},
             'cmi.objectives._children':{'defaultvalue': '' + _this.objectives_children + '', 'mod':'r', 'writeerror':'402'},
             'cmi.objectives._count':{'mod':'r', 'defaultvalue':'0', 'writeerror':'402'},
             'cmi.objectives.n.id':{'pattern':_this.CMIIndex, 'format':_this.CMIIdentifier, 'mod':'rw', 'writeerror':'405'},
             'cmi.objectives.n.score._children':{'pattern':_this.CMIIndex, 'mod':'r', 'writeerror':'402'},
             'cmi.objectives.n.score.raw':{'defaultvalue':'', 'pattern':_this.CMIIndex, 'format':_this.CMIDecimal, 'range':_this.score_range, 'mod':'rw', 'writeerror':'405'},
             'cmi.objectives.n.score.min':{'defaultvalue':'', 'pattern':_this.CMIIndex, 'format':_this.CMIDecimal, 'range':_this.score_range, 'mod':'rw', 'writeerror':'405'},
             'cmi.objectives.n.score.max':{'defaultvalue':'', 'pattern':_this.CMIIndex, 'format':_this.CMIDecimal, 'range':_this.score_range, 'mod':'rw', 'writeerror':'405'},
             'cmi.objectives.n.status':{'pattern':_this.CMIIndex, 'foramat':_this.CMIStatus2, 'mod':'rw', 'writeerror':'405'},
             'cmi.interactions._children':{'defaultvalue':_this.interactions_children, 'mod':'r', 'writeerror':'402'},
             'cmi.interactions._count':{'defaultvalue':'0', 'mod':'r', 'writeerror':'402'},
             'cmi.interactions.n.id':{'pattern':_this.CMIIndex, 'format':_this.CMIIdentifier, 'mod':'w', 'readerror':'404', 'writeerror':'405'},
             'cmi.interactions.n.objectives._count':{'pattern':_this.CMIIndex, 'mod':'r', 'defaultvalue':'0', 'writeerror':'402'},
             'cmi.interactions.n.objectives.n.id':{'pattern':_this.CMIIndex, 'format':_this.CMIIdentifier, 'mod':'w', 'readerror':'404', 'writeerror':'405'},
             'cmi.interactions.n.time':{'pattern':_this.CMIIndex, 'format':_this.CMITime, 'mod':'w', 'readerror':'404', 'writeerror':'405'},
             'cmi.interactions.n.type':{'pattern':_this.CMIIndex, 'format':_this.CMIType, 'mod':'w', 'readerror':'404', 'writeerror':'405'},
             'cmi.interactions.n.correct_responses._count':{'pattern':_this.CMIIndex, 'mod':'r', 'defaultvalue':'0', 'writeerror':'402'},
             'cmi.interactions.n.correct_responses.n.pattern':{'pattern':_this.CMIIndex, 'format':_this.CMIFeedback, 'mod':'w', 'readerror':'404', 'writeerror':'405'},
             'cmi.interactions.n.weighting':{'pattern':_this.CMIIndex, 'format':_this.CMIDecimal, 'range':_this.weighting_range, 'mod':'w', 'readerror':'404', 'writeerror':'405'},
             'cmi.interactions.n.student_response':{'pattern':_this.CMIIndex, 'format':_this.CMIFeedback, 'mod':'w', 'readerror':'404', 'writeerror':'405'},
             'cmi.interactions.n.result':{'pattern':_this.CMIIndex, 'format':_this.CMIResult, 'mod':'w', 'readerror':'404', 'writeerror':'405'},
             'cmi.interactions.n.latency':{'pattern':_this.CMIIndex, 'format':_this.CMITimespan, 'mod':'w', 'readerror':'404', 'writeerror':'405'},
             'nav.event':{'defaultvalue':'', 'format':_this.NAVEvent, 'mod':'w', 'readerror':'404', 'writeerror':'405'}
            };
           //
           // Datamodel inizialization
           //
           // Navigation Object
           var nav = new Object();

           for (element in datamodel) {
             if (element.match(/\.n\./) == null) {
               if ((typeof eval('datamodel["' + element + '"].defaultvalue')) != 'undefined') {
                 eval(element+' = datamodel["' + element + '"].defaultvalue;');
               } else {
                 eval(element+' = "";');
               }
             }
           }
           _this.data = data;
           _this.SCORM2004_DATA = data;
           _this.cmi = cmi;

           _this.datamodel = datamodel;
        }
      _this.pingTimer = setInterval(_this.Ping,300000); //5 Min
    }
  );
}

//---------------------------------------------------------------------------------
//Functions to Call the SCORM API
//SCORM 1.2 RTE API LMSInitialize
SCORMAPI.prototype.LMSInitialize = function() {

  var _this = this;
  _this.errorCode = "0";
   if (!_this.Initialized) {
    _this.DoSetLog("In SCORM_Initialize");
    _this.Initialized = true;
    var _this = this;
    _this.DoSetLog('Initialize : ' + this.LMSGetErrorString(_this.errorCode));
    return "true";
  } else {
    _this.errorCode = "101";
  }
  _this.DoSetLog('Initialize : ' + this.LMSGetErrorString(_this.errorCode));
  return "false";
};

//SCORM 1.2 RTE API LMSGetValue
SCORMAPI.prototype.LMSGetValue = function(element) {

  var _this = this;
  this.errorCode = "0";

  if (this.Initialized) {
    this.DoSetLog("In LMSGetValue");
    if (element !="") {
        expression = new RegExp(_this.CMIIndex,'g');
        elementmodel = String(element).replace(expression,'.n.');

        if ((typeof eval('_this.datamodel["' + elementmodel + '"]')) != "undefined") {
            if (eval('_this.datamodel["' + elementmodel + '"].mod') != 'w') {
                element = String(element).replace(expression, "_$1.");
                elementIndexes = element.split('.');
                subelement = 'cmi';
                i = 1;
                while ((i < elementIndexes.length) ) {
                    subelement += '.' + elementIndexes[i++];
                }
                if (subelement == element) {
                  _this.errorCode = "0";
                  _this.DoSetLog("In LMSGetValue" + element + "=" + eval('_this.' + element));
                  return eval('_this.' + element);
                } else {
                    _this.errorCode = "0"; // Need to check if it is the right errorCode
                }
            } else {
                _this.errorCode = eval('_this.datamodel["' + elementmodel + '"].readerror');
            }
        } else {
            childrenstr = '._children';
            countstr = '._count';
            if (elementmodel.substr(elementmodel.length-childrenstr.length, elementmodel.length) == childrenstr) {
                parentmodel = elementmodel.substr(0, elementmodel.length - childrenstr.length);
                if ((typeof eval('_this.datamodel["' + parentmodel + '"]')) != "undefined") {
                    _this.errorCode = "202";
                } else {
                    _this.errorCode = "201";
                }
            } else if (elementmodel.substr(elementmodel.length-countstr.length, elementmodel.length) == countstr) {
                parentmodel = elementmodel.substr(0, elementmodel.length - countstr.length);
                if ((typeof eval('_this.datamodel["' + parentmodel + '"]')) != "undefined") {
                    _this.errorCode = "203";
                } else {
                    _this.errorCode = "201";
                }
            } else {
                _this.errorCode = "201";
            }
        }
    } else {
        _this.errorCode = "201";
    }
  } else {
      this.errorCode = "301";
  }
  this.DoSetLog('GetValue(' + element + ') -> ' + this.LMSGetErrorString(this.errorCode));
  return "";
};

//SCORM 1.2 RTE API LMSSetValue
SCORMAPI.prototype.LMSSetValue = function(element, value) {
  var _this = this;
  if (this.Initialized) {
      this.DoSetLog("In LMSSetValue");
      _this.errorCode = "0";
      if (element != "") {
          expression = new RegExp(_this.CMIIndex,'g');
          elementmodel = String(element).replace(expression,'.n.');
          if ((typeof eval('_this.datamodel["' + elementmodel + '"]')) != "undefined") {
              if (eval('_this.datamodel["' + elementmodel + '"].mod') != 'r') {
                  expression = new RegExp(eval('_this.datamodel["' + elementmodel + '"].format'));
                  value = value+'';
                  matches = value.match(expression);

                  if (matches != null) {
                      //Create dynamic data model element
                      if (element != elementmodel) {
                          elementIndexes = element.split('.');
                          subelement = 'cmi';
                          for (i = 1; i < elementIndexes.length - 1; i++) {
                              elementIndex = elementIndexes[i];
                              if (elementIndexes[i+1].match(/^\d+$/)) {
                                  if ((typeof eval('_this.' + subelement + '_' + elementIndex)) == "undefined") {
                                      eval('_this.' + subelement + '.'+ elementIndex + ' = new Object();');
                                      eval('_this.' + subelement + '.'  + elementIndex + '._count = 0;');
                                  }
                                  if (elementIndexes[i+1] == eval('_this.' + subelement + '.' + elementIndex + '._count')) {
                                      eval('_this.' + subelement + '.' + elementIndex + '._count++;');
                                  }
                                  if (elementIndexes[i+1] > eval('_this.' + subelement + '.' + elementIndex+'._count')) {
                                      _this.errorCode = "201";
                                  }
                                  subelement = subelement.concat('.'+elementIndex + '_' + elementIndexes[i+1]);
                                  i++;
                              } else {
                                  subelement = subelement.concat('.' + elementIndex);
                              }
                              if ((typeof eval('_this.' + subelement)) == "undefined") {
                                  eval('_this.' + subelement+' = new Object();');
                                  if (subelement.substr(0,14) == 'cmi.objectives') {
                                      eval('_this.' + subelement+'.score = new Object();');
                                      eval('_this.' + subelement+'.score._children = _this.score_children;');
                                      eval('_this.' + subelement+'.score.raw = "";');
                                      eval('_this.' + subelement+'.score.min = "";');
                                      eval('_this.' + subelement+'.score.max = "";');
                                  }
                                  if (subelement.substr(0,16) == 'cmi.interactions') {
                                      eval('_this.' + subelement+'.objectives = new Object();');
                                      eval('_this.' + subelement+'.objectives._count = 0;');
                                      eval('_this.' + subelement+'.correct_responses = new Object();');
                                      eval('_this.' + subelement+'.correct_responses._count = 0;');
                                  }
                              }
                          }
                          element = subelement.concat('.' + elementIndexes[elementIndexes.length-1]);
                      }
                      //Store data
                      if (_this.errorCode == "0") {
                          if ((typeof eval('_this.datamodel["' + elementmodel + '"].range')) != "undefined") {
                              range = eval('_this.datamodel["' + elementmodel + '"].range');
                              ranges = range.split('#');
                              value = value * 1.0;
                              if ((value >= ranges[0]) && (value <= ranges[1])) {
                                  eval('_this.' + element + '=value;');
                                  _this.errorCode = "0";
                                  _this.DoSetLog('SetValue('+element+','+ value+') -> OK ');
                                  return "true";
                              } else {
                                  _this.errorCode = eval('_this.datamodel["' + elementmodel + '"].writeerror');
                              }
                          } else {
                              if (element == 'cmi.comments') {
                                  _this.cmi.comments = _this.cmi.comments + value;
                              } else {
                                  eval('_this.' + element + '=value;');
                              }
                              _this.errorCode = "0";
                              _this.DoSetLog('SetValue('+element+','+ value+') -> OK ');
                              return "true";
                          }

                      }
                  } else {
                      _this.errorCode = eval('_this.datamodel["' + elementmodel + '"].writeerror');
                  }
              } else {
                  _this.errorCode = eval('_this.datamodel["' + elementmodel + '"].writeerror');
              }
          } else {
              _this.errorCode = "201";
          }
      } else {
          _this.errorCode = "201";
      }
  } else {
      this.errorCode = "301";
  }
  this.DoSetLog('SetValue('+element+', '+value+') -> ' + this.LMSGetErrorString(this.errorCode));
  return "true";
};

//SCORM 1.2 RTE API LMSFinish
SCORMAPI.prototype.LMSFinish = function() {

  var result, _this = this;
  _this.errorCode = "0";

  if (_this.Initialized) {
    _this.DoSetLog('In LMSFinish');
    _this.Initialized = false;
    result = _this.StoreData(_this.cmi, true);
    result = ('_this' == result) ? 'true' : 'false';

    return result;
  } else {
   _this.errorCode = "301";
  }
  this.DoSetLog('Terminate : ' + this.LMSGetErrorString(this.errorCode));
  return "false";
};

//SCORM 1.2 RTE API LMSCommit
SCORMAPI.prototype.LMSCommit = function() {

    var result, _this = this;
    this.errorCode = "0";

    if (this.Initialized) {
      this.DoSetLog('In LMSCommit');

      result = _this.StoreData(_this.cmi, true);

      result = ('true' === result) ? 'true' : 'false';
      _this.errorCode = (result ==='true')? '0' : '101';

      return result;
    } else {
        this.errorCode = "301";
    }
    this.DoSetLog('Commited: ' + _this.LMSGetErrorString(_this.errorCode));
    return "false";
};

SCORMAPI.prototype.LMSGetLastError = function() {
  return this.errorCode;
};

SCORMAPI.prototype.LMSGetErrorString = function(errNo) {
  switch (errNo) {
    case '0':
      return 'No error';
    case '101':
      return 'General exception';
    case '201':
      return 'Invalid argument error';
    case '202':
      return 'Element cannot have children';
    case '203':
      return 'Element not an array - cannot have count';
    case '301':
      return 'Not initialized';
    case '401':
      return 'Not implemented error';
    case '402':
      return 'Invalid set value, element is a keyword';
    case '403':
      return 'Element is read only';
    case '404':
      return 'Element is read only';
    case '405':
      return 'Incorrect data type';
    default:
      return 'Default error code';
  }
};

SCORMAPI.prototype.LMSGetDiagnostic = function(errNo) {
  return this.LMSGetErrorString(errNo);
};

//SCORM 1.2 General API StoreData
SCORMAPI.prototype.StoreData = function(data, storetotaltime) {
  var _this = this;
  var datastring = '';
  this.statusChanged = true;

  if (storetotaltime) {

      if (_this.cmi.core.lesson_status == _this.SCORM_NOT_ATTEMPTED) {
          data.core.lesson_status = _this.SCORM_COMPLETED;
      }

      if (this.cmi.core.lesson_mode == this.SCORM_NORMAL) {
          if (this.cmi.core.credit == this.SCORM_CREDIT) {
              if (this.cmi.student_data.mastery_score !== '' && this.cmi.core.score.raw !== '') {
                  if (parseFloat(this.cmi.core.score.raw) >= parseFloat(this.cmi.student_data.mastery_score)) {
                      data.core.lesson_status = this.SCORM_PASSED;
                  } else {
                      data.core.lesson_status = this.SCORM_FAILED;
                  }
              }
          } else {
            data.core.lesson_status = this.cmi.core.lesson_status;
          }
      }

      if (this.cmi.core.lesson_mode == this.SCORM_BROWSE) {
          if (this.datamodel['cmi.core.lesson_status'].defaultvalue == '' && this.cmi.core.lesson_status == 'not attempted') {
              data.cmi.core.lesson_status = this.SCORM_BROWSE;
          }
      }
      datastring = this.CollectData(data, 'cmi');
      datastring += this.TotalTime();
  } else {
      datastring = this.CollectData(data, 'cmi');
  }

  delete this.data.cmi_core_student_id;
  delete this.data.cmi_core_student_name;
  delete this.data.cmi_core_score_raw;
  delete this.data.cmi_core_score_max;
  delete this.data.cmi_core_score_min;
  delete this.data.cmi_score_scaled;
  delete this.data.cmi_core_lesson_mode;
  delete this.data.cmi_core_lesson_location;
  delete this.data.cmi_core_credit;
  delete this.data.cmi_core_exit;
  delete this.data.cmi_core_entry;
  delete this.data.cmi_suspend_data;
  delete this.data.cmi_core_lesson_status;
  delete this.data.cmi_comments;
  delete this.data.cmi_core_total_time;
  delete this.data.cmi_launch_data;

  var url = window.location.protocol + '//' + window.location.host;
  var pathname = window.location.pathname;
  var protocol = window.location.protocol + '//';

    var fullUrl = window.location.href;
    fullUrl = fullUrl.substr(protocol.length);
    fullUrl = fullUrl.substr(0, fullUrl.indexOf('/elearning/'));


  //url = url + "/elearning/json/" + this.scorm + "/" + this.sco + "/" + this.user + "/" + this.vs + "/putSCORM";
  //url = protocol + fullUrl + "/elearning/json/" + this.scorm + "/" + this.sco + "/" + this.user + "/" + this.vs + "/putSCORM";
  url = protocol + fullUrl + "/elearning/json/" + this.scorm + "/" + this.sco + "/" + this.user + "/" + this.vs + "/" + this.attempt + "/putSCORM";

  $.ajax({
        url: url,
        dataType: 'json',
        async: false,
        data: _this.data,
        success: function(result){
          console.log(result);
        },
        error : function(error){
          console.log(error);
        }
  });

  return "true";
};

//SCORM 1.2 General API CollectData
SCORMAPI.prototype.CollectData = function(data, parent) {
    var _this = this;
    var datastring = '';
    for (property in data) {
        if (typeof data[property] == 'object') {
            datastring += this.CollectData(data[property], parent + '.' + property);
        } else {
            element = parent+'.'+property;
            expression = new RegExp(_this.CMIIndex,'g');
            elementmodel = String(element).replace(expression,'.n.');

            // ignore the session time element
            if (element != "cmi.core.session_time") {
                // check if this specific element is not defined in the datamodel,
                if ((eval('typeof this.datamodel["' + element + '"]')) == "undefined"
                    && (eval('typeof this.datamodel["' + elementmodel + '"]')) != "undefined") {
                    eval('this.datamodel["' + element + '"] = this.CloneObj(this.datamodel["' + elementmodel + '"]);');
                }

                // check if the current element exists in the datamodel

                if ((typeof eval('this.datamodel["' + element + '"]')) != "undefined") {
                    // make sure this is not a read only element
                    if (eval('this.datamodel["' + element + '"].mod') != 'r') {
                        elementstring = '&' + this.underscore(element) + '=' + encodeURIComponent(data[property]);
                        // check if the element has a default value
                        if ((typeof eval('this.datamodel["' + element + '"].defaultvalue')) != "undefined") {
                            if (eval('this.datamodel["' + element + '"].defaultvalue') != "" ||
                              element == 'cmi.core.score.raw' ||
                              element == 'cmi.core.score.max' ||
                              element == 'cmi.core.score.min' ||
                              element == 'cmi.suspend_data' ||
                              element == 'cmi.core.lesson_location' ||
                              element == 'cmi.comments') {
                                // append the URI fragment to the string we plan to commit
                                datastring += elementstring;
                                //this.data[this.underscore(element)] = encodeURIComponent(data[property]);
                                this.data[this.underscore(element)] = data[property];

                                // update the element default to reflect the current committed value
                                eval('this.datamodel["' + element+'"].defaultvalue = data[property];');
                            }
                        } else {
                            // append the URI fragment to the string we plan to commit
                            datastring += elementstring;
                            //this.data[this.underscore(element)] = encodeURIComponent(data[property]);
                            this.data[this.underscore(element)] = data[property];
                            // no default value for the element, so set it now
                            eval('this.datamodel["' + element + '"].defaultvalue= data[property];');
                        }
                    }
                }
            }
        }
    }

    return datastring;
};

SCORMAPI.prototype.underscore = function(str) {
    str = String(str).replace(/.N/g,".");
    return str.replace(/\./g,"__");
}

SCORMAPI.prototype.CloneObj = function(obj){
    if(obj == null || typeof(obj) != 'object') {
        return obj;
    }

    var temp = new obj.constructor(); // changed (twice)
    for(var key in obj) {
        temp[key] = this.CloneObj(obj[key]);
    }

    return temp;
};

SCORMAPI.prototype.AddTime = function(first, second) {
  var sFirst = first.split(":");
  var sSecond = second.split(":");
  var cFirst = sFirst[2].split(".");
  var cSecond = sSecond[2].split(".");
  var change = 0;

  FirstCents = 0;  //Cents
  if (cFirst.length > 1) {
      FirstCents = parseInt(cFirst[1], 10);
  }

  SecondCents = 0;

  if (cSecond.length > 1) {
      SecondCents = parseInt(cSecond[1], 10);
  }

  var cents = FirstCents + SecondCents;

  change = Math.floor(cents / 100);
  cents = cents - (change * 100);

  if (Math.floor(cents) < 10) {
      cents = "0" + cents.toString();
  }

  var secs = parseInt(cFirst[0],10)+parseInt(cSecond[0],10) + change;  //Seconds

  change = Math.floor(secs / 60);
  secs = secs - (change * 60);

  if (Math.floor(secs) < 10) {
      secs = "0" + secs.toString();
  }

  mins = parseInt(sFirst[1],10)+parseInt(sSecond[1],10) + change;   //Minutes
  change = Math.floor(mins / 60);
  mins = mins - (change * 60);

  if (mins < 10) {
      mins = "0" + mins.toString();
  }

  hours = parseInt(sFirst[0],10)+parseInt(sSecond[0],10) + change;  //Hours

  if (hours < 10) {
      hours = "0" + hours.toString();
  }

  if (cents != '0') {
      return hours + ":" + mins + ":" + secs + '.' + cents;
  } else {
      return hours + ":" + mins + ":" + secs;
  }
};

SCORMAPI.prototype.TotalTime = function() {

  if (this.cmi.core.total_time == "null") {
		total_time = "00:00:00";
  } else {
		total_time = this.cmi.core.total_time;
  }

  if (this.cmi.core.session_time == "null") {
	  session_time = "00:00:00";
  } else {
		session_time = this.cmi.core.session_time;
  }

  var total_time = this.AddTime(total_time, session_time);

  this.data[this.underscore('cmi__core__session_time')] = this.cmi.core.session_time;
  this.data[this.underscore('cmi__core__total_time')] = total_time;

  return '&' + this.underscore('cmi.core.total_time') + '=' + encodeURIComponent(total_time);
};

//---------------------------------------------------------------------------------
//Functions to Call the SCORM 2004 API
//SCORM 2004 RTE API Initialize
SCORMAPI.prototype.Initialize = function() {
    this.SCORM2004_errorCode = "0";

    if ((!this.SCORM2004_Initialized) && (!this.SCORM2004_Terminated)) {

        this.DoSetLog('In Initialize');
        this.SCORM2004_Initialized = true;
        this.SCORM2004_errorCode = "0";
        // Write started log and get Log Session
        return "true";
    } else {
        if (this.SCORM2004_Initialized) {
            this.SCORM2004_errorCode = "103";
        } else {
            this.SCORM2004_errorCode = "104";
        }
    }
    this.DoSetLog('Initialize : '+this.GetErrorString(this.SCORM2004_errorCode));
    return "false";
};

//SCORM 2004 RTE API GetValue
SCORMAPI.prototype.GetValue = function(element) {
  element = element.replace(/cmi/g, 'ccmi');
  if ((this.SCORM2004_Initialized) && (!this.SCORM2004_Terminated)) {
        this.DoSetLog('In GetValue');
        this.SCORM2004_errorCode = "0";
        this.SCORM2004_diagnostic = "";

        if (element !="") {
            var expression = new RegExp(this.SCORM2004_CMIIndex,'g');
            var elementmodel = String(element).replace(expression,'.n.');

            if ((typeof eval('this.SCORM2004_datamodel["'+elementmodel+'"]')) != "undefined") {

                if (eval('this.SCORM2004_datamodel["'+elementmodel+'"].mod') != 'w') {

                    element = String(element).replace(/\.(\d+)\./, ".N$1.");
                    element = element.replace(/\.(\d+)\./, ".N$1.");

                    var elementIndexes = element.split('.');
                    var subelement = element.substr(0,4);
                    var i = 1;

                    while ((i < elementIndexes.length) && (typeof eval('this.' + subelement) != "undefined")) {
                        subelement += '.'+elementIndexes[i++];
                    }

                    if (subelement == element) {
                        this.DoSetLog("GetValue (" + element + "=" + eval('this.' + element) + ")");
                        if ((typeof eval('this.' + subelement) != "undefined") && (eval('this.' + subelement) != null)) {
                            this.SCORM2004_errorCode = "0";
                            return eval('this.' + element);
                        } else {
                            this.SCORM2004_errorCode = "403";
                        }
                    } else {
                        this.SCORM2004_errorCode = "301";
                    }
                } else {
                    this.SCORM2004_errorCode = "405";
                }
            } else {
                var childrenstr = '._children';
                var countstr = '._count';
                var parentmodel = '';
                if (elementmodel.substr(elementmodel.length-childrenstr.length,elementmodel.length) == childrenstr) {
                    parentmodel = elementmodel.substr(0,elementmodel.length-childrenstr.length);
                    if ((typeof eval('this.SCORM2004_datamodel["'+parentmodel+'"]')) != "undefined") {
                        this.SCORM2004_errorCode = "301";
                        this.SCORM2004_diagnostic = "Data Model Element Does Not Have Children";
                    } else {
                        this.SCORM2004_errorCode = "401";
                    }
                } else if (elementmodel.substr(elementmodel.length-countstr.length,elementmodel.length) == countstr) {
                    parentmodel = elementmodel.substr(0,elementmodel.length-countstr.length);
                    if ((typeof eval('this.SCORM2004_datamodel["'+parentmodel+'"]')) != "undefined") {
                        this.SCORM2004_errorCode = "301";
                        this.SCORM2004_diagnostic = "Data Model Element Cannot Have Count";
                    } else {
                        this.SCORM2004_errorCode = "401";
                    }
                } else {
                    parentmodel = 'adl.nav.request_valid.';
                    if (element.substr(0,parentmodel.length) == parentmodel) {
                        if (element.substr(parentmodel.length).match(this.NAVTarget) == null) {
                            this.SCORM2004_errorCode = "301";
                        } else {
                            if (adl.nav.request == element.substr(parentmodel.length)) {
                                return "true";
                            } else if (adl.nav.request == '_none_') {
                                return "unknown";
                            } else {
                                return "false";
                            }
                        }
                    } else {

                        if (element == 'cmi.success_status' || element == 'cmi.completion_status') {
                            return this.SCORM2004_NOT_ATTEMPTED;
                        } else if (element == 'cmi.mode') {
                            return this.SCORM2004_NORMAL;
                        }
                    }
                }
            }
        } else {
            this.SCORM2004_errorCode = "301";
        }
  } else {
      if (this.SCORM2004_Terminated) {
          this.SCORM2004_errorCode = "123";
      } else {
          this.SCORM2004_errorCode = "122";
      }
  }
  this.DoSetLog('GetValue('+element+') -> '+this.GetErrorString(this.SCORM2004_errorCode));
  return "";
};

//SCORM 2004 RTE API SetValue
SCORMAPI.prototype.SetValue = function(element, value) {
  if ((this.SCORM2004_Initialized) && (!this.SCORM2004_Terminated)) {

        this.DoSetLog('In SetValue');
        this.SCORM2004_errorCode = "0";
        this.SCORM2004_diagnostic = "";
        element = element.replace(/cmi/g, 'ccmi');
        if (element != "") {
            var expression = new RegExp(this.SCORM2004_CMIIndex,'g');
            var elementmodel = String(element).replace(expression,'.n.');
            if ((typeof eval('this.SCORM2004_datamodel["' + elementmodel + '"]')) != "undefined") {
                if (eval('this.SCORM2004_datamodel["' + elementmodel + '"].mod') != 'r') {
                    if (eval('this.SCORM2004_datamodel["' + elementmodel + '"].format') != 'SCORM2004_CMIFeedback') {
                        expression = new RegExp(eval('this.SCORM2004_datamodel["' + elementmodel + '"].format'));
                    } else {
                        expression = new RegExp(this.SCORM2004_CMIFeedback);
                    }
                    value = value+'';
                    var matches = value.match(expression);

                    if ((matches != null) && ((matches.join('').length > 0) || (value.length == 0))) {
                        // Value match dataelement format
                        if (element != elementmodel) {
                            //This is a dynamic SCORM2004_datamodel element
                            var elementIndexes = element.split('.');
                            var subelement = 'ccmi';
                            var parentelement = 'ccmi';
                            for (var i=1;(i < elementIndexes.length-1) && (this.SCORM2004_errorCode=="0");i++) {
                                var elementIndex = elementIndexes[i];
                                if (elementIndexes[i+1].match(/^\d+$/)) {
                                    if ((parseInt(elementIndexes[i+1]) > 0) && (elementIndexes[i+1].charAt(0) == 0)) {
                                        // Index has a leading 0 (zero), this is not a number
                                        this.SCORM2004_errorCode = "351";
                                    }
                                    parentelement = subelement + '.' + elementIndex;
                                    if ((typeof eval('this.' + parentelement) == "undefined") || (typeof eval('this.' + parentelement+'._count') == "undefined")) {
                                        this.SCORM2004_errorCode="408";
                                    } else {
                                        if (elementIndexes[i+1] > eval('this.' + parentelement+'._count')) {
                                            this.SCORM2004_errorCode = "351";
                                            this.SCORM2004_diagnostic = "Data Model Element Collection Set Out Of Order";
                                        }
                                        subelement = subelement.concat('.'+elementIndex+'.N'+elementIndexes[i+1]);
                                        i++;

                                        if (((typeof eval('this.' + subelement)) == "undefined") && (i < elementIndexes.length-2)) {
                                            this.SCORM2004_errorCode="408";
                                        }
                                    }
                                } else {
                                    subelement = subelement.concat('.'+elementIndex);
                                }
                            }
                            if (this.SCORM2004_errorCode == "0") {
                                // Till now it's a real SCORM2004_datamodel element
                                element = subelement.concat('.'+elementIndexes[elementIndexes.length-1]);
                                if ((typeof eval('this.' + subelement)) == "undefined") {
                                    switch (elementmodel) {
                                        case 'ccmi.objectives.n.id':
                                            if (!this.SCORM2004_DuplicatedID(element,parentelement,value)) {
                                                if (elementIndexes[elementIndexes.length-2] == eval('this.' + parentelement+'._count')) {
                                                    eval('this.' + parentelement+'._count++;');
                                                    eval('this.' + subelement+' = new Object();');
                                                    var subobject = eval('this.' + subelement);
                                                    subobject.success_status = this.SCORM2004_datamodel["ccmi.objectives.n.success_status"].defaultvalue;
                                                    subobject.completion_status = this.SCORM2004_datamodel["ccmi.objectives.n.completion_status"].defaultvalue;
                                                    subobject.progress_measure = this.SCORM2004_datamodel["ccmi.objectives.n.progress_measure"].defaultvalue;
                                                    subobject.score = new Object();
                                                    subobject.score._children = this.SCORM2004_score_children;
                                                    subobject.score.scaled = this.SCORM2004_datamodel["ccmi.objectives.n.score.scaled"].defaultvalue;
                                                    subobject.score.raw = this.SCORM2004_datamodel["ccmi.objectives.n.score.raw"].defaultvalue;
                                                    subobject.score.min = this.SCORM2004_datamodel["ccmi.objectives.n.score.min"].defaultvalue;
                                                    subobject.score.max = this.SCORM2004_datamodel["ccmi.objectives.n.score.max"].defaultvalue;
                                                }
                                            } else {
                                                this.SCORM2004_errorCode="351";
                                                this.SCORM2004_diagnostic = "Data Model Element ID Already Exists";
                                            }
                                        break;
                                        case 'ccmi.interactions.n.id':
                                            if (elementIndexes[elementIndexes.length-2] == eval('this.' + parentelement+'._count')) {
                                                eval('this.' + parentelement+'._count++;');
                                                eval('this.' + subelement+' = new Object();');
                                                var subobject = eval('this.' + subelement);

                                                subobject.objectives = new Object();
                                                subobject.objectives._count = 0;
                                            }
                                        break;
                                        case 'ccmi.interactions.n.objectives.n.id':
                                            if (typeof eval('this.' + parentelement) != "undefined") {
                                                if (!this.SCORM2004_DuplicatedID(element,parentelement,value)) {
                                                    if (elementIndexes[elementIndexes.length-2] == eval('this.' + parentelement+'._count')) {
                                                        eval('this.' + parentelement+'._count++;');
                                                        eval('this.' + subelement+' = new Object();');
                                                    }
                                                } else {
                                                    this.SCORM2004_errorCode="351";
                                                    this.SCORM2004_diagnostic = "Data Model Element ID Already Exists";
                                                }
                                            } else {
                                                this.SCORM2004_errorCode="408";
                                            }
                                        break;
                                        case 'ccmi.interactions.n.correct_responses.n.pattern':
                                            if (typeof eval('this.' + parentelement) != "undefined") {
                                                // Use ccmi.interactions.n.type value to check the right dataelement format
                                                if (elementIndexes[elementIndexes.length-2] == eval('this.' + parentelement+'._count')) {
                                                    var interactiontype = eval('this.' + String(parentelement).replace('correct_responses','type'));
                                                    var interactioncount = eval('this.' + parentelement+'._count');
                                                    // trap duplicate values, which is not allowed for type choice
                                                    if (interactiontype == 'choice') {
                                                        for (var i=0; (i < interactioncount) && (this.SCORM2004_errorCode=="0"); i++) {
                                                           if (eval('this.' + parentelement+'.N'+i+'.pattern') == value) {
                                                               this.SCORM2004_errorCode = "351";
                                                           }
                                                        }
                                                    }
                                                    if ((typeof this.SCORM2004_correct_responses[interactiontype].limit == 'undefined') ||
                                                        (eval('this.' + parentelement+'._count') < this.SCORM2004_correct_responses[interactiontype].limit)) {
                                                        var nodes = new Array();
                                                        if (this.SCORM2004_correct_responses[interactiontype].delimiter != '') {
                                                            nodes = value.split(this.SCORM2004_correct_responses[interactiontype].delimiter);
                                                        } else {
                                                            nodes[0] = value;
                                                        }
                                                        if ((nodes.length > 0) && (nodes.length <= this.SCORM2004_correct_responses[interactiontype].max)) {
                                                            this.SCORM2004_errorCode = this.SCORM2004_CRcheckValueNodes (element, interactiontype, nodes, value, this.SCORM2004_errorCode);
                                                        } else if (nodes.length > this.SCORM2004_correct_responses[interactiontype].max) {
                                                            this.SCORM2004_errorCode = "351";
                                                            this.SCORM2004_diagnostic = "Data Model Element Pattern Too Long";
                                                        }
                                                        if ((this.SCORM2004_errorCode == "0") && ((this.SCORM2004_correct_responses[interactiontype].duplicate == false) ||
                                                           (!this.SCORM2004_DuplicatedPA(element,parentelement,value))) || (this.SCORM2004_errorCode == "0" && value == "")) {
                                                           eval('this.' + parentelement+'._count++;');
                                                           eval('this.' + subelement+' = new Object();');
                                                        } else {
                                                            if (this.SCORM2004_errorCode == "0") {
                                                                this.SCORM2004_errorCode="351";
                                                                this.SCORM2004_diagnostic = "Data Model Element Pattern Already Exists";
                                                            }
                                                        }
                                                    } else {
                                                        this.SCORM2004_errorCode="351";
                                                        this.SCORM2004_diagnostic = "Data Model Element Collection Limit Reached";
                                                    }
                                                } else {
                                                    this.SCORM2004_errorCode="351";
                                                    this.SCORM2004_diagnostic = "Data Model Element Collection Set Out Of Order";
                                                }
                                            } else {
                                                this.SCORM2004_errorCode="408";
                                            }
                                        break;
                                        default:
                                            if ((parentelement != 'ccmi.objectives') && (parentelement != 'ccmi.interactions') &&
                                                    (typeof eval('this.' + parentelement) != "undefined")) {

                                                if (elementIndexes[elementIndexes.length-2] == eval('this.' + parentelement+'._count')) {

                                                    eval('this.' + parentelement+'._count++;');
                                                    eval('this.' + subelement+' = new Object();');
                                                } else {
                                                    this.SCORM2004_errorCode="351";
                                                    this.SCORM2004_diagnostic = "Data Model Element Collection Set Out Of Order";
                                                }
                                            } else {
                                                this.SCORM2004_errorCode="408";
                                            }
                                        break;
                                    }
                                } else {
                                    switch (elementmodel) {
                                        case 'ccmi.objectives.n.id':
                                            if (eval('this.' + element) != value) {
                                                this.SCORM2004_errorCode = "351";
                                                this.SCORM2004_diagnostic = "Write Once Violation";
                                            }
                                        break;
                                        case 'ccmi.interactions.n.objectives.n.id':
                                            if (this.SCORM2004_DuplicatedID(element,parentelement,value)) {
                                                this.SCORM2004_errorCode = "351";
                                                this.SCORM2004_diagnostic = "Data Model Element ID Already Exists";
                                            }
                                        break;
                                        case 'ccmi.interactions.n.type':
                                            var subobject = eval('this.' + subelement);
                                            subobject.correct_responses = new Object();
                                            subobject.correct_responses._count = 0;
                                        break;
                                        case 'ccmi.interactions.n.learner_response':
                                            if (typeof eval('this.' + subelement+'.type') == "undefined") {
                                                this.SCORM2004_errorCode="408";
                                            } else {
                                                // Use ccmi.interactions.n.type value to check the right dataelement format
                                                interactiontype = eval('this.' + subelement+'.type');
                                                var nodes = new Array();
                                                if (this.SCORM2004_learner_response[interactiontype].delimiter != '') {
                                                    nodes = value.split(this.SCORM2004_learner_response[interactiontype].delimiter);
                                                } else {
                                                    nodes[0] = value;
                                                }
                                                if ((nodes.length > 0) && (nodes.length <= this.SCORM2004_learner_response[interactiontype].max)) {
                                                    expression = new RegExp(this.SCORM2004_learner_response[interactiontype].format);
                                                    for (var i=0; (i < nodes.length) && (this.SCORM2004_errorCode=="0"); i++) {
                                                        if (typeof this.SCORM2004_learner_response[interactiontype].delimiter2 != 'undefined') {
                                                            values = nodes[i].split(this.SCORM2004_learner_response[interactiontype].delimiter2);
                                                            if (values.length == 2) {
                                                                matches = values[0].match(expression);
                                                                if (matches == null) {
                                                                    this.SCORM2004_errorCode = "406";
                                                                } else {
                                                                    var expression2 = new RegExp(this.SCORM2004_learner_response[interactiontype].format2);
                                                                    matches = values[1].match(expression2);
                                                                    if (matches == null) {
                                                                        this.SCORM2004_errorCode = "406";
                                                                    }
                                                                }
                                                            } else {
                                                                this.SCORM2004_errorCode = "406";
                                                            }
                                                        } else {
                                                            matches = nodes[i].match(expression);
                                                            if (matches == null) {
                                                                this.SCORM2004_errorCode = "406";
                                                            } else {
                                                                if ((nodes[i] != '') && (this.SCORM2004_learner_response[interactiontype].unique)) {
                                                                    for (var j=0; (j<i) && (errorCode=="0"); j++) {
                                                                        if (nodes[i] == nodes[j]) {
                                                                            this.SCORM2004_errorCode = "406";
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                } else if (nodes.length > this.SCORM2004_learner_response[interactiontype].max) {
                                                    this.SCORM2004_errorCode = "351";
                                                    this.SCORM2004_diagnostic = "Data Model Element Pattern Too Long";
                                                }
                                            }
                                         break;
                                     case 'ccmi.interactions.n.correct_responses.n.pattern':
                                          subel= subelement.split('.');
                                          subel1= 'ccmi.interactions.'+subel[2];
                                            if (typeof eval('this.' + subel1+'.type') == "undefined") {
                                                this.SCORM2004_errorCode="408";
                                            } else {
                                                // Use ccmi.interactions.n.type value to check the right
                                                var interactiontype = eval('this.' + subel1+'.type');
                                                var interactioncount = eval('this.' + parentelement+'._count');
                                                // trap duplicate values, which is not allowed for type choice
                                                if (interactiontype == 'choice') {
                                                    for (var i=0; (i < interactioncount) && (this.SCORM2004_errorCode=="0"); i++) {
                                                       if (eval('this.' + parentelement+'.N'+i+'.pattern') == value) {
                                                           this.SCORM2004_errorCode = "351";
                                                       }
                                                    }
                                                }
                                                var nodes = new Array();
                                                if (this.SCORM2004_correct_responses[interactiontype].delimiter != '') {
                                                    nodes = value.split(this.SCORM2004_correct_responses[interactiontype].delimiter);
                                                } else {
                                                    nodes[0] = value;
                                                }

                                                if ((nodes.length > 0) && (nodes.length <= this.SCORM2004_correct_responses[interactiontype].max)) {
                                                    this.SCORM2004_errorCode = this.SCORM2004_CRcheckValueNodes (element, interactiontype, nodes, value, errorCode);
                                                } else if (nodes.length > this.SCORM2004_correct_responses[interactiontype].max) {
                                                    this.SCORM2004_errorCode = "351";
                                                    this.SCORM2004_diagnostic = "Data Model Element Pattern Too Long";
                                                }
                                            }
                                         break;
                                    }
                                }
                            }
                        }
                        //Store data
                        if (this.SCORM2004_errorCode == "0") {
                            if ((typeof eval('this.SCORM2004_datamodel["' + elementmodel + '"].range')) != "undefined") {
                                range = eval('this.SCORM2004_datamodel["' + elementmodel + '"].range');
                                ranges = range.split('#');
                                value = value*1.0;

                                if (value >= ranges[0]) {
                                    if ((ranges[1] == '*') || (value <= ranges[1])) {
                                        this.DoSetLog('SetValue(' + element + ',' + value + ') -> OK ');
                                        eval('this.' + element+'=value;');
                                        this.SCORM2004_errorCode = "0";
                                        return "true";
                                    } else {
                                        this.SCORM2004_errorCode = '407';
                                    }
                                } else {
                                    this.SCORM2004_errorCode = '407';
                                }
                            } else {
                                this.DoSetLog('SetValue(' + element + ',' + value + ') -> OK ');
                                eval('this.' + element+'=value;');
                                this.SCORM2004_errorCode = "0";
                                return "true";
                            }
                        }
                    } else {
                        this.SCORM2004_errorCode = "406";
                    }
                } else {
                    this.SCORM2004_errorCode = "404";
                }
            } else {
                this.SCORM2004_errorCode = "401"
            }
        } else {
            this.SCORM2004_errorCode = "351";
        }
  } else {
      if (this.SCORM2004_Terminated) {
          this.SCORM2004_errorCode = "133";
      } else {
          this.SCORM2004_errorCode = "132";
      }
  }
  this.DoSetLog('SetValue : '+this.GetErrorString(this.SCORM2004_errorCode));
  return "false";
};

//SCORM 2004 RTE API Terminate
SCORMAPI.prototype.Terminate = function() {
    this.SCORM2004_errorCode = "0";
    if ((this.SCORM2004_Initialized) && (!this.SCORM2004_Terminated)) {
        this.DoSetLog('In Terminate');
        var AJAXResult = this.SCORM2004_StoreData(this.ccmi, true);
        result = ('true' == AJAXResult) ? 'true' : 'false';
        this.SCORM2004_Initialized = ('true' == result)? '0' : '101'; // General exception for any AJAX fault
        this.SCORM2004_Initialized = false;
        this.SCORM2004_Terminated = true;
        return result;
    } else {
        if (this.SCORM2004_Terminated) {
            this.SCORM2004_errorCode = "113";
        } else {
            this.SCORM2004_errorCode = "112";
        }
    }
    this.DoSetLog('Terminate : '+this.GetErrorString(this.SCORM2004_errorCode));
    return "false";
};

//SCORM 2004 RTE API Commit
SCORMAPI.prototype.Commit = function() {

  if ((this.SCORM2004_Initialized) && (!this.SCORM2004_Terminated)) {
      this.DoSetLog('In Commit');
      this.SCORM2004_errorCode = "0";
      this.SCORM2004_diagnostic = "";
      var AJAXResult = this.SCORM2004_StoreData(this.ccmi, false);
      var result = ('true' == AJAXResult) ? 'true' : 'false';
      this.SCORM2004_errorCode = ('true' == result)? '0' : '101'; // General exception for any AJAX fault

      if ('false' == result) {
          this.SCORM2004_diagnostic = "Failure calling the Commit remote callback: the server replied with HTTP Status " + AJAXResult;
      }
      return result;
  } else {
      if (this.SCORM2004_Terminated) {
          this.SCORM2004_errorCode = "143";
      } else {
          this.SCORM2004_errorCode = "142";
      }
  }
  this.DoSetLog('Commit : '+this.GetErrorString(this.SCORM2004_errorCode));
  return "false";
};

//---------------------------------------------------------------------------------
//Error Handling Functions
//SCORM 2004 RTE API GetLastError
SCORMAPI.prototype.GetLastError = function() {
  return this.SCORM2004_errorCode;
};

//SCORM 2004 RTE API this.GetErrorString
SCORMAPI.prototype.GetErrorString = function(errNo) {
  switch (errNo) {
    case '0':
      return 'No error';
    case '101':
      return 'General exception';
    case '102':
      return 'General Inizialization Failure';
    case '103':
      return 'Already Initialized';
    case '104':
      return 'Content Instance Terminated';
    case '111':
      return 'General Termination Failure';
    case '112':
      return 'Termination Before Inizialization';
    case '113':
      return 'Termination After Termination';
    case '122':
      return 'Retrieve Data Before Initialization';
    case '123':
      return 'Retrieve Data After Termination';
    case '132':
      return 'Store Data Before Inizialization';
    case '133':
      return 'Store Data After Termination';
    case '142':
      return 'Commit Before Inizialization';
    case '143':
      return 'Commit After Termination';
    case '201':
      return 'General Argument Error';
    case '301':
      return 'General Get Failure';
    case '351':
      return 'General Set Failure';
    case '391':
      return 'General Commit Failure';
    case '401':
      return 'Undefinited Data Model';
    case '402':
      return 'Unimplemented Data Model Element';
    case '403':
      return 'Data Model Element Value Not Initialized';
    case '404':
      return 'Data Model Element Is Read Only';
    case '405':
      return 'Data Model Element Is Write Only';
    case '406':
      return 'Data Model Element Type Mismatch';
    case '407':
      return 'Data Model Element Value Out Of Range';
    case '408':
      return 'Data Model Dependency Not Established';
    default:
      return 'Default error code';
  }
};

//SCORM 2004 RTE API GetDiagnostic
SCORMAPI.prototype.GetDiagnostic = function() {
  return this.SCORM2004_diagnostic;
};

//SCORM 2004 General functions SCORM2004_DuplicatedPA
SCORMAPI.prototype.SCORM2004_DuplicatedPA = function(element, parent, value) {
    var found = false;
    var elements = eval('this' . parent+'._count');
    for (var n=0;(n < elements) && (!found);n++) {
        if ((parent+'.N'+n+'.pattern' != element) && (eval('this' . parent+'.N'+n+'.pattern') == value)) {
            found = true;
        }
    }
    return found;
};

//SCORM 2004 General functions SCORM2004_DuplicatedID
SCORMAPI.prototype.SCORM2004_DuplicatedID = function(element, parent, value) {
    var found = false;
    var elements = eval('this.' + parent + '._count');
    for (var n=0;(n < elements) && (!found);n++) {
        if ((parent + '.N' + n + '.id' != element) && (eval('this.' + parent + '.N' + n + '.id') == value)) {
            found = true;
        }
    }
    return found;
};

//SCORM 2004 General functions SCORM2004_AddTime
SCORMAPI.prototype.SCORM2004_AddTime = function(first, second) {
    var timestring = 'P';
    var matchexpr = /^P((\d+)Y)?((\d+)M)?((\d+)D)?(T((\d+)H)?((\d+)M)?((\d+(\.\d{1,2})?)S)?)?$/;
    var firstarray = first.match(matchexpr);
    var secondarray = second.match(matchexpr);
    if ((firstarray != null) && (secondarray != null)) {
        var firstsecs=0;
        if(parseFloat(firstarray[13],10)>0){ firstsecs=parseFloat(firstarray[13],10); }
        var secondsecs=0;
        if(parseFloat(secondarray[13],10)>0){ secondsecs=parseFloat(secondarray[13],10); }
        var secs = firstsecs+secondsecs;  //Seconds
        var change = Math.floor(secs/60);
        secs = Math.round((secs-(change*60))*100)/100;
        var firstmins=0;
        if(parseInt(firstarray[11],10)>0){ firstmins=parseInt(firstarray[11],10); }
        var secondmins=0;
        if(parseInt(secondarray[11],10)>0){ secondmins=parseInt(secondarray[11],10); }
        var mins = firstmins+secondmins+change;   //Minutes
        change = Math.floor(mins / 60);
        mins = Math.round(mins-(change*60));
        var firsthours=0;
        if(parseInt(firstarray[9],10)>0){ firsthours=parseInt(firstarray[9],10); }
        var secondhours=0;
        if(parseInt(secondarray[9],10)>0){ secondhours=parseInt(secondarray[9],10); }
        var hours = firsthours+secondhours+change; //Hours
        change = Math.floor(hours/24);
        hours = Math.round(hours-(change*24));
        var firstdays=0;
        if(parseInt(firstarray[6],10)>0){ firstdays=parseInt(firstarray[6],10); }
        var seconddays=0;
        if(parseInt(secondarray[6],10)>0){ firstdays=parseInt(secondarray[6],10); }
        var days = Math.round(firstdays+seconddays+change); // Days
        var firstmonths=0;
        if(parseInt(firstarray[4],10)>0){ firstmonths=parseInt(firstarray[4],10); }
        var secondmonths=0;
        if(parseInt(secondarray[4],10)>0){ secondmonths=parseInt(secondarray[4],10); }
        var months = Math.round(firstmonths+secondmonths);
        var firstyears=0;
        if(parseInt(firstarray[2],10)>0){ firstyears=parseInt(firstarray[2],10); }
        var secondyears=0;
        if(parseInt(secondarray[2],10)>0){ secondyears=parseInt(secondarray[2],10); }
        var years = Math.round(firstyears+secondyears);
    }
    if (years > 0) {
        timestring += years + 'Y';
    }
    if (months > 0) {
        timestring += months + 'M';
    }
    if (days > 0) {
        timestring += days + 'D';
    }
    if ((hours > 0) || (mins > 0) || (secs > 0)) {
        timestring += 'T';
        if (hours > 0) {
            timestring += hours + 'H';
        }
        if (mins > 0) {
            timestring += mins + 'M';
        }
        if (secs > 0) {
            timestring += secs + 'S';
        }
    }
    return timestring;
};

//SCORM 2004 General functions SCORM2004_TotalTime
SCORMAPI.prototype.SCORM2004_TotalTime = function() {
    var total_time = this.SCORM2004_AddTime(this.ccmi.total_time, this.ccmi.session_time);
    this.data[this.underscore('cmi__session_time')] = this.ccmi.session_time;
    this.data[this.underscore('cmi__total_time')] = total_time;
    return '&'+ this.underscore('ccmi.total_time') + '=' + encodeURIComponent(total_time);
};

SCORMAPI.prototype.SCORM2004_CloneObj = function(obj){
    if(obj == null || typeof(obj) != 'object') {
        return obj;
    }

    var temp = new obj.constructor(); // changed (twice)
    for(var key in obj) {
        temp[key] = this.SCORM2004_CloneObj(obj[key]);
    }

    return temp;
};

//SCORM 2004 General functions SCORM2004_CollectData
SCORMAPI.prototype.SCORM2004_CollectData =  function(data, parent) {
    var datastring = '';

    for (property in data) {
        if (typeof data[property] == 'object') {
            datastring += this.SCORM2004_CollectData(data[property], parent + '.' + property);
        } else {
            var element = parent + '.' + property;
            var expression = new RegExp(this.SCORM2004_CMIIndexStore, 'g');
            var elementmodel = String(element).replace(expression,'.n.');

            if ((eval('typeof this.SCORM2004_datamodel["' + element + '"]')) == "undefined"
                && (eval('typeof this.SCORM2004_datamodel["' + elementmodel + '"]')) != "undefined") {
                eval('this.SCORM2004_datamodel["' + element + '"] = this.SCORM2004_CloneObj(this.SCORM2004_datamodel["' + elementmodel + '"]);');
            }

            if ((typeof eval('this.SCORM2004_datamodel["' + elementmodel + '"]')) != "undefined") {
                if (eval('this.SCORM2004_datamodel["' + elementmodel + '"].mod') != 'r') {

                    var elementstring = '&' + this.underscore(element) + '=' + encodeURIComponent(data[property]);

                    if ((typeof eval('this.SCORM2004_datamodel["' + elementmodel + '"].defaultvalue')) != "undefined") {
                        if ((eval('this.SCORM2004_datamodel["' + elementmodel + '"].defaultvalue') != data[property] ||
                          eval('typeof(this.SCORM2004_datamodel["' + elementmodel + '"].defaultvalue)') != typeof(data[property])) ||
                          (
                            elementmodel == 'ccmi.score.max' ||
                            elementmodel == 'ccmi.score.raw' ||
                            elementmodel == 'ccmi.score.min' ||
                            elementmodel == 'ccmi.suspend_data')) {
                            datastring += elementstring;
                            // update the element default to reflect the current committed value
                            eval('this.SCORM2004_datamodel["' + element+'"].defaultvalue = data[property];');
                            var preElement = element.replace(/ccmi/g, 'cmi');
                            //this.data[this.underscore(preElement)] = encodeURIComponent(data[property]);
                            this.data[this.underscore(preElement)] = data[property];
                        } else if (elementmodel == 'ccmi.success_status' ||
                                elementmodel == 'ccmi.completion_status') {
                            datastring += elementstring;
                            // update the element default to reflect the current committed value
                            var preElement = element.replace(/ccmi/g, 'cmi');
                            //this.data[this.underscore(preElement)] = encodeURIComponent(data[property]);
                            this.data[this.underscore(preElement)] = data[property];
                        }
                    } else {
                        datastring += elementstring;
                        // update the element default to reflect the current committed value
                        eval('this.SCORM2004_datamodel["' + element+'"].defaultvalue = data[property];');
                        var preElement = element.replace(/ccmi/g, 'cmi');
                        //this.data[this.underscore(preElement)] = encodeURIComponent(data[property]);
                        this.data[this.underscore(preElement)] = data[property];
                    }
                } else {

                }
            }
        }

    }
    return datastring;
};

//SCORM 2004 General functions SCORM2004_StoreData
SCORMAPI.prototype.SCORM2004_StoreData = function(data, storetotaltime) {

    var _this = this;
    var datastring = '';
    this.statusChanged = true;

    if (storetotaltime) {
        if (this.ccmi.mode == 'normal') {
            if (this.ccmi.credit == 'credit') {
                if ((this.ccmi.completion_threshold != null) && (this.ccmi.progress_measure != null)) {
                    if (this.ccmi.progress_measure >= this.ccmi.completion_threshold) {
                        data.completion_status = this.SCORM2004_COMPLETED;
                    } else {
                        data.completion_status = this.SCORM2004_INCOMPLETE;
                    }
                }
                if ((this.ccmi.scaled_passed_score != null) && (this.ccmi.score.scaled != '')) {
                    if (this.ccmi.score.scaled >= this.ccmi.scaled_passed_score) {
                        data.success_status = this.SCORM2004_PASSED;
                    } else {
                        data.success_status = this.SCORM2004_FAILED;
                    }
                }
            }
        }
        datastring += this.SCORM2004_TotalTime();
    }

    datastring += this.SCORM2004_CollectData(data, 'ccmi');
    if (this.data.cmi_success_status == this.SCORM2004_PASSED && this.data.cmi_score_scaled == 1) {
        this.data.cmi_completion_status = this.SCORM2004_COMPLETED;
    } else {
        this.data.cmi_completion_status = this.SCORM2004_INCOMPLETE;
    }

    delete this.data.cmi_learner_id;
    delete this.data.cmi_learner_name;
    delete this.data.cmi_score_raw;
    delete this.data.cmi_score_max;
    delete this.data.cmi_score_min;
    delete this.data.cmi_score_scaled;
    delete this.data.cmi_mode;
    delete this.data.cmi_location;
    delete this.data.cmi_credit;
    delete this.data.cmi_exit;
    delete this.data.cmi_entry;
    delete this.data.cmi_suspend_data;
    delete this.data.cmi_completion_status;
    delete this.data.cmi_total_time;
    delete this.data.cmi_launch_data;

    this.data._token = this.token;
    var url = window.location.protocol + '//' + window.location.host;
    var pathname = window.location.pathname;
    var protocol = window.location.protocol + '//';

    var fullUrl = window.location.href;
    fullUrl = fullUrl.substr(protocol.length);
    fullUrl = fullUrl.substr(0, fullUrl.indexOf('/elearning/'));

    //url = url + "/elearning/json/" + this.scorm + "/" + this.sco + "/" + this.user + "/" + this.vs + "/putSCORM";
    //url = protocol + fullUrl + "/elearning/json/" + this.scorm + "/" + this.sco + "/" + this.user + "/" + this.vs + "/putSCORM";
    url = protocol + fullUrl + "/elearning/json/" + this.scorm + "/" + this.sco + "/" + this.user + "/" + this.vs + "/" + this.attempt + "/putSCORM";

    $.ajax({
          url: url,
          dataType: 'json',
          async: false,
          data: _this.data,
		      type: "POST",
          success: function(data){
          if(_this.data['cmi_core_lesson_status'] && _this.statusChanged) _this.sentResult = true;
            return _this.errorCode = 0;
          },
          error : function(error){
            console.log(error);
          }
    });

    return true;
};

//SCORM 2004 General function SCORM2004_CRcheckValueNodes
SCORMAPI.prototype.SCORM2004_CRcheckValueNodes = function(element, interactiontype, nodes, value, errorCode) {
    expression = new RegExp(this.SCORM2004_correct_responses[interactiontype].format);
    for (var i=0; (i < nodes.length) && (this.SCORM2004_errorCode == "0"); i++) {
        if (interactiontype.match('^(fill-in|long-fill-in|matching|performance|sequencing)$')) {
            result = this.SCORM2004_CRremovePrefixes(nodes[i]);
            this.SCORM2004_errorCode = result.errorCode;
            nodes[i] = result.node;
        }

        // check for prefix on each node
        if (this.SCORM2004_correct_responses[interactiontype].pre != '') {
            matches = nodes[i].match(this.SCORM2004_correct_responses[interactiontype].pre);
            if (matches != null) {
                nodes[i] = nodes[i].substr(matches[1].length);
            }
        }

        if (this.SCORM2004_correct_responses[interactiontype].delimiter2 != undefined) {
            values = nodes[i].split(this.SCORM2004_correct_responses[interactiontype].delimiter2);
            if (values.length == 2) {
                matches = values[0].match(expression);
                if (matches == null) {
                    this.SCORM2004_errorCode = "406";
                } else {
                    var expression2 = new RegExp(this.SCORM2004_correct_responses[interactiontype].format2);
                    matches = values[1].match(expression2);
                    if (matches == null) {
                        this.SCORM2004_errorCode = "406";
                    }
                }
            } else {
                 this.SCORM2004_errorCode = "406";
            }
        } else {
            matches = nodes[i].match(expression);
            //if ((matches == null) || (matches.join('').length == 0)) {
            if ((matches == null && value != "")||(matches == null && interactiontype=="true-false")){
                this.SCORM2004_errorCode = "406";
            } else {
                // numeric range - left must be <= right
                if (interactiontype == 'numeric' && nodes.length > 1) {
                    if (parseFloat(nodes[0]) > parseFloat(nodes[1])) {
                        this.SCORM2004_errorCode = "406";
                    }
                } else {
                    if ((nodes[i] != '') && (this.SCORM2004_correct_responses[interactiontype].unique)) {
                        for (var j=0; (j < i) && (errorCode=="0"); j++) {
                            if (nodes[i] == nodes[j]) {
                                this.SCORM2004_errorCode = "406";
                            }
                        }
                    }
                }
            }
        }
    } // end of for each nodes
    return this.SCORM2004_errorCode;
};

//SCORM 2004 General function SCORM2004_CRremovePrefixes
SCORMAPI.prototype.SCORM2004_CRremovePrefixes = function (node) {
    // check for prefixes lang, case, order
    // case and then order
    var seenOrder = false;
    var seenCase = false;
    var seenLang = false;
    this.SCORM2004_errorCode = "0";

    while (matches = node.match('^(\{(lang|case_matters|order_matters)=([^\}]+)\})')) {
        switch (matches[2]) {
            case 'lang':
                // check for language prefix on each node
                langmatches = node.match(this.CMILangcr);
                if (langmatches != null) {
                    lang = langmatches[3];
                    // check that language string definition is valid
                    if (lang.length > 0 && lang != undefined) {
                        if (validLanguages[lang.toLowerCase()] == undefined) {
                            this.SCORM2004_errorCode = "406";
                        }
                    }
                }
                seenLang = true;
            break;

            case 'case_matters':
                // check for correct case answer
                if (! seenLang && ! seenOrder && ! seenCase) {
                    if (matches[3] != 'true' && matches[3] != 'false') {
                        this.SCORM2004_errorCode = "406";
                    }
                }
                seenCase = true;
            break;

            case 'order_matters':
                // check for correct case answer
                if (! seenCase && ! seenLang && ! seenOrder) {
                    if (matches[3] != 'true' && matches[3] != 'false') {
                        this.SCORM2004_errorCode = "406";
                    }
                }
                seenOrder = true;
            break;

            default:
            break;
        }
        node = node.substr(matches[1].length);
    }
    return {'errorCode': this.SCORM2004_errorCode, 'node': node};
};

SCORMAPI.prototype.Ping = function() {

};

SCORMAPI.prototype.PopupError = function(msg) {
   $("<p>"+msg+'</p>' ).dialog({
      title : 'Error',
      modal: true,
        buttons: {
          Ok: function() {
          $( this ).dialog( "close" );
        }
      }
    });
};

SCORMAPI.prototype.LeavedPage = function() {

};

SCORMAPI.prototype.clear = function() {
  this.data = {
    obid: this.options.obid,
    cmicsi: this.options.cmicsi
  };
  this.interactions = {
    obid: this.options.obid,
    cmicsi: this.options.cmicsi
  };
  return console.log('');
};

SCORMAPI.prototype.open = function(session_id, url) {
  this.setSessionId(session_id);
  return window.open(url);
};

SCORMAPI.prototype.setSessionId = function(session_id) {
  this.options.obid = session_id;
  return this.clear();
};

SCORMAPI.prototype.DoSetLog = function(log) {
  console.log(log);
};
