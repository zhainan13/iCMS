<?php
/**
* iCMS - i Content Management System
* Copyright (c) 2007-2012 idreamsoft.com iiimon Inc. All rights reserved.
*
* @author coolmoo <idreamsoft@qq.com>
* @site http://www.idreamsoft.com
* @licence http://www.idreamsoft.com/license.php
* @version 6.0.0
*/
class admincpApp{
    public function __construct() {}
    public function do_logout(){
   	    members::logout();
    	iUI::success('注销成功!','url:'.iPHP_SELF);
    }

    public function do_iCMS(){
        //数据统计
        $rs=iDB::all("SHOW FULL TABLES FROM `".iPHP_DB_NAME."` WHERE table_type = 'BASE TABLE';");
        foreach($rs as $k=>$val) {
            if(strstr(iPHP_DB_PREFIX, $val['Tables_in_'.iPHP_DB_NAME])===false) {
                $iTable[]=strtoupper($val['Tables_in_'.iPHP_DB_NAME]);
            }else {
                $oTable[]=$val['Tables_in_'.iPHP_DB_NAME];
            }
        }
        $content_datasize = 0;
        $tables = iDB::all("SHOW TABLE STATUS");
        $_count	= count($tables);
        for ($i=0;$i<$_count;$i++) {
            $tableName	= strtoupper($tables[$i]['Name']);
            if(in_array($tableName,$iTable)) {
                $datasize += $tables[$i]['Data_length'];
                $indexsize += $tables[$i]['Index_length'];
                if (stristr(strtoupper(iPHP_DB_PREFIX."article,".iPHP_DB_PREFIX."category,".iPHP_DB_PREFIX."comment,".iPHP_DB_PREFIX."article_data"),$tableName)) {
                    $content_datasize += $tables[$i]['Data_length']+$tables[$i]['Index_length'];
                }
            }
        }
        $acc = iDB::value("SELECT count(*) FROM `#iCMS@__category` WHERE `appid`='".iCMS_APP_ARTICLE."'");
        $tac = iDB::value("SELECT count(*) FROM `#iCMS@__category` WHERE `appid`='".iCMS_APP_TAG."'");
        $pac = iDB::value("SELECT count(*) FROM `#iCMS@__category` WHERE `appid`='".iCMS_APP_PUSH."'");

        $ac  = iDB::value("SELECT count(*) FROM `#iCMS@__article`");
        $ac0 = iDB::value("SELECT count(*) FROM `#iCMS@__article` WHERE `status`='0'");
        $ac2 = iDB::value("SELECT count(*) FROM `#iCMS@__article` WHERE `status`='2'");

        $ctc = iDB::value("SELECT count(*) FROM `#iCMS@__comment`");
        $tc  = iDB::value("SELECT count(*) FROM `#iCMS@__tags`");
        $kc  = iDB::value("SELECT count(*) FROM `#iCMS@__keywords`");
        $pc  = iDB::value("SELECT count(*) FROM `#iCMS@__push`");
        $uc  = iDB::value("SELECT count(*) FROM `#iCMS@__user`");
        $fdc = iDB::value("SELECT count(*) FROM `#iCMS@__file_data`");
        $lc  = iDB::value("SELECT count(*) FROM `#iCMS@__links`");

    	include admincp::view("index");
    }
    public function okORno($o) {
        return $o?'<font color="green"><i class="fa fa-check"></i></font>':'<font color="red"><i class="fa fa-times"></i></font>';
    }
}