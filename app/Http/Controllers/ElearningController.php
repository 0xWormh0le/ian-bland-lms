<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Course;
use App\Module;
use App\Elearning;
use Alert;
use Illuminate\Support\Facades\Storage;
use App\SCORMDispatchAPI\SCORMDispatchService;
use GuzzleHttp\Client;
use App\SCORM ;
use Illuminate\Support\Facades\Input;
//use App\Helpers\libs\SCORMLib;


class ElearningController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        $scormService = new SCORMDispatchService;
        $courseService = $scormService->getCourseService();
        $redirectUrl = route('courses.index');
        $courses = $courseService->GetCourseList();
        $datas = [];

        foreach($courses as $course)
        {
            $id = $course->getCourseId();
            $summary = $courseService->GetCourseSummary($id, $redirectUrl);
            $launch_url = $courseService->GetPreviewUrl($id, $redirectUrl);

            $datas[] = [
                'title' => $course->getTitle(),
                'added_date' => $course->getAddedDate(),
                'complete' => $summary->getComplete(),
                'success' => $summary->getSuccess(),
                'score' => $summary->getScore(),
                'time' => $summary->getTotalTime(),
                'launch_url' => $launch_url,
            ];
        }
        return view('courses.index', compact('datas'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($slug)
    {
        $title = trans('controllers.add_new').session('moduleLabel')['elearning'];
        $course = Course::findBySlug($slug);
        if($course)
        {
            $scormService = new SCORMDispatchService;
            $courseService = $scormService->getCourseService();
            $url = $courseService->GetUploadCourseUrl();
            $breadcrumbs = [
                route('courses.index') => trans('controllers.list_of_courses'),
                route('courses.show', $course->slug) => $course->title,
                '' => $title,
            ];
            return view('elearning.form', compact('title', 'url', 'breadcrumbs', 'course'));
        }
        return redirect('courses.index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $rules = [
            'title' => 'required',
          //  'scorm_file' => 'required|file|mimes:zip',
        ];

        $request->validate($rules);

        $record = new Module;

        $message = null;
        $record = $this->save($record, $request, $message);
        if($record)
        {
            Alert::success(__('messages.save_success'))->autoclose(3000);
            return redirect()->route('courses.show', ['id '=> $record->course->slug]);
            // return redirect()->route('elearning.show', ['course'=>$record->course->slug, 'module' => $record->slug]);
        }
        Alert::error(__('messages.save_failed') . '<br>'. $message)->autoclose(3000);
        return redirect()->back()->withInput();
    }
    /**
     * Display the specified resource.
     *
     * @param  string  $course_slug
     * @param  string  $module_slug
     * @return \Illuminate\Http\Response
     */
    public function show($course_slug, $module_slug)
    {

        $course = Course::findBySlug($course_slug);
        $data = Module::findBySlug($module_slug);

        if($data)
        {
            $breadcrumbs = [
                route('courses.index') => trans('controllers.courses'),
                route('courses.show', $course_slug) => $course->title,
                '' => $data->title,
            ];
            $title = trans('controllers.details_of').$data->title;
            return view('elearning.details', compact('title', 'breadcrumbs', 'data'));
        }
        Alert::error(__('messages.invalid_request'));
        return redirect()->route('courses.show', $course_slug);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  string  $course_slug
     * @param  string  $module_slug
     * @return \Illuminate\Http\Response
     */
    public function edit($course_slug, $module_slug)
    {
        $course = Course::findBySlug($course_slug);
        $data = Module::findBySlug($module_slug);

        $elearning = Elearning::where('module_id', $data->id)->first();
        $data->scorm = '';
        $data->description = '';

        if($elearning) {
          $data->description = $elearning->description ;

          if($elearning->scorm_id > 0)
          {
            $scorm  =  SCORM::where("id", $elearning->scorm_id)->first();
            $data->scorm = $scorm->reference;
          }
        }

        if($data)
        {
            $breadcrumbs = [
                route('courses.index') => trans('controllers.courses'),
                route('courses.show', $course_slug) => $course->title,
                route('elearning.show', ['course' => $course_slug, 'module'=> $module_slug]) => $data->title,
                '' => trans('controllers.edit'),
            ];
            $title = trans('controllers.edit').' '.$data->title;
            return view('elearning.form', compact('title', 'breadcrumbs', 'data', 'course'));
        }
        Alert::error(__('messages.invalid_request'));
        return redirect()->route('courses.show', $course_slug);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'title' => 'required',
        ];
        $request->validate($rules);

        $record = Module::find(decrypt($id));
        if($record)
        {
            $message = null;
            $record = $this->save($record, $request, $message);
            if($record)
            {
                Alert::success(__('messages.save_success'))->autoclose(3000);
                return redirect()->route('courses.show', ['id '=> $record->course->slug]);
                // return redirect()->route('elearning.show', ['course'=>$record->course->slug, 'module'=>$record->slug]);
            }
            Alert::error(__('messages.save_failed').'<br>'.$message)->autoclose(3000);
            return redirect()->back()->withInput();
        }
        Alert::error(__('messages.record_not_found'))->autoclose(3000);
        return redirect()->back()->withInput();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

	public function delTree($dir)
			{
        if(\File::exists($dir))
        {
				$files = array_diff(scandir($dir), array('.', '..'));

				foreach ($files as $file) {
					(is_dir("$dir/$file")) ? $this->delTree("$dir/$file") : unlink("$dir/$file");
				}

				return rmdir($dir);
        }
        return true;
			}

    public function destroy($id)
    {
        $record = Module::find(decrypt($id));
        if($record)
        {
            $detail = Elearning::where('module_id', $record->id)->first();
            if(@$detail->scorm_id)
            {
                //$scormService = new SCORMDispatchService;
                //$courseService = $scormService->getCourseService();
                //$result = $courseService->DeleteCourse($detail->scorm_id);

				$scorm =  SCORM::where('id', $detail->scorm_id)->first();

				$myfolder = $scorm->repository;
				system("sudo rm -rf ".escapeshellarg($myfolder));
				$this->delTree($myfolder);
            }



			//echo 'R: ' . $scorm->repository;
			//exit();
           if($detail)
            $detail->delete();

            $record->deleted_by = \Auth::id();
            $record->deleted_at = date('Y-m-d H:i:s');
            if($record->save())
            {
                Alert::success(__('messages.delete_success'))->autoclose(3000);
                return redirect()->route('courses.show', $record->course->slug);
            }
            Alert::error(__('messages.delete_failed'))->autoclose(3000);
            return redirect()->route('courses.index');
        }
        Alert::error(__('messages.record_not_found'))->autoclose(3000);
        return redirect()->route('courses.index');
    }

    public static function save($record, $request, &$error)
    {

        $record->course_id = $request->course_id;
        $record->title = $request->title;
        $record->order_no = $request->order_no;
        $record->type = 'Elearning';

        $scorm = 0 ;

        if($record->id)
        {
            $record->slug = null;
            $record->updated_by = \Auth::id();
        }
        else
            $record->created_by = \Auth::id();

        if($record->save())
        {

            $detail = Elearning::where('module_id', $record->id)->first();


            if(!$detail)
                $detail = new Elearning;

            $detail->course_id = $record->course_id;
            $detail->module_id = $record->id;
            $detail->title = $record->title;
            $detail->description = $request->description;
            $detail->course_stream = @$request->course_stream ? true : false;

            if($request->hasFile('scorm_file'))
            {

              header('Access-Control-Allow-Origin: *');
              include_once(app_path().'/Helpers/libs/scormlib.php');//.'/Helpers/libs/scormlib.php'
              include_once(app_path().'/Helpers/libs/settings.php');//.'/Helpers/libs/settings.php'
              include_once(app_path().'/Helpers/libs/LIB_parse.php');//.'/Helpers/libs/functions.php'

              $scormLib = new \App\Helpers\libs\SCORMLib;

              $repository = public_path() . '/uploads/';//Config::get('scorm.setting.repository');
              $scorm_assets = public_path() . '/scorm_templates/scorm_12';
              $random_key = $scormLib->create_guid();
              $reference = $random_key;

              $results['status'] = FALSE;
              $results['message'] = "An error occurred while storing the contents - Please try again.";

              //TODO check uploaded file is zip
              $files = $_FILES['scorm_file']['name'];


              $path_parts = pathinfo($_FILES['scorm_file']['name']);
              $extension = strtolower($path_parts['extension']);

              $oldmask = umask(0);

              if (in_array($extension, $allowedExtensions) || $extension == 'zip') {

                if(!file_exists($repository))
      	            mkdir($repository, 0777);

                $src_dir = $repository . \Auth::user()->org_id . '/';

      	        if(!file_exists($src_dir))
      	            mkdir($src_dir, 0777);
      //scorm_file
      //UploadedSCORM
                $file_name = basename($_FILES['scorm_file']['name']);

                $full_path_name = $src_dir . $reference . '/' . $file_name;

          	        if (! mkdir($src_dir . $reference, 0777)) {
          	            return false;
          	        }

          			move_uploaded_file($_FILES['scorm_file']['tmp_name'], $full_path_name);




                if (strtolower($extension) != 'mp4' && strtolower($extension) != 'zip') {
          				$destpath = $src_dir . $reference . '/';

          				// Convert to mp4 file START
          				$ext = pathinfo($file_name, PATHINFO_EXTENSION);
                        $name = substr($filename, 0,strrpos($filename,'.'));
          				$destname = $src_dir . $reference . '/' . $name . '.mp4';

          				$call = "ffmpeg -i " . $full_path_name . "  -vf scale=320:240 -c:v libx264 -preset fast -c:a aac " . $destname;

          				include_once(app_path().'/Helpers/libs/ffmpeg_cmd.php');//.'/Helpers/libs/ffmpeg_cmd.php'

          				$convert = (popen($convert_call, "r"));


          		        if (ob_get_level() == 0)
          		            ob_start();

          		        while(!feof($convert)) {

          		            $buffer = fgets($convert);
          		            $buffer = trim(htmlspecialchars($buffer));

          		            ob_flush();
          		            flush();
          		            sleep(0);
          		        }

          				pclose($convert);
          				ob_end_flush();

          				$extension = 'mp4';
          				$full_path_name = $destname;
          			}




                $scormdir = (new self)->make_upload_directory($reference, $src_dir);

                $scormdir = (new self)->cleardoubleslashes($scormdir);
                $file_size = $_FILES['scorm_file']['size'];

                $results['scormdir'] = $scormdir;
                $results['full_path_name'] = $full_path_name;
                $results['size'] = $file_size;

                $path_parts = pathinfo((new self)->cleardoubleslashes($full_path_name));

                $results['zippath'] = $path_parts["dirname"];       //The path of the zip file
                $results['zipfilename'] = $path_parts["basename"];  //The name of the zip file
                $results['extension'] = $path_parts["extension"];    //The extension of the file
                $results['filename'] = $path_parts["filename"];
                $popup_file = '';
                $version = 'SCORM_12';

                $results['status'] = true;


                if ($path_parts["extension"] == 'mp4') {

                  $zipFile = $scorm_assets;
        		    	$fileTozip = $src_dir . $reference . '/' . str_replace(" ", "", $path_parts["filename"]) . '.zip';
        				  $files = glob($zipFile . '/*');

          				$dest = $src_dir . $reference . '/';

          				\Zipper::make($fileTozip)->add($files)->add($dest)->close();

          				$full_path_name = $fileTozip;
        			 }

               if ((new self)->unzip_file($full_path_name, $scormdir)) {


                 $add_file_name_flag = 0 ;
                 $manifest = $repository. \Auth::user()->company_id . '/' . $reference . '/imsmanifest.xml';

				if(!file_exists($manifest))
                {
                  $manifest = $repository. $reference . '/imsmanifest.xml';
                }

//echo "<pre>R: ";print($manifest); exit();
                if(!file_exists($manifest))
                {
                  $manifest = $repository. \Auth::user()->company_id . '/' . $reference .'/'. $results['filename']. '/imsmanifest.xml';
                  $add_file_name_flag = 1;
                }

                $popup_file = $repository. \Auth::user()->company_id . '/' . $reference . '/index.html';

                 if(!file_exists($manifest))
                 {
                   $popup_file = $repository. \Auth::user()->company_id . '/' . $reference.'/'. $results['filename'] . '/index.html';

                 }

                 $results['reference'] = $manifest;

                 if ($extension == 'mp4') {

                   $xml_data = file_get_contents($manifest);
                   $xml_data = str_replace("%%ORG%%", $_POST["orgname"], $xml_data);
                   $xml_data = str_replace("%%TITLE%%", $_POST["title"], $xml_data);
                   $xml_data = str_replace("%%VIDNAME%%", $file_name, $xml_data);
                   $xml_data = str_replace("%%ASSETID%%", $_POST["assetid"], $xml_data);

                   $myfile = fopen($manifest, "w");

                   fwrite($myfile, $xml_data);
                   fclose($myfile);

                   $video_file = $repository. \Auth::user()->org_id . '/' . $reference . '/video.html';
                   $video_data = file_get_contents($video_file);
                   $video_data = str_replace("%%VIDNAME%%", $file_name, $video_data);
                   $video_data = str_replace("%%WIDTH%%", $_POST["width"], $video_data);
                   $video_data = str_replace("%%HEIGHT%%", $_POST["height"], $video_data);
                   $video_data = str_replace("%%ASSETID%%", $_POST["assetid"], $video_data);

                   $myfile = fopen($video_file, "w");

                   fwrite($myfile, $video_data);
                   fclose($myfile);
                 }

               if (file_exists($manifest)) {

                   $validation_manifest = $scormLib->checkManifestFile($manifest);

                   if ($validation_manifest->resultFlag) {

                     // Adding the SCORM data into lms_scorm table
                     try {

                       $objSCORM = new SCORM;

                       $org_id =  \Auth::user()->company_id ;

                       if($org_id == "") $org_id = 0 ;

                       $objSCORM->org_id = $org_id;
                       $objSCORM->course_id = Input::get("course_id");
                       $objSCORM->scormname = $path_parts["filename"];
                       $objSCORM->reference = $path_parts["basename"];
                       $objSCORM->filesize = $file_size;

                       if($add_file_name_flag == 1)
                            $objSCORM->repository = $repository . \Auth::user()->org_id . '/'.$objSCORM->scormname.'/' . $reference;
                       else {
                          $objSCORM->repository = $repository . \Auth::user()->org_id . '/' . $reference;
                       }
                       $objSCORM->format = $validation_manifest->version;
                    //   $objSCORM->targetid = $reference;
                    if($add_file_name_flag == 1)
                        $objSCORM->targetid = $reference. '/'.$objSCORM->scormname;
                    else
                        $objSCORM->targetid = $reference;

                        if($objSCORM->save()){

                                    /*   $elearning = new Elearning();
                                       $elearning->org_id = $objSCORM->org_id ;
                                       $elearning->course_id = $objSCORM->course_id ;
                                       $elearning->scorm_id = $objSCORM->id ;
                                       $elearning->title = $objSCORM->scormname ;
                                       $elearning->versions = is_numeric($ver = substr($objSCORM->format, stripos($objSCORM->format, 'SCORM_')+6)) ? $ver : null;
                                       $elearning->registrations = 0;
                                       $elearning->created_at = $objSCORM->created_at;
                                       $elearning->updated_at = $objSCORM->updated_at;
                                       $elearning->save();*/
                                   }

                       // Getting the last PK

                        $scorm = $objSCORM->id; //DB::getPdo()->lastInsertId();

                        $results['status'] = TRUE;
                        $results['scorm'] = $scorm;


                        $version = $validation_manifest->version;
                        $launch = $scormLib->parseSCORM($manifest, $scorm);

                        if ($launch != "") {
                            $results['message'] = "[ " . $results['zipfilename'] . " ] File Stored Successfully.";
                        } else {
                            $results['message'] = "An error occurred while storing the contents - this is mostly an issue with the course you are trying to upload - Please try again.";
                        }

                     } catch(Exception $ex) {
                       $results['message'] = "An error occurred while storing the contents - this is mostly an issue with the course you are trying to upload - Please try again.";
                       $error = $results['message'];
                       return false;
                     }

                   } else {
                     $results['message'] = "An error occurred while validating the contents - this is mostly an issue with the course you are trying to upload - Please try again.";
                     $error = $results['message'];
                     return false;
                   }
                 } else {
                   $results['message'] = "There is no any IMSManifest.XML file. Check structure of your SCORM zip - Please try again.";
                   $error = $results['message'];
                   return false;
                 }
               }

              }else {
                      $results['message'] = "It need to upload SCORM Package zip file or video mp4 file. - Please try again.";
                      $error = $results['message'];
                      return false;
          		}

              	umask($oldmask);



              /*
                $scormService = new SCORMDispatchService;
                $courseService = $scormService->getCourseService();

                $oldIsDeleted = true;
                // if has Scorm ID
                if($detail->scorm_id)
                {
                    $result = $courseService->DeleteCourse($detail->scorm_id);
                    if ($result['data']['status'] !== true)
                        $oldIsDeleted = false;
                    else{
                        \App\CourseResult::where('module_id', $record->id)->update(['scorm_regid' => null]);
                    }
                }
                if($oldIsDeleted)
                {
                    $url = $courseService->GetUploadCourseUrl();

                    // Store file to local disk for temporary
                    $dir = uniqid();
                    $file = $request->file('scorm_file');
                    $filename = $file->getClientOriginalName();
                    $filepath = $file->storeAs($dir, $filename);
                    $fullpath = Storage::disk('local')->path($filepath);

                    // Upload elearning SCORM file to API with Guzzle client
                    $client = new Client();
                    $res = $client->request('POST', $url, [
                        'multipart' => [
                            [
                                'name'     => 'title',
                                'contents' => $request->title
                            ],
                            [
                                'name'     => 'filedata',
                                'contents' => fopen($fullpath, 'r')
                            ],
                        ]
                    ]);
                    $result= json_decode($res->getBody(), true);
                    if($result)
                    {
                        // Remove temporary path & directory
                        Storage::disk('local')->delete($filepath);
                        Storage::disk('local')->deleteDirectory($dir);
                        $scorm_id = $result['data']['status'];
                    }
                }
                $detail->scorm_id = $scorm_id;

                */

            }

           if($scorm > 0)
            $detail->scorm_id = $scorm;


            $detail->save();

            return $record;
        }
        return false;
    }

    //Replace 1 or more slashes or backslashes to 1 slash
  public function cleardoubleslashes($path) {
        return preg_replace('/(\/|\\\){1,}/','/',$path);
    }

     public function make_upload_directory($directory, $zipPath) {
      $currdir = $zipPath;
        umask(0000);
        if (!file_exists($currdir)) {
            if (! mkdir($currdir, 0777)) {
                return false;
            }
        }

        $dirarray = explode('/', $directory);

        foreach ($dirarray as $dir) {
            $currdir = $currdir .'/'. $dir;

            if (! file_exists($currdir)) {
              $isResult = mkdir($currdir, 0777);
                if (!$isResult & is_bool($isResult)) {
                    return false;
                }
            }
        }

        return $currdir;
    }

    public function unzip_file ($zipfile, $destination = '') {
      //Unzip one zip file to a destination dir
      //Both parameters must be FULL paths
      //If destination isn't specified, it will be the
      //SAME directory where the zip file resides.

        //Extract everything from zipfile
        $path_parts = pathinfo($this->cleardoubleslashes($zipfile));
        $zippath = $path_parts["dirname"];       //The path of the zip file
        $zipfilename = $path_parts["basename"];  //The name of the zip file
        $extension = $path_parts["extension"];    //The extension of the file

        //If no file, error
        if (empty($zipfilename)) {
            return false;
        }

        //If no extension, error
        if (empty($extension)) {
            return false;
        }

        //Clear $zipfile
        $zipfile = $this->cleardoubleslashes($zipfile);

        //Check zipfile exists
        if (!file_exists($zipfile)) {
            return false;
        }

        //If no destination, passed let's go with the same directory
        if (empty($destination)) {
            $destination = $zippath;
        }

        //Clear $destination
        $destpath = rtrim($this->cleardoubleslashes($destination), "/");

        //Check destination path exists
        if (!is_dir($destpath)) {
            return false;
        }

        //Check destination path is writable. TODO!!

        //Everything is ready:
        //    -$zippath is the path where the zip file resides (dir)
        //    -$zipfilename is the name of the zip file (without path)
        //    -$destpath is the destination path where the zip file will uncompressed (dir)

        $list = array();
        include_once(app_path().'/Helpers/libs/pclzip.lib.php');
        $archive = new \App\Helpers\libs\PclZip($this->cleardoubleslashes("$zippath/$zipfilename"));
      //  $archive = new PclZip($this->cleardoubleslashes("$zippath/$zipfilename"));

        /*if (!$list = $archive->extract(PCLZIP_OPT_PATH, $destpath,
                                       PCLZIP_CB_PRE_EXTRACT, 'unzip_cleanfilename',
                                       PCLZIP_OPT_EXTRACT_DIR_RESTRICTION, $destpath)) {
            //return false;
        }*/
          \Zipper::make($zipfile)->extractTo($destination);
        return true;
    }
}
