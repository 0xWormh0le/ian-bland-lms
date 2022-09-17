<?php namespace App\Helpers\libs;


use Exception;
use App\SCORMSCOData;
use App\SCORMSCOs;
use App\SCORMSeqMapinfo;
use App\SCORMSeqObjective;
use App\SCORMSeqRollupRule;
use App\SCORMSeqRollupRuleCond;
use App\SCORMSeqRuleCond;
use App\SCORMSeqRuleConds;
use stdClass;
use DB;

class SCORMLib {
    /**
     * Create upload file handler
     * @param string $field_name Form field name
     */
  /*  function SCORMLib ()
    {

    }*/

    function scorm_get_resources($blocks) {
        $resources = array();
        foreach ($blocks as $block) {
            if ($block['name'] == 'RESOURCES') {
                foreach ($block['children'] as $resource) {
                    if ($resource['name'] == 'RESOURCE') {
                        $resources[$this->addslashes_js($resource['attrs']['IDENTIFIER'])] = $resource['attrs'];
                    }
                }
            }
        }
        return $resources;
    }

    function addslashes_js($var) {
        if (is_string($var)) {
            $var = str_replace('\\', '\\\\', $var);
            $var = str_replace(array('\'', '"', "\n", "\r", "\0"), array('\\\'', '\\"', '\\n', '\\r', '\\0'), $var);
            $var = str_replace('</', '<\/', $var);   // XHTML compliance
        } else if (is_array($var)) {
            $var = array_map('addslashes_js', $var);
        } else if (is_object($var)) {
            $a = get_object_vars($var);
            foreach ($a as $key=>$value) {
              $a[$key] = addslashes_js($value);
            }
            $var = (object)$a;
        }
        return $var;
    }

    public function checkManifestFile($manifest)
    {
        $validation = new stdClass();
        $validation->resultFlag = false;
        $validation->version = "";

        if (is_file($manifest))
        {
            $_dirRoot = substr($manifest, 0, stripos($manifest, basename($manifest)));
            $xmltext = file_get_contents($manifest);

            $pattern = '/&(?!\w{2,6};)/';
            $replacement = '&amp;';
            $xmltext = preg_replace($pattern, $replacement, $xmltext);

            $objXML = new xml2Array();
            $manifests = $objXML->parse($xmltext);

            if (count($manifests) > 0) {
                $m_sXmlns2004 = "http://www.imsglobal.org/xsd/imscp_v1p1";
                $m_sXmlns12 = "http://www.imsproject.org/xsd/imscp_rootv1p1p2";

                foreach ($manifests as $block)
                {

                    if($block['name'] == 'MANIFEST')
                    {
                        $manifestIdentifier = $this->addslashes_js($block['attrs']['IDENTIFIER']);

                        $checkFlag = false;

                        if(isset($manifestIdentifier) && $manifestIdentifier != "")
                        {
                            if(isset($block['attrs']['XMLNS']) && $block['attrs']['XMLNS'] != "")
                            {
                                if ($block['attrs']['XMLNS'] == $m_sXmlns2004 || $block['attrs']['XMLNS'] == $m_sXmlns12)
                                 {
                                    $checkFlag = true;

                                    if( isset($block['attrs']['XMLNS:XSI']) &&
                                        isset($block['attrs']['XSI:SCHEMALOCATION']))
                                        {
                                            $xsdLocation = $block['attrs']['XSI:SCHEMALOCATION'];
                                            if( $xsdLocation != "")
                                            {
                                                $checkFlag = $this->checkXsdFile($_dirRoot, $block, "XMLNS", $xsdLocation);
                                                $checkFlag = $this->checkXsdFile($_dirRoot, $block, "XMLNS:ADLCP", $xsdLocation);
                                                $checkFlag = $this->checkXsdFile($_dirRoot, $block, "XMLNS:ADLSEQ", $xsdLocation);
                                                $checkFlag = $this->checkXsdFile($_dirRoot, $block, "XMLNS:ADLNAV", $xsdLocation);
                                                $checkFlag = $this->checkXsdFile($_dirRoot, $block, "XMLNS:IMSSS", $xsdLocation);

                                            }
                                        }

                                        if ($checkFlag == true)
                                            $validation->resultFlag = true;
                                 }
                            }
                        }
                    }

                    $blocks =  $block['children'];

                    if (isset($blocks))
                    {
                        foreach ($blocks as $block)
                        {
                            if($block['name'] == 'METADATA')
                            {
                                if (isset($block['children']))
                                {
                                    foreach ($block['children'] as $metadata)
                                    {
                                        if ($metadata['name'] == 'SCHEMAVERSION')
                                        {
                                            if (isset($metadata['tagData']) && (preg_match("/^(1\.2)$|^(CAM )?(1\.3)$/",$metadata['tagData'],$matches))) {

                                                $validation->version = 'SCORM_'.str_ireplace(".", "", $matches[count($matches)-1]);
                                                $validation->resultFlag = true;
                                            } else {
                                                if (isset($metadata['tagData']) && (preg_match("/^2004 3rd Edition$/",$metadata['tagData'],$matches))) {
                                                    $validation->version = 'SCORM_13';
                                                    $validation->resultFlag = true;
                                                } else if (isset($metadata['tagData']) && (preg_match("/^2004 4th Edition$/",$metadata['tagData'],$matches))) {
                                                    $validation->version = 'SCORM_13';
                                                    $validation->resultFlag = true;
                                                } else {
                                                    $validation->resultFlag = false;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return $validation;
    }

    function checkXsdFile($dirPath, $node, $namespaceName, $xsdLocation)
    {
        $checkFlag = true;

        if(isset($node['attrs'][$namespaceName]))
        {
            $realNamespace = $node['attrs'][$namespaceName];
            $realPrefix = substr($realNamespace, strripos($realNamespace, "/")+1);

            if( $realPrefix == "imsss")
                $realPrefix = "imsss_v1p0";
            $containPos = stripos($xsdLocation, $realNamespace." ".$realPrefix);

            if ($containPos >= 0)
            {
                $realDir = $dirPath.$realPrefix.".xsd";
                if (is_file($realDir))
                {
                    $checkFlag = true;
                } else {
                    $checkFlag = false;
                }
            }
        }
        return $checkFlag;
    }

    function getSCORMManifest($blocks, $scoes) {
        static $parents = array();
        static $resources;

        static $manifest;
        static $organization;

        if (count($blocks) > 0) {
            foreach ($blocks as $block) {
                //file_put_contents('/var/www/lms_test/app/Helpers/libs/sco-errors.log', print_r('blname:'.$block['name'].PHP_EOL, true), FILE_APPEND);
                switch ($block['name']) {
                    case 'METADATA':
                        if (isset($block['children'])) {
                            foreach ($block['children'] as $metadata) {
                                if ($metadata['name'] == 'SCHEMAVERSION') {

                                    if (empty($scoes->version)) {
                                        if (isset($metadata['tagData']) && (preg_match("/^(1\.2)$|^(CAM )?(1\.3)$/",$metadata['tagData'],$matches))) {
                                            $scoes->version = 'SCORM_'.str_ireplace(".", "", $matches[count($matches)-1]);
                                        } else {
                                            if (isset($metadata['tagData']) && (preg_match("/^2004 3rd Edition$/",$metadata['tagData'],$matches))) {
                                                $scoes->version = 'SCORM_13';
                                            } else if (isset($metadata['tagData']) && (preg_match("/^2004 4th Edition$/",$metadata['tagData'],$matches))) {
                                                 $scoes->version = 'SCORM_13';
                                            } else  {
                                                $scoes->version = 'SCORM_12';
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    break;
                    case 'MANIFEST':
                        $manifest = $this->addslashes_js($block['attrs']['IDENTIFIER']);
                        $organization = '';
                        $resources = array();
                        $resources = $this->scorm_get_resources($block['children']);

                        $scoes = $this->getSCORMManifest($block['children'], $scoes);
                        if (count($scoes->elements) <= 0) {
                            foreach ($resources as $item => $resource) {
                                if (!empty($resource['HREF'])) {
                                    $sco = new stdClass();
                                    $sco->identifier = $item;
                                    $sco->title = $item;
                                    $sco->parent = '/';
                                    $sco->launch = $this->addslashes_js($resource['HREF']);
                                    $sco->scormtype = $this->addslashes_js($resource['ADLCP:SCORMTYPE']);

                                    $scoes->elements[$manifest][$organization][$item] = $sco;
                                }
                            }
                        }
                    break;
                    case 'ORGANIZATIONS':
                        if (!isset($scoes->defaultorg) && isset($block['attrs']['DEFAULT'])) {
                            $scoes->defaultorg = $this->addslashes_js($block['attrs']['DEFAULT']);
                        }
                        $scoes = $this->getSCORMManifest($block['children'],$scoes);
                    break;
                    case 'ORGANIZATION':
                        $identifier = $this->addslashes_js($block['attrs']['IDENTIFIER']);
                        $organization = '';
                        $scoes->elements[$manifest][$organization][$identifier] = new stdClass();
                        $scoes->elements[$manifest][$organization][$identifier]->identifier = $identifier;
                        $scoes->elements[$manifest][$organization][$identifier]->parent = '/';
                        $scoes->elements[$manifest][$organization][$identifier]->launch = '';
                        $scoes->elements[$manifest][$organization][$identifier]->scormtype = '';

                        $parents = array();
                        $parent = new stdClass();
                        $parent->identifier = $identifier;
                        $parent->organization = $organization;
                        array_push($parents, $parent);
                        $organization = $identifier;

                        $scoes = $this->getSCORMManifest($block['children'],$scoes);

                        array_pop($parents);
                    break;
                    case 'ITEM':
                        $parent = array_pop($parents);
                        array_push($parents, $parent);

                        $identifier = $this->addslashes_js($block['attrs']['IDENTIFIER']);
                        $scoes->elements[$manifest][$organization][$identifier] = new stdClass();
                        $scoes->elements[$manifest][$organization][$identifier]->identifier = $identifier;
                        $scoes->elements[$manifest][$organization][$identifier]->parent = $parent->identifier;
                        if (!isset($block['attrs']['ISVISIBLE'])) {
                            $block['attrs']['ISVISIBLE'] = 'true';
                        }
                        $scoes->elements[$manifest][$organization][$identifier]->isvisible = $this->addslashes_js($block['attrs']['ISVISIBLE']);
                        if (!isset($block['attrs']['PARAMETERS'])) {
                            $block['attrs']['PARAMETERS'] = '';
                        }
                        $scoes->elements[$manifest][$organization][$identifier]->parameters = $this->addslashes_js($block['attrs']['PARAMETERS']);
                        if (!isset($block['attrs']['IDENTIFIERREF'])) {
                            $scoes->elements[$manifest][$organization][$identifier]->launch = '';
                            $scoes->elements[$manifest][$organization][$identifier]->scormtype = 'asset';
                        } else {
                            $idref = $this->addslashes_js($block['attrs']['IDENTIFIERREF']);
                            $base = '';
                            if (isset($resources[$idref]['XML:BASE'])) {
                                $base = $resources[$idref]['XML:BASE'];
                            }
                            $scoes->elements[$manifest][$organization][$identifier]->launch = $this->addslashes_js($base.$resources[$idref]['HREF']);
                            if (empty($resources[$idref]['ADLCP:SCORMTYPE'])) {
                                $resources[$idref]['ADLCP:SCORMTYPE'] = 'asset';
                            }
                            $scoes->elements[$manifest][$organization][$identifier]->scormtype = $this->addslashes_js($resources[$idref]['ADLCP:SCORMTYPE']);
                        }

                        $parent = new stdClass();
                        $parent->identifier = $identifier;
                        $parent->organization = $organization;
                        array_push($parents, $parent);

                        $scoes = $this->getSCORMManifest($block['children'],$scoes);

                        array_pop($parents);
                    break;
                    case 'TITLE':
                        $parent = array_pop($parents);
                        array_push($parents, $parent);
                        if (!isset($block['tagData'])) {
                            $block['tagData'] = '';
                        }
                        $scoes->elements[$manifest][$parent->organization][$parent->identifier]->title = $this->addslashes_js($block['tagData']);
                    break;
                    case 'ADLCP:PREREQUISITES':
                        if ($block['attrs']['TYPE'] == 'aicc_script') {
                            $parent = array_pop($parents);
                            array_push($parents, $parent);
                            if (!isset($block['tagData'])) {
                                $block['tagData'] = '';
                            }
                            $scoes->elements[$manifest][$parent->organization][$parent->identifier]->prerequisites = $this->addslashes_js($block['tagData']);
                        }
                    break;
                    case 'ADLCP:MAXTIMEALLOWED':
                        $parent = array_pop($parents);
                        array_push($parents, $parent);
                        if (!isset($block['tagData'])) {
                            $block['tagData'] = '';
                        }
                        $scoes->elements[$manifest][$parent->organization][$parent->identifier]->maxtimeallowed = $this->addslashes_js($block['tagData']);
                    break;
                    case 'ADLCP:TIMELIMITACTION':
                        $parent = array_pop($parents);
                        array_push($parents, $parent);
                        if (!isset($block['tagData'])) {
                            $block['tagData'] = '';
                        }
                        $scoes->elements[$manifest][$parent->organization][$parent->identifier]->timelimitaction = $this->addslashes_js($block['tagData']);
                    break;
                    case 'ADLCP:DATAFROMLMS':
                        $parent = array_pop($parents);
                        array_push($parents, $parent);
                        if (!isset($block['tagData'])) {
                            $block['tagData'] = '';
                        }
                        $scoes->elements[$manifest][$parent->organization][$parent->identifier]->datafromlms = $this->addslashes_js($block['tagData']);
                    break;
                    case 'ADLCP:MASTERYSCORE':
                        $parent = array_pop($parents);
                        array_push($parents, $parent);
                        if (!isset($block['tagData'])) {
                            $block['tagData'] = '';
                        }
                        $scoes->elements[$manifest][$parent->organization][$parent->identifier]->masteryscore = $this->addslashes_js($block['tagData']);
                    break;
                    case 'ADLCP:COMPLETIONTHRESHOLD':
                        $parent = array_pop($parents);
                        array_push($parents, $parent);
                        if (!isset($block['tagData'])) {
                            $block['tagData'] = '';
                        }
                        $scoes->elements[$manifest][$parent->organization][$parent->identifier]->threshold = $this->addslashes_js($block['tagData']);
                    break;
                    case 'ADLNAV:PRESENTATION':
                        $parent = array_pop($parents);
                        array_push($parents, $parent);
                        if (!empty($block['children'])) {
                            foreach ($block['children'] as $adlnav) {
                                if ($adlnav['name'] == 'ADLNAV:NAVIGATIONINTERFACE') {
                                    foreach ($adlnav['children'] as $adlnavInterface) {
                                        if ($adlnavInterface['name'] == 'ADLNAV:HIDELMSUI') {
                                            if ($adlnavInterface['tagData'] == 'continue') {
                                                $scoes->elements[$manifest][$parent->organization][$parent->identifier]->hidecontinue = 1;
                                            }
                                            if ($adlnavInterface['tagData'] == 'previous') {
                                                $scoes->elements[$manifest][$parent->organization][$parent->identifier]->hideprevious = 1;
                                            }
                                            if ($adlnavInterface['tagData'] == 'exit') {
                                                $scoes->elements[$manifest][$parent->organization][$parent->identifier]->hideexit = 1;
                                            }
                                            if ($adlnavInterface['tagData'] == 'exitAll') {
                                                $scoes->elements[$manifest][$parent->organization][$parent->identifier]->hideexitall = 1;
                                            }
                                            if ($adlnavInterface['tagData'] == 'abandon') {
                                                $scoes->elements[$manifest][$parent->organization][$parent->identifier]->hideabandon = 1;
                                            }
                                            if ($adlnavInterface['tagData'] == 'abandonAll') {
                                                $scoes->elements[$manifest][$parent->organization][$parent->identifier]->hideabandonall = 1;
                                            }
                                            if ($adlnavInterface['tagData'] == 'suspendAll') {
                                                $scoes->elements[$manifest][$parent->organization][$parent->identifier]->hidesuspendall = 1;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    break;
                    case 'IMSSS:SEQUENCING':
                        $parent = array_pop($parents);
                        array_push($parents, $parent);
                        if (!empty($block['children'])) {
                            foreach ($block['children'] as $sequencing) {
                                if ($sequencing['name']=='IMSSS:CONTROLMODE') {
                                    if (isset($sequencing['attrs']['CHOICE'])) {
                                        $scoes->elements[$manifest][$parent->organization][$parent->identifier]->choice = $sequencing['attrs']['CHOICE'] == 'true'?1:0;
                                    }
                                    if (isset($sequencing['attrs']['CHOICEEXIT'])) {
                                        $scoes->elements[$manifest][$parent->organization][$parent->identifier]->choiceexit = $sequencing['attrs']['CHOICEEXIT'] == 'true'?1:0;
                                    }
                                    if (isset($sequencing['attrs']['FLOW'])) {
                                        $scoes->elements[$manifest][$parent->organization][$parent->identifier]->flow = $sequencing['attrs']['FLOW'] == 'true'?1:0;
                                    }
                                    if (isset($sequencing['attrs']['FORWARDONLY'])) {
                                        $scoes->elements[$manifest][$parent->organization][$parent->identifier]->forwardonly = $sequencing['attrs']['FORWARDONLY'] == 'true'?1:0;
                                    }
                                    if (isset($sequencing['attrs']['USECURRENTATTEMPTOBJECTINFO'])) {
                                        $scoes->elements[$manifest][$parent->organization][$parent->identifier]->usecurrentattemptobjectinfo = $sequencing['attrs']['USECURRENTATTEMPTOBJECTINFO'] == 'true'?1:0;
                                    }
                                    if (isset($sequencing['attrs']['USECURRENTATTEMPTPROGRESSINFO'])) {
                                        $scoes->elements[$manifest][$parent->organization][$parent->identifier]->usecurrentattemptprogressinfo = $sequencing['attrs']['USECURRENTATTEMPTPROGRESSINFO'] == 'true'?1:0;
                                    }
                                }
                                if ($sequencing['name']=='ADLSEQ:CONSTRAINEDCHOICECONSIDERATIONS') {
                                    if (isset($sequencing['attrs']['CONSTRAINCHOICE'])) {
                                        $scoes->elements[$manifest][$parent->organization][$parent->identifier]->constrainchoice = $sequencing['attrs']['CONSTRAINCHOICE'] == 'true'?1:0;
                                    }
                                    if (isset($sequencing['attrs']['PREVENTACTIVATION'])) {
                                        $scoes->elements[$manifest][$parent->organization][$parent->identifier]->preventactivation = $sequencing['attrs']['PREVENTACTIVATION'] == 'true'?1:0;
                                    }
                                }
                                if ($sequencing['name']=='ADLSEQ:ROLLUPCONSIDERATIONS')
                                {
                                    if (isset($sequencing['attrs']['REQUIREDFORSATISFIED'])) {
                                        $scoes->elements[$manifest][$parent->organization][$parent->identifier]->requiredforsatisfied = $sequencing['attrs']['REQUIREDFORSATISFIED'];
                                    }
                                    if (isset($sequencing['attrs']['REQUIREDFORNOTSATISFIED'])) {
                                        $scoes->elements[$manifest][$parent->organization][$parent->identifier]->requiredfornotsatisfied = $sequencing['attrs']['REQUIREDFORNOTSATISFIED'];
                                    }
                                    if (isset($sequencing['attrs']['REQUIREDFORCOMPLETED'])) {
                                        $scoes->elements[$manifest][$parent->organization][$parent->identifier]->requiredforcompleted = $sequencing['attrs']['REQUIREDFORCOMPLETED'];
                                    }
                                    if (isset($sequencing['attrs']['REQUIREDFORINCOMPLETE'])) {
                                        $scoes->elements[$manifest][$parent->organization][$parent->identifier]->requiredforincomplete = $sequencing['attrs']['REQUIREDFORINCOMPLETE'];
                                    }
                                    if (isset($sequencing['attrs']['MEASURESATISFACTIONIFACTIVE'])) {
                                        $scoes->elements[$manifest][$parent->organization][$parent->identifier]->measuresatisfactionifactive = $sequencing['attrs']['MEASURESATISFACTIONIFACTIVE'] == 'true'?1:0;
                                    }
                                }
                                if ($sequencing['name']=='IMSSS:RANDOMIZATIONCONTROLS')
                                {
                                    if (isset($sequencing['attrs']['RANDOMIZATIONTIMING'])) {
                                        $scoes->elements[$manifest][$parent->organization][$parent->identifier]->randomizationtiming = $sequencing['attrs']['RANDOMIZATIONTIMING'];
                                    }
                                    if (isset($sequencing['attrs']['SELECTCOUNT'])) {
                                        $scoes->elements[$manifest][$parent->organization][$parent->identifier]->selectcount = $sequencing['attrs']['SELECTCOUNT'];
                                    }
                                    if (isset($sequencing['attrs']['REORDERCHILDREN'])) {
                                        $scoes->elements[$manifest][$parent->organization][$parent->identifier]->reorderchildren = $sequencing['attrs']['REORDERCHILDREN'] == 'true'?1:0;
                                    }
                                    if (isset($sequencing['attrs']['SELECTTIMING'])) {
                                        $scoes->elements[$manifest][$parent->organization][$parent->identifier]->selectiontiming = $sequencing['attrs']['SELECTTIMING'];
                                    }
                                }
                                if ($sequencing['name']=='IMSSS:DELIVERYCONTROLS')
                                {
                                    if (isset($sequencing['attrs']['TRACKED'])) {
                                        $scoes->elements[$manifest][$parent->organization][$parent->identifier]->tracked = $sequencing['attrs']['TRACKED'] == 'true'?1:0;
                                    }
                                    if (isset($sequencing['attrs']['COMPLETIONSETBYCONTENT'])) {
                                        $scoes->elements[$manifest][$parent->organization][$parent->identifier]->completionsetbycontent = $sequencing['attrs']['COMPLETIONSETBYCONTENT'] == 'true'?1:0;
                                    }
                                    if (isset($sequencing['attrs']['OBJECTIVESETBYCONTENT'])) {
                                        $scoes->elements[$manifest][$parent->organization][$parent->identifier]->objectivesetbycontent = $sequencing['attrs']['OBJECTIVESETBYCONTENT'] == 'true'?1:0;
                                    }
                                }
                                if ($sequencing['name']=='IMSSS:OBJECTIVES') {
                                    $objectives = array();
                                    foreach ($sequencing['children'] as $objective) {
                                        $objectivedata = new stdClass();
                                        $objectivedata->primaryobj = 0;
                                        switch ($objective['name']) {
                                            case 'IMSSS:PRIMARYOBJECTIVE':
                                                $objectivedata->primaryobj = 1;
                                            case 'IMSSS:OBJECTIVE':
                                                $objectivedata->satisfiedbymeasure = 0;
                                                if (isset($objective['attrs']['SATISFIEDBYMEASURE'])) {
                                                    $objectivedata->satisfiedbymeasure = $objective['attrs']['SATISFIEDBYMEASURE']== 'true'?1:0;
                                                }
                                                $objectivedata->objectiveid = '';
                                                if (isset($objective['attrs']['OBJECTIVEID'])) {
                                                    $objectivedata->objectiveid = $objective['attrs']['OBJECTIVEID'];
                                                }
                                                $objectivedata->minnormalizedmeasure = 1.0;
                                                if (!empty($objective['children'])) {
                                                    $mapinfos = array();
                                                    foreach ($objective['children'] as $objectiveparam) {
                                                        if ($objectiveparam['name']=='IMSSS:MINNORMALIZEDMEASURE') {
                                                            if (isset($objectiveparam['tagData'])) {
                                                                $objectivedata->minnormalizedmeasure = $objectiveparam['tagData'];
                                                            } else {
                                                                $objectivedata->minnormalizedmeasure = 0;
                                                            }
                                                        }
                                                        if ($objectiveparam['name']=='IMSSS:MAPINFO') {
                                                            $mapinfo = new stdClass();
                                                            $mapinfo->targetobjectiveid = '';
                                                            if (isset($objectiveparam['attrs']['TARGETOBJECTIVEID'])) {
                                                                $mapinfo->targetobjectiveid = $objectiveparam['attrs']['TARGETOBJECTIVEID'];
                                                            }
                                                            $mapinfo->readsatisfiedstatus = 1;
                                                            if (isset($objectiveparam['attrs']['READSATISFIEDSTATUS'])) {
                                                                $mapinfo->readsatisfiedstatus = $objectiveparam['attrs']['READSATISFIEDSTATUS'] == 'true'?1:0;
                                                            }
                                                            $mapinfo->writesatisfiedstatus = 0;
                                                            if (isset($objectiveparam['attrs']['WRITESATISFIEDSTATUS'])) {
                                                                $mapinfo->writesatisfiedstatus = $objectiveparam['attrs']['WRITESATISFIEDSTATUS'] == 'true'?1:0;
                                                            }
                                                            $mapinfo->readnormalizedmeasure = 1;
                                                            if (isset($objectiveparam['attrs']['READNORMALIZEDMEASURE'])) {
                                                                $mapinfo->readnormalizedmeasure = $objectiveparam['attrs']['READNORMALIZEDMEASURE'] == 'true'?1:0;
                                                            }
                                                            $mapinfo->writenormalizedmeasure = 0;
                                                            if (isset($objectiveparam['attrs']['WRITENORMALIZEDMEASURE'])) {
                                                                $mapinfo->writenormalizedmeasure = $objectiveparam['attrs']['WRITENORMALIZEDMEASURE'] == 'true'?1:0;
                                                            }
                                                            array_push($mapinfos,$mapinfo);
                                                        }
                                                    }
                                                    if (!empty($mapinfos)) {
                                                        $objectivedata->mapinfos = $mapinfos;
                                                    }
                                                }
                                            break;
                                        }

                                        array_push($objectives,$objectivedata);
                                        //file_put_contents('/var/www/lms_test/app/Helpers/libs/sco-errors.log', print_r($objectives, true), FILE_APPEND);
                                    }
                                    $scoes->elements[$manifest][$parent->organization][$parent->identifier]->objectives = $objectives;
                                }
                                if ($sequencing['name']=='IMSSS:LIMITCONDITIONS') {
                                    if (isset($sequencing['attrs']['ATTEMPTLIMIT'])) {
                                        $scoes->elements[$manifest][$parent->organization][$parent->identifier]->attemptLimit = $sequencing['attrs']['ATTEMPTLIMIT'];
                                    }
                                    if (isset($sequencing['attrs']['ATTEMPTABSOLUTEDURATIONLIMIT'])) {
                                        $scoes->elements[$manifest][$parent->organization][$parent->identifier]->attemptAbsoluteDurationLimit = $sequencing['attrs']['ATTEMPTABSOLUTEDURATIONLIMIT'];
                                    }
                                }
                                if ($sequencing['name']=='IMSSS:ROLLUPRULES') {
                                    if (isset($sequencing['attrs']['ROLLUPOBJECTIVESATISFIED'])) {
                                        $scoes->elements[$manifest][$parent->organization][$parent->identifier]->rollupobjectivesatisfied = $sequencing['attrs']['ROLLUPOBJECTIVESATISFIED'] == 'true'?1:0;;
                                    }
                                    if (isset($sequencing['attrs']['ROLLUPPROGRESSCOMPLETION'])) {
                                        $scoes->elements[$manifest][$parent->organization][$parent->identifier]->rollupprogresscompletion = $sequencing['attrs']['ROLLUPPROGRESSCOMPLETION'] == 'true'?1:0;
                                    }
                                    if (isset($sequencing['attrs']['OBJECTIVEMEASUREWEIGHT'])) {
                                        $scoes->elements[$manifest][$parent->organization][$parent->identifier]->objectivemeasureweight = $sequencing['attrs']['OBJECTIVEMEASUREWEIGHT'];
                                    }

                                    if (!empty($sequencing['children'])){
                                        $rolluprules = array();
                                        foreach ($sequencing['children'] as $sequencingrolluprule) {
                                            if ($sequencingrolluprule['name']=='IMSSS:ROLLUPRULE' ) {
                                                $rolluprule = new stdClass();
                                                $rolluprule->childactivityset = 'all';
                                                if (isset($sequencingrolluprule['attrs']['CHILDACTIVITYSET'])) {
                                                    $rolluprule->childactivityset = $sequencingrolluprule['attrs']['CHILDACTIVITYSET'];
                                                }
                                                $rolluprule->minimumcount = 0;
                                                if (isset($sequencingrolluprule['attrs']['MINIMUMCOUNT'])) {
                                                    $rolluprule->minimumcount = $sequencingrolluprule['attrs']['MINIMUMCOUNT'];
                                                }
                                                $rolluprule->minimumpercent = 0.0000;
                                                if (isset($sequencingrolluprule['attrs']['MINIMUMPERCENT'])) {
                                                    $rolluprule->minimumpercent = $sequencingrolluprule['attrs']['MINIMUMPERCENT'];
                                                }
                                                if (!empty($sequencingrolluprule['children'])) {
                                                    foreach ($sequencingrolluprule['children'] as $rolluproleconditions) {
                                                        if ($rolluproleconditions['name']=='IMSSS:ROLLUPCONDITIONS') {
                                                            $conditions = array();
                                                            $rolluprule->conditioncombination = 'all';
                                                            if (isset($rolluproleconditions['attrs']['CONDITIONCOMBINATION'])) {
                                                                $rolluprule->conditioncombination = $rolluproleconditions['attrs']['CONDITIONCOMBINATION'];
                                                            }
                                                            foreach ($rolluproleconditions['children'] as $rolluprulecondition) {
                                                                if ($rolluprulecondition['name']=='IMSSS:ROLLUPCONDITION') {
                                                                    $condition = new stdClass();
                                                                    if (isset($rolluprulecondition['attrs']['CONDITION'])) {
                                                                        $condition->cond = $rolluprulecondition['attrs']['CONDITION'];
                                                                    }
                                                                    $condition->operator = 'noOp';
                                                                    if (isset($rolluprulecondition['attrs']['OPERATOR'])) {
                                                                        $condition->operator = $rolluprulecondition['attrs']['OPERATOR'];
                                                                    }
                                                                    array_push($conditions,$condition);
                                                                }
                                                            }
                                                            $rolluprule->conditions = $conditions;
                                                        }
                                                        if ($rolluproleconditions['name']=='IMSSS:ROLLUPACTION') {
                                                            $rolluprule->rollupruleaction = $rolluproleconditions['attrs']['ACTION'];
                                                        }
                                                    }
                                                }
                                                array_push($rolluprules, $rolluprule);
                                            }
                                        }
                                        $scoes->elements[$manifest][$parent->organization][$parent->identifier]->rolluprules = $rolluprules;
                                    }
                                }

                                if ($sequencing['name']=='IMSSS:SEQUENCINGRULES') {
                                    if (!empty($sequencing['children'])) {
                                        $sequencingrules = array();
                                        foreach ($sequencing['children'] as $conditionrules) {
                                            $conditiontype = -1;
                                            switch($conditionrules['name']) {
                                                case 'IMSSS:PRECONDITIONRULE':
                                                    $conditiontype = 0;
                                                break;
                                                case 'IMSSS:POSTCONDITIONRULE':
                                                    $conditiontype = 1;
                                                break;
                                                case 'IMSSS:EXITCONDITIONRULE':
                                                    $conditiontype = 2;
                                                break;
                                            }
                                            if (!empty($conditionrules['children'])) {
                                                $sequencingrule = new stdClass();
                                                foreach ($conditionrules['children'] as $conditionrule) {
                                                    if ($conditionrule['name']=='IMSSS:RULECONDITIONS') {
                                                        $ruleconditions = array();
                                                        $sequencingrule->conditioncombination = 'all';
                                                        if (isset($conditionrule['attrs']['CONDITIONCOMBINATION'])) {
                                                            $sequencingrule->conditioncombination = $conditionrule['attrs']['CONDITIONCOMBINATION'];
                                                        }
                                                        foreach ($conditionrule['children'] as $rulecondition) {
                                                            if ($rulecondition['name']=='IMSSS:RULECONDITION') {
                                                                $condition = new stdClass();
                                                                if (isset($rulecondition['attrs']['CONDITION'])) {
                                                                    $condition->cond = $rulecondition['attrs']['CONDITION'];
                                                                }
                                                                $condition->operator = 'noOp';
                                                                if (isset($rulecondition['attrs']['OPERATOR'])) {
                                                                    $condition->operator = $rulecondition['attrs']['OPERATOR'];
                                                                }
                                                                $condition->measurethreshold = 0.0000;
                                                                if (isset($rulecondition['attrs']['MEASURETHRESHOLD'])) {
                                                                    $condition->measurethreshold = $rulecondition['attrs']['MEASURETHRESHOLD'];
                                                                }
                                                                $condition->referencedobjective = '';
                                                                if (isset($rulecondition['attrs']['REFERENCEDOBJECTIVE'])) {
                                                                    $condition->referencedobjective = $rulecondition['attrs']['REFERENCEDOBJECTIVE'];
                                                                }
                                                                array_push($ruleconditions,$condition);
                                                            }
                                                        }
                                                        $sequencingrule->ruleconditions = $ruleconditions;
                                                    }
                                                    if ($conditionrule['name']=='IMSSS:RULEACTION') {
                                                        $sequencingrule->action = $conditionrule['attrs']['ACTION'];
                                                    }
                                                    $sequencingrule->type = $conditiontype;
                                                }
                                                array_push($sequencingrules,$sequencingrule);
                                            }
                                        }
                                        $scoes->elements[$manifest][$parent->organization][$parent->identifier]->sequencingrules = $sequencingrules;
                                    }
                                }
                            }
                        }
                    break;
                }
            }
        }

        return $scoes;
    }

    public function parseSCORM($manifestfile, $scorm) {
        global $db;

        $launch = "";
        try {
            if (is_file($manifestfile)) {
                $xmltext = file_get_contents($manifestfile);

                $pattern = '/&(?!\w{2,6};)/';
                $replacement = '&amp;';
                $xmltext = preg_replace($pattern, $replacement, $xmltext);

                $objXML = new xml2Array();
                $manifests = $objXML->parse($xmltext);

                $scoes = new stdClass();
                $scoes->version = '';

                $scoes = $this->getSCORMManifest($manifests, $scoes);

                if($scoes != null)
                {
                    if (count($scoes->elements) > 0) {
                        //file_put_contents('/var/www/lms_test/app/Helpers/libs/sco-errors.log', print_r(count($scoes->elements).PHP_EOL, true), FILE_APPEND);
                        foreach ($scoes->elements as $manifest => $organizations) {
                            foreach ($organizations as $organization => $items) {
                                foreach ($items as $identifier => $item) {
                                    //file_put_contents('/var/www/lms_test/app/Helpers/libs/sco-errors.log', print_r($item, true), FILE_APPEND);
                                    // This new db mngt will support all SCORM future extensions
                                    /* scorm_scoes */
                                    $newitem = new SCORMSCOs();

                                    $newitem->scormid = $scorm;
                                    $newitem->manifest = $manifest;
                                    $newitem->organization = $organization;

                                    $standarddatas = array('parent', 'identifier', 'launch', 'scormtype', 'title');

                                    foreach ($standarddatas as $standarddata) {
                                        if (isset($item->$standarddata)) {
                                            $newitem->$standarddata = $this->addslashes_js($item->$standarddata);
                                        } else {
                                            $newitem->$standarddata = '';
                                        }
                                        //file_put_contents('/var/www/lms_test/app/Helpers/libs/sco-errors.log', print_r($newitem->$standarddata, true).PHP_EOL, FILE_APPEND);
                                    }

                                    $newitem->save();

                                    $sco = DB::getPdo()->lastInsertId();
                                    /* scorm_scoes_data */
                                    if ($optionaldatas = $this->scorm_optionals_data($item, $standarddatas)) {

                                        $data = new SCORMSCOData();

                                        $data->scormid = $scorm;
                                        $data->scoid = $sco;

                                        foreach ($optionaldatas as $optionaldata) {
                                            if (isset($item->$optionaldata)) {

                                                $data->elementname =  $optionaldata;
                                                $data->elementvalue = $this->addslashes_js($item->$optionaldata);

                                                $data->save();
                                            }
                                        }
                                    }
                                    /* scorm_seq_ruleconds / scorm_seq_rulecond */
                                    if (isset($item->sequencingrules)) {
                                        foreach($item->sequencingrules as $sequencingrule) {
                                            $rule = new SCORMSeqRuleConds();
                                            $rule->scormid = $scorm;//$scormid;
                                            $rule->scoid = $sco;//$id;
                                            $rule->ruletype = $sequencingrule->type;
                                            $rule->conditioncombination = $sequencingrule->conditioncombination;
                                            $rule->action = $sequencingrule->action;

                                            $rule->save();
                                            $ruleid = DB::getPdo()->lastInsertId();

                                            if (isset($sequencingrule->ruleconditions)) {
                                                foreach($sequencingrule->ruleconditions as $rulecondition) {
                                                    $rulecond = new SCORMSeqRuleCond();
                                                    $rulecond->scormid = $scorm;//$scormid;
                                                    $rulecond->scoid = $sco;//$id;
                                                    $rulecond->ruleconditionsid = $ruleid;
                                                    $rulecond->operator = $rulecondition->operator;
                                                    $rulecond->referencedobjective = $rulecondition->referencedobjective;
                                                    $rulecond->measurethreshold = $rulecondition->measurethreshold;
                                                    $rulecond->conditions = $rulecondition->cond;

                                                    $rulecond->save();
                                                    $rulecondid = DB::getPdo()->lastInsertId();
                                                }
                                            }
                                        }
                                    }

                                    /* scorm_seq_rolluprule / scorm_seq_rolluprulecond */
                                    if (isset($item->rolluprules)) {
                                        foreach($item->rolluprules as $rolluprule) {
                                            $rollup = new SCORMSeqRollupRule();
                                            $rollup->scormid = $scorm;//$scormid;
                                            $rollup->scoid =  $sco;//$id;
                                            $rollup->childactivityset = $rolluprule->childactivityset;
                                            $rollup->minimumcount = $rolluprule->minimumcount;
                                            $rollup->minimumpercent = $rolluprule->minimumpercent;
                                            $rollup->rollupruleaction = $rolluprule->rollupruleaction;
                                            $rollup->conditioncombination = $rolluprule->conditioncombination;

                                            $rollup->save();
                                            $rollupruleid = $rollup->id; //DB::getPdo()->lastInsertId();

                                            if (isset($rolluprule->conditions)) {
                                                foreach($rolluprule->conditions as $condition){
                                                    $cond = new SCORMSeqRollupRuleCond();
                                                    $cond->scormid = $scorm;//$scormid;
                                                    $cond->scoid = $sco;//$id;
                                                    $cond->rollupruleid = $rollupruleid;
                                                    $cond->operator = $condition->operator;
                                                    $cond->conditions = $condition->cond;

                                                    $cond->save();
                                                    $conditionid = $cond->id; //DB::getPdo()->lastInsertId();
                                                }
                                            }
                                        }
                                    }
                                    /* scorm_seq_objective / scorm_seq_mapinfo */
                                    if (isset($item->objectives)) {
                                        foreach($item->objectives as $objective) {
                                            $obj = new SCORMSeqObjective();
                                            $obj->scorm = $scorm;//$scormid;
                                            $obj->scoid = $sco;//$id;
                                            $obj->primaryobj = $objective->primaryobj;
                                            $obj->satisfiedbymeasure = $objective->satisfiedbymeasure;
                                            $obj->objectiveid = $objective->objectiveid;
                                            $obj->minnormalizedmeasure = $objective->minnormalizedmeasure;
                                            //file_put_contents('/var/www/lms_test/app/Helpers/libs/sco-errors.log', print_r($obj, true).PHP_EOL, FILE_APPEND);
                                            $obj->save();
                                            $objectiveid = DB::getPdo()->lastInsertId();

                                            if (isset($objective->mapinfos)) {
                                                foreach($objective->mapinfos as $objmapinfo) {
                                                    $mapinfo = new SCORMSeqMapinfo();
                                                    $mapinfo->scormid = $scorm;//$scormid;
                                                    $mapinfo->scoid = $sco;//$id;
                                                    $mapinfo->objectiveid = $objectiveid;
                                                    $mapinfo->targetobjectiveid = $objmapinfo->targetobjectiveid;
                                                    $mapinfo->readsatisfiedstatus = $objmapinfo->readsatisfiedstatus;
                                                    $mapinfo->writesatisfiedstatus = $objmapinfo->writesatisfiedstatus;
                                                    $mapinfo->readnormalizedmeasure = $objmapinfo->readnormalizedmeasure;
                                                    $mapinfo->writenormalizedmeasure = $objmapinfo->writenormalizedmeasure;
                                                    //file_put_contents('/var/www/lms_test/app/Helpers/libs/sco-errors.log', print_r($mapinfo, true).PHP_EOL, FILE_APPEND);
                                                    $mapinfo->save();
                                                    $mapinfoid = DB::getPdo()->lastInsertId();
                                                }
                                            }
                                        }
                                    }

                                    if ($newitem->parent != "/") {
                                        $launch = $newitem->launch;
                                    }
                                }
                            }
                        }
                    }
                }
                else
                    return -1;
            }
        } catch(Exception $ex) {
            $launch = $ex;
        }
        return $launch;
    }

    function scorm_optionals_data($item, $standarddata) {
        $result = array();
        $sequencingdata = array('sequencingrules','rolluprules','objectives');
        foreach ($item as $element => $value) {
            if (! in_array($element, $standarddata)) {
                if (! in_array($element, $sequencingdata)) {
                    $result[] = $element;
                }
            }
        }
        return $result;
    }

    function scorm_is_leaf($sco) {
        if (getRecord('scorm_scoes','scorm',$sco->scorm,'parent',$sco->identifier)) {
            return false;
        }
        return true;
    }

    function scorm_get_parent($sco) {
        if ($sco->parent != '/') {
            if ($parent = getRecord('scorm_scoes','scorm',$sco->scorm,'identifier',$sco->parent)) {
                return scorm_get_sco($parent->id);
            }
        }
        return null;
    }

    function scorm_get_children($sco) {
        if ($children = getRecords('scorm_scoes','scorm',$sco->scorm,'parent',$sco->identifier, " order by id")) {//originally this said parent instead of childrean

            return $children;
        }
        return null;
    }

    function array_search_object($activity, $scos=array())
    {
        $number = 0;
        if(!empty($scos))
        {
            foreach ($scos as $sco)
            {
                if($activity->id == $sco->id)
                    return $number;
                else
                    $number++;
            }
        }
        return -1;
    }
    function scorm_get_available_descendent($descend = array(),$sco){
        if($sco == null){
            return $descend;
        }
        else{
            $avchildren = scorm_get_available_children($sco);
            foreach($avchildren as $avchild){
                array_push($descend,$avchild);
            }
            foreach($avchildren as $avchild){
                scorm_get_available_descendent($descend,$avchild);
            }
        }
    }

    function scorm_get_siblings($sco) {
        if ($siblings = getRecords('scorm_scoes','scorm',$sco->scorm,'parent',$sco->parent)) {

            if (!empty($siblings)) {
               return $siblings;
            }
        }
        return null;
    }

    function scorm_get_ancestors($sco, $ancestors) {
        if ($sco->parent != '/') {
            $parent = scorm_get_parent($sco);
            array_push($ancestors, $sco);
            $ancestors = scorm_get_ancestors($parent, $ancestors);

            return $ancestors;
        } else {
            array_push($ancestors, $sco);
            return $ancestors;
        }

    }

    function scorm_get_preorder($sco) {
      /*if ($sco != null) {
            array_push($preorder,$sco);
            $children = scorm_get_children($sco);
            foreach ($children as $child){
                scorm_get_preorder($sco);
            }
        } else {
            return $preorder;
        }*/
        if(isset($sco)){
            // modified by OIJ 2010/06/03
            return getRecords("scorm_scoes", "scorm", $sco->scorm, '', '', 'ORDER BY id asc');
        }
        else
            return null;
    }

    function scorm_find_common_ancestor($ancestors, $sco) {
        $pos = scorm_array_search($sco,$ancestors);
        if ($sco->parent != '/') {
            if ($pos == -1) {
                return scorm_find_common_ancestor($ancestors,scorm_get_parent($sco));
            }
        }

        return $pos;
    }
    /**
     * A temporary method of generating GUIDs of the correct format for our DB.
     * @return String contianing a GUID in the format: aaaaaaaa-bbbb-cccc-dddd-eeeeeeeeeeee
     *
     * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
     * All Rights Reserved.
     * Contributor(s): ______________________________________..
     */
    function create_guid()
    {
        $microTime = microtime();
        list($a_dec, $a_sec) = explode(" ", $microTime);

        $dec_hex = dechex($a_dec* 1000000);
        $sec_hex = dechex($a_sec);

        $this->ensure_length($dec_hex, 5);
        $this->ensure_length($sec_hex, 6);

        $guid = "";
        $guid .= $dec_hex;
        $guid .= $this->create_guid_section(3);
        $guid .= '-';
        $guid .= $this->create_guid_section(4);
        $guid .= '-';
        $guid .= $this->create_guid_section(4);
        $guid .= '-';
        $guid .= $this->create_guid_section(4);
        $guid .= '-';
        $guid .= $sec_hex;
        $guid .= $this->create_guid_section(6);

        return $guid;

    }

    function create_guid_section($characters)
    {
        $return = "";
        for($i=0; $i<$characters; $i++)
        {
            $return .= dechex(mt_rand(0,15));
        }
        return $return;
    }

    function ensure_length(&$string, $length)
    {
        $strlen = strlen($string);
        if($strlen < $length)
        {
            $string = str_pad($string,$length,"0");
        }
        else if($strlen > $length)
        {
            $string = substr($string, 0, $length);
        }
    }
}

/* Usage
 Grab some XML data, either from a file, URL, etc. however you want. Assume storage in $strYourXML;

 $objXML = new xml2Array();
 $arrOutput = $objXML->parse($strYourXML);
 print_r($arrOutput); //print it out, or do whatever!

*/
class xml2Array {

   var $arrOutput = array();
   var $resParser;
   var $strXmlData;

   /**
   * Convert a utf-8 string to html entities
   *
   * @param string $str The UTF-8 string
   * @return string
   */
   function utf8_to_entities($str) {
       global $CFG;

       $entities = '';
       $values = array();
       $lookingfor = 1;

       return $str;
   }

   /**
   * Parse an XML text string and create an array tree that rapresent the XML structure
   *
   * @param string $strInputXML The XML string
   * @return array
   */
   function parse($strInputXML) {
           $this->resParser = xml_parser_create ('UTF-8');
           xml_set_object($this->resParser,$this);
           xml_set_element_handler($this->resParser, "tagOpen", "tagClosed");

           xml_set_character_data_handler($this->resParser, "tagData");

           $this->strXmlData = xml_parse($this->resParser,$strInputXML );
           if(!$this->strXmlData) {
               die(sprintf("XML error: %s at line %d",
                           xml_error_string(xml_get_error_code($this->resParser)),
                           xml_get_current_line_number($this->resParser)));
           }

           xml_parser_free($this->resParser);

           return $this->arrOutput;
   }

   function tagOpen($parser, $name, $attrs) {
       $tag=array("name"=>$name,"attrs"=>$attrs);
       array_push($this->arrOutput,$tag);
   }

   function tagData($parser, $tagData) {
       if(trim($tagData)) {
           if(isset($this->arrOutput[count($this->arrOutput)-1]['tagData'])) {
               $this->arrOutput[count($this->arrOutput)-1]['tagData'] .= $this->utf8_to_entities($tagData);
           } else {
               $this->arrOutput[count($this->arrOutput)-1]['tagData'] = $this->utf8_to_entities($tagData);
           }
       }
   }

   function tagClosed($parser, $name) {
       $this->arrOutput[count($this->arrOutput)-2]['children'][] = $this->arrOutput[count($this->arrOutput)-1];
       array_pop($this->arrOutput);
   }

}
