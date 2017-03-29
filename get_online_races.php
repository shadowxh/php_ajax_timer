<?php

		
	class Racer{
			public $driver_reg;
			public $car_no;
			public $_name;
			public $team;
			public $best_time;
			public $best_time_ddd;
			public $last_time;
			public $last_time_ddd;
			public $laps;
			public $total_time;
			public $total_time_ddd;
			function __construct($driver_reg_,$car_no_,$name_,$team_,$best_time_,$best_time_ddd_,$last_time_,$last_time_ddd_,$laps_,$total_time_,$total_time_ddd_){
	                	$this->driver_reg=$driver_reg_;
                        	$this->car_no=$car_no_;
				$this->_name=$name_;
                        	$this->team=$team_;
                        	$this->best_time=$best_time_;
                        	$this->best_time_ddd=$best_time_ddd_;
                        	$this->last_time=$last_time_;
                        	$this->last_time_ddd=$last_time_ddd_;
                        	$this->laps=$laps_;
                        	$this->total_time=$total_time_;
                        	$this->total_time_ddd=$total_time_ddd_;
		}
	}
	function get_data_from($database_name,$datatable_name)
	{
		$con=mysql_connect("localhost:3306","root","") or die ('Not connected:'.mysql_error());
        	mysql_select_db($database_name,$con);
		mysql_query('set names utf8');
        	$now_info=mysql_query("SELECT * FROM ".$datatable_name);
        	while($row=mysql_fetch_array($now_info)) $Data[]=$row;
        	mysql_close($con);
		return $Data;
	}
	
	function addobj($record,$flag)
	{
		$flag=(int)$flag;
		$man=new Racer($record['Driver_Reg'],$record['Car_no'],$record['Driver_name'],$record['Team'],$record['Best_time'],$record['Best_time_DDD'],$flag==1?"":$record['Last_time'],$flag==1?"":$record['Last_time_DDD'],$record['Laps'],$record['Total_time'],$record['Total_time_DDD']);
		return $man;
	}
	function cmp($a,$b)
	{
		$tmp1=($a->total_time).($a->total_time_ddd);
		$tmp2=($b->total_time).($b->total_time_ddd);
		return $tmp1>$tmp2?1:-1;
	}
	function cal_time($time,$ms)
	{
		$t=explode(":",$time);
		$h=(int)$t[0];$m=(int)$t[1];$s=(int)$t[2];
		$ret=$h*3600+$m*60+$s;
		$ret=$ret*1000+(int)$ms;
		return $ret;
	}
	function to_int($double)
	{
		return (int)(floor($double));
	}
	function cal_diff($time1,$ms1,$time2,$ms2)
	{
		$ret="";
		$cnt1=cal_time($time1,$ms1);$cnt2=cal_time($time2,$ms2);
		$cnt=$cnt1-$cnt2;
		$h_unit=60*60*1000;
		$hh=to_int($cnt/$h_unit);
		if($hh)
		{
			$ret.=(($hh<10?"0":"").(string)$hh.":");
		}
		$cnt%=$h_unit;
		$mm=to_int($cnt/(60*1000));
		$ret.=(($mm<10?"0":"").(string)$mm.":");
		$cnt%=(60*1000);
		$ss=to_int($cnt/1000);
		$ret.=(($ss<10?"0":"").(string)$ss.".");
		$ms=$cnt%1000;
		$ret.=(string)$ms;
		if($ms<10) $ret.="00";
		else if($ms<100) $ret.="0";
		return $ret;
	}
	function add_race($one_race)
	{
		return '
		<div class="row-fluid">
                        <!-- 基本信息表 -->
                        <div class="block">
                            <div class="navbar navbar-inner block-header">
                                <div class="muted pull-left">'.substr($one_race[7],0,4).'年度 - '.$one_race[0].' - '.$one_race[1].' - '.$one_race[2].'</div>
                            </div>
                            <div class="block-content collapse in">
                                <div class="span12">
  									<table class="table">
						              <thead>
						                <tr>
						                  <th>赛事名称</th>
						                  <th>分项赛名称</th>
						                  <th>场次</th>
						                  <th>赛车场名称</th>
						                  <th>赛道长度</th>
						                  <th>赛事类别</th>
						                  <th>赛事模式</th>
						                  <th>开始日期</th>
						                  <th>开始时间</th>
						                </tr>
						              </thead>
						              <tbody>
						                <tr>
						                  <td>'.$one_race[0].'</td>
						                  <td>'.$one_race[1].'</td>
						                  <td>'.$one_race[2].'</td>
						                  <td>'.$one_race[3].'</td>
						                  <td>'.$one_race[4].'</td>
						                  <td>'.$one_race[5].'</td>
						                  <td>'.$one_race[6].'</td>
						                  <td>'.$one_race[7].'</td>
						                  <td>'.$one_race[8].'</td>
						                </tr>
						              </tbody>
						            </table>
                                </div>
                            </div>
                        </div>
                        <!-- /block -->
                    </div>
		';

	}
	function add_man($racers,$flag)
	{
		$ret='
			<div class="row-fluid">
                        <!-- 成绩 -->
                        <div class="block">
                            <div class="navbar navbar-inner block-header">
                                <div class="muted pull-left">比赛成绩排名</div>
                            </div>
                            <div class="block-content collapse in">
                                <div class="span12">
  									<table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered">
										<thead>
											<tr>
												<th>排名</th>
												<th>车号</th>
												<th>车手</th>
												<th>车队</th>'.($flag=="1"?'':'<th>最快单圈</th><th>当前圈速</th><th>总圈数</th>').
												'<th>总时间</th>
												<th>差距</th>
											</tr>
										</thead>
										<tbody>';
		for($i=0;$i<count($racers);$i++)
		{
			$ret.='<tr class="'.($i%2==0?"odd":"even").'">
				<td>'.($i+1).'</td>
				<td>'.$racers[$i]->car_no.'</td>
				<td>'.$racers[$i]->_name.'</td>
				<td class="center">'.$racers[$i]->team.'</td>'
				.($flag=="1"?'':('<td class="center">'.$racers[$i]->best_time.'.'.$racers[$i]->best_time_ddd.'</td>
                                <td class="center">'.$racers[$i]->last_time.'.'.$racers[$i]->last_time_ddd.'</td>
                                <td class="center">'.$racers[$i]->laps.'</td>')).
                                '<td class="center">'.$racers[$i]->total_time.'.'.$racers[$i]->total_time_ddd.'</td>
				<td class="center">'.($i==0?"":cal_diff($racers[$i]->total_time,$racers[$i]->total_time_ddd,$racers[$i-1]->total_time,$racers[$i-1]->total_time_ddd)).'</td>
				</tr>';
		}								
		$ret.='</tbody></table></div></div></div>
                        <!-- /block -->
                    </div>';
		
		return $ret;
	}

	if(!empty($_GET) && ($_GET['page']=='0'))
	{
		$response="";
		$races=get_data_from("testcom","now");
		$race_ids=array();
		foreach($races as $record) 
		{
			$race_ids[]=$record['Race_id'];
		}
		$cnt=count($race_ids);
		if(!$cnt) {echo "no races is going on!";exit(1);}
		sort($race_ids);
		$race_ids_unique=array();
		$race_ids_unique[$race_ids[0]]=array();
		for($i=1;$i<count($race_ids);$i++)
		{
			if($race_ids[$i]!=$race_ids[$i-1])
			{
				$race_ids_unique[$race_ids[$i]]=array();
			}
		}
		
		foreach($races as $record)
		{
			$race_ids_unique[$record['Race_id']][]=addobj($record,$_GET['page']);
		}
		$Data=get_data_from("testcom","race");
		$all_races=array();
		foreach($Data as $record)
		{
			$all_races[$record['Race_id']]=array($record['Race'],$record['Race_Group'],$record['Race_Event'],$record['Circuit_Name'],$record['Circuit_Length'],$record['Race_Run'],$record['Race_Type'],$record['Race_start_Data'],$record['Race_start_Time']);
		}
		foreach($race_ids_unique as $key=>$val)
		{
			$response.=add_race($all_races[$key]);
			usort($race_ids_unique[$key],"cmp");
			$response.=add_man($race_ids_unique[$key],$_GET['page']);
		}
		
		echo $response;


	}
	if(!empty($_GET) && ($_GET['page']=='1'))
	{
		$response="";
                $races=get_data_from("testcom","history");
                $race_ids=array();
                foreach($races as $record)
                {
                        $race_ids[]=$record['Race_id'];
                }
                $cnt=count($race_ids);
                if(!$cnt) {echo "no historical races!";exit(1);}
                sort($race_ids);
                $race_ids_unique=array();
                $race_ids_unique[$race_ids[0]]=array();
                for($i=1;$i<count($race_ids);$i++)
                {
                        if($race_ids[$i]!=$race_ids[$i-1])
                        {
                                $race_ids_unique[$race_ids[$i]]=array();
                        }
                }
		foreach($races as $record)
                {
                        $race_ids_unique[$record['Race_id']][]=addobj($record,$_GET['page']);
                }
                $Data=get_data_from("testcom","race");
                $all_races=array();
                foreach($Data as $record)
                {
                        $all_races[$record['Race_id']]=array($record['Race'],$record['Race_Group'],$record['Race_Event'],$record['Circuit_Name'],$record['Circuit_Length'],$record['Race_Run'],$record['Race_Type'],$record['Race_start_Data'],$record['Race_start_Time']);
                }
                foreach($race_ids_unique as $key=>$val)
                {
                        $response.=add_race($all_races[$key]);
                        usort($race_ids_unique[$key],"cmp");
                        $response.=add_man($race_ids_unique[$key],$_GET['page']);
                }

                echo $response;

	}


?>
