<?php

use Illuminate\Database\Seeder;

class CertificateDesignsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('certificate_designs')->delete();
        
        \DB::table('certificate_designs')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Certificate',
                'orientation' => 'landscape',
                'pagesize' => 'a4',
                'background' => 'b53b1c84-8c7d-46bd-bc88-87fbd58b47fc.jpg',
                'thumbnail' => NULL,
                'content' => '<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Starter Template</title>
<style>
@import url(\'https://fonts.googleapis.com/css?family=Great+Vibes|Open+Sans|Oswald\');
@font-face {
font-family: \'HanWangHeiLight\';
src: url(\'@FONT_PATH\') format(\'truetype\');
}
@page { margin: 0in; }
body {
font-family: \'Open Sans\', sans-serif;
background: url(@BACKGROUND);
background-position: center center;
background-repeat: no-repeat;
background-size: 100%;
width:100%;
height:100%;
}
.course-title {
font-family: \'HanWangHeiLight\', sans-serif
}
.title {
font-family: \'Oswald\', sans-serif;
text-align: center;
font-size: 48px;
color: #414042;
margin-top: 200px;
line-height: 0.1;
}
.action {
font-family: \'Oswald\', sans-serif;
text-align: center;
font-size: 18px;
color: #594741;
margin-top: 65px;
}
.name {
font-family: \'Great Vibes\', cursive;
text-align: center;
font-size: 90px;
color: #594741;
margin-top: 14px;
}
.reason {
text-align: center;
font-size: 15px;
line-height: 0.8;
color: #414042;
margin-top: 8px;
width: 60%;
margin-left: 20%;
}
table.info {
width: 500px;
border: none;
margin-top: 90px;
margin-left: 100px;
}
.date {
font-size: 14px;
color:  #414042;
text-align: center;
}
.certified_date {
font-size: 12px;
color:  #414042;
text-align: center;
padding-top: 0;
line-height: 1;
letter-spacing: 2px;
}
.validity {
font-size: 10px;
text-align: center;
color:  #414042;
}
.qrcode {
position: absolute;
bottom: 200px;
right: 200px;
}
</style>
</head>
<body style="background: url(@BACKGROUND)">
<div style="height:25px"></div>
<div class="title">Certificate of Completion</div>
<div class="action">This is Presented to :</div>
<div class="name">@RECIPIENT</div>
<div class="reason">
For completing learning course :
<br/>
<br/>
<strong class="course-title">@COURSETITLE</strong>
</div>

<table class="info">
<tr>
<td width="260" class="certified_date">@VALIDITY_DURATION</td>
<td width="90"> </td>
<td width="260" class="certified_date">@SIGNATURE</td>
</tr>
<tr>
<td class="date">VALID DATE</td>
<td></td>
<td class="date">@SIGNER</td>
</tr>
<tr>
<td class="validity"></td>
<td></td>
<td class="validity">@POSITION</td>
</tr>
</table>

<img src="data:image/png;base64, @QRCODE " class="qrcode">

</body>
</html>',
                'draft' => 0,
                'created_by' => 1,
                'updated_by' => 1,
                'deleted_by' => NULL,
                'created_at' => '2019-01-07 19:13:10',
                'updated_at' => '2019-01-07 19:15:14',
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}